<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>公共会计师协会集团（IPA Group）</title>
    <link rel="stylesheet" href="{{ asset('assets/index.css') }}">
</head>
<body>
    <div id="root"></div>

    <script>
        window.pageData = @json($pageData ?? []);
    </script>
    <script type="module" src="{{ asset('assets/index.js') }}"></script>
</body>
</html>