# 02 — Upgrade do phpBB 3.0.5 → 3.3.17 em MariaDB

Segundo passo da migração (issue #3). Com o banco de produção já restaurado num MariaDB local efêmero (passo [01](01-restore-mariadb.md)), aponta os arquivos do **phpBB 3.3.17 vanilla** para esse banco e roda o **updater oficial**, que migra o schema 3.0.5 → 3.3.17. As customizações antigas (MOD phpBB-SEO editado no core; temas AeroBlack/acidtech_tiger) são descartadas — simplesmente não são trazidas nos arquivos novos. O conteúdo de usuário (anexos, avatares enviados) é preservado e tratado num passo posterior. Ao final, valida-se o fórum em 3.3.17/MariaDB.

Esta fatia carrega o **gate de validação manual (HITL)**: o que não tem CLI no core (reconstrução do índice de busca, ressincronização de estatísticas) e o que é comportamental (boot, renderização, login) viram checklist executado durante a janela de migração.

## O que mudou no repositório

- `www/forum/` passou a ser o **phpBB 3.3.17 vanilla**. Os arquivos MOD'ados do 3.0.5, o diretório `phpbb_seo/` e os temas custom não existem mais — só o estilo `prosilver` acompanha o pacote.
- `www/forum/config.php` foi reescrito no **formato 3.3** (`$dbms = 'phpbb\db\driver\mysqli'`, `$acm_type = 'phpbb\cache\driver\file'`, `$phpbb_adm_relative_path`, linhas `@define`). Mantém o padrão do repositório de resolver `DB_*` via `getenv()`.
- O diretório `install/` foi removido: o updater roda por CLI (`bin/phpbbcli.php`, que não precisa dele) e o board só sobe normalmente com o `install/` ausente.
- **Preservados** (conteúdo de usuário, fora do escopo desta issue): `www/forum/files/` (anexos) e `www/forum/images/avatars/upload/` (avatares enviados).

### Procedência do phpBB 3.3.17

Pacote completo oficial `phpBB-3.3.17.zip` de `https://download.phpbb.com/pub/release/3.3/3.3.17/`. O pacote já vem com `vendor/` (dependências do Composer instaladas), então não há passo de `composer install`.

## Pré-requisitos

- O MariaDB efêmero do passo 01 no ar e `healthy` (`scripts/restore_mariadb.sh`).
- Docker com o plugin `docker compose`; `python3` (para a verificação).
- `.env` na raiz com `DB_*` e `MARIADB_ROOT_PASSWORD` (mesmo arquivo do passo 01).

## Executar

```sh
./scripts/upgrade_phpbb.sh
```

O script é idempotente. O que ele faz:

1. Constrói (se faltar) a imagem CLI descartável `lithharbor-phpbb-cli` — PHP 7.4 com o mesmo conjunto de extensões do `Dockerfile` de produção, espelhando o runtime real.
2. (Re)cria o usuário de banco da aplicação no MariaDB efêmero — o dump restaura só a *database* `lithharbor`, não os grants do `mysql`, então o usuário precisa existir para o CLI conectar por TCP.
3. **`db:migrate --safe-mode`**: cria a tabela `phpbb_migrations` e aplica todas as migrations cumulativas. O migrator detecta `config['version'] = 3.0.5` e pula via `effectively_installed()` tudo ≤ 3.0.5, executando de 3.0.6 até 3.3.17. `--safe-mode` isola o core de qualquer código de MOD/extensão remanescente.
4. **`reparser:reparse`**: reprocessa o BBCode/smilies/links antigos para o s9e/TextFormatter (passo separado — o `db:migrate` não reparseia).
5. **`cache:purge`**.

## Verificar

```sh
./scripts/verify_upgrade.sh
```

Asserções (o que dá para checar do banco):

- versão do board = `3.3.17` e a migration final `v3317` registrada;
- estilo padrão = `prosilver` e nenhum usuário apontando para estilo inexistente;
- **todos** os posts reparseados para s9e/TextFormatter (`post_text` em XML — prova do reparse);
- `phpbb_posts` e `phpbb_topics` batem com o dump (a migração não toca conteúdo de membro);
- integridade referencial: nenhuma mensagem privada sem destinatário, nenhum post sem tópico.

### Deltas esperados e benignos (reportados, não asseridos)

A migração oficial altera de propósito duas tabelas — sempre tocando apenas linhas de bot ou já quebradas, nunca conteúdo real de membro:

- **Usuários 2058 → 2057.** As migrations `bot_update` (v310, v33x) renovam a lista de bots: removem 6 bots obsoletos de 2009 (`NG-Search`, `Nutch/CVS`, `OmniExplorer`, `Seekport`, `Synoo`, `WiseNut [Bot]`) e adicionam 5 atuais (`Bing`, `DuckDuckGo`, `Ahrefs`, `Amazon`, `Semrush [Bot]`). Saldo −1, **tudo bot** (`user_type = 2`, 0 posts). Membros reais (`user_type <> 2`) = **2007**, inalterados.
- **Mensagens privadas 236 → 233.** A migration `release_3_0_11_rc1` remove PMs órfãs (sem linha em `phpbb_privmsgs_to`). As três removidas (`msg_id` 1, 2, 225) tinham **zero destinatários** no dump original — já estavam quebradas. Posts (765) e tópicos (60) preservados integralmente.

### Estilo

O board 3.0.5 tinha `default_style = 22` (`AeroBlack`, custom) e os 2058 usuários apontando para ele. Como só o diretório `prosilver` acompanha os arquivos vanilla, a migration `style_update_p1` instala `prosilver`, define-o como padrão e reaponta todos os usuários para ele — satisfazendo o critério "estilo padrão em prosilver" sem intervenção manual.

## Validação manual (gate HITL)

Sem CLI no core 3.3 — executar pelo ACP após o upgrade:

- **Reconstruir o índice de busca**: ACP → Maintenance → Search index (delete + create).
- **Ressincronizar estatísticas / contagens de post**: ACP → "Resynchronise or reset statistics" e "Resynchronise post counts".

Checklist comportamental (frágil de automatizar nesta migração one-shot):

- [ ] O fórum sobe em 3.3.17/MariaDB sem erro de schema nem de estilo.
- [ ] Posts antigos com BBCode, smilies e links renderizam corretamente (prova visual do reparse s9e).
- [ ] Login com senha legada funciona.
- [ ] Permissões, grupos, moderadores, contagens de posts e patentes preservados.
- [ ] Estatísticas do fórum (totais e último post) corretas após a ressincronização.
- [ ] ACP funcional.

## Descartar

O upgrade roda sobre o MariaDB efêmero do passo 01; para zerar tudo, use o teardown daquele passo (`docker compose -p lithharbor-dev -f compose.dev.yml down -v`) e refaça restore → upgrade. A imagem CLI descartável sai com `docker rmi lithharbor-phpbb-cli`.
