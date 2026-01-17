<footer class="bg-lumastake-light-blue py-24">
    <div class="max-w-7xl mx-auto px-4 md:px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <a href="{{ route('home') }}" class="inline-block mb-10">
                    <img src="{{ asset('img/home/logo-final.png') }}" alt="LumaStake Logo" class="h-12 w-auto">
                </a>
                <p class="text-lumastake-navy text-3xl font-medium leading-tight max-w-xl">
                    Your future shouldn't depend on market luck. With LumaStake, <span class="font-bold">you earn passively</span>, <span class="font-bold">stake confidently</span>, and <span class="font-bold">sleep peacefully</span>.
                </p>
            </div>
            <div class="flex flex-col items-center lg:items-end">
                <div class="flex flex-col space-y-5 w-full max-w-sm">
                    <a href="{{ route('register') }}" class="border-2 border-lumastake-navy text-lumastake-navy text-center py-4 rounded-xl text-2xl font-bold hover:bg-lumastake-navy hover:text-white transition-all">Get Started Now</a>
                    <a href="{{ route('profit-tiers') }}" class="border-2 border-lumastake-navy text-lumastake-navy text-center py-4 rounded-xl text-2xl font-bold hover:bg-lumastake-navy hover:text-white transition-all">Explore Plans</a>
                    <a href="{{ route('contact') }}" class="border-2 border-lumastake-navy text-lumastake-navy text-center py-4 rounded-xl text-2xl font-bold hover:bg-lumastake-navy hover:text-white transition-all">Contact Support</a>
                </div>
            </div>
        </div>

        <div class="mt-24 pt-10 border-t border-blue-200 flex flex-col md:flex-row justify-between items-center">
            <p class="text-lumastake-navy text-xl mb-8 md:mb-0 font-medium">© 2026 All rights reserved.</p>

            <div class="flex space-x-8">
                <a href="#" class="text-lumastake-navy hover:text-lumastake-blue transition-all transform hover:scale-110">
                    <img src="{{ asset('img/instagram.svg') }}" alt="Instagram" class="w-10 h-10">
                </a>
                <a href="#" class="text-lumastake-navy hover:text-lumastake-blue transition-all transform hover:scale-110">
                    <img src="{{ asset('img/facebook.svg') }}" alt="Facebook" class="w-10 h-10">
                </a>
                <a href="#" class="text-lumastake-navy hover:text-lumastake-blue transition-all transform hover:scale-110">
                    <img src="{{ asset('img/xlink.svg') }}" alt="Twitter" class="w-10 h-10">
                </a>
                <a href="#" class="text-lumastake-navy hover:text-lumastake-blue transition-all transform hover:scale-110">
                    <img src="{{ asset('img/tiktok.svg') }}" alt="TikTok" class="w-10 h-10">
                </a>
            </div>
        </div>
    </div>
</footer>

