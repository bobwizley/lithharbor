# Issue tracker: Gitea

Issues and PRDs for this repo live in CodeLab Gitea. Use the `tea` CLI for issue tracker operations.

## Repository

Remote: `ssh://git@git.codelab.tec.br:2222/vctrtvfrrr/lithharbor`

Infer the repo from `git remote -v` when possible.

## Conventions

- Create an issue: use `tea issue create`.
- Read an issue: use `tea issue <number>` or the equivalent `tea` view command available in the installed CLI.
- List issues: use `tea issues` with appropriate label filters.
- Comment on an issue: use `tea comment <number>` or the installed `tea` issue comment command.
- Apply or remove labels: use the installed `tea` issue update/edit command with label flags.
- Close an issue: use the installed `tea` issue close command, adding an explanatory comment first when needed.

If the installed `tea` command names differ, run `tea help` or `tea issue --help` and use the matching Gitea operation.

## Pull requests as a triage surface

External pull requests are not configured as a triage request surface for this repo.

## When a skill says "publish to the issue tracker"

Create a Gitea issue with `tea`.

## When a skill says "fetch the relevant ticket"

Fetch the Gitea issue by number with `tea`.
