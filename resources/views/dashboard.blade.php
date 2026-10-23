<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="dashboard-page">
<main id="dashboard-antenna" class="dashboard">
    <header class="dashboard__header">
        <h1 class="dashboard__title">{{ config('app.name') }}</h1>
    </header>
    @include('dashboard._antenna-table')
    @include('dashboard._modal-create')
    @include('dashboard._modal-close')
</main>
</body>
</html>
