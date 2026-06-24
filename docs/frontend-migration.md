# IPA 前台迁移说明（Frontend Migration Handoff）

> **用途**：供新 Cursor 工作区（`D:\Laragon\www\ipaau-cms`）的 AI / 开发者快速了解前期静态化工作与后续 Laravel 接入方向。  
> **最后更新**：2026-06-23  
> **原维护目录**：`d:\web\trae\new\`（内容已拷贝到本项目的 `public/`，以本项目为准）

---

## 1. 环境与路径

| 项 | 值 |
|---|---|
| Laravel 项目根 | `D:\Laragon\www\ipaau-cms\` |
| Web 根目录 | `D:\Laragon\www\ipaau-cms\public\` |
| 本地访问 | `http://ipaau-cms.test` |
| 后台（Filament） | `http://ipaau-cms.test/admin` |
| APP_URL | `http://ipaau-cms.test`（见 `.env`） |
| 数据库 | MySQL，`ipaau_cms` |

**注意**：`public/` 根目录与 `public/new/` 下存在**重复的旧导出文件**，长期应只保留根目录一套，避免 `asset()` 与相对路径混乱。

---

## 2. 项目目标（已达成共识）

1. **前台**：以 React/Vite 导出的 IPA 官网静态 HTML 为视觉与交互基准，逐步改为 **Laravel Blade 模板**。
2. **内容**：页面正文**硬编码在 HTML/Blade 中**，或经**构建期**生成；**不要**在浏览器运行时 `fetch` JSON 拼页面。
3. **后台**：已有 Filament CMS，后续把首页 section、导航、文章、单页等接入数据库。
4. **沟通**：与用户交流使用**中文**；**不要擅自 git commit**，除非用户明确要求。

---

## 3. 当前 `public/` 静态页面清单

以下 HTML 已从 `d:\web\trae\new\` 拷贝到 `public/` 根目录：

| 文件 | 说明 |
|------|------|
| `home-exported.html` | 首页（约 5900 行，已做部分中文微调） |
| `about-the-ipa.html` | About 主介绍页 |
| `recognition.html` | 认可/荣誉 |
| `contact-us.html` | 联系我们 |
| `news.html` | 新闻列表（22 张卡片硬编码） |
| `a-year-in-review-most-common-sources-of-compliance-issues.html` | 新闻详情示例 |
| `leadership-team.html` | 领导团队列表 |
| `catherine-atkinson.html` | 领导成员详情 |

**预览方式**（过渡期）：

- 静态首页：`http://ipaau-cms.test/home-exported.html`
- Laravel 路由首页：`http://ipaau-cms.test/` → 目前仍指向旧的 `resources/views/frontend/home.blade.php`（2.2MB Vite 导出，**不是**新静态页）

---

## 4. 静态资源结构

```
public/
├── assets/
│   ├── css/
│   │   ├── fonts.css          # 本地 Inter 字体
│   │   ├── home.css           # 全站主样式
│   │   ├── about-ipa-pages.css
│   │   ├── news-pages.css
│   │   └── leadership-team.css
│   ├── js/
│   │   ├── home.js            # blob 滚动、accordion、表单等
│   │   ├── about-ipa-pages.js
│   │   └── news-pages.js
│   ├── img/                   # logo、about/news/leadership 图片
│   ├── svg/
│   └── menu.js                # 导航 megamenu、tab 切换、testimonial 轮播
├── home-exported.html
└── …（其他 .html）
```

**全站 favicon**（已统一）：

```html
<link rel="icon" type="image/png" href="assets/img/monogram_ipa.png" />
```

**首页 header logo** 子页链回：`./home-exported.html`（Laravel 化后应改为 `route('home')` 或 `/`）。

---

## 5. 前期已完成的前台工作

### 5.1 全站基础

- [x] 本地化 `fonts.css`，移除 `home.css` 在线 Google Fonts `@import`
- [x] 静态化脚本处理链接、图片、去除 React 运行时依赖
- [x] `home.js` 补充 accordion 等交互
- [x] Header / Footer 与首页导出保持一致；子页独立 CSS/JS

### 5.2 About 三页（about-the-ipa / recognition / contact-us）

- 共用 `about-ipa-pages.css`、`about-ipa-pages.js`
- 子页 hero blob：`blobBackground left` + `home.js` 中 `initBlobScrollProgress()` 滚动驱动
- Acorn 图蒙层：`.about-cta__image--acorn` 背景透明
- Contact 按钮、copyBlock 按钮样式已按设计微调

