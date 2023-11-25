<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Appointment Manager - Login</title>
    <style>
        body {
            background: #f3f3f3;
            color: #333;
            font-family: 'Arial', sans-serif;
        }
        .container {
            display: flex;
            justify-content: center;
            height: 100vh;
        }
        .card {
            background: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .card-header {
            background-color: #3498db;
            color: #fff;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }
        .form-group {
            margin: 20px;
        }
        .form-control {
            border: none;
            border-bottom: 2px solid #3498db;
            border-radius: 0;
            background: transparent;
            color: #333;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #3498db;
        }
        .btn-primary {
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            color: #fff;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background-color: #5dade2;
            color: #fff;
        }
        .alert {
            border-radius: 5px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        .register-link a {
            color: #333;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="padding-inline: 5rem; margin-bottom:5rem;">
        <a class="navbar-brand " href="#" style="font-size: 24px; font-weight: bold; color: #fff;">Appointment Manager</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav ml-4 mr-4">
                <!-- Add other navbar items if needed -->
                <li class="nav-item" style="margin: left">

                </li>
            </ul>
        </div>
    </nav>

<div class="container">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Login
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('userLogin') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="Enter Email">
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Enter Password">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>

                @if (\Session::has('error'))
                    <div class="alert alert-danger mt-3">
                        {{ \Session::get('error') }}
                    </div>
                @endif

                <div class="register-link">
                    <p>Don't have an account? <a href="/register">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
