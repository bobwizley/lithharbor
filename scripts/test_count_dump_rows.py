"""Tests for the SQL-aware extended-INSERT row counter."""

from count_dump_rows import count_rows


def test_counts_simple_numeric_tuples():
    sql = "INSERT INTO `t` VALUES (1,2),(3,4),(5,6);"
    assert count_rows(sql, "t") == 3


def test_ignores_other_tables():
    sql = (
        "INSERT INTO `other` VALUES (1),(2),(3);\n"
        "INSERT INTO `t` VALUES (1),(2);\n"
    )
    assert count_rows(sql, "t") == 2


def test_parens_inside_quoted_strings_do_not_split_rows():
    sql = "INSERT INTO `t` VALUES (1,'a),(b'),(2,'c),(d),(e');"
    assert count_rows(sql, "t") == 2


def test_escaped_quote_keeps_string_open():
    sql = r"INSERT INTO `t` VALUES (1,'it\'s),(fine'),(2,'ok');"
    assert count_rows(sql, "t") == 2


def test_table_name_prefix_is_not_a_partial_match():
    sql = (
        "INSERT INTO `phpbb_users_extra` VALUES (1),(2),(3),(4);\n"
        "INSERT INTO `phpbb_users` VALUES (1),(2);\n"
    )
    assert count_rows(sql, "phpbb_users") == 2


def test_multiple_insert_statements_for_same_table_accumulate():
    sql = (
        "INSERT INTO `t` VALUES (1),(2);\n"
        "INSERT INTO `t` VALUES (3),(4),(5);\n"
    )
    assert count_rows(sql, "t") == 5


def test_rows_split_across_lines_like_mariadb_dump():
    sql = "INSERT INTO `t` VALUES\n(1,'a'),\n(2,'b;c'),\n(3,'d');"
    assert count_rows(sql, "t") == 3


def test_missing_table_returns_zero():
    sql = "INSERT INTO `t` VALUES (1),(2);"
    assert count_rows(sql, "absent") == 0
