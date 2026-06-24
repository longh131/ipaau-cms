import os

# 输入文件路径
input_file = r'd:\Laragon\www\ipaau-cms\resources\views\frontend\home.blade.php'

# 输出文件路径
output_file = r'd:\Laragon\www\ipaau-cms\resources\views\frontend\home_clean.blade.php'

# 读取文件内容
with open(input_file, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# 第一部分：保留第1-19行（head部分开头）
part1 = lines[:19]

# 第二部分：需要添加的外部CSS引用
css_links = """    <!-- 外部CSS引用 -->
    <link rel="stylesheet" href="/build/assets/app-D_OndOrZ.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

"""

# 第三部分：保留第25431行之后的内容（HTML正文）
# 找到最后一个 </style> 标签后的内容
start_line = 25431
part3 = lines[start_line:]

# 合并所有部分
clean_content = ''.join(part1) + css_links + ''.join(part3)

# 写入新文件
with open(output_file, 'w', encoding='utf-8') as f:
    f.write(clean_content)

print(f"文件清理完成！")
print(f"原文件行数: {len(lines)}")
print(f"新文件行数: {len(clean_content.splitlines())}")
print(f"新文件已保存到: {output_file}")
