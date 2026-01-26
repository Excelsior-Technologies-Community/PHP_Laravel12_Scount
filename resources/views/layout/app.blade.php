<!DOCTYPE html>
<html>
<head>
    <title>Laravel Scout Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        header {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            padding: 20px 40px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        nav {
            margin-top: 10px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin-right: 15px;
            font-weight: 500;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        hr {
            border: none;
            height: 1px;
            background: #e5e7eb;
            margin: 20px 0;
        }

        footer {
            text-align: center;
            padding: 15px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>

<header>
    <h1>🚀 Laravel 12 + Scout</h1>
    <nav>
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/posts/create') }}">Add Post</a>
    </nav>
</header>

<div class="container">
    @yield('content')
</div>

<footer>
    © {{ date('Y') }} Laravel Scout Demo
</footer>

</body>
</html>
