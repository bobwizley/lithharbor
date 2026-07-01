#!/usr/bin/env python3
"""Verify phpBB attachment records match the files directory."""

from __future__ import annotations

import argparse
import os
import subprocess
import sys
from dataclasses import dataclass
from pathlib import Path
from typing import Iterable, Sequence


DEFAULT_IGNORED_FILES = frozenset({".htaccess", "index.htm"})


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
class AttachmentIntegrityReport:
    missing_files: tuple[str, ...]
    orphan_files: tuple[str, ...]

    @property
    def ok(self) -> bool:
        return not (self.missing_files or self.orphan_files)


def compare_attachment_files(
    physical_filenames: Iterable[str],
    files_dir_entries: Iterable[str],
    ignored_files: Iterable[str] = DEFAULT_IGNORED_FILES,
) -> AttachmentIntegrityReport:
    expected = {name for name in physical_filenames if name}
    ignored = set(ignored_files)
    actual = {name for name in files_dir_entries if name not in ignored}
    return AttachmentIntegrityReport(
        missing_files=tuple(sorted(expected - actual)),
        orphan_files=tuple(sorted(actual - expected)),
    )


def render_report(report: AttachmentIntegrityReport) -> str:
    if report.ok:
        return ">> ATTACHMENT INTEGRITY PASSED"

    lines = [">> ATTACHMENT INTEGRITY FAILED"]
    for filename in report.missing_files:
        lines.append(f"FAIL: missing file for phpbb_attachments record: {filename}")
    for filename in report.orphan_files:
        lines.append(f"FAIL: orphan file without phpbb_attachments record: {filename}")
    return "\n".join(lines)


def run(cmd: Sequence[str]) -> list[str]:
    completed = subprocess.run(cmd, check=True, text=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    return [line for line in completed.stdout.splitlines() if line]


def postgres_attachment_filenames(container: str, db_name: str, db_user: str, db_password: str) -> list[str]:
    sql = "SELECT physical_filename FROM phpbb_attachments ORDER BY attach_id"
    return run(
        [
            "docker",
            "exec",
            "-e",
            f"PGPASSWORD={db_password}",
            container,
            "psql",
            "-U",
            db_user,
            "-d",
            db_name,
            "-At",
            "-c",
            sql,
        ]
    )


def files_dir_entries(path: Path) -> list[str]:
    if not path.is_dir():
        raise FileNotFoundError(f"attachments directory does not exist: {path}")
    return sorted(entry.name for entry in path.iterdir() if entry.is_file())


def verify(args: argparse.Namespace) -> int:
    report = compare_attachment_files(
        postgres_attachment_filenames(args.postgres_container, args.db_name, args.db_user, args.db_password),
        files_dir_entries(args.files_dir),
        args.ignore_file,
    )
    print(render_report(report))
    return 0 if report.ok else 1


def main(argv: Sequence[str] | None = None) -> int:
    load_dotenv()
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--postgres-container", default="postgres")
    parser.add_argument("--db-name", default=os.environ.get("DB_NAME", "lithharbor"))
    parser.add_argument("--db-user", default=os.environ.get("DB_USER", "lithharbor"))
    parser.add_argument("--db-password", default=os.environ.get("DB_PASSWORD") or "dev")
    parser.add_argument("--files-dir", type=Path, default=Path("www/forum/files"))
    parser.add_argument("--ignore-file", action="append", default=sorted(DEFAULT_IGNORED_FILES))
    return verify(parser.parse_args(argv))


if __name__ == "__main__":
    sys.exit(main())
