#!/usr/bin/env python3
"""Count rows of a table inside a mariadb-dump file, SQL-aware.

mariadb-dump writes one extended INSERT per table, so the whole table lives on a
single line as `VALUES (row),(row),...;`. Naive `),(` counting breaks when a
text column contains those characters, so this walks the bytes tracking string
state and parenthesis depth, counting each top-level tuple.
"""

import re
import sys


def count_rows(sql, table):
    insert_re = re.compile(r"INSERT INTO `" + re.escape(table) + r"` VALUES\b")
    total = 0
    for match in insert_re.finditer(sql):
        total += _count_tuples(sql, match.end())
    return total


def _count_tuples(sql, start):
    rows = 0
    depth = 0
    in_string = False
    i = start
    n = len(sql)
    while i < n:
        ch = sql[i]
        if in_string:
            if ch == "\\":
                i += 2
                continue
            if ch == "'":
                in_string = False
        elif ch == "'":
            in_string = True
        elif ch == "(":
            if depth == 0:
                rows += 1
            depth += 1
        elif ch == ")":
            depth -= 1
        elif ch == ";" and depth == 0:
            break
        i += 1
    return rows


def main():
    dump_path, table = sys.argv[1], sys.argv[2]
    needle = "INSERT INTO `" + table + "` VALUES"
    total = 0
    statement = None
    with open(dump_path, encoding="utf-8", errors="surrogateescape") as fh:
        for line in fh:
            if statement is None:
                if needle not in line:
                    continue
                statement = [line]
            else:
                statement.append(line)
            if statement[-1].rstrip().endswith(";"):
                total += count_rows("".join(statement), table)
                statement = None
    print(total)


if __name__ == "__main__":
    main()
