# IPA 后台技术交接文档

> **用途**：供新开发者快速了解后台结构，继续开发和测试工作。  
> **最后更新**：2026-06-23  
> **项目根目录**：`D:\Laragon\www\ipaau-cms\`

---

## 1. 环境信息

| 项 | 值 |
|---|---|
| Laravel 版本 | 13.11.2 |
| PHP 版本 | 8.3.30 |
| 后台框架 | Filament v5 |
| 数据库 | MySQL，`ipaau_cms` |
| 本地访问 | `http://ipaau-cms.test` |
| 后台地址 | `http://ipaau-cms.test/admin` |

---

## 2. 后台功能模块

### 2.1 网站设置
- **路径**：`/admin/settings`
- **文件**：`app/filament/pages/Settings.php`
- **功能**：
  - 网站名称（必填）
  - SEO 信息（标题、描述、关键词）
  - 维护模式开关
- **数据表**：`settings`

### 2.2 栏目管理
- **路径**：`/admin/categories`
- **文件**：
  - 模型：`app/Models/Category.php`
  - 资源：`app/filament/resources/CategoryResource.php`
- **功能**：
  - 无限层级栏目结构
  - 栏目类型：单页类型、新闻类型
  - 父子关系支持
  - 树形排序显示
- **数据表**：`categories`

### 2.3 菜单管理
- **路径**：`/admin/menus`
- **文件**：
  - 模型：`app/Models/Menu.php`
  - 资源：`app/filament/resources/MenuResource.php`
- **功能**：
  - 创建和管理菜单
  - 设置菜单位置（location）
  - 启用/禁用状态
- **数据表**：`menus`

### 2.4 菜单项管理
- **路径**：`/admin/menu-items?menu_id={id}`
- **文件**：
  - 模型：`app/Models/MenuItem.php`
  - 资源：`app/filament/resources/MenuItemResource.php`
  - 页面：`app/filament/resources/MenuItemResource/Pages/ListMenuItems.php`
- **功能**：
  - 树形结构显示
  - 展开/折叠子菜单
  - 批量导入栏目
  - 上移/下移排序
  - 内联编辑和删除
- **数据表**：`menu_items`

---

## 3. 数据库表结构

### 3.1 settings 表
| 字段 | 类型 | 说明 |
|------|------|------|
| key | varchar(255) | 配置键名 |
| value | text | 配置值 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 3.2 categories 表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| parent_id | bigint | 父栏目ID（0或null为顶级） |
| name | varchar(255) | 栏目名称 |
| slug | varchar(255) | URL别名 |
| type | enum | 类型：page/news |
| content | longtext | 内容（单页类型） |
| sort_order | int | 排序 |
| is_active | tinyint | 是否启用 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 3.3 menus 表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| name | varchar(255) | 菜单名称 |
| location | varchar(255) | 菜单位置（自动生成） |
| description | text | 描述 |
| is_active | tinyint | 是否启用 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 3.4 menu_items 表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| menu_id | bigint | 菜单ID |
| parent_id | bigint | 父菜单项ID |
| title | varchar(255) | 菜单标题 |
| route | varchar(255) | 路由名称 |
| route_params | text | 路由参数JSON |
| url | varchar(500) | 外部链接 |
| sort_order | int | 排序 |
| is_active | tinyint | 是否启用 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

---

## 4. 核心代码说明

### 4.1 栏目模型 - Category.php
```php
// 获取排序后的树形结构（支持无限层级）
public static function getSortedTree()

// 递归获取所有子栏目
public function allChildren()
```

### 4.2 菜单模型 - Menu.php
```php
// 自动生成 location 字段
protected static function booted()
```

### 4.3 菜单项链接规范 - MenuItemLink.php

前台 URL 规范（后台保存 `route` + `route_params`，外链存 `url`）：

