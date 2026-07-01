# 03 — Schema canônico PG17, transplante e paridade

Terceiro passo da migração (issue #4). Com o MariaDB local já atualizado para phpBB 3.3.17 (passo [02](02-upgrade-3.3.17.md)), gera um schema canônico instalando um phpBB 3.3.17 zerado contra PostgreSQL 17, transplanta os dados 1:1 e roda o harness de paridade.

## Pré-requisitos

- `scripts/restore_mariadb.sh` e `scripts/upgrade_phpbb.sh` já executados com sucesso.
- Docker com `docker compose`.
- `.env` com `DB_NAME`, `DB_USER`, `DB_PASSWORD` e `MARIADB_ROOT_PASSWORD`. Para o Postgres efêmero, `DB_PASSWORD` vazio usa o default local `dev`.
- Pacote oficial `phpBB-3.3.17.zip` extraído fora do git, com `PHPBB_PACKAGE_ROOT` apontando para o diretório `phpBB3` extraído. Exemplo: `PHPBB_PACKAGE_ROOT=data/phpbb/phpBB3`.

O repositório não versiona `install/`; o board só deve subir com esse diretório ausente. Por isso o schema canônico vem de um pacote oficial limpo, não da árvore runtime em `www/forum/`.

## Executar

```sh
PHPBB_PACKAGE_ROOT=data/phpbb/phpBB3 ./scripts/create_pg_schema.sh
./scripts/transplant_to_postgres.sh
```

`create_pg_schema.sh` sobe o serviço `postgres` do `compose.dev.yml`, zera o schema `public`, roda o instalador CLI oficial do phpBB contra PostgreSQL 17 e grava `data/migration/pg17-canonical-schema.sql` como evidência local.

`transplant_to_postgres.sh` usa a lista de tabelas do schema canônico no Postgres, exige que todas existam no MariaDB atualizado, exporta dados via `mariadb-dump --tab`, carrega com `psql \copy`, reseta as sequences para `max(id)+1` e chama o harness.

Durante a execução real, o MariaDB 3.3.17 pós-upgrade carregou duas colunas de dados que não existem numa instalação limpa:

- `phpbb_profile_fields_data.pf_tempo_jogo` — campo custom de perfil;
- `phpbb_topics.topic_url` — resíduo de dado do MOD phpBB-SEO descartado no código.

O transplante preserva essas colunas adicionando no PostgreSQL qualquer coluna `phpbb_` presente na origem e ausente no schema limpo, antes do `TRUNCATE` e do `COPY`. A carga usa lista explícita de colunas na ordem da origem, então diferenças de ordem física entre MariaDB e PostgreSQL não corrompem os dados.

## Verificar

```sh
scripts/db_transplant.py verify
```

O harness falha com exit code != 0 diante de qualquer divergência:

- contagem de linhas por tabela `phpbb_` do schema canônico, MariaDB origem vs PostgreSQL destino;
- cada sequence owned pelo schema canônico com próximo valor >= `max(id)+1`;
- spot-checks de conteúdo nas tabelas de alto valor `phpbb_users`, `phpbb_posts`, `phpbb_topics` e `phpbb_privmsgs`.

Cobertura unitária da lógica de relatório:

```sh
python3 -m pytest scripts/test_db_transplant.py
```

## Validação manual

Depois da paridade, o gate comportamental desta fatia é subir o fórum apontando para o PostgreSQL local e navegar o board. O próximo passo de aplicação troca `config.php` e os defaults de ambiente para o driver PostgreSQL definitivo; até lá, esta fatia prova a carga e o schema localmente.

## Descartar

```sh
docker compose -p lithharbor-dev -f compose.dev.yml down -v
```

Remove MariaDB, PostgreSQL e seus volumes efêmeros. Os artefatos em `data/migration/` são ignorados pelo git.
