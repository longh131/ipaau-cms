#!/usr/bin/env python3
"""Fix CSS issues - replace @@media with @media"""

# Read the CSS file
with open(r'd:\Laragon\www\ipaau-cms\public\assets\css\home.css', 'r', encoding='utf-8') as f:
    css_content = f.read()

# Fix @@media to @media
css_content = css_content.replace('@@media', '@media')

# Fix other potential issues
css_content = css_content.replace('@charset "UTF-8";', '')
css_content = css_content.replace('@@import', '@import')

# Write back
with open(r'd:\Laragon\www\ipaau-cms\public\assets\css\home.css', 'w', encoding='utf-8') as f:
    f.write(css_content)

print("[OK] CSS fixed successfully!")
