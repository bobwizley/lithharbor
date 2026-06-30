#!/usr/bin/env python3
"""Parity harness for the local MariaDB -> PostgreSQL phpBB transplant."""

from __future__ import annotations

import argparse
import os
import subprocess
import sys
from dataclasses import dataclass
from typing import Iterable, Sequence


HIGH_VALUE_SPOT_CHECKS = {
    "phpbb_users": ("user_id", ("user_id", "username", "user_email", "user_posts", "user_regdate")),
    "phpbb_posts": ("post_id", ("post_id", "topic_id", "poster_id", "post_time", "post_subject", "post_text")),
    "phpbb_topics": (
        "topic_id",
        ("topic_id", "forum_id", "topic_title", "topic_poster", "topic_time", "topic_posts_approved"),
    ),
    "phpbb_privmsgs": ("msg_id", ("msg_id", "author_id", "message_time", "message_subject", "message_text")),
}


def load_dotenv(path: str = ".env") -> None:
    if not os.path.exists(path):
        return
    with open(path, encoding="utf-8") as env_file:
        for raw_line in env_file:
            line = raw_line.strip()
            if not line or line.startswith("#") or "=" not in line:
                continue
            key, value = line.split("=", 1)
            os.environ.setdefault(key, value.strip().strip('"').strip("'"))


@dataclass(frozen=True)
class CountMismatch:
    table: str
    source: int
    destination: int


@dataclass(frozen=True)
class SequenceMismatch:
    sequence: str
    table: str
    column: str
    next_value: int
    required_next: int


@dataclass(frozen=True)
class SpotCheckMismatch:
    table: str
    primary_key: str
    source: str
    destination: str


@dataclass(frozen=True)
class ParityReport:
    count_mismatches: tuple[CountMismatch, ...]
    sequence_mismatches: tuple[SequenceMismatch, ...]
    spot_check_mismatches: tuple[SpotCheckMismatch, ...]

    @property
    def ok(self) -> bool:
        return not (self.count_mismatches or self.sequence_mismatches or self.spot_check_mismatches)


def quote_ident(identifier: str) -> str:
    return '"' + identifier.replace('"', '""') + '"'


def quote_mysql_ident(identifier: str) -> str:
    return "`" + identifier.replace("`", "``") + "`"


def compare_counts(source: dict[str, int], destination: dict[str, int]) -> tuple[CountMismatch, ...]:
    mismatches: list[CountMismatch] = []
    for table in sorted(destination):
        if source.get(table) != destination[table]:
            mismatches.append(CountMismatch(table, source.get(table, -1), destination[table]))
    return tuple(mismatches)


def compare_sequences(rows: Iterable[tuple[str, str, str, int, int]]) -> tuple[SequenceMismatch, ...]:
    mismatches: list[SequenceMismatch] = []
    for sequence, table, column, next_value, max_id in rows:
        required_next = max_id + 1
        if next_value < required_next:
            mismatches.append(SequenceMismatch(sequence, table, column, next_value, required_next))
    return tuple(mismatches)


def compare_spot_checks(rows: Iterable[tuple[str, str, str, str]]) -> tuple[SpotCheckMismatch, ...]:
    mismatches: list[SpotCheckMismatch] = []
    for table, primary_key, source, destination in rows:
        if source != destination:
            mismatches.append(SpotCheckMismatch(table, primary_key, source, destination))
    return tuple(mismatches)


def render_report(report: ParityReport) -> str:
    lines: list[str] = []
    if report.ok:
        return ">> PG TRANSPLANT PARITY PASSED"

    lines.append(">> PG TRANSPLANT PARITY FAILED")
    for item in report.count_mismatches:
        lines.append(f"FAIL: count {item.table}: MariaDB={item.source} PostgreSQL={item.destination}")
    for item in report.sequence_mismatches:
        lines.append(
            "FAIL: sequence "
            f"{item.sequence} ({item.table}.{item.column}) next={item.next_value} required>={item.required_next}"
        )
    for item in report.spot_check_mismatches:
        lines.append(f"FAIL: spot-check {item.table}.{item.primary_key} differs")
    return "\n".join(lines)


class DockerDbs:
    def __init__(self, mariadb_container: str, postgres_container: str, db_name: str, db_user: str, db_password: str):
        self.mariadb_container = mariadb_container
        self.postgres_container = postgres_container
        self.db_name = db_name
        self.db_user = db_user
        self.db_password = db_password
        self.mariadb_root_password = os.environ["MARIADB_ROOT_PASSWORD"]

    def mariadb(self, sql: str) -> list[str]:
        env = f"MYSQL_PWD={self.mariadb_root_password}"
        return run(
            [
                "docker",
                "exec",
                "-e",
                env,
                self.mariadb_container,
                "mariadb",
                "-uroot",
                "-N",
                "-B",
                self.db_name,
                "-e",
                sql,
            ]
        )

    def postgres(self, sql: str) -> list[str]:
        return run(
            [
                "docker",
                "exec",
                "-e",
                f"PGPASSWORD={self.db_password}",
                self.postgres_container,
                "psql",
                "-U",
                self.db_user,
                "-d",
                self.db_name,
                "-At",
                "-F",
                "\t",
                "-c",
                sql,
            ]
        )


