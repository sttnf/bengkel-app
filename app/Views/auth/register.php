<body class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Register</h2>
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php if (isset($_SESSION['registration_error'])): ?>

                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?= $_SESSION['registration_error'] ?></span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-red-500" role="button"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path
                                        fill-rule="evenodd"
                                        d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.586l-2.651 3.263a1.2 1.2 0 0 1-1.697-1.697L8.314 10l-3.263-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.314l2.651-3.263a1.2 1.2 0 0 1 1.697 1.697L11.686 10l3.263 2.651a1.2 1.2 0 0 1 0 1.697z"/></svg>
                        </span>
                </div>
                <br>
                <?php unset($_SESSION['registration_error']); ?>
            <?php endif; ?>

            <form class="space-y-6" action="/register" method="POST">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="lucide lucide-user h-5 w-5 text-gray-400">
                                <path d="M19 21a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2"/>
                                <path d="M16 7a4 4 0 1 0-8 0"/>
                            </svg>
                        </div>
                        <input id="name" name="name" type="text" autocomplete="name" required
                               class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Your Full Name">
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="lucide lucide-mail h-5 w-5 text-gray-400">
                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="your@email.com">
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="lucide lucide-lock h-5 w-5 text-gray-400">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter your password">
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                        Password</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="lucide lucide-lock h-5 w-5 text-gray-400">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               autocomplete="new-password" required
                               class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Confirm your password">
                    </div>
                </div>
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg
                                       shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500
                                       disabled:opacity-50 disabled:cursor-not-allowed">
                        Register
                    </button>
                </div>
            </form>
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Already have an account?
                    <a href="/login" class="font-medium text-blue-600 hover:text-blue-500">
                        Log in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
<script src="/js/form.js"></script>
</body>