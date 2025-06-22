<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - UD.DNL PUTRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-200 flex items-center justify-center h-screen">

    <div class="p-10 rounded-lg w-full max-w-sm shadow-md" style="background-color: #72949C;">
        <div class="bg-gray-600 text-white text-center py-3 rounded-t-md mb-6">
            <h1 class="text-lg font-semibold leading-tight">REGISTER AKUN<br>UD.DNL PUTRA</h1>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4 text-sm">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}">
            @csrf
            <div class="mb-4">
                <input type="text" name="name" class="w-full px-4 py-2 bg-gray-600 text-white rounded focus:outline-none" placeholder="Nama" required>
            </div>

            <div class="mb-4">
                <input type="email" name="email" class="w-full px-4 py-2 bg-gray-600 text-white rounded focus:outline-none" placeholder="Email" required>
            </div>

            <div class="mb-4">
                <input type="password" name="password" class="w-full px-4 py-2 bg-gray-600 text-white rounded focus:outline-none" placeholder="Password" required>
            </div>

            <div class="mb-4">
                <input type="password" name="password_confirmation" class="w-full px-4 py-2 bg-gray-600 text-white rounded focus:outline-none" placeholder="Konfirmasi Password" required>
            </div>

            <div class="mb-4">
                <select name="role" class="w-full px-4 py-2 bg-gray-600 text-white rounded focus:outline-none" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="operator">Operator</option>
                    <option value="pimpinan">Pimpinan</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full bg-gray-600 text-white py-2 rounded hover:bg-gray-700 transition">Daftar</button>
            </div>
        </form>

        <div class="text-right mt-1">Kembali ke 
            <a href="{{ route('login') }}" class="text-white underline">login</a>
        </div>
        
    </div>

</body>
</html>
