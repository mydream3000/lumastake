<header class="fixed top-0 left-0 right-0 w-full md:border md:border-arbitex-accent z-40 bg-arbitex-dark" id="header">
    <div class="max-w-7xl mx-auto">
        <!-- Desktop Header -->
        <div class="hidden md:block my-2">
            <div class="navigation-pc bg-arbitex-dark rounded-lg p-4 flex justify-between items-center transition-all duration-300">
                <div class="flex items-center">
                    <a class="logo" href="{{ route('home') }}">
                        <img src="{{ asset('assets/6a0b72f45b3813e4f0d5d2588a45414aca681d7a.png') }}" alt="Arbitex Logo" class="h-8 w-auto md:h-12">
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="flex items-center space-x-8 text-neue-haas text-lg">
                    <a href="{{ route('about') }}" class="hover:text-arbitex-accent transition-colors">About Us</a>
                    <a href="{{ route('profit-tiers') }}" class="hover:text-arbitex-accent transition-colors">Profit Tiers</a>
                    <a href="{{ route('blog') }}" class="hover:text-arbitex-accent transition-colors">Blog</a>
                    <a href="{{ route('contact') }}" class="hover:text-arbitex-accent transition-colors">Contact Us</a>
                    <a href="{{ route('faq') }}" class="hover:text-arbitex-accent transition-colors">FAQ's</a>
                </nav>

                <!-- Desktop Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="bg-arbitex-orange text-white px-6 py-2 rounded text-sm md:text-base hover:bg-orange-600 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="border border-arbitex-orange text-white px-6 py-2 rounded text-sm md:text-base hover:bg-arbitex-orange transition-colors">Register</a>
                </div>
            </div>
        </div>

        <!-- Mobile Header -->
        <div class="md:hidden mx-1 ">
            <div class="bg-[rgba(217,217,217,0.06)] border border-arbitex-green rounded-lg p-3 flex justify-between items-center">
                <div class="flex items-center">
                    <a class="logo logo-mob" href="{{ route('home') }}">
                        <img src="{{ asset('assets/6a0b72f45b3813e4f0d5d2588a45414aca681d7a.png') }}" alt="Arbitex Logo" class="h-7 w-auto">
                    </a>
                </div>

                <!-- Mobile Login/Register Buttons -->
                <div class="flex items-center space-x-4 text-xs font-neue-haas">
                    <a href="{{ route('login') }}" class="bg-[#FF451C] text-white px-3 py-1 rounded-sm hover:bg-orange-600 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="border border-[#FF451C] text-white px-3 py-1 rounded-sm hover:bg-[#FF451C] transition-colors">Register</a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="text-white p-1">
                    <img src="{{ asset('assets/mobile_open.svg') }}" alt="Menu" class="w-6 h-6">
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Slide-in Menu (Outside sticky header for proper fixed positioning) -->
<div id="mobile-menu" class="fixed inset-0 z-50 md:hidden transform translate-x-full transition-transform duration-300 ease-in-out">
        <div class="bg-arbitex-dark w-full p-3 shadow-lg">
            <!-- Menu Header -->
            <div class="flex justify-between items-center p-8 pb-4">
                <a class="logo logo-mob" href="{{ route('home') }}">
                    <img src="{{ asset('assets/6a0b72f45b3813e4f0d5d2588a45414aca681d7a.png') }}" alt="Arbitex Logo" class="h-7 w-auto">
                </a>
                <button id="mobile-menu-close" class="text-white p-2 hover:bg-gray-800 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Menu Navigation -->
            <nav class="px-4 pt-8">
                <div class="space-y-6">
                    <div class="border-b border-gray-600 pb-3">
                        <a href="{{ route('home') }}" class="block text-white text-lg font-poppins hover:text-arbitex-accent transition-colors">Home</a>
                    </div>
                    <div class="border-b border-gray-600 pb-3">
                        <a href="{{ route('about') }}" class="block text-white text-lg font-poppins hover:text-arbitex-accent transition-colors">About Us</a>
                    </div>
                    <div class="border-b border-gray-600 pb-3">
                        <a href="{{ route('profit-tiers') }}" class="block text-white text-lg font-poppins hover:text-arbitex-accent transition-colors">Profit Tiers</a>
                    </div>
                    <div class="border-b border-gray-600 pb-3">
                        <a href="{{ route('blog') }}" class="block text-white text-lg font-poppins hover:text-arbitex-accent transition-colors">Blog</a>
                    </div>
                    <div class="border-b border-gray-600 pb-3">
                        <a href="{{ route('contact') }}" class="block text-white text-lg font-poppins hover:text-arbitex-accent transition-colors">Contact Us</a>
                    </div>
                    <div class="border-b border-gray-600 pb-3">
                        <a href="{{ route('faq') }}" class="block text-white text-lg font-poppins hover:text-arbitex-accent transition-colors">FAQ'S</a>
                    </div>
                </div>
            </nav>

            <!-- Login/Register Buttons -->
            <div class="px-4 pt-6">
                <div class="flex space-x-4">
                    <a href="{{ route('login') }}" class="bg-arbitex-orange text-white px-4 py-2 rounded-sm text-xs font-neue-haas hover:bg-orange-600 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="border border-arbitex-orange text-white px-4 py-2 rounded-sm text-xs font-neue-haas hover:bg-arbitex-orange transition-colors">Register</a>
                </div>
            </div>

            <!-- Social Media Icons -->
            <div class="bottom-20 px-8 left-8">
                <div class="flex mt-3 space-x-1">
                    @php
                        $mobileIcons = [
                            'Instagram' => 'assets/insta-mob.svg',
                            'Facebook' => 'assets/facebook-mob.svg',
                            'Twitter' => 'assets/twiter-mob.svg',
                            'TikTok' => 'assets/tik-tok-mob.svg'
                        ];

                        $linksByPlatformMobile = [];
                        foreach($socialLinks ?? [] as $link) {
                            $linksByPlatformMobile[$link->platform] = $link->url;
                        }
                    @endphp

                    @foreach($mobileIcons as $platform => $icon)
                        @php
                            $url = $linksByPlatformMobile[$platform] ?? '#';
                            $hasLink = isset($linksByPlatformMobile[$platform]);
                        @endphp
                        <a href="{{ $url }}"
                           @if($hasLink) target="_blank" rel="noopener noreferrer" @endif
                           class="block w-8 h-8 rounded {{ !$hasLink ? 'opacity-50' : '' }}">
                            <img src="{{ asset($icon) }}" alt="{{ $platform }}" class="w-full h-full object-cover">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
</div>

<script>
// Mobile menu slide-in functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const mobileMenu = document.getElementById('mobile-menu');
    let isMenuOpen = false;

    function openMenu() {
        if (!isMenuOpen) {
            // Slide in from right
            mobileMenu.classList.remove('translate-x-full');
            mobileMenu.classList.add('translate-x-0');
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
            isMenuOpen = true;
        }
    }

    function closeMenu() {
        if (isMenuOpen) {
            // Slide out to right
            mobileMenu.classList.remove('translate-x-0');
            mobileMenu.classList.add('translate-x-full');
            // Restore body scroll
            document.body.style.overflow = '';
            isMenuOpen = false;
        }
    }

    // Open menu when hamburger button is clicked
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            openMenu();
        });
    }

    // Close menu when close button is clicked
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', function(e) {
            e.stopPropagation();
            closeMenu();
        });
    }

    // Close menu when clicking on navigation links
    const navLinks = mobileMenu.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            closeMenu();
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (isMenuOpen && !mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
            closeMenu();
        }
    });

    // Close menu on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && isMenuOpen) {
            closeMenu();
        }
    });
});
</script>
