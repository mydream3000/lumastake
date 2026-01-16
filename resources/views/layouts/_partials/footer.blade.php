<footer class="bg-[#E0F2FF] py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <a href="{{ route('home') }}" class="inline-block mb-8">
                    <img src="{{ asset('img/logo.png') }}" alt="LumaStake Logo" class="h-10 w-auto">
                </a>
                <p class="text-[#262262] text-3xl font-medium leading-tight max-w-xl">
                    Your future shouldn't depend on market luck. With LumaStake, <span class="font-bold">you earn passively</span>, <span class="font-bold">stake confidently</span>, and <span class="font-bold">sleep peacefully</span>.
                </p>
            </div>
            <div class="flex flex-col items-center lg:items-end">
                <div class="flex flex-col space-y-4 w-full max-w-xs">
                    <a href="{{ route('register') }}" class="border-2 border-[#262262] text-[#262262] text-center py-3 rounded-lg text-xl font-bold hover:bg-[#262262] hover:text-white transition-all">Get Started Now</a>
                    <a href="{{ route('profit-tiers') }}" class="border-2 border-[#262262] text-[#262262] text-center py-3 rounded-lg text-xl font-bold hover:bg-[#262262] hover:text-white transition-all">Explore Plans</a>
                    <a href="{{ route('contact') }}" class="border-2 border-[#262262] text-[#262262] text-center py-3 rounded-lg text-xl font-bold hover:bg-[#262262] hover:text-white transition-all">Contact Support</a>
                </div>
            </div>
        </div>

        <div class="mt-20 pt-8 border-t border-blue-200 flex flex-col md:flex-row justify-between items-center">
            <p class="text-[#262262] text-lg mb-6 md:mb-0">В© 2025 All rights reserved.</p>

            <div class="flex space-x-6">
                <a href="#" class="text-[#262262] hover:text-blue-600 transition-colors">
                    <img src="{{ asset('img/instagram.svg') }}" alt="Instagram" class="w-8 h-8">
                </a>
                <a href="#" class="text-[#262262] hover:text-blue-600 transition-colors">
                    <img src="{{ asset('img/facebook.svg') }}" alt="Facebook" class="w-8 h-8">
                </a>
                <a href="#" class="text-[#262262] hover:text-blue-600 transition-colors">
                    <img src="{{ asset('img/xlink.svg') }}" alt="Twitter" class="w-8 h-8">
                </a>
                <a href="#" class="text-[#262262] hover:text-blue-600 transition-colors">
                    <img src="{{ asset('img/tiktok.svg') }}" alt="TikTok" class="w-8 h-8">
                </a>
            </div>
        </div>
    </div>
</footer>

