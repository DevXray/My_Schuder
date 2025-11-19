<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#082a98',
                        'primary-orange': '#F67C1F',
                        'dark-blue': '#022085',
                        'light-blue': '#eff6ff',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    <div class="grid lg:grid-cols-2 min-h-screen">
        <!-- Left Side - Illustration -->
        <div class="hidden lg:flex bg-gradient-to-br from-primary-blue to-primary-orange p-12 relative overflow-hidden">
            <!-- Decorative Circles -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-48 -mt-48"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full -ml-40 -mb-40"></div>
            
            <div class="relative z-10 flex flex-col justify-center items-center text-white w-full">
                <!-- Logo Section -->
                <div class="text-center mb-12">
                    <img src="{{ Vite::asset('resources/assets/logo_akademik_hd.png') }}" alt="Logo" class="w-24 h-24 mx-auto mb-4 drop-shadow-2xl">
                    <h1 class="text-4xl font-bold mb-2">My Schuder</h1>
                    <p class="text-blue-100 text-lg">Portal Pembelajaran Modern</p>
                </div>

                <!-- Illustration Icon -->
                <div class="mb-8">
                    <i class="fas fa-graduation-cap text-9xl opacity-90"></i>
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold mb-3">Selamat Datang Kembali!</h2>
                    <p class="text-blue-100 text-lg max-w-md">Akses materi pembelajaran, tugas, dan jadwal kelas Anda dengan mudah.</p>
                </div>

                <!-- Features -->
                <div class="space-y-4 w-full max-w-md">
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <span class="text-lg">Akses Materi Kapan Saja</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <span class="text-lg">Kelola Tugas & Deadline</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <span class="text-lg">Pantau Progress Belajar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex items-center justify-center p-8 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ Vite::asset('resources/assets/logo_akademik_hd.png') }}" alt="Logo" class="w-20 h-20 mx-auto mb-3">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-primary-blue to-primary-orange bg-clip-text text-transparent">My Schuder</h1>
                </div>

                <!-- Form Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Login ke Akun Anda</h2>
                    <p class="text-gray-600">Masukkan kredensial Anda untuk melanjutkan</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <p class="text-green-700">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-primary-blue"></i>
                            Email
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-blue focus:ring-4 focus:ring-blue-50 outline-none transition @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com"
                        />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-primary-blue"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-primary-blue focus:ring-4 focus:ring-blue-50 outline-none transition @error('password') border-red-500 @enderror"
                                placeholder="Masukkan password"
                            />
                            <button 
                                type="button" 
                                onclick="togglePassword()" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-primary-blue border-gray-300 rounded focus:ring-2 focus:ring-primary-blue">
                            <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary-blue hover:text-primary-orange transition">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-primary-blue to-primary-orange text-white font-bold py-4 rounded-xl hover:shadow-xl hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </button>

                    <!-- Register Link -->
                    <p class="text-center text-gray-600">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="font-semibold text-primary-blue hover:text-primary-orange transition">
                            Daftar Sekarang
                        </a>
                    </p>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Atau login dengan</span>
                    </div>
                </div>

                <!-- Social Login -->
                <div class="grid grid-cols-2 gap-4">
                    <button class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition font-semibold text-gray-700">
                        <i class="fab fa-google text-xl text-red-500"></i>
                        Google
                    </button>
                    <button class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition font-semibold text-gray-700">
                        <i class="fab fa-microsoft text-xl text-blue-500"></i>
                        Microsoft
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>