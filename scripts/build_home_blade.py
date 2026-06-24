#!/usr/bin/env python3
"""Extract main + footer from home-exported.html and convert asset paths for Blade."""

import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "public" / "home-exported.html"
OUT_MAIN = ROOT / "resources" / "views" / "partials" / "home" / "main-content.blade.php"
OUT_FOOTER = ROOT / "resources" / "views" / "partials" / "footer" / "footer-main.blade.php"


def convert_assets(html: str) -> str:
    """Convert static asset paths to Laravel asset() helper."""

    def repl_attr(match: re.Match) -> str:
        attr, path = match.group(1), match.group(2)
        return f'{attr}="{{{{ asset(\'{path}\') }}}}"'

    html = re.sub(
        r'(href|src|srcset|data-img)=["\'](assets/[^"\']+)["\']',
        repl_attr,
        html,
    )

    # url("assets/...") in inline styles
    html = re.sub(
        r'url\(["\']?(assets/[^"\')\s]+)["\']?\)',
        lambda m: f"url({{{{ asset('{m.group(1)}') }}}})",
        html,
    )

    return html


def extract_between(text: str, start_marker: str, end_marker: str) -> str:
    start = text.index(start_marker)
    end = text.index(end_marker, start)
    return text[start:end].strip()


def main() -> None:
    text = SOURCE.read_text(encoding="utf-8")

    main_html = extract_between(text, '<main id="main">', "</main>")
    main_html = convert_assets(main_html)

    footer_html = extract_between(text, "<footer", "</footer>")
    footer_html = "<footer\n" + footer_html[len("<footer") :].lstrip("\n")
    footer_html = convert_assets(footer_html)

    OUT_MAIN.parent.mkdir(parents=True, exist_ok=True)
    OUT_FOOTER.parent.mkdir(parents=True, exist_ok=True)

    OUT_MAIN.write_text(main_html + "\n", encoding="utf-8")
    OUT_FOOTER.write_text(footer_html + "\n", encoding="utf-8")

    print(f"Wrote {OUT_MAIN} ({len(main_html.splitlines())} lines)")
    print(f"Wrote {OUT_FOOTER} ({len(footer_html.splitlines())} lines)")


if __name__ == "__main__":
    main()