def run(cmd: Sequence[str]) -> list[str]:
    completed = subprocess.run(cmd, check=True, text=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    return [line for line in completed.stdout.splitlines() if line]


def pg_tables(dbs: DockerDbs) -> list[str]:
    sql = (
        "SELECT table_name FROM information_schema.tables "
        "WHERE table_schema='public' AND table_type='BASE TABLE' "
        "AND table_name LIKE 'phpbb\\_%' ESCAPE '\\' "
        "ORDER BY table_name"
    )
    return dbs.postgres(sql)


def table_counts(dbs: DockerDbs, tables: Sequence[str], engine: str) -> dict[str, int]:
    counts: dict[str, int] = {}
    for table in tables:
        if engine == "mariadb":
            rows = dbs.mariadb(f"SELECT COUNT(*) FROM {quote_mysql_ident(table)}")
        else:
            rows = dbs.postgres(f"SELECT COUNT(*) FROM {quote_ident(table)}")
        counts[table] = int(rows[0])
    return counts


def sequence_rows(dbs: DockerDbs) -> list[tuple[str, str, str, int, int]]:
    sql = r"""
SELECT seq.relname || '|' || tbl.relname || '|' || col.attname
FROM pg_class seq
JOIN pg_depend dep ON dep.objid = seq.oid AND dep.deptype = 'a'
JOIN pg_class tbl ON tbl.oid = dep.refobjid
JOIN pg_attribute col ON col.attrelid = tbl.oid AND col.attnum = dep.refobjsubid
JOIN pg_namespace ns ON ns.oid = seq.relnamespace
WHERE seq.relkind = 'S' AND ns.nspname = 'public'
ORDER BY tbl.relname, col.attname
"""
    sequence_refs = [line.split("|", 2) for line in dbs.postgres(sql)]
    rows: list[tuple[str, str, str, int, int]] = []
    for sequence, table, column in sequence_refs:
        quoted_sequence = quote_ident(sequence)
        quoted_table = quote_ident(table)
        quoted_column = quote_ident(column)
        seq_line = dbs.postgres(f"SELECT last_value, is_called FROM {quoted_sequence}")[0]
        last_value_text, is_called_text = seq_line.split("\t", 1)
        last_value = int(last_value_text)
        next_value = last_value + 1 if is_called_text == "t" else last_value
        max_id = int(dbs.postgres(f"SELECT COALESCE(MAX({quoted_column}), 0) FROM {quoted_table}")[0])
        rows.append((sequence, table, column, next_value, max_id))
    return rows


def spot_check_rows(dbs: DockerDbs) -> list[tuple[str, str, str, str]]:
    rows: list[tuple[str, str, str, str]] = []
    for table, (pk, columns) in HIGH_VALUE_SPOT_CHECKS.items():
        quoted_mysql_table = quote_mysql_ident(table)
        quoted_mysql_pk = quote_mysql_ident(pk)
        ids = dbs.mariadb(
            f"SELECT {quoted_mysql_pk} FROM {quoted_mysql_table} "
            f"ORDER BY {quoted_mysql_pk} LIMIT 3"
        )
        for value in ids:
            mysql_columns = ", ".join(
                f"COALESCE(CAST({quote_mysql_ident(c)} AS CHAR), '<NULL>')" for c in columns
            )
            pg_columns = ", ".join(
                f"COALESCE(CAST({quote_ident(c)} AS text), '<NULL>')" for c in columns
            )
            source = dbs.mariadb(
                f"SELECT MD5(CONCAT_WS(CHAR(31), {mysql_columns})) FROM {quoted_mysql_table} "
                f"WHERE {quoted_mysql_pk} = {int(value)}"
            )[0]
            destination = dbs.postgres(
                f"SELECT md5(array_to_string(ARRAY[{pg_columns}], chr(31))) "
                f"FROM {quote_ident(table)} WHERE {quote_ident(pk)} = {int(value)}"
            )[0]
            rows.append((table, f"{pk}={value}", source, destination))
    return rows


def verify(args: argparse.Namespace) -> int:
    dbs = DockerDbs(args.mariadb_container, args.postgres_container, args.db_name, args.db_user, args.db_password)
    tables = pg_tables(dbs)
    report = ParityReport(
        count_mismatches=compare_counts(table_counts(dbs, tables, "mariadb"), table_counts(dbs, tables, "postgres")),
        sequence_mismatches=compare_sequences(sequence_rows(dbs)),
        spot_check_mismatches=compare_spot_checks(spot_check_rows(dbs)),
    )
    print(render_report(report))
    return 0 if report.ok else 1


def main(argv: Sequence[str] | None = None) -> int:
    load_dotenv()
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--mariadb-container", default="mariadb")
    parser.add_argument("--postgres-container", default="postgres")
    parser.add_argument("--db-name", default=os.environ.get("DB_NAME", "lithharbor"))
    parser.add_argument("--db-user", default=os.environ.get("DB_USER", "lithharbor"))
    parser.add_argument("--db-password", default=os.environ.get("DB_PASSWORD") or "dev")
    subcommands = parser.add_subparsers(dest="command", required=True)
    subcommands.add_parser("verify")
    args = parser.parse_args(argv)
    if args.command == "verify":
        return verify(args)
    parser.error(f"unknown command {args.command}")
    return 2


if __name__ == "__main__":
    sys.exit(main())
