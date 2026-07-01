# AGENTS.md

lithharbor — phpBB forum served at `lithharbor.net`, deployed to the CodeLab VPS. Two images build from this repo: `lithharbor` (php-fpm) and `lithharbor-nginx`.

## Agent skills

### Issue tracker

Issues and PRDs are tracked in CodeLab Gitea using the `tea` CLI. See `docs/agents/issue-tracker.md`.

### Triage labels

Uses the default triage label vocabulary. See `docs/agents/triage-labels.md`.

### Domain docs

Single-context repo: read root `CONTEXT.md` and root `docs/adr/` when present. See `docs/agents/domain.md`.

---

## Platform contract

- The two images publish to the Gitea registry as `git.codelab.tec.br/vctrtvfrrr/lithharbor` (php-fpm) and `git.codelab.tec.br/vctrtvfrrr/lithharbor-nginx`. CI builds and pushes both under the same tag (`${{ github.sha }}`); `compose.yml` references them via `${IMAGE_TAG}`, which `deploy-stack` exports.
- `web` (Traefik-owned, public) and `postgres` (shared Postgres, host `postgres:5432`) are external, platform-owned networks — never declare them. The stack's own bridge network (`internal`) carries php↔nginx FastCGI, since nginx lives on `web` and php on `postgres`.
- No database service in `compose.yml`: the managed PostgreSQL is reached over the external `postgres` network.
- Traefik and probe labels live on the `lithharbor-nginx` service (port 80): apex `lithharbor.net` + SAN `*.lithharbor.net`, `certresolver=letsencrypt`, `www`→apex redirect.
- This project owns its own DNS zone and serves the apex directly; it does NOT use the platform's wildcard-subdomain pattern.
- The `deploy-stack` reference in `deploy.yml` must be a full Gitea URL (`https://git.codelab.tec.br/...@master`), invoked with the `app` and `image-tag` inputs.
- `.env.example` is the full env schema (every variable the app reads, each with a local-dev default). Keep non-secret config there; never hoist it into `compose.yml`.
- Keep `clear_env = no` in the `Dockerfile` php-fpm config — without it the `env_file` vars don't reach worker processes and `config.php` loses its `DB_*` connection.
- The only secret is `DB_PASSWORD`, injected via the `APPENV_DB_PASSWORD` workflow env; `deploy-stack` strips the `APPENV_` prefix and renders it into `/opt/codelab/apps/lithharbor/.env`. `DB_PASSWORD` stays empty in `.env.example`.
- Persistent state (phpBB `files/`, `store/`, `cache/`, `images/avatars/upload/`) lives in bind-mounts under `./data`, which maps to `/opt/codelab/apps/lithharbor/data` (chown uid 1000 by `deploy-stack`).
- This repo must carry the Gitea topic `codelab-stack` to appear in the derived stack inventory.

Canonical contract (networks, middlewares, deploy action, stack template) lives in [codelab-infra](https://git.codelab.tec.br/codelab/infra) and [deploy-stack](https://git.codelab.tec.br/codelab/deploy-stack). When in doubt, read these.
