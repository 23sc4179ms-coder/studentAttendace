<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Successful</title>
    <meta http-equiv="refresh" content="2;url={{ $redirectUrl }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; }
    </style>
</head>
<body>
    <h1 style="color:green">{{ $msg ?? 'Login successful' }}</h1>
    <p>Redirecting to the students landing page... <a href="{{ $redirectUrl }}">Click here if not redirected</a></p>
</body>
</html>
