# IPA 前台 Blade 模板说明

> **用途**：说明 `resources/views/` 下各 Blade 文件的职责、层级关系与修改指引。  
> **最后更新**：2026-06-23  
> **相关文档**：[frontend-migration.md](./frontend-migration.md)

---

## 1. 页面如何拼出来

访问 `http://ipaau-cms.test/` 时，渲染顺序如下：

```
routes/web.php  →  FrontendController::home()
                        ↓
              frontend/home.blade.php          （首页入口，填 SEO + 正文）
                        ↓ @extends
              layouts/app.blade.php            （全站 HTML 骨架）
                        ├── <head>             （meta、CSS、menu.js）
                        ├── partials/header/*  （顶栏）
                        ├── <main>             ← @yield('content')
                        │       └── partials/home/main-content.blade.php
                        │               └── sections/home/*.blade.php（12 块）
                        │               └── partials/decorators/*（3 块装饰）
                        ├── partials/footer/*  （页脚 + 附加块）
                        └── </body> 前脚本     （home.js 等）
```

**约定**：

- **Head** 没有单独 `head.blade.php`，集中在 `layouts/app.blade.php` 的 `<head>` 内。
- **Footer** 主体在 `partials/footer/footer-main.blade.php`；`</footer>` 之后的 portal/Ada 等在 `site-extras.blade.php`。
- 子页将来也应 `@extends('layouts.app')`，只替换 `@section('content')`，复用同一套 header/footer。

---

## 2. 布局层

| 文件 | 类型 | 说明 |
|------|------|------|
| `resources/views/layouts/app.blade.php` | **全站母版** | 整页 HTML 外壳：`<!doctype>`、`<html>`、`<head>`、`<body>`、`#root`、Skip link、header/main/footer 插槽、`@stack('styles'/'head'/'scripts')`。所有新前台页应继承此文件。 |

### Head 内固定引入的资源

| 资源 | 路径 | 说明 |
|------|------|------|
| Favicon | `assets/img/monogram_ipa.png` | 全站图标 |
| 字体定义 | `assets/css/fonts.css` | 历史 @font-face（Inter、ApexSerif 等，已被 site-fonts 覆盖显示） |
| 主样式 | `assets/css/home.css` | 全站 Tailwind/组件样式（约 2.5 万行） |
| 站点字体 | `assets/css/site-fonts.css` | **覆盖层**：英文 Arial、中文等线 |
| 导航脚本 | `assets/menu.js` | Megamenu、Tab、轮播等（defer） |

### Body 末尾固定脚本

| 资源 | 说明 |
|------|------|
| `assets/js/home.js` | Blob 滚动、手风琴、Newsletter 表单等（defer） |

### 可扩展插槽（子页用 `@push`）

| 插槽 | 位置 |
|------|------|
| `@section('title')` | `<title>` |
| `@section('canonical')` | `<link rel="canonical">` |
| `@section('og_title')` | Open Graph 标题 |
| `@section('json_ld')` | JSON-LD 结构化数据 |
| `@section('content')` | `<main id="main">` 内正文 |
| `@stack('styles')` | 额外 CSS |
| `@stack('head')` | head 内额外标签 |
| `@stack('scripts')` | body 末尾额外 JS |

### 布局可用变量

| 变量 | 默认 | 说明 |
|------|------|------|
| `$menuItems` | `[]` | 传给 header 的导航树；Controller 从 DB 读取，空则 fallback |
| `$htmlLang` | `en` | `<html lang="">` |
| `$bodyClass` | 空 | 追加到 `<body class="">` |

---

## 3. 前台页面（`frontend/`）

| 文件 | 路由 | 状态 | 说明 |
|------|------|------|------|
| `frontend/home.blade.php` | `GET /`（`route('home')`） | **当前正式首页** | 继承 `layouts.app`；定义 title/canonical/og/json_ld；`@section('content')` 引入 `partials.home.main-content`。 |
| `frontend/_home-vite-export.blade.php.bak` | — | 备份 | 旧版 2.2MB React/Vite 整页导出，**勿再使用**；仅作历史参考。 |
| `frontend/article.blade.php` | `GET /article/{slug}` | 旧 SPA 壳 | 依赖 `window.pageData` + 旧 `index.js`，与当前 `public/assets` 不一致；待阶段 5 重写。 |
| `frontend/category.blade.php` | `GET /category/{slug}` | 旧 SPA 壳 | 同上。 |
| `frontend/member.blade.php` | — | 遗留 | 旧前台会员页，未接入新 layout。 |
| `frontend/test.blade.php` | `GET /test-menu` | 调试 | 菜单 DOM 检测脚本，非正式页面。 |
| `frontend/home-menu-test.blade.php` | — | 调试 | 首页菜单测试页，非正式页面。 |

