<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - UD.DNL PUTRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-200 flex items-center justify-center h-screen">

    <div class="p-10 rounded-lg w-full max-w-sm shadow-md" style="background-color: #72949C;">
        <div class="bg-gray-600 text-white text-center py-3 rounded-t-md mb-6">
            <h1 class="text-lg font-semibold leading-tight">WELCOME TO<br>UD.DNL PUTRA</h1>
        </div>

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <input 
                    type="email" 
                    name="email" 
                    class="w-full px-4 py-2 bg-gray-600 text-white rounded focus:outline-none" 
                    placeholder="Email" 
                    required 
                    autofocus>
            </div>

            <div class="mb-4">
                <input 
                    type="password" 
                    name="password" 
                    class="w-full px-4 py-2 bg-gray-600 text-white rounded focus:outline-none" 
                    placeholder="Password" 
                    required>
                    <div class="text-right mt-1">Lupa Password?
                        <a href="{{ route('password.request') }}" class="text-white underline hover:text-gray-200 text-sm">
                            Reset
                        </a>
                    </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="w-full bg-gray-600 text-white py-2 rounded hover:bg-gray-700 transition">
                    Login
                </button>
            </div>  
            <div class="text-right mt-1">Belum punya akun?
                <a href="{{ route('register') }}" class="text-white underline hover:text-gray-200 text-sm">
                    Daftar di sini
                </a>
            </div>
        </form>
    </div>

</body>
</html>
