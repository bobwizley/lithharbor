# 04 - Integridade e seed de anexos/avatares

Quarto passo da migracao (issue #5). Depois do transplante para PostgreSQL, os arquivos de usuario deixam de fazer parte do git e passam a ser estado persistente do phpBB.

## Verificar integridade dos anexos

Com o PostgreSQL local do passo [03](03-postgres-transplant.md) no ar:

```sh
./scripts/verify_attachments.py
```

O verificador consulta `phpbb_attachments.physical_filename` no container `postgres` e compara com `www/forum/files/`. Ele falha se:

- um registro em `phpbb_attachments` nao tem arquivo fisico correspondente;
- existe arquivo fisico em `www/forum/files/` sem registro no banco.

Os sentinelas do phpBB (`.htaccess`, `index.htm`) sao ignorados porque nao representam anexos.

## Remover conteudo de usuario do git

`www/forum/files/` e `www/forum/images/avatars/upload/` ficam ignorados pelo git. Os arquivos podem continuar existindo na maquina de migracao para o seed, mas nao entram em novos commits. Um checkout limpo desta branch nao contem esses arquivos; a origem do seed deve ser o workspace de migracao atual ou outro artefato operacional preservado fora do git.

## Seed para o bind-mount persistente

O seed e idempotente e copia anexos e avatares enviados para o layout de estado persistente:

```sh
./scripts/seed_user_content.sh www/forum data/forum-state
```

No go-live do VPS, execute o mesmo script apontando o segundo argumento para o bind-mount persistente da aplicacao, por exemplo:

```sh
./scripts/seed_user_content.sh www/forum /opt/codelab/apps/lithharbor/data
```

O script usa `rsync -a --delete`, entao pode ser reexecutado: o destino volta a refletir a origem. Ele tambem cria `cache/` e `store/` no destino, mas nao semeia conteudo nesses diretorios porque sao gravaveis e regeneraveis pelo phpBB. No repositorio, apenas os sentinelas `.htaccess`/`index.htm` desses diretorios continuam versionados; artefatos gerados como `cache/production/` e `store/migrations_*.log` ficam ignorados.

Depois do seed, o bind-mount persistente deve expor estes caminhos para o container PHP:

- `files/` -> `www/forum/files/`
- `images/avatars/upload/` -> `www/forum/images/avatars/upload/`
- `cache/` -> `www/forum/cache/`
- `store/` -> `www/forum/store/`
