# 05 — Carga one-shot no Postgres gerido e go-live

Quinto e último passo da migração (issue #8). Com o banco já migrado e validado em PostgreSQL 17 local (passo [03](03-postgres-transplant.md)) e a integridade de anexos verificada (passo [04](04-attachment-state.md)), esta fase gera o artefato de dump, carrega-o no Postgres gerido via tunnel SSH, semeia os arquivos de usuário no bind-mount do VPS e valida o fórum no ar.

Toda a carga de dados aqui é uma operação **one-shot, manual, fora do CI/CD** — o deploy recorrente (`.gitea/workflows/deploy.yml`) nunca reexecuta a migração.

## Pré-requisitos

- Passos 01–04 concluídos; harness de paridade (`scripts/db_transplant.py verify`) e integridade de anexos (`scripts/verify_attachments.py`) passando contra o PostgreSQL 17 local.
- Cliente `psql` e `rsync` instalados na máquina de migração.
- Alias `ssh codelab` configurado (o Postgres gerido só é alcançável de fora por SSH).
- `.env` com `DB_NAME`, `DB_USER` e o `DB_PASSWORD` **do banco gerido** exportado no ambiente ao rodar a carga.
- Database/role `lithharbor` já provisionados no Postgres gerido (fora de escopo desta migração) e vazios.

## 1. Gerar o pg_dump determinístico

```sh
./scripts/dump_migrated_pg.sh
```

Gera `data/migration/lithharbor-pg17.sql` a partir do PostgreSQL 17 local. O dump é `--no-owner --no-privileges` (role-agnóstico, restaura sob qualquer role) e `--clean --if-exists` (reexecutável contra um destino já populado). É o artefato portátil da carga.

## 2. Restaurar no Postgres gerido via tunnel SSH

```sh
DB_PASSWORD='<senha-do-banco-gerido>' ./scripts/load_managed_postgres.sh
```

Abre um port-forward local (`127.0.0.1:15432` → `postgres:5432` no VPS) via `ssh codelab`, carrega o dump com `psql -v ON_ERROR_STOP=1` e derruba o tunnel ao final. Variáveis ajustáveis: `SSH_HOST`, `LOCAL_PORT`, `DB_TUNNEL_HOST`, `DB_TUNNEL_PORT`, `DUMP`.

Confira a paridade contra a origem local antes de seguir (contagem de linhas nas tabelas de alto valor):

```sh
for t in phpbb_users phpbb_posts phpbb_topics phpbb_privmsgs; do
  echo -n "$t local="; docker exec -e PGPASSWORD="${DB_PASSWORD:-dev}" postgres \
    psql -U lithharbor -d lithharbor -At -c "SELECT count(*) FROM $t;"
done
```

Compare com as mesmas contagens no destino (via o tunnel aberto, `psql -h 127.0.0.1 -p 15432 ...`).

## 3. Semear anexos e avatares no bind-mount do VPS

```sh
./scripts/seed_managed_vps.sh
```

`seed_managed_vps.sh` é o caminho canônico de go-live: faz `rsync -a --delete` dos arquivos de usuário do workspace local direto para o bind-mount remoto do VPS, no layout que o `compose.yml` monta:

- `www/forum/files/` → `/opt/codelab/apps/lithharbor/data/files/`
- `www/forum/images/avatars/upload/` → `/opt/codelab/apps/lithharbor/data/avatars/`

O `scripts/seed_user_content.sh` do passo [04](04-attachment-state.md) é apenas o seed **local** (para `data/forum-state`, usado na validação em PG17 local); ele não semeia o VPS e não corresponde ao layout de `compose.yml`.

`cache/` e `store/` são graváveis e regeneráveis pelo phpBB — não são semeados. Como os arquivos saíram do git (passo 04), a origem do seed é o workspace de migração atual.

O `deploy-stack` faz `chown` do diretório de dados para uid 1000 a cada deploy. Rode um deploy após o seed (ou aplique o chown 1000 no VPS) para que o php-fpm — que roda como uid 1000 — seja dono dos arquivos.

## 4. Subir o stack e validar (checklist manual)

Estes gates são comportamentais e frágeis de automatizar numa migração one-shot; ficam como checklist executado durante a janela de migração:

- [ ] Deploy disparado (push no branch `main`) publica as duas imagens e sobe o stack; o `deploy.yml` **não** contém nenhum passo de carga de dados.
- [ ] `https://lithharbor.net` responde com certificado TLS válido (`letsencrypt`).
- [ ] `https://www.lithharbor.net` redireciona (301) para o apex.
- [ ] Conteúdo antigo visível: tópicos, posts (BBCode/smilies/links renderizados pelo reparse s9e), contagens de posts.
- [ ] Download de anexo antigo funciona.
- [ ] Avatares antigos aparecem.
- [ ] Login com senha legada funciona.
- [ ] ACP (painel de administração 3.3.17) acessível; permissões, grupos e moderadores preservados.
- [ ] Novo upload de anexo grava no bind-mount (prova de que o php-fpm uid 1000 tem permissão de escrita) e sobrevive a um redeploy.

## Notas

- **uid 1000:** o `Dockerfile` remapeia `www-data` para uid/gid 1000, alinhando o processo php-fpm ao `chown` que o `deploy-stack` aplica no bind-mount. Sem isso, uploads e cache falhariam por permissão.
- **Determinismo do dump:** o artefato é portátil e reexecutável — mesma ordem de objetos (ordem estável do `pg_dump`), sem owner/privileges e com `--clean`. A integridade de dados já foi provada pelo harness de paridade na origem; reconfira as contagens no destino.
