<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel com Tailwind</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body>
    
    <livewire:calendar />
    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>


