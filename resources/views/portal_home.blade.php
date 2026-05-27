<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet" href="{{ asset('css/home.css')}}"></link>
</head>
<body>
    <x-layout title="Home">
        <div class="container py-5">
            <h1 class="display-4">This is homepage</h1>
            <a href="/nark" class="btn btn-primary mt-3">Go to Nark</a>
        </div>
    </x-layout>
</body>
</html>