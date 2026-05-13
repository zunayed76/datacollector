<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | DataCollector</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-vector {
            background-image: url("{{ asset('images/bckground.svg') }}");
            background-size: cover;
            background-position: center;
        }
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 leading-normal antialiased">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0">
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        DataCollector
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium transition">Log in</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            Get Started
                        </a>
                    @endguest

                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium transition">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="border border-gray-300 px-6 py-2.5 rounded-full font-semibold hover:bg-gray-100 transition">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative min-h-screen flex items-center bg-vector pt-20">
        <!-- Optional overlay to make text pop -->
        <div class="absolute inset-0 bg-white/40"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center lg:text-left grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <!-- <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-gray-900 leading-tight">
                    Manage Your Data, <br>
                    <span class="text-blue-600 font-bold">Effortlessly.</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    Streamline collection, insights, and collaboration in one beautiful platform designed for modern teams.
                </p> -->
                
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:bg-blue-700 transition shadow-xl shadow-blue-200">
                            Register
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:bg-blue-700 transition shadow-xl shadow-blue-200">
                            Back to Dashboard
                        </a>
                    @endguest
              
                </div>
            </div>

            <!-- Optional: You can put a screenshot or another illustration here -->
            <!-- <div class="hidden lg:block relative">
                 <div class="absolute -inset-4 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-3xl opacity-20 blur-2xl"></div>
                 <img src="https://via.placeholder.com/600x400" alt="App Preview" class="relative rounded-2xl shadow-2xl border border-white/50">
            </div> -->
        </div>
    </header>

</body>
</html>