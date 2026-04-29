<!-- This is the main layout file that includes the navigation bar -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory Management System</title>
    <link href="/assets/output.css" rel="stylesheet">
</head>

<body class="bg-orange-50">
    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-100 bg-linear-to-r from-amber-400 via-amber-500 to-orange-500 shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-white text-2xl font-bold">PIM System</a>
            <ul class="flex gap-8 list-none">
                <li><a href="/dashboard"
                        class="text-white font-medium hover:bg-white/20 px-4 py-2 rounded transition">Dashboard</a></li>
                <li><a href="#"
                        class="text-white font-medium hover:bg-white/20 px-4 py-2 rounded transition">Products</a></li>
                <li><a href="#"
                        class="text-white font-medium hover:bg-white/20 px-4 py-2 rounded transition">Inventory</a></li>
                <li><a href="/users-list"
                        class="text-white font-medium hover:bg-white/20 px-4 py-2 rounded transition">Users</a></li>
                <li><a href="#" class="text-white font-medium hover:bg-white/20 px-4 py-2 rounded transition">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 ">
        <!-- Page content will go here -->
    </div>
</body>

</html>