<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Inodebox</title>
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/inode-table-preview.tsx'])
    </head>
    <body class="bg-[#FDFDFC] font-sans antialiased dark:bg-[#0a0a0a]">
        <div id="root"></div>
    </body>
</html>