### 5.3 新闻页

- `news.html` + 一篇详情在根目录
- 列表 22 张卡片**硬编码**在 HTML
- `news-pages.js`：View more、搜索过滤
- 导航 News 链接：`./news.html`

### 5.4 Leadership

- `leadership-team.html`、`catherine-atkinson.html`
- Blob 改为 `left` + scroll 驱动

### 5.5 首页微调（`home-exported.html`）

| 区块 | 改动 |
|------|------|
| Footnote 卡片 | 4 → **6** 张；大屏 `--ipa-card-basis-lg: calc(100% / 6 - 2.5rem)` |
| 会员按钮区 | 「了解会员权益」+ 白底蓝字「入会途径」「我是小白」 |
| Tab 切换 | 4 项：声誉与认可 / 倡导（AU）/ 政策（AU）/ PA新闻 |
| Tab 内容 | 首屏中文文案；`menu.js` 中 `TABBED_CONTENT_PRESETS` 已扩展为 4 项 |
| 评价轮播 | 分页 4 个；`menu.js` 在 slide 不足时**克隆第 3 张**为第 4 张（HTML 内仍只有 3 个 slide） |
| Newsletter 表单 | 6 字段中文：姓名、手机号、邮箱、公司、现任职务、第一高等学历 |

### 5.6 已删除（用户要求不要 JSON 构建）

- `_page_data/`、`_build_about_pages.py`、`_build_news_pages.py`、`_fetch_page_data.py`

---

## 6. 已知问题与教训

### 6.1 `standardize_html.py` 曾破坏 header DOM

**现象**：导航栏错位到页面底部。

**原因**：`patch_subpage_blob()` 正则替换多留了一个 `</div>`，导致 `<header>` 提前关闭，导航脱离 header。

**正确 header 结构**：

```html
<header>
  <div class="relative">
    <div class="blobBackground left">…</div>
  </div>
  <div class="mx-auto h-full flex …">  <!-- 导航必须在 header 内 -->
```

**处理**：已用 `fix_header_structure.py` 修复；`standardize_html.py` 正则已改正。

**⚠️ 请勿再次盲目运行 `standardize_html.py`**。任何批量 HTML 脚本必须带 DOM 校验（如 header 内 div 闭合检查）。

### 6.2 首页行数膨胀

约 5900 行主因：Header ~3000 行 + Testimonial 重复 SVG ~900 行 + 未模板化。  
拆分方案见本文第 8 节。

---

## 7. Laravel 后台现状（接入前必读）

### 7.1 已有能力（Filament `/admin`）

| 模型 | 用途 |
|------|------|
| `Page` | 单页（title, slug, 富文本 content, SEO） |
| `PageComponent` | 首页组件块（`page_slug`, `component_type`, JSON `data`, `sort_order`） |
| `Menu` / `MenuItem` | 导航树 |
| `Article` / `Category` | 新闻/文章 |
| `Form` / `FormSubmission` / `Subscriber` | 表单与订阅 |
| `Faq`, `Event`, `Gallery`, `Media`, `Member`, `Setting` 等 | 扩展内容 |

### 7.2 已有前台代码（尚未与新静态页统一）

| 文件 | 状态 |
|------|------|
| `app/Http/Controllers/FrontendController.php` | 有 `home()`、`render()`；菜单从 DB 读取，空则 fallback 默认菜单 |
| `resources/views/components/menu.blade.php` | **可用的动态导航 Blade 组件**（`$menuItems`） |
| `resources/views/frontend/home.blade.php` | **旧版** 2.2MB Vite 整页导出；**未使用** `$menuItems` |
| `resources/views/frontend/article.blade.php` 等 | 旧 SPA 壳（`window.pageData` + `index.js`），与当前 `public/assets` 不一致 |
| `resources/views/page/show.blade.php` | 另一套旧 SPA 壳 |

### 7.3 已知断层（接入时要先修）

1. `FrontendController::render` 引用 `view('frontend.page')`，**该视图不存在**。
2. 路由 `Route::get('/page/{slug}', …)` 与 `render($type, $slug)` **参数不匹配**。
3. `category` / `article` 视图期望 `$pageData`，Controller 传入变量名不一致。
4. 三套前台并行：新静态 HTML、`home.blade.php`、旧 SPA 壳。

---

## 8. 首页 Section 拆分规划（Blade 化目标）

`home-exported.html` 的 `<main>` 建议拆为：

