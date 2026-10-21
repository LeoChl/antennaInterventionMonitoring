<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
<main id="dashboard-antenna" class="dashboard container py-4">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="dashboard__title h3 mb-0">{{ config('app.name') }}</h1>
    </header>
</main>
</body>
</html>
