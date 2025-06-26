@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Login')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.css') }}">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            background-color: #ffffff !important;
        }

        .auth-page-bg {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        .auth-card {
            overflow: hidden;
            display: flex;
            flex-direction: row;
            width: 1000px;
            max-width: 95%;
            height: 750px;
            min-height: 650px;
            max-height: 650px;
        }

        .login-left-image {
            background: url('{{ URL::asset('/assets/images/left-side-login.png') }}') no-repeat center center;
            background-size: contain;
            width: 50%;
            border-radius: 20px 20px 20px 20px;
        }

        .login-right-form {
            padding: 50px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .auth-logo {
            position: absolute;
            top: 20px;
            left: 40px;
        }

        .auth-logo img {
            height: 55px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            height: 45px;
            border-radius: 8px;
        }

        .form-check {
            margin: 20px 0;
        }

        /* Mobile Responsive */
       /* Phones (≤425px) */
@media (max-width: 425px) {
    .auth-card {
        flex-direction: column;
        width: 95%;
        height: auto;
        min-height: auto;
        max-height: none;
        border-radius: 20px;
    }

    .login-left-image {
        display: none;
    }

    .login-right-form {
        padding: 30px 20px;
    }

    .auth-logo {
        top: 20px;
        left: 20px;
    }

    .btn-primary {
        width: 100% !important;
    }

    .d-flex {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}

/* Small Tablets (426px - 767px) */
@media (max-width: 767px) and (min-width: 426px) {
    .auth-card {
        flex-direction: column;
        width: 95%;
        height: auto;
        min-height: auto;
        max-height: none;
        border-radius: 20px;
    }

    .login-left-image {
        display: none;
    }

    .login-right-form {
        padding: 40px 30px;
    }

    .auth-logo {
        top: 25px;
        left: 30px;
    }

    .btn-primary {
        width: 50% !important;
    }

    .d-flex {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}

/* Medium Devices (768px - 991px) */
@media (max-width: 991px) and (min-width: 768px) {
    .auth-card {
        width: 95%;
        height: auto;
        min-height: auto;
        max-height: none;
        flex-direction: row;
        border-radius: 20px;
    }

    .login-left-image {
        width: 45%;
        background-size: cover;
    }

    .login-right-form {
        padding: 50px 35px;
    }

    .auth-logo {
        top: 10px;
        left: 30px;
    }
}

        .form-check-input:checked {
            background-color: #002b5c !important;
            border-color: #002b5c !important
        }

        .btn-primary {
            background-color: #002B5C !important;
            color: #ffffff !important;
            border: none !important;
            padding: 14px 0 !important;
            border-radius: 6px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            width: 40% !important;
            text-align: center !important;
            transition: background-color 0.3s ease !important;
        }

        .btn-primary:hover {
            background-color: #003366 !important;
            color: #ffffff !important;
        }

        .btn-outline-secondary:hover,
        .btn-outline-secondary:focus,
        .btn-outline-secondary:active,
        .btn-outline-secondary {
            background-color: unset !important;
            color: unset !important;
            border-color: #ced4da !important;

        }

        .btn-primary:focus,
        .btn-primary:active {
            background-color: #001F3F !important;
            color: #ffffff !important;
            outline: none !important;
            border: none !important;
        }
    </style>
@endsection



@section('content')
    <div class="auth-page-bg">
        <div class="auth-card">

            <div class="login-left-image"></div>

            <div class="login-right-form">

                <div class="auth-logo">
                    <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="State Energy">
                </div>

                <h4 class="mb-3 mt-5 pt-4">Login!</h4>
                <p class="text-muted mb-4">Enter authorized email address & password.</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" id="email"
                            placeholder="superman@mystateenergy.com" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="Password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>
                        </div>
                    </div>



                    <div class="d-flex" style="justify-content: space-between;">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Stay Signed In</label>
                        </div>
                        <button class="btn btn-primary" type="submit">Login</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('mdi-eye-outline');
            this.querySelector('i').classList.toggle('mdi-eye-off-outline');
        });
    </script>
@endsection
