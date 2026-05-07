<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Product Inventory Management System</title>
    <link href="./assets/output.css" rel="stylesheet">
    <script src="/assets/js/jquery.min.js"></script>
</head>

<body class="login flex items-center justify-center min-h-screen bg-orange-100">
    <div class="container mx-3">
        <p class="bg-amber-500 max-w-sm mx-auto text-center text-lg font-bold text-white p-2 rounded-lg">Product
            Inventory Management System</p>
        <main class="login-card max-w-sm mx-auto mt-5 py-2 px-4 bg-white rounded-lg shadow-md">

            <section class="brand text-center text-2xl font-bold mb-3">
                <h1>Sign In</h1>
            </section>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address:</label> <br />
                    <input type="email"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        id="email" name="email" placeholder="Enter your email" autocomplete="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label> <br />
                    <input type="password"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        id="password" name="password" placeholder="Enter your password" autocomplete="current-password"
                        required>
                </div>

                <button class="btn bg-amber-500 hover:bg-amber-600 mt-3 text-white font-bold py-2 px-4 rounded w-full"
                    type="submit">Login</button>

                <p class="text-red-500 mt-3" id="errorMsg"></p>
            </form>

            <script>
                $('#loginForm').on('submit', function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: '/login/authenticate',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            email: $('#email').val(),
                            password: $('#password').val()
                        },
                        success: function (response) {
                            if (response.success) {
                                window.location.href = response.redirect || '/dashboard';
                            } else {
                                $('#errorMsg').text(response.message);
                            }
                        },
                        error: function () {
                            $('#errorMsg').text('Login failed. Please try again.');
                        }
                    });
                });
            </script>
        </main>
    </div>
</body>

</html>