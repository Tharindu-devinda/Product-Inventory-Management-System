<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Product Inventory Management System</title>
    <link href="/assets/output.css" rel="stylesheet">
</head>

<body class="login flex items-center justify-center min-h-screen bg-orange-100">
    <div class="container mx-3">
        <p class="bg-amber-500 max-w-sm mx-auto text-center text-lg font-bold text-white p-2 rounded-lg">Product
            Inventory Management System</p>
        <main class="login-card max-w-sm mx-auto mt-5 py-2 px-4 bg-white rounded-lg shadow-md">

            <section class="brand text-center text-2xl font-bold">
                <h1>Create Account</h1>
            </section>

            <?php if (!empty($success)): ?>
                <p class="text-green-600 text-center"><?= $success ?></p>
            <?php endif; ?>

            <form action="/register/store_user" method="POST">
                <div class="form-group mt-1">
                    <label for="username">User Name :</label> <br />
                    <input type="text" minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+"
                        value="<?= $old['username'] ?? '' ?>"
                        title="Only letters, numbers, underscore. Min 3 characters." class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2
                    focus:ring-amber-500" id="username" name="username" placeholder="Enter your user name" required>
                    <small class="text-red-500"> <?= $errors['username'] ?? '' ?> </small>
                </div>


                <div class="form-group mt-1">
                    <label for="email">Email Address :</label> <br />
                    <input type="email" value="<?= $old['email'] ?? '' ?>" class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2
                    focus:ring-amber-500" id="email" name="email" placeholder="example@gmail.com" autocomplete="email"
                        minlength="6" pattern=".{6,}" title="Minimum 6 characters required" required>
                    <small class="text-red-500"> <?= $errors['email'] ?? '' ?> </small>
                </div>

                <div class="form-group mt-1">
                    <label for="password">Password</label> <br />
                    <input type="password"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        id="password" name="password" placeholder="Enter your password" autocomplete="new-password"
                        minlength="6" pattern=".{6,}" title="Minimum 6 characters required" required>
                    <small class="text-red-500"> <?= $errors['password'] ?? '' ?> </small>
                </div>

                <div class="form-group mt-1">
                    <label for="confirm_password">Confirm Password</label> <br />
                    <input type="password"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        id="confirm_password" name="confirm_password" placeholder="Confirm your password"
                        autocomplete="new-password" required>
                    <small class="text-red-500"> <?= $errors['confirm_password'] ?? '' ?> </small>
                </div>

                <p id="error" style="color:red;"></p>

                <button class="btn bg-amber-500 hover:bg-amber-600 mt-3 text-white font-bold py-2 px-4 rounded w-full"
                    type="submit">Register</button>
            </form>
        </main>
    </div>
    <script>
        document.querySelector("form").addEventListener("submit", function (e) {

            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let error = document.getElementById("error");

            if (password !== confirmPassword) {
                error.textContent = "Passwords do not match";
                e.preventDefault();
            } else {
                error.textContent = "";
            }

        });
    </script>
</body>

</html>