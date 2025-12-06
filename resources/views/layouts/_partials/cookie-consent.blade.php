{{-- Cookie Consent Banner --}}
<div x-data="cookieConsent()"
     x-show="showBanner"
     x-cloak
     class="cookies-accepts fixed bottom-0 left-0 right-0 z-[9999] bg-arbitex-dark/95 backdrop-blur-sm border-t border-gray-700 shadow-2xl"
     style="display: none;">

    <div class="container mx-auto px-4 py-4 md:py-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

            {{-- Content --}}
            <div class="flex-1 pr-0 md:pr-8">
                <div class="flex items-start gap-3">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-arbitex-orange" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 9a1 1 0 112 0v4a1 1 0 11-2 0V9zm1-5a1 1 0 100 2 1 1 0 000-2z"/>
                        </svg>
                    </div>

                    {{-- Text --}}
                    <div class="flex-1">
                        <h3 class="text-white font-semibold text-sm md:text-base mb-1">We Use Cookies</h3>
                        <p class="text-gray-300 text-xs md:text-sm leading-relaxed">
                            We use cookies and similar technologies to provide, secure, and improve our services.
                            By clicking "Accept All", you consent to our use of cookies.
                            You can manage your preferences or learn more in our
                            <a href="{{ route('privacy') }}" class="text-arbitex-green hover:text-arbitex-green/80 underline font-medium" target="_blank" rel="noopener">Privacy Policy</a> and
                            <a href="{{ route('terms') }}" class="text-arbitex-green hover:text-arbitex-green/80 underline font-medium" target="_blank" rel="noopener">Terms of Service</a>.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-row md:flex-row items-center gap-2 md:gap-3 w-full md:w-auto">
                {{-- Decline Button --}}
                <button @click="declineCookies"
                        class="flex-1 md:flex-none px-4 md:px-6 py-2 md:py-2.5 text-xs md:text-sm font-medium text-white bg-gray-700 hover:bg-gray-600 rounded-md transition-colors duration-200 whitespace-nowrap">
                    Decline
                </button>

                {{-- Accept Button --}}
                <button @click="acceptCookies"
                        class="flex-1 md:flex-none px-4 md:px-6 py-2 md:py-2.5 text-xs md:text-sm font-semibold text-white bg-arbitex-green hover:bg-arbitex-green/90 rounded-md transition-colors duration-200 whitespace-nowrap">
                    Accept All
                </button>

                {{-- Close Button --}}
                <button @click="closeBanner"
                        class="flex-shrink-0 p-2 text-gray-400 hover:text-white transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>
</div>

<script>
function cookieConsent() {
    return {
        showBanner: false,

        init() {
            // Check if user has already made a choice
            const consent = this.getCookie('cookie_consent');

            if (!consent) {
                // Show banner after a short delay for better UX
                setTimeout(() => {
                    this.showBanner = true;
                }, 1000);
            } else {
                // Apply user's previous choice
                this.applyConsent(consent === 'accepted');
            }
        },

        acceptCookies() {
            this.setCookie('cookie_consent', 'accepted', 365);
            this.applyConsent(true);
            this.showBanner = false;
        },

        declineCookies() {
            this.setCookie('cookie_consent', 'declined', 365);
            this.applyConsent(false);
            this.showBanner = false;
        },

        closeBanner() {
            // Just hide the banner temporarily (will show again on next visit)
            this.showBanner = false;
        },

        applyConsent(accepted) {
            if (accepted) {
                // Enable analytics and tracking cookies here
                console.log('Cookies accepted - analytics enabled');

                // Example: Enable Google Analytics
                // window.gtag && window.gtag('consent', 'update', {
                //     'analytics_storage': 'granted'
                // });
            } else {
                // Disable analytics and tracking cookies here
                console.log('Cookies declined - analytics disabled');

                // Example: Disable Google Analytics
                // window.gtag && window.gtag('consent', 'update', {
                //     'analytics_storage': 'denied'
                // });
            }
        },

        setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
        },

        getCookie(name) {
            const nameEQ = name + '=';
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
