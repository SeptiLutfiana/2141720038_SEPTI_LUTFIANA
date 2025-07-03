<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Menara PeFI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col md:flex-row font-sans overflow-auto">

    <!-- Left Side -->
    <div class="md:w-1/2 w-full bg-white flex flex-col items-center justify-center p-6">
        <img src="{{ asset('img/logo-1.png') }}" alt="Logo" class="w-3/5 max-w-sm" />
    </div>

    <!-- Right Side -->
    <div class="md:w-1/2 w-full bg-lime-500 flex items-center justify-center p-6">
        <div class="w-full max-w-md text-white">
            <h2 class="text-3xl font-bold text-center mb-6 text-black">Login</h2>

            @if (Session::has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">{{ Session::get('error') }}</strong>
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="login" class="block text-sm text-white mb-1">Username (Email or NPK)</label>
                    <input id="login" name="login" type="text" placeholder="Masukkan NPK atau Email" required
                        class="w-full px-4 py-2 rounded-full text-black focus:outline-none focus:ring-2 focus:ring-white" />
                    @error('login')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm text-white mb-1">Password</label>
                    <input id="password" name="password" type="password" placeholder="Masukkan Password" required
                        class="w-full px-4 py-2 rounded-full text-black focus:outline-none focus:ring-2 focus:ring-white" />
                    @error('password')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-between text-sm">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-green-600" name="remember">
                        <span class="ml-2 text-white">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-white underline hover:text-gray-200">
                        Forgot password?
                    </a>
                </div>
                <input type="hidden" name="g-recaptcha-response" id="recaptchaToken">
                <script src="https://www.google.com/recaptcha/api.js?render={{ env('NOCAPTCHA_SITEKEY') }}"></script>
                <script>
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ env('NOCAPTCHA_SITEKEY') }}', {
                            action: 'login'
                        }).then(function(token) {
                            document.getElementById('recaptchaToken').value = token;
                        });
                    });
                </script>
                <button type="submit"
                    class="w-full sm:w-1/2 mx-auto block bg-green-800 hover:bg-green-900 text-white py-2 rounded-full font-semibold transition">
                    Login
                </button>

                <div class="flex items-center my-4">
                    <div class="flex-grow border-t border-white"></div>
                    <span class="mx-4 text-black text-sm">Login with Others</span>
                    <div class="flex-grow border-t border-white"></div>
                </div>

                <div class="flex justify-center">
                    <a href="{{ url('/auth/google/redirect') }}"
                        class="w-full flex items-center justify-center gap-3 py-2 border border-white rounded-xl text-white hover:bg-green-700 transition">
                        <img src="https://cdn-icons-png.flaticon.com/512/2702/2702602.png" alt="Google"
                            class="w-6 h-6">
                        <span class="text-sm font-semibold">Login with <span class="font-bold">Google</span></span>
                    </a>
                </div>
            </form>

        </div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>

</html>
