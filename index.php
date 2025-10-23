<?php
include "root.php";
session_start();
if (isset($_SESSION['username'])) {
    $root->redirect("home.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Aplikasi Penjualan</title>
    <link rel="stylesheet" href="assets/awesome/css/font-awesome.min.css">
    <style>
        @font-face {
            font-family: 'Titillium';
            src: url(assets/TitilliumWeb-SemiBold.ttf);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Titillium', sans-serif;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url("assets/img/wppp.jpg") no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 20px;
        }

        .login {
            width: 400px;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transform-style: preserve-3d;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .login:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .login::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #41b3f9, #2fa7ee, #1e9ae5);
        }

        h3 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
            position: relative;
            padding-bottom: 15px;
        }

        h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #41b3f9, #2fa7ee);
        }

        .login input {
            width: 100%;
            margin-bottom: 25px;
            border: none;
            border-bottom: 2px solid #ddd;
            padding: 12px 10px;
            font-size: 16px;
            transition: all 0.3s;
            background: transparent;
        }

        .login input:focus {
            border-bottom-color: #41b3f9;
            outline: none;
            box-shadow: 0 5px 10px -8px rgba(65, 179, 249, 0.3);
        }

        .login button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #41b3f9, #2fa7ee);
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(65, 179, 249, 0.4);
        }

        .login button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(65, 179, 249, 0.6);
        }

        .login button i {
            margin-right: 8px;
        }

        #status {
            margin-top: 20px;
            padding: 12px;
            border-radius: 8px;
            display: none;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        .red {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #f5c6cb;
        }

        .green {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #c3e6cb;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo i {
            font-size: 50px;
            color: #41b3f9;
            background: linear-gradient(135deg, #41b3f9, #2fa7ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
    <script src="assets/jquery.js"></script>
    <script>
        $(document).ready(function(){
            $("#loginapp").submit(function(){
                $.ajax({
                    type: 'POST',
                    url: 'handler.php?action=login',
                    data: $(this).serialize(),
                    success: function(data){
                        $("#status").fadeIn(200).html(data);
                        setTimeout(function(){
                            $('#status').fadeOut(300);
                        }, 3000);
                    }
                });
                return false;
            });
        });
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login">
            <div class="logo">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <h3>Login Aplikasi Penjualan</h3>
            <form id="loginapp">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit"><i class="fa fa-sign-in"></i> Login</button>
            </form>
            <div id="status"></div>
        </div>
    </div>
</body>
</html>