#!/usr/bin/env python3
"""Fix asset paths from absolute to relative"""

import re

# Read the file
with open(r'd:\Laragon\www\ipaau-cms\public\home-new.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace absolute paths with relative paths
content = content.replace('href="/assets/', 'href="assets/')
content = content.replace('src="/assets/', 'src="assets/')

# Write back
with open(r'd:\Laragon\www\ipaau-cms\public\home-new.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("[OK] Paths fixed successfully!")
