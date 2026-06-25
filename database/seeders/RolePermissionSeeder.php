<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'content.view', 'display_name' => '查看内容', 'group_name' => '内容'],
            ['name' => 'content.create', 'display_name' => '创建内容', 'group_name' => '内容'],
            ['name' => 'content.edit', 'display_name' => '编辑内容', 'group_name' => '内容'],
            ['name' => 'content.delete', 'display_name' => '删除内容', 'group_name' => '内容'],
            ['name' => 'content.publish', 'display_name' => '发布/审核内容', 'group_name' => '内容'],
            ['name' => 'home.manage', 'display_name' => '管理首页板块', 'group_name' => '内容'],
            ['name' => 'media.manage', 'display_name' => '管理媒体库', 'group_name' => '内容'],
            ['name' => 'menu.manage', 'display_name' => '管理菜单', 'group_name' => '结构'],
            ['name' => 'category.manage', 'display_name' => '管理栏目', 'group_name' => '结构'],
            ['name' => 'settings.manage', 'display_name' => '系统设置', 'group_name' => '系统'],
            ['name' => 'users.manage', 'display_name' => '用户管理', 'group_name' => '权限'],
            ['name' => 'roles.manage', 'display_name' => '角色管理', 'group_name' => '权限'],
        ];

        $permissionModels = collect($permissions)->map(function (array $data) {
            return Permission::firstOrCreate(['name' => $data['name']], $data);
        })->keyBy('name');

        $roles = [
            'super_admin' => [
                'display_name' => '超级管理员',
                'description' => '拥有全部权限，可管理角色与用户',
                'permissions' => $permissionModels->keys()->all(),
            ],
            'admin' => [
                'display_name' => '管理员',
                'description' => '管理内容与系统设置，不含角色管理',
                'permissions' => [
                    'content.view', 'content.create', 'content.edit', 'content.delete', 'content.publish',
                    'home.manage', 'media.manage', 'menu.manage', 'category.manage', 'settings.manage', 'users.manage',
                ],
            ],
            'reviewer' => [
                'display_name' => '审核员',
                'description' => '审核并发布内容，不可删除与用户管理',
                'permissions' => [
                    'content.view', 'content.edit', 'content.publish', 'home.manage', 'media.manage',
                ],
            ],
            'editor' => [
                'display_name' => '编辑员',
                'description' => '创建与编辑内容，不可发布或管理结构',
                'permissions' => [
                    'content.view', 'content.create', 'content.edit', 'home.manage', 'media.manage',
                ],
            ],
        ];

        foreach ($roles as $name => $config) {
            $role = Role::firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => $config['display_name'],
                    'description' => $config['description'],
                ]
            );

            $role->update([
                'display_name' => $config['display_name'],
                'description' => $config['description'],
            ]);

            $permissionIds = collect($config['permissions'])
                ->map(fn (string $perm) => $permissionModels[$perm]->id ?? null)
                ->filter()
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
