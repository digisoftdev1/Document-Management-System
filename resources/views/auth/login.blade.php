<x-guest-layout>

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">

            <div
                class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-gradient-to-r from-[#03396c] to-[#2160BC] shadow-xl">

                <img src="{{ $setting?->website_logo ? Storage::url($setting->website_logo) : asset('assets/logo/logo.png') }}"
                    class="h-12 w-12 object-contain">

            </div>

            <h1 class="mt-6 text-4xl font-bold text-gray-800">
                Document Management System
            </h1>

            <p class="mt-2 text-gray-500">
                Secure Digital Record Management
            </p>

        </div>

        {{-- Card --}}
        <div class="rounded-3xl border border-white/50 bg-white/80 backdrop-blur-xl p-8 shadow-2xl">

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">

                @csrf

                {{-- Username --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">

                        Username / Email

                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        </i>

                        <input type="text" name="login" value="{{ old('login') }}"
                            placeholder="Enter username or email" required autofocus autocomplete="username"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 pl-12 outline-none transition focus:border-[#2160BC] focus:ring-4 focus:ring-blue-100">

                    </div>

                    <x-input-error :messages="$errors->get('login')" class="mt-2" />

                </div>


                {{-- Password --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">

                        Password

                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        </i>

                        <input type="password" id="password" name="password" placeholder="Enter your password" required
                            autocomplete="current-password"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 pl-12 pr-12 outline-none transition focus:border-[#2160BC] focus:ring-4 focus:ring-blue-100">

                        <button type="button" onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">

                            <i id="passwordIcon" class="fa-solid fa-eye">
                            </i>

                        </button>

                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />

                </div>


                {{-- Remember --}}
                <div class="flex items-center justify-between">

                    <label class="flex items-center gap-3 cursor-pointer">

                        <input type="checkbox" name="remember"
                            class="rounded border-gray-300 text-[#2160BC] focus:ring-[#2160BC]">

                        <span class="text-sm text-gray-600">
                            Remember Me
                        </span>

                    </label>

                </div>


                {{-- Button --}}
                <button type="submit"
                    class="w-full rounded-2xl bg-gradient-to-r from-[#03396c] to-[#2160BC] py-3.5 text-white font-semibold shadow-lg transition hover:scale-[1.01] hover:shadow-xl">

                    <i class="fa-solid fa-right-to-bracket mr-2"></i>

                    Sign In

                </button>

            </form>

        </div>

        <div class="mt-8 text-center text-sm text-gray-500">

            © {{ date('Y') }}
            DigiSoft Developers Pvt. Ltd.

        </div>

    </div>

    <script>
        function togglePassword() {

            const input = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');

            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');

            } else {

                input.type = 'password';

                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</x-guest-layout>
