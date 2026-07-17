# 会员系统开发说明

## 数据源

- 文件：`bak/会员全数据.xlsx`（62 列）
- 导入规则：**持证会员编号为空**的行跳过；每次导入**全量覆盖** `ipa_members`
- 命令：`php artisan members:import [--file=...] [--dry-run]`

## 后台（Filament `/admin`）

- **会员管理**（`IpaMemberResource`）：增删改查 + 上传 Excel 覆盖导入
- **会员统计**：性别、年龄、级别、地区、资格状态、入会年份

## 会员门户（前台 `/member`）

| 路由 | 说明 |
|------|------|
| `GET /member/login` | 手机号 + 验证码登录 |
| `POST /member/send-code` | 发送短信验证码 |
| `POST /member/verify` | 验证并登录 |
| `POST /member/logout` | 退出 |
| `GET /member` | Dashboard（参考 bak/会员登录后页面.png） |
| `GET /member/profile` | 个人信息只读（参考 bak/会员信息修改.png） |

- 顶部导航与官网一致，**无 Cart**
- 登录：数据库中有手机号的会员均可登录
- 短信接口参考 `bak/sendMSG.aspx.cs`，配置见 `.env` 中 `SMS_*`

## 字段映射

Excel 中文列名 → `ipa_members` 英文字段，见 `App\Support\MemberFieldMap`。