---

## 4. Header  partials（`partials/header/`）

| 文件 | 说明 |
|------|------|
| `site-header.blade.php` | **Header 总装**。包含 blob、Logo（链到 `route('home')`）、桌面/移动 `<x-menu>`、搜索、Sign In、移动菜单抽屉。页面顶栏只 `@include` 此文件即可。 |
| `blob-home.blade.php` | 首页顶部 animated blob 背景（SVG filter + 色块）。子页将来可换 `blob-sub` 变体。 |
| `search-bar.blade.php` | 搜索图标按钮 + 下拉全宽搜索表单（`#main-nav-search`）。 |

**Header 在 HTML 中的位置**：`<body>` → `#root` → Skip link **之后**、`<main>` **之前**。

---

## 5. Footer partials（`partials/footer/`）

| 文件 | 对应 HTML | 说明 |
|------|-----------|------|
| `footer-main.blade.php` | `<footer class="footer-main">` | Logo、机构简介、社交媒体图标、Footer 导航列、版权、Back to top 浮动按钮。 |
| `site-extras.blade.php` | `</footer>` 之后 | `#portal-wrapper`、Ask Ada 浮层、`#invertshallowconvex-path` 全局 clipPath SVG。 |

---

## 6. 首页正文组装（`partials/home/`）

| 文件 | 说明 |
|------|------|
| `main-content.blade.php` | **首页 section 清单**（仅 `@include` 串联，约 16 行）。调整首页区块顺序时改此文件；不要在此堆 HTML。 |

---

## 7. 首页 Section（`sections/home/`）

每个文件对应 `<main>` 内一个可见或装饰性区块，内容来自 `home-exported.html` 拆分，目前为**硬编码 HTML**；阶段 3 可接 `PageComponent` 后台数据。

| 文件 | `data-type` / 标识 | 页面内容 |
|------|-------------------|----------|
| `hero.blade.php` | `heroBanner` index 0 | 首屏 eyebrow、双行 H1、描述、Become a Member / Find out more |
| `footnote-cards.blade.php` | `footnote` index 0.5 | 6 张快捷卡片（Global Certification、Events、IPA Programmes 等） |
| `membership.blade.php` | `basicContentWithColumns` index 1 | 会员区：Your Membership 标题 + **了解会员权益 / 入会途径 / 我是小白** |
| `stats.blade.php` | `statsCards` index 2 | 1 / 50k / 100+ 三组数据卡 |
| `cpd-intro.blade.php` | `basicContentWithColumns` index 4 | 「Empowering… Online CPD, Events and Courses」标题区 |
| `tabbed-content.blade.php` | `tabbedContent` index 5 | 4 Tab：**声誉与认可 / 倡导（AU）/ 政策（AU）/ PA新闻** + 首屏中文文案 |
| `testimonials.blade.php` | `testimonialCarousel` index 6 | 会员评价 Swiper 轮播（含 4 个分页点） |
| `about-intro.blade.php` | index 8 | About the IPA 长文案 + Learn more / Our team + 配图 |
| `diversity.blade.php` | `basicContentWithColumns` index 9 | diverse / innovative 渐变标题 |
| `cta-section.blade.php` | `ctaSection` index 10 | Shaping the future + Have your say |
| `faq.blade.php` | `accordion` index 12 | Frequently Asked Questions 手风琴 4 条 |
| `newsletter.blade.php` | `newsletter` index 13 | 订阅区 + **6 字段中文表单**（姓名、手机号、邮箱、公司、职务、学历） |

---

## 8. 装饰层（`partials/decorators/`）

纯视觉 SVG/背景，无交互文案；`aria-hidden="true"`，大屏显示。

| 文件 | `datatype` | 插入位置（在 main 内） |
|------|------------|------------------------|
| `decorator-1.blade.php` | `decorator1` | stats 与 cpd-intro 之间 |
| `decorator-2.blade.php` | `decorator2` | testimonials 与 about-intro 之间 |
| `decorator-3.blade.php` | `decorator3` | cta-section 与 faq 之间 |

---

## 9. 可复用组件（`components/`）

