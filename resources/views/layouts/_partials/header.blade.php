<header class="fixed top-0 left-0 right-0 w-full z-50 bg-white/90 backdrop-blur-md" id="header">
    <div class="max-w-7xl mx-auto px-4 md:px-6">
        <!-- Desktop Header -->
        <div class="hidden md:flex justify-between items-center py-6">
            <div class="flex items-center">
                <a class="logo" href="{{ route('home') }}">
                    <img src="{{ asset('img/home/logo-final.png') }}" alt="LumaStake Logo" class="h-12 w-auto">
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="flex items-center space-x-10">
                <a href="{{ route('about') }}" class="text-lumastake-navy text-xl font-medium hover:text-lumastake-blue transition-colors">About Us</a>
                <a href="{{ route('profit-tiers') }}" class="text-lumastake-navy text-xl font-medium hover:text-lumastake-blue transition-colors">Profit Tiers</a>
                <a href="{{ route('blog') }}" class="text-lumastake-navy text-xl font-medium hover:text-lumastake-blue transition-colors">Blog</a>
                <a href="{{ route('contact') }}" class="text-lumastake-navy text-xl font-medium hover:text-lumastake-blue transition-colors">Contact Us</a>
                <a href="{{ route('faq') }}" class="text-lumastake-navy text-xl font-medium hover:text-lumastake-blue transition-colors">FAQ’s</a>
            </nav>

            <!-- Desktop Buttons -->
            <div class="flex items-center space-x-6">
                <a href="{{ route('login') }}" class="bg-lumastake-blue text-white px-8 py-2.5 rounded shadow-sm text-lg font-bold hover:bg-blue-700 transition-colors text-center min-w-[120px]">Login</a>
                <a href="{{ route('register') }}" class="border border-lumastake-navy text-lumastake-navy px-8 py-2.5 rounded text-lg font-medium hover:bg-gray-50 transition-colors text-center min-w-[120px]">Register</a>
            </div>
        </div>

        <!-- Mobile Header -->
        <div class="md:hidden flex justify-between items-center py-4">
            <a class="logo" href="{{ route('home') }}">
                <img src="{{ asset('img/home/logo-final.png') }}" alt="LumaStake Logo" class="h-10 w-auto">
            </a>

            <div class="flex items-center space-x-4">
                <button id="mobile-menu-button" class="text-lumastake-navy">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Slide-in Menu -->
<div id="mobile-menu" class="fixed inset-0 z-50 md:hidden transform translate-x-full transition-transform duration-300 ease-in-out">
    <div class="bg-white w-full h-full p-6 shadow-lg">
        <div class="flex justify-between items-center mb-10">
            <a href="{{ route('home') }}">
                <img src="{{ asset('img/home/logo-final.png') }}" alt="LumaStake Logo" class="h-10 w-auto">
            </a>
            <button id="mobile-menu-close" class="text-[#262262]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="space-y-6">
            <a href="{{ route('home') }}" class="block text-xl font-medium text-[#262262]">Home</a>
            <a href="{{ route('about') }}" class="block text-xl font-medium text-[#262262]">About Us</a>
            <a href="{{ route('profit-tiers') }}" class="block text-xl font-medium text-[#262262]">Profit Tiers</a>
            <a href="{{ route('blog') }}" class="block text-xl font-medium text-[#262262]">Blog</a>
            <a href="{{ route('contact') }}" class="block text-xl font-medium text-[#262262]">Contact Us</a>
            <a href="{{ route('faq') }}" class="block text-xl font-medium text-[#262262]">FAQ's</a>
        </nav>

        <div class="mt-10 flex flex-col space-y-4">
            <a href="{{ route('login') }}" class="bg-[#D9FF00] text-[#262262] text-center py-3 rounded font-bold">Login</a>
            <a href="{{ route('register') }}" class="border border-[#262262] text-[#262262] text-center py-3 rounded font-medium">Register</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('mobile-menu-button');
        const closeButton = document.getElementById('mobile-menu-close');
        const mobileMenu = document.getElementById('mobile-menu');

        if (menuButton && closeButton && mobileMenu) {
            menuButton.addEventListener('click', () => {
                mobileMenu.classList.remove('translate-x-full');
            });

            closeButton.addEventListener('click', () => {
                mobileMenu.classList.add('translate-x-full');
            });
        }
    });
</script>
