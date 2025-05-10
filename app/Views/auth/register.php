<?php
echo "<script>
    console.log(" . json_encode($_SESSION) . ");
</script>";
?>

<body class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
<div class="sm:mx-auto sm:w-full sm:max-w-md mt-8">
    <h2 class="text-center text-3xl font-extrabold text-gray-900">Register</h2>
    <div class="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <?php if (!empty($_SESSION['registration_errors'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-1 ml-4 list-disc">
                    <?php foreach ($_SESSION['registration_errors'] as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php unset($_SESSION['registration_errors']); ?>
            </div>
            <br>
        <?php endif; ?>

        <form class="space-y-6" method="POST">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <div class="mt-1 relative">
                    <input id="name" name="name" type="text" autocomplete="name" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Your Full Name">
                </div>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1 relative">
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                           placeholder="your@email.com">
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1 relative">
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Enter your password">
                </div>
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                    Password</label>
                <div class="mt-1 relative">
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           autocomplete="new-password" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Confirm your password">
                </div>
            </div>
            <div>
                <button type="submit"
                        class="w-full py-2 px-4 border rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:ring-green-500">
                    Register
                </button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Already have an account?
                <a href="/login" class="font-medium text-primary-600 hover:text-primary-500">Log in</a>
            </p>
        </div>
    </div>
</div>
<script src="/js/form.js"></script>
</body>