# 01 — Restaurar o dump de produção em MariaDB local efêmero

Primeiro passo da migração (issue #1). Estabelece um MariaDB local descartável e carrega nele **apenas** a database `lithharbor` a partir do dump de produção mais recente. É a fundação para todo o trabalho local de migração: reproduzível e jogável fora — não evolui para um ambiente de dev permanente.

## Pré-requisitos

- Docker com o plugin `docker compose`.
- `python3` (para a verificação de contagem de linhas).
- Um dump de produção em `data/dumps/all-*.sql` (o mais recente por data de modificação é usado automaticamente). Os dumps são `--all-databases` e contêm `codelab`, `lithharbor` e `mysql`.
- Um arquivo `.env` na raiz (copie de `.env.example`). Adicione nele `MARIADB_ROOT_PASSWORD` — variável só de tooling local da migração (consumida por `compose.dev.yml`), fora do schema de app do `.env.example`. Um valor qualquer serve, ex.: `MARIADB_ROOT_PASSWORD=dev`.

## Executar

```sh
./scripts/restore_mariadb.sh
```

O script é idempotente: cada execução recria o volume de dados do zero (`docker compose down -v`) e recarrega o dump. Ao final ele chama a verificação automaticamente.

O que ele faz:

1. Sobe o MariaDB 11.4 efêmero (`compose.dev.yml`, volume nomeado `mariadb-data`, dumps montados read-only) e espera ficar `healthy`.
2. Filtra o dump com `awk`, encaminhando ao servidor **só** o cabeçalho de `SET` de sessão e a seção da database `lithharbor`. As databases `codelab` e `mysql` nunca chegam ao servidor — não dependemos de `--one-database`, que poderia descartar os `SET` iniciais (por exemplo `NO_AUTO_VALUE_ON_ZERO`).
3. Verifica o resultado (ver abaixo).

## Verificar

Roda junto do restore, mas também é reexecutável isoladamente:

```sh
./scripts/verify_restore.sh
```

Asserções:

- a database `codelab` está **ausente** (só `lithharbor` foi carregada);
- `lithharbor` tem as **62 tabelas core do phpBB 3.0.x** (`phpbb_*`);
- as contagens de linhas das tabelas de alto valor (`phpbb_users`, `phpbb_posts`, `phpbb_topics`, `phpbb_privmsgs`) batem com o dump.

A contagem do dump é independente da carga: `scripts/count_dump_rows.py` percorre o SQL respeitando aspas e profundidade de parênteses, então `(` e `)` dentro de textos de post não inflam o número. Cobertura em `scripts/test_count_dump_rows.py` (`python3 -m pytest scripts/test_count_dump_rows.py`).

## Achado: tabelas `flarum_` na database de produção

A database `lithharbor` de produção carrega, além das 62 tabelas do phpBB, **22 tabelas `flarum_`** — resquício de uma instalação abandonada do Flarum que compartilhou o mesmo banco. Isso contraria a premissa do PRD ("zero tabelas custom"). Elas são **fora de escopo** para a migração phpBB→phpBB: o restore as preserva (carga fiel, sem perda de dados), a verificação as reporta sem falhar, e o updater oficial do phpBB 3.3.17 (próximo passo) as ignora. Decidir se descartá-las antes do transplante para PostgreSQL fica para o passo de upgrade.

## Descartar

```sh
docker compose -p lithharbor-dev -f compose.dev.yml down -v
```

Remove o container e o volume `mariadb-data`. Nada de estado sobrevive — basta rodar o restore de novo para reconstruir.
