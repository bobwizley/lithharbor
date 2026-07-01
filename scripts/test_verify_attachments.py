"""Tests for the phpBB attachment integrity verifier."""

from verify_attachments import AttachmentIntegrityReport, compare_attachment_files, render_report


def test_compare_attachment_files_accepts_matching_records_and_files():
    report = compare_attachment_files(["68_abcd", "75_efgh"], ["68_abcd", "75_efgh", ".htaccess", "index.htm"])

    assert report == AttachmentIntegrityReport(missing_files=(), orphan_files=())


def test_compare_attachment_files_reports_missing_physical_files():
    report = compare_attachment_files(["68_abcd", "75_efgh"], ["68_abcd"])

    assert report.missing_files == ("75_efgh",)
    assert report.orphan_files == ()


def test_compare_attachment_files_reports_orphan_files():
    report = compare_attachment_files(["68_abcd"], ["68_abcd", "75_efgh"])

    assert report.missing_files == ()
    assert report.orphan_files == ("75_efgh",)


def test_render_report_is_noisy_on_failure():
    rendered = render_report(AttachmentIntegrityReport(("missing.bin",), ("orphan.bin",)))

    assert "FAILED" in rendered
    assert "missing.bin" in rendered
    assert "orphan.bin" in rendered


def test_render_report_is_concise_on_success():
    assert render_report(AttachmentIntegrityReport((), ())) == ">> ATTACHMENT INTEGRITY PASSED"
