<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 12 Scout Demo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            color: #333;

            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            padding: 18px 40px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .12);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
        }

        nav {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            transition: .3s;
            font-weight: 500;
        }

        nav a:hover {
            background: rgba(255, 255, 255, .18);
        }

        .container {
            max-width: 1200px;
            margin: 35px auto;
            padding: 25px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);

            flex: 1;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid #10b981;
        }

        footer {
            background: #111827;
            color: #d1d5db;
            text-align: center;
            padding: 20px;
            margin-top: auto;
        }

        footer p {
            margin: 5px 0;
        }

        @media(max-width:768px) {

            header {
                padding: 20px;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            nav {
                width: 100%;
            }

            nav a {
                width: 100%;
                text-align: center;
            }

            .container {
                margin: 20px;
                padding: 20px;
            }

            .logo {
                font-size: 22px;
            }

        }
    </style>

</head>

<body>

    <header>

        <div class="navbar">

            <div class="logo">
                🚀 Laravel Scout
            </div>

            <nav>

                <a href="{{ route('posts.index') }}">
                    🏠 Home
                </a>

                <a href="{{ route('posts.create') }}">
                    ➕ Add Post
                </a>

                <a href="{{ route('posts.export') }}">
                    📄 Export CSV
                </a>

                <a href="{{ route('search.analytics') }}">
                    📊 Analytics
                </a>

            </nav>

        </div>

    </header>

    <div class="container">

        @if(session('success'))

        <div class="alert-success">
            {{ session('success') }}
        </div>

        @endif

        @yield('content')

    </div>

    <footer>

        <p><strong>Laravel 12 Scout Demo</strong></p>

        <p>
            Search • Pagination • Statistics • Export CSV
        </p>

        <p>
            © {{ date('Y') }} All Rights Reserved.
        </p>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>