# 富文本编辑器已知问题（RichEditor / Source AI）

> **状态**：暂不改造，已知限制；需硬编码 HTML 时使用「正文（HTML 源码）」Textarea 区块。  
> **最后更新**：2026-07-21

---

## 背景

后台 Filament RichEditor（TipTap）+ Source AI 源码模式。项目已接入 `InlineStylePlugin`（`style` / `class` 白名单扩展），但**无法**做到任意 HTML 无损往返。

---

## 核心机制（与保存无关）

Source AI 切换逻辑（`public/js/naturalgroove/.../source-ai.js`）：

1. **进入源码**：`getHTML()` → 格式化 → 显示在 textarea  
2. **源码内输入**：每次 `input` → `setContent(html)`（实时重解析）  
3. **退出源码**：`setContent(textarea 全文)`（再次重解析）  
4. **再进源码**：`getHTML()` 只能导出 parse 后的文档

因此：**仅「写 → 切可视化 → 再开源码」即可丢失 style/class**，不必等到点保存。

---

## 实测往返结果（2026-07-21）

**输入：**

```html
<p style="color:red">test</p>
<p style="text-align: start">test</p>
<span class="text-warm-plum">test</span>
<table style="margin-left:auto"><tr><td>a</td></tr></table>
```

**输出（切回可视化后再开源码）：**

- `color:red` → 丢失，变成 `text-align: start`
- `text-align: start` → 字符串级保留（编辑器不识别为合法对齐值）
- `<span class="text-warm-plum">` → 变成普通 `<p>`，class 丢失
- `table margin-left:auto` → 变成 `min-width: 25px` + colgroup/tbody + 单元格内嵌 `<p>`

---

## 丢失原因分类

| 类型 | 原因 |
|------|------|
| 段落 `color` 等 style | 与 Filament 内置 `textAlign` 扩展争用同一 `style` 属性，导出时被覆盖 |
| `text-align: start` | `textAlign` 只认 left/center/right/justify；`start` 仅作 style 字符串保留 |
| 裸 `<span class="...">` | 文档根须为块级节点；span 被 wrap 成 `<p>` 时 `genericSpan` 易丢失 |
| 表格手写 style | Table 扩展重写为列宽 `min-width`，并规范化 tbody/colgroup/单元格内 paragraph |
| 未知标签 | 不在 TipTap schema → 剥离 |

---

## 当前推荐做法

| 需求 | 方案 |
|------|------|
| 复杂 HTML、表格居中、任意 class/style | **正文（HTML 源码）** Textarea 区块（`html_body`）或基本/治理页 HTML 字段 |
| 常规富文本（标题、列表、链接、图片） | RichEditor 可视化编辑 |
| 渐变字、H4 梅紫色等 | 已知 class（如 `text-gradient-*`、`text-warm-plum`）+ 块级结构（如 `<h4><span class="...">`） |

---

## 若将来要改（未实施）

可选方向（架构级，非小补丁）：

1. 扩 TipTap schema / 修复 textAlign 与 inlineStyle 冲突  
2. 改 Source AI：源码模式不实时 `setContent`，或退出时存独立 HTML 字符串  
3. 更多场景改用 Textarea 存 raw HTML  

相关代码：

- `app/Filament/RichEditor/Plugins/InlineStylePlugin.php`
- `public/js/filament/rich-content-plugins/inline-style.js`
- `app/Support/RichContent.php`
- `public/js/naturalgroove/laravel-filament-rich-editor-source-ai/rich-content-plugins/source-ai.js`
