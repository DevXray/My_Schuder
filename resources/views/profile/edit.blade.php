<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
     @vite(['resources/css/dashboard.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#082a98',
                        'primary-orange': '#F67C1F',
                        'dark-blue': '#022085f2',
                        'light-blue': '#eff6ff',
                        'dark-orange': '#db6308',
                        'light-orange': '#ffe5d5',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    
    @include('partials.header')
    @include('partials.sidebar')

    <main class="ml-0 lg:ml-[260px] mt-[70px] p-4 md:p-8 transition-all duration-300">
        
        <!-- Profile Header -->
        <div class="bg-gradient-to-br from-primary-blue to-primary-orange rounded-2xl p-6 md:p-8 mb-6 shadow-xl relative overflow-hidden">
            <!-- Decorative Circles -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6">
                <!-- Avatar -->
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-4xl md:text-5xl font-bold shadow-2xl border-4 border-white/30">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                
                <!-- User Info -->
                <div class="flex-1 text-center md:text-left text-white">
                    <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ Auth::user()->name }}</h1>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full mb-4">
                        <i class="fas fa-shield-alt"></i>
                        <span class="font-semibold">{{ Auth::user()->getRoleDisplayName() }}</span>
                    </div>
                    <div class="space-y-2 text-white/90">
                        <p class="flex items-center justify-center md:justify-start gap-2">
                            <i class="fas fa-envelope"></i> 
                            {{ Auth::user()->email }}
                        </p>
                        <p class="flex items-center justify-center md:justify-start gap-2">
                            <i class="fas fa-calendar-alt"></i> 
                            Bergabung sejak {{ Auth::user()->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Update Profile Information Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="bg-gradient-to-r from-light-blue to-white p-6 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-blue to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-user-edit text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Informasi Profil</h2>
                            <p class="text-sm text-gray-600">Perbarui informasi akun Anda</p>
                        </div>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="p-6 space-y-5">
                    @csrf
                    @method('patch')

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-primary-blue"></i>
                            {{ __('Nama Lengkap') }}
                        </label>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-blue focus:ring-4 focus:ring-blue-50 outline-none transition"
                            value="{{ old('name', $user->name) }}" 
                            required 
                            autofocus 
                            autocomplete="name"
                        />
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-primary-blue"></i>
                            {{ __('Email') }}
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-blue focus:ring-4 focus:ring-blue-50 outline-none transition"
                            value="{{ old('email', $user->email) }}" 
                            required 
                            autocomplete="username"
                        />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-3 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="text-sm text-yellow-800 mb-2">{{ __('Email Anda belum diverifikasi.') }}</p>
                                        <form method="post" action="{{ route('verification.send') }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold text-yellow-700 hover:text-yellow-900 underline">
                                                <i class="fas fa-paper-plane"></i>
                                                {{ __('Kirim ulang email verifikasi') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @if (session('status') === 'verification-link-sent')
                                <div class="mt-2 p-3 bg-green-50 border-l-4 border-green-500 rounded-lg">
                                    <p class="text-sm text-green-700 flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __('Link verifikasi baru telah dikirim ke email Anda.') }}
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="flex-1 md:flex-none bg-gradient-to-r from-primary-blue to-primary-orange text-white font-bold py-3 px-6 rounded-xl hover:shadow-xl hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            {{ __('Simpan Perubahan') }}
                        </button>

                        @if (session('status') === 'profile-updated')
                            <div class="hidden md:flex items-center gap-2 text-sm text-green-600 font-semibold animate-fade-in">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Profil berhasil diperbarui!') }}
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Update Password Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="bg-gradient-to-r from-yellow-50 to-white p-6 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-lock text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Keamanan Akun</h2>
                            <p class="text-sm text-gray-600">Perbarui kata sandi Anda</p>
                        </div>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="p-6 space-y-5">
                    @csrf
                    @method('put')

                    <!-- Current Password -->
                    <div>
                        <label for="update_password_current_password" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-key text-yellow-600"></i>
                            {{ __('Kata Sandi Saat Ini') }}
                        </label>
                        <input 
                            id="update_password_current_password" 
                            name="current_password" 
                            type="password" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-50 outline-none transition"
                            autocomplete="current-password"
                        />
                        @error('current_password', 'updatePassword')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="update_password_password" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-yellow-600"></i>
                            {{ __('Kata Sandi Baru') }}
                        </label>
                        <input 
                            id="update_password_password" 
                            name="password" 
                            type="password" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-50 outline-none transition"
                            autocomplete="new-password"
                        />
                        @error('password', 'updatePassword')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="update_password_password_confirmation" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-shield-alt text-yellow-600"></i>
                            {{ __('Konfirmasi Kata Sandi Baru') }}
                        </label>
                        <input 
                            id="update_password_password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-50 outline-none transition"
                            autocomplete="new-password"
                        />
                        @error('password_confirmation', 'updatePassword')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="flex-1 md:flex-none bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold py-3 px-6 rounded-xl hover:shadow-xl hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            {{ __('Update Password') }}
                        </button>

                        @if (session('status') === 'password-updated')
                            <div class="hidden md:flex items-center gap-2 text-sm text-green-600 font-semibold animate-fade-in">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Password berhasil diubah!') }}
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Delete Account Card (Full Width) -->
            <div class="lg:col-span-2 bg-red-50 rounded-2xl shadow-lg border-2 border-red-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="bg-gradient-to-r from-red-50 to-red-100 p-6 border-b-2 border-red-200">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-red-800">Zona Bahaya</h2>
                            <p class="text-sm text-red-600">Hapus akun Anda secara permanen</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <p class="text-gray-700 mb-6">
                        {{ __('Setelah akun Anda dihapus, semua data dan informasi akan dihapus secara permanen. Sebelum menghapus akun, silakan unduh data yang ingin Anda simpan.') }}
                    </p>

                    <button 
                        type="button" 
                        class="bg-gradient-to-r from-red-500 to-red-600 text-white font-bold py-3 px-6 rounded-xl hover:shadow-xl hover:scale-[1.02] transition-all duration-300 flex items-center gap-2"
                        onclick="document.getElementById('deleteAccountModal').classList.remove('hidden')"
                    >
                        <i class="fas fa-trash-alt"></i>
                        {{ __('Hapus Akun') }}
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Delete Account Modal -->
    <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-scale-in">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold">{{ __('Apakah Anda yakin?') }}</h2>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-gray-700 mb-6">
                    {{ __('Setelah akun Anda dihapus, semua data akan hilang secara permanen. Silakan masukkan password Anda untuk konfirmasi.') }}
                </p>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="mb-6">
                        <label for="password" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-key text-red-600"></i>
                            {{ __('Password') }}
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-50 outline-none transition"
                            placeholder="{{ __('Masukkan password Anda') }}"
                        />
                        @error('password', 'userDeletion')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex gap-3">
                        <button 
                            type="button" 
                            class="flex-1 bg-gray-100 text-gray-700 font-bold py-3 px-4 rounded-xl hover:bg-gray-200 transition-all duration-300 flex items-center justify-center gap-2"
                            onclick="document.getElementById('deleteAccountModal').classList.add('hidden')"
                        >
                            <i class="fas fa-times"></i>
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="flex-1 bg-gradient-to-r from-red-500 to-red-600 text-white font-bold py-3 px-4 rounded-xl hover:shadow-xl hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-trash-alt"></i>
                            {{ __('Hapus Akun') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>

    <script>
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('deleteAccountModal').classList.add('hidden');
        }
    });

    // Auto-hide success messages after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelectorAll('.animate-fade-in').forEach(el => {
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.3s';
                setTimeout(() => el.remove(), 300);
            });
        }, 3000);
    });
    </script>

    <style>
    @keyframes scale-in {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .animate-scale-in {
        animation: scale-in 0.3s ease;
    }
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease;
    }
    </style>
</body>
</html>