<x-guest-layout>

    <div class="mx-auto w-full max-w-md px-4">

        {{-- Logo and Heading --}}
        <div class="mb-7 text-center">

            <div class="flex justify-center items-center">
                <img src="{{ asset('assets/logo/logo2.png') }}" alt="DigiSoft Logo" class="h-24 w-72 object-contain">
            </div>

            <h1 class="mt-3 text-2xl sm:text-2xl font-bold leading-tight text-gray-800">
                Document Management System
            </h1>

            <p class="mt-2 text-sm sm:text-base text-gray-500">
                Secure Digital Record Management
            </p>

        </div>

        {{-- Login Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">

            <x-auth-session-status class="mb-5" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">

                @csrf

                {{-- Username --}}
                <div>
                    <label for="login" class="mb-2 block text-sm font-semibold text-gray-700">
                        Username / Email
                    </label>

                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input id="login" type="text" name="login" value="{{ old('login') }}"
                            placeholder="Enter username or email" required autofocus autocomplete="username"
                            class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-[#2160BC] focus:ring-4 focus:ring-blue-100">
                    </div>

                    <x-input-error :messages="$errors->get('login')" class="mt-2" />
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">
                        Password
                    </label>

                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input id="password" type="password" name="password" placeholder="Enter your password" required
                            autocomplete="current-password"
                            class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-11 pr-12 text-sm text-gray-800 outline-none transition focus:border-[#2160BC] focus:ring-4 focus:ring-blue-100">

                        <button type="button" onclick="togglePassword()" aria-label="Toggle password visibility"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-gray-600">
                            <i id="passwordIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <label class="flex cursor-pointer items-center gap-2.5">
                        <input type="checkbox" name="remember"
                            class="h-4 w-4 rounded border-gray-300 text-[#2160BC] focus:ring-[#2160BC]">

                        <span class="text-sm text-gray-600">
                            Remember Me
                        </span>
                    </label>
                </div>

                {{-- Sign In Button --}}
                <button type="submit"
                    class="flex w-full items-center justify-center rounded-xl bg-[#2160BC] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1b4f9d] focus:outline-none focus:ring-4 focus:ring-blue-200">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i>
                    Sign In
                </button>

            </form>
        </div>

        {{-- Footer --}}
        <footer class="mt-6 text-center text-xs text-gray-500 sm:text-sm">
            © {{ date('Y') }} DigiSoft Developers Pvt. Ltd.
        </footer>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');

            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';

            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        }
    </script>

</x-guest-layout>
