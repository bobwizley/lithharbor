"""Tests for the PostgreSQL transplant parity harness."""

from db_transplant import (
    ParityReport,
    compare_counts,
    compare_sequences,
    compare_spot_checks,
    render_report,
)


def test_compare_counts_accepts_matching_tables():
    assert compare_counts({"phpbb_users": 2}, {"phpbb_users": 2}) == ()


def test_compare_counts_reports_destination_tables_with_different_counts():
    mismatches = compare_counts({"phpbb_users": 2}, {"phpbb_users": 3})
    assert mismatches[0].table == "phpbb_users"
    assert mismatches[0].source == 2
    assert mismatches[0].destination == 3


def test_compare_sequences_accepts_next_value_at_max_plus_one():
    assert compare_sequences([("phpbb_users_seq", "phpbb_users", "user_id", 43, 42)]) == ()


def test_compare_sequences_reports_next_value_that_would_collide():
    mismatches = compare_sequences([("phpbb_users_seq", "phpbb_users", "user_id", 42, 42)])
    assert mismatches[0].required_next == 43


def test_compare_spot_checks_reports_changed_content():
    mismatches = compare_spot_checks([("phpbb_posts", "post_id=10", "old text", "new text")])
    assert mismatches[0].table == "phpbb_posts"
    assert mismatches[0].primary_key == "post_id=10"


def test_render_report_is_noisy_on_failure():
    report = ParityReport(
        count_mismatches=compare_counts({"phpbb_posts": 10}, {"phpbb_posts": 9}),
        sequence_mismatches=(),
        spot_check_mismatches=(),
    )
    rendered = render_report(report)
    assert "FAILED" in rendered
    assert "phpbb_posts" in rendered


def test_render_report_is_concise_on_success():
    assert render_report(ParityReport((), (), ())) == ">> PG TRANSPLANT PARITY PASSED"
