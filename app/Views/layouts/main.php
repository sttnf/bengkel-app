<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP MVC App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="text-xl font-bold text-indigo-600">PHP MVC</a>
                    </div>
                    <div class="ml-6 flex items-center space-x-4">
                        <a href="/" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900">Home</a>
                        <a href="/users" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900">Users</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            {{content}}
        </div>
    </main>

    <footer class="bg-white shadow-inner mt-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                PHP MVC Framework &copy; <?= date('Y') ?>
            </p>
        </div>
    </footer>
</body>
</html>