<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首页菜单测试</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        .warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <h1>首页菜单功能测试</h1>
    <div id="test-results"></div>

    <script>
        const results = [];

        function addResult(message, type = 'info') {
            results.push({ message, type });
            const div = document.createElement('div');
            div.className = `test-result ${type}`;
            div.textContent = message;
            document.getElementById('test-results').appendChild(div);
        }

        // 测试1: 检查menu.js文件是否加载
        addResult('开始测试首页菜单功能...', 'info');

        // 等待页面加载完成
        window.addEventListener('load', function() {
            setTimeout(() => {
                // 测试2: 检查页面是否有菜单元素
                const desktopNav = document.querySelector('[data-type="desktop-navigation"]');
                if (desktopNav) {
                    addResult('✓ 找到桌面导航菜单', 'success');
                } else {
                    addResult('✗ 未找到桌面导航菜单', 'error');
                }

                // 测试3: 检查菜单项
                const menuItems = document.querySelectorAll('[data-type="desktop-navigation"] [data-level="0"]');
                if (menuItems.length > 0) {
                    addResult(`✓ 找到 ${menuItems.length} 个一级菜单项`, 'success');
                    menuItems.forEach((item, index) => {
                        addResult(`  - 菜单项 ${index + 1}: ${item.textContent.trim().substring(0, 30)}...`, 'info');
                    });
                } else {
                    addResult('✗ 未找到一级菜单项', 'error');
                }

                // 测试4: 检查菜单面板
                const menuPanels = document.querySelectorAll('[data-type="megamenu-panel"]');
                if (menuPanels.length > 0) {
                    addResult(`✓ 找到 ${menuPanels.length} 个菜单面板`, 'success');
                } else {
                    addResult('✗ 未找到菜单面板', 'error');
                }

                // 测试5: 检查CSS类
                let hasCorrectClasses = true;
                menuItems.forEach((item, index) => {
                    if (!item.classList.contains('inactive')) {
                        hasCorrectClasses = false;
                        addResult(`✗ 菜单项 ${index + 1} 缺少inactive类`, 'error');
                    }
                });
                if (hasCorrectClasses && menuItems.length > 0) {
                    addResult('✓ 所有菜单项都有正确的CSS类', 'success');
                }

                // 测试6: 检查JavaScript文件
                if (typeof window.menuScriptLoaded !== 'undefined') {
                    addResult('✓ 菜单JavaScript已加载', 'success');
                } else {
                    addResult('⚠ 菜单JavaScript加载状态未知', 'warning');
                }

                addResult('测试完成！', 'info');
                addResult('提示：请访问首页 http://127.0.0.1:8000 查看实际菜单效果。', 'info');
            }, 2000);
        });
    </script>
</body>
</html>