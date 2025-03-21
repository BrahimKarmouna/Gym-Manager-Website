<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant Test Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-800 text-white flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-gray-700 shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
            <h2 class="text-xl font-bold mb-4 text-center">Assistant Test Login</h2>
            <p class="mb-4 bg-blue-600 p-3 rounded text-sm">
                This is a test login page for assistants. Use the pre-filled credentials below to log in.
            </p>
            
            @if ($errors->any())
            <div class="bg-red-500 p-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form method="POST" action="{{ route('assistant.login.test.submit') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-800 leading-tight focus:outline-none focus:shadow-outline" 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ $email }}" 
                        required 
                        readonly
                    >
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-800 mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                        id="password" 
                        type="text" 
                        name="password" 
                        value="{{ $password }}" 
                        required 
                        readonly
                    >
                    <p class="text-xs italic">Password is visible for testing purposes only.</p>
                </div>
                
                <div class="flex items-center mb-4">
                    <input class="mr-2" type="checkbox" id="remember" name="remember">
                    <label for="remember" class="text-sm">
                        Remember me
                    </label>
                </div>
                
                <div class="flex items-center justify-between">
                    <button 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                        type="submit"
                    >
                        Sign In
                    </button>
                    <a href="/" class="text-blue-300 text-sm hover:underline">
                        Back to main login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