| 类型 | 路由名 | URL 模式 |
|------|--------|----------|
| 单页 | `page.show` | `/page/{slug}` |
| 栏目 | `category.show` | `/category/{slug}` |
| 文章 | `article.show` | `/article/{slug}` |
| 外链 | — | `menu_items.url` 完整 URL |

- **工具类**：`app/Support/MenuItemLink.php`
- **后台表单**：`link_type` / `link_id` 为虚拟字段，保存时由 `MenuItemLink::apply()` 转换
- **前台解析**（预备，尚未接入 Blade）：`MenuItemLink::resolveUrl()`

### 4.4 菜单项资源 - MenuItemResource.php
- 表格显示：树形结构，深度缩进
- 批量导入：支持多选栏目、包含子栏目、全选功能
- 排序操作：上移/下移交换排序值

### 4.5 列表页面 - ListMenuItems.php
```php
// 树形排序算法
private function sortByTree(Collection $records): Collection
// 获取所有菜单项后按父子关系排序
```

### 4.6 权限与用户

- **角色表** `roles`：`name`（内部标识）、`display_name`（显示名），无 `slug`
- **用户角色**：多对多 `user_roles`，无 `users.role_id` / `is_active`
- **操作日志** `activity_logs`：`event`, `model_type`, `model_id`, `old_values`, `new_values`

### 4.7 视图文件
| 文件 | 用途 |
|------|------|
| `resources/views/filament/resources/menu-item/columns/tree-title.blade.php` | 树形标题列显示 |
| `resources/views/layouts/app.blade.php` | 前台母版（静态 section，尚未接 CMS 数据） |

---

## 5. 前台页面

> **说明**：`public/new/` 目录已废弃并删除。前台以 Blade 模板为准（见 `docs/blade-templates.md`）。CMS 数据接入暂缓。

### 5.1 首页
- **路由**：`GET /` → `frontend.home`
- **模板**：`resources/views/frontend/home.blade.php`（section 拆分，当前为静态内容）

### 5.2 内容页路由（已注册，视图待完善）
- `/page/{slug}` → `page.show`
- `/category/{slug}` → `category.show`
- `/article/{slug}` → `article.show`

---

## 6. 待优化项

### 6.1 后台待完成功能
- [ ] 菜单项拖拽排序（已移除错误闭包路由，可用上移/下移）
- [ ] RBAC 权限校验接入 Filament Policy
- [ ] 维护模式 middleware
- [ ] Settings 页 `notify()` 方法兼容 Filament v5
- [ ] 文章/栏目/单页前台 Blade 与 CMS 数据联动

### 6.2 前台待完成功能
- [ ] 首页内容与后台数据联动
- [ ] 菜单组件读取 `menus.location = main`
- [ ] 其他静态 HTML 页面迁移为 Blade

### 6.3 技术债务
- [ ] `public/` 根目录存在重复的旧导出 HTML，需清理
- [x] 迁移文件与 live DB 对齐（baseline 2026_05_25_*）

---

## 7. 开发规范

### 7.1 文件命名
- 模型：单数形式，首字母大写
- 资源：Resource 结尾
- 视图：kebab-case

### 7.2 Git 规范
- **不要**擅自 git commit，除非用户明确要求
- 使用中文与用户沟通

### 7.3 数据库操作
- 修改前先备份数据
- 使用 Laravel Eloquent ORM
- 避免直接写 SQL

---

## 8. 常见问题

### 8.1 访问报错 "Class not found"
```bash
composer dump-autoload
php artisan cache:clear
```

### 8.2 页面不更新
```bash
php artisan view:clear
php artisan config:clear
```

### 8.3 数据库迁移
```bash
php artisan migrate
php artisan migrate:fresh  # 清空数据后重新迁移
```

---

## 9. 相关文档

- `docs/frontend-migration.md` - 前台迁移说明
- `docs/blade-templates.md` - Blade 模板说明
- `docs/首页栏目结构及名称.xlsx` - 栏目结构数据

---

## 10. 联系方式

如有疑问，请联系项目负责人获取更多信息。
