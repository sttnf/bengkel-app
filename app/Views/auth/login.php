<?php
$redirect = htmlspecialchars($_GET['redirect'] ?? '/dashboard');
?>

<body class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
<div class="sm:mx-auto sm:w-full sm:max-w-md mt-12">
    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Login</h2>
    <div class="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" method="POST">
            <input type="hidden" name="redirect" value="<?= $redirect ?>">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                        </svg>
                    </div>
                    <input id="email" name="email" type="email" required
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Masukkan email Anda">
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <input id="password" name="password" type="password" required
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Masukkan password Anda">
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox"
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
                </div>
                <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500">Lupa password?</a>
            </div>
            <div>
                <button type="submit"
                        class="w-full py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary700 focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Masuk
                </button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Belum punya akun?
                <a href="/register" class="font-medium text-green-600 hover:text-green-500">Daftar</a>
            </p>
        </div>
    </div>
</div>
<script src="/js/form.js"></script>
</body>