| 文件 | 用法 | 说明 |
|------|------|------|
| `components/menu.blade.php` | `<x-menu :menuItems="$menuItems" variant="desktop" />` | 导航 megamenu 树形 HTML。`variant`：`desktop`（默认）或 `mobile`。`site-header` 内各渲染一次。数据来源：`FrontendController::getMenuItems()` 或默认 fallback 数组。 |

---

## 10. 其他视图（非新前台体系）

| 文件 | 说明 |
|------|------|
| `page/show.blade.php` | 旧 SPA 单页壳，`window.pageData` 方案。 |
| `welcome.blade.php` | Laravel 默认欢迎页，与 IPA 官网无关。 |
| `filament/**` | Filament 后台 `/admin` 专用，勿与前台 layout 混用。 |

---

## 11. 静态资源与模板的对应关系

| 目录/文件 | 模板中的引用方式 |
|-----------|------------------|
| `public/assets/css/` | `asset('assets/css/...')` 或在 layout 中 link |
| `public/assets/js/home.js` | layout body 末尾 |
| `public/assets/menu.js` | layout head 末尾 |
| `public/assets/img/` | section 内 `{{ asset('assets/img/...') }}` |
| `public/home-exported.html` | 静态预览基准；与 Blade 首页应对齐，长期可 301 到 `/` |

---

## 12. 常见修改场景

| 需求 | 改哪个文件 |
|------|------------|
| 全站 title / 追加 CSS | `layouts/app.blade.php` 或具体页的 `@section` / `@push` |
| Logo / 搜索 / Sign In | `partials/header/site-header.blade.php` 或 `search-bar.blade.php` |
| 导航结构（DB 为空时的默认项） | `app/Http/Controllers/FrontendController.php` → `getDefaultMenuItems()` |
| 导航 HTML 结构 | `components/menu.blade.php` |
| 页脚文案 / 社交链接 | `partials/footer/footer-main.blade.php` |
| 首页某一区块文案或图片 | `sections/home/` 下对应文件 |
| 首页区块显示顺序 | `partials/home/main-content.blade.php` 的 `@include` 顺序 |
| 全站字体（Arial + 等线） | `public/assets/css/site-fonts.css` |
| 新建子页（如 About） | 新建 `frontend/xxx.blade.php` → `@extends('layouts.app')` → 填 `@section('content')` |

---

## 13. 目录树速查

```
resources/views/
├── layouts/
│   └── app.blade.php                 # 全站母版（含 head / body 框架）
├── frontend/
│   ├── home.blade.php                # 首页入口 ★
│   ├── article.blade.php             # 旧 SPA（待迁移）
│   ├── category.blade.php            # 旧 SPA（待迁移）
│   └── _home-vite-export.blade.php.bak
├── partials/
│   ├── header/
│   │   ├── site-header.blade.php     # Header 总装
│   │   ├── blob-home.blade.php       # 首页 blob
│   │   └── search-bar.blade.php      # 搜索
│   ├── footer/
│   │   ├── footer-main.blade.php     # 主 Footer ★
│   │   └── site-extras.blade.php     # portal / Ada / SVG
│   ├── home/
│   │   └── main-content.blade.php    # 首页 section 清单
│   └── decorators/
│       ├── decorator-1.blade.php
│       ├── decorator-2.blade.php
│       └── decorator-3.blade.php
├── sections/home/                    # 首页 12 个内容块 ★
│   ├── hero.blade.php
│   ├── footnote-cards.blade.php
│   ├── membership.blade.php
│   ├── stats.blade.php
│   ├── cpd-intro.blade.php
│   ├── tabbed-content.blade.php
│   ├── testimonials.blade.php
│   ├── about-intro.blade.php
│   ├── diversity.blade.php
│   ├── cta-section.blade.php
│   ├── faq.blade.php
│   └── newsletter.blade.php
└── components/
    └── menu.blade.php                # 动态导航组件 ★
```

★ 标记为日常开发最常改动的文件。

---

## 14. 后续演进（参考 migration 文档）

| 阶段 | 模板侧工作 |
|------|------------|
| 阶段 3 | `sections/home/*.blade.php` 读取 `PageComponent` JSON |
| 阶段 4 | 新增 `frontend/about-*.blade.php` 等子页，复用 `layouts.app` |
| 阶段 5 | 重写 `article` / `category`，废弃旧 SPA 壳 |
| 阶段 6 | 静态 `.html` 301 到 Blade 路由 |
