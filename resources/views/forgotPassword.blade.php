<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - UD.DNL PUTRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-200 flex items-center justify-center h-screen">

    <div class="bg-slate-400 p-6 rounded shadow-md w-full max-w-sm">
        <h2 class="text-center text-white font-bold mb-4 bg-slate-600 py-2 rounded">RESET PASSWORD<br>UD.DNL PUTRA</h2>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-2 rounded mb-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" class="w-full px-3 py-2 rounded mb-3 bg-slate-500 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required autofocus>

            <button type="submit" class="w-full bg-slate-700 text-white py-2 rounded hover:bg-slate-800">
                Kirim Link Reset
            </button>
        </form>

        <div class="mt-3 text-center text-sm">
            <a href="{{ route('login') }}" class="text-blue-100 hover:underline">Kembali ke login</a>
        </div>
    </div>

</body>
</html>