| data-index / type | 建议 Blade 文件 | 后台数据来源 |
|-------------------|-----------------|--------------|
| `heroBanner` | `sections/home/hero.blade.php` | `PageComponent` |
| footnote 卡片 ×6 | `sections/home/footnote-cards.blade.php` | `PageComponent` |
| `basicContentWithColumns` (会员) | `sections/home/membership.blade.php` | `PageComponent` |
| `statsCards` | `sections/home/stats.blade.php` | `PageComponent` |
| decorator1/2/3 | `partials/decorators/…` | 静态或 Setting |
| `tabbedContent` | `sections/home/tabbed-content.blade.php` | `PageComponent`（4 tab 应 HTML 硬编码 panel，勿依赖 JS 克隆） |
| `testimonialCarousel` | `sections/home/testimonials.blade.php` | `PageComponent`；SVG 星星抽 sprite |
| `accordion` | `sections/home/faq.blade.php` | `Faq` 或 `PageComponent` |
| `newsletter` | `sections/home/newsletter.blade.php` | `Form` + `Subscriber` |

**全站共用 partial**：

- `layouts/app.blade.php`
- `partials/header/`（`blob-home` vs `blob-sub`）
- `partials/footer/footer-main.blade.php`
- `components/cta-button.blade.php`
- `components/menu.blade.php`（已存在）

**子页**：about / news / leadership 可先「固定 Blade 模板 + 部分字段后台可配」，或逐步迁入 `Page` 模型。

---

## 9. 建议接入顺序

```
阶段 0  理顺入口：/ 走 Laravel；清理 public/new 重复资源
阶段 1  从 home-exported.html 拆 layouts + header/footer partial
阶段 2  新 home.blade.php 替换旧 2.2MB 文件；接入 <x-menu />
阶段 3  首页 sections ↔ PageComponent 数据
阶段 4  子页 Blade（about / news / leadership）
阶段 5  修复路由 + FrontendController + article/category 视图
阶段 6  静态 .html 退役或 301 到新 URL
```

---

## 10. 待用户确认（未最终拍板）

在新对话中如用户未说明，可主动确认：

1. **正式首页 URL**：`/` 是否只走 Blade（推荐）？
2. **子页 URL 规范**：`/about-the-ipa` vs `/page/about-the-ipa` vs 保留 `.html`？
3. **子页类型**：全走 `Page` 富文本，还是固定 Blade + 局部后台字段？
4. **新闻**：是否一律 `Article` + `Category`？
5. **首页可编辑粒度**：仅文案图片，还是 section 顺序/增删也可配？
6. **是否彻底放弃**旧 React SPA（`index.js` / `window.pageData`）？

---

## 11. 给 AI 的操作提醒

1. **工作目录**：以 `D:\Laragon\www\ipaau-cms` 为准；`d:\web\trae\new` 仅作历史参考。
2. **修改静态页时**：优先改 `public/` 下文件；同步考虑是否应改 Blade 而非继续堆 HTML。
3. **不要**再次用不安全正则批量改 header/blob。
4. **不要**恢复 `_page_data` 运行时 JSON 方案，除非用户明确要求。
5. **Testimonial 第 4 张**：目前部分逻辑在 `public/assets/menu.js` 克隆生成；Blade 化时应改为 HTML 内 4 个 slide。
6. **验证**：改 header 后检查导航是否在 `<header>` 内、是否位于页面顶部。

---

## 12. 关键文件速查

| 路径 | 说明 |
|------|------|
| `public/home-exported.html` | 新首页静态基准 |
| `public/assets/menu.js` | 导航、tab、轮播；含 `TABBED_CONTENT_PRESETS` |
| `public/assets/js/home.js` | blob、accordion、newsletter 表单 |
| `app/Http/Controllers/FrontendController.php` | 前台入口 |
| `resources/views/components/menu.blade.php` | 动态菜单组件 |
| `app/Models/PageComponent.php` | 首页组件数据模型 |
| `app/Filament/Resources/PageComponentResource.php` | 后台页面组件管理 |
| `routes/web.php` | 前台路由（需整理） |
| `d:\web\trae\new\standardize_html.py` | 历史脚本，有破坏风险，勿盲目运行 |

---

## 13. 新对话推荐开场

```
请先阅读 docs/frontend-migration.md，
然后继续 IPA 前台 Blade 接入工作。
当前阶段：[填写，如「拆 layout + 替换 home.blade.php」]
```
