# IPA 后台技术交接文档

> **用途**：供新开发者快速了解后台结构，继续开发和测试工作。  
> **最后更新**：2026-06-24  
> **项目根目录**：`D:\Laragon\www\ipaau-cms\`

---

## 1. 环境信息

| 项 | 值 |
|---|---|
| Laravel 版本 | 13.11.2 |
| PHP 版本 | 8.3.30 |
| 后台框架 | Filament v3 |
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

### 4.3 菜单项资源 - MenuItemResource.php
- 表格显示：树形结构，深度缩进
- 批量导入：支持多选栏目、包含子栏目、全选功能
- 排序操作：上移/下移交换排序值

### 4.4 列表页面 - ListMenuItems.php
```php
// 树形排序算法
private function sortByTree(Collection $records): Collection
// 获取所有菜单项后按父子关系排序
```

### 4.5 视图文件
| 文件 | 用途 |
|------|------|
| `resources/views/filament/resources/menu-item/columns/tree-title.blade.php` | 树形标题列显示 |
| `public/new/home-new.html` | 前台首页（纯HTML+CSS+JS） |

---

## 5. 前台页面

### 5.1 新首页
- **文件**：`public/new/home-new.html`
- **特点**：纯 HTML+CSS+JS，无框架依赖
- **功能**：
  - 选项卡切换（EVENTS/COURSES/ONLINE CPD）
  - 轮播图（自动播放、左右切换、暂停）
  - 下拉菜单（点击触发）
  - 背景光晕动画（丝滑效果）

### 5.2 SVG 资源
| 文件 | 用途 |
|------|------|
| `public/new/svg/hero-wave.svg` | 英雄区波浪 |
| `public/new/svg/section-wave.svg` | 区域分隔波浪 |
| `public/new/svg/decoration-blob.svg` | 装饰性 blob |

---

## 6. 待优化项

### 6.1 后台待完成功能
- [ ] 菜单项拖拽排序（已移除，待完善）
- [ ] 菜单前端显示（需创建 Blade 模板）
- [ ] 文章详情页开发
- [ ] 栏目内容编辑（富文本编辑器）

### 6.2 前台待完成功能
- [ ] 首页内容与后台数据联动
- [ ] 响应式布局优化
- [ ] 其他静态页面迁移

### 6.3 技术债务
- [ ] `public/` 根目录存在重复的旧导出文件，需清理
- [ ] 首页 `home.blade.php` 代码冗余（约26000行）

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
