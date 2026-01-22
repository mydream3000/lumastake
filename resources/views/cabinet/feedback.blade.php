<x-cabinet-layout>
    <div class="max-w-3xl mx-auto px-4 py-6">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('cabinet.dashboard') }}" class="text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="font-poppins font-semibold text-xl text-[#222222]">Contact Us</h1>
        </div>

        {{-- Contact Form --}}
        <form action="{{ route('cabinet.contact.store') }}" method="POST" class="space-y-5" x-data="contactForm()">
            @csrf

            {{-- Row 1: First Name + Last Name --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <input
                        type="text"
                        name="first_name"
                        x-model="form.first_name"
                        placeholder="First Name"
                        class="w-full px-4 py-3 bg-[#FFFDE7] border border-[#E0E0E0] rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:border-cabinet-green transition font-poppins"
                        required
                    >
                </div>
                <div>
                    <input
                        type="text"
                        name="last_name"
                        x-model="form.last_name"
                        placeholder="Last Name"
                        class="w-full px-4 py-3 bg-[#FFFDE7] border border-[#E0E0E0] rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:border-cabinet-green transition font-poppins"
                        required
                    >
                </div>
            </div>

            {{-- Row 2: Email + Phone --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Email --}}
                <div>
                    <input
                        type="email"
                        name="email"
                        x-model="form.email"
                        placeholder="Email"
                        class="w-full px-4 py-3 bg-[#FFFDE7] border border-[#E0E0E0] rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:border-cabinet-green transition font-poppins"
                        required
                    >
                </div>

                {{-- Phone with country code --}}
                <div x-data="phoneInput()">
                    <div class="flex w-full items-stretch rounded-md border border-[#E0E0E0] bg-[#FFFDE7] overflow-hidden focus-within:border-cabinet-green">
                        <button
                            type="button"
                            @click="open = !open"
                            class="px-3 py-3 bg-[#FFFDE7] text-gray-700 flex items-center gap-2 select-none hover:bg-[#FFF9C4] transition border-r border-[#E0E0E0]"
                        >
                            <span :class="selectedCountry.flag_class" class="text-base"></span>
                            <span class="text-sm font-medium" x-text="selectedCountry.phone_code"></span>
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <input
                            type="tel"
                            x-model="phone"
                            @input="formatPhone"
                            name="phone"
                            maxlength="19"
                            placeholder="Phone number"
                            class="px-3 py-3 flex-1 min-w-0 bg-transparent border-0 outline-none text-gray-900 placeholder-gray-400 font-poppins"
                        />
                    </div>

                    {{-- Country dropdown --}}
                    <div class="relative">
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-cloak
                            class="absolute z-50 mt-1 w-full bg-white border border-[#E0E0E0] rounded-md shadow-lg max-h-60 overflow-y-auto"
                        >
                            <template x-for="country in countries" :key="country.code">
                                <button
                                    type="button"
                                    @click="selectCountry(country)"
                                    class="w-full px-4 py-2.5 text-left hover:bg-gray-100 flex items-center gap-3 font-poppins transition"
                                >
                                    <span :class="country.flag_class" class="text-base"></span>
                                    <span class="font-medium text-sm" x-text="country.phone_code"></span>
                                    <span class="text-gray-600 text-sm" x-text="country.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <input type="hidden" name="country_code" x-model="selectedCountry.phone_code">
                    <input type="hidden" name="country" x-model="selectedCountry.code">
                </div>
            </div>

            {{-- Row 3: Message --}}
            <div>
                <textarea
                    name="message"
                    x-model="form.message"
                    placeholder="Message"
                    rows="5"
                    class="w-full px-4 py-3 bg-[#FFFDE7] border border-[#E0E0E0] rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:border-cabinet-green transition resize-none font-poppins"
                    required
                ></textarea>
            </div>

            {{-- Row 4: Email link (right aligned) --}}
            <div class="flex justify-end">
                <a href="mailto:lumastake@gmail.com" class="inline-flex items-center gap-2 text-gray-500 hover:text-cabinet-green transition text-sm font-poppins">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    lumastake@gmail.com
                </a>
            </div>

            {{-- Row 5: Submit button + Social icons --}}
            <div class="flex items-center justify-between gap-4 pt-2">
                <button
                    type="submit"
                    class="px-12 py-3 bg-[#3F51B5] text-white rounded-full text-base font-semibold hover:bg-[#303F9F] transition font-poppins"
                >
                    Submit
                </button>

                {{-- Social icons - blue with white icons --}}
                <div class="flex items-center gap-3">
                    <a href="#" class="w-9 h-9 bg-[#3F51B5] rounded-full flex items-center justify-center text-white hover:bg-[#303F9F] transition">
                        {{-- Instagram --}}
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-[#3F51B5] rounded-full flex items-center justify-center text-white hover:bg-[#303F9F] transition">
                        {{-- Facebook --}}
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-[#3F51B5] rounded-full flex items-center justify-center text-white hover:bg-[#303F9F] transition">
                        {{-- X (Twitter) --}}
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Success Modal --}}
    <div
        x-data="{ show: {{ session('show_confirmation_modal') ? 'true' : 'false' }} }"
        x-show="show"
        x-init="if (show) { setTimeout(() => { show = false }, 10000); }"
        @keydown.escape.window="show = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        x-cloak
    >
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/75"
            @click="show = false"
        ></div>

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block bg-white rounded-2xl px-6 pt-6 pb-6 text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-md sm:w-full"
                @click.stop
            >
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Message Sent!</h3>
                <p class="text-center text-gray-600 text-sm mb-4">
                    Thank you for reaching out. We'll get back to you within 24-48 hours.
                </p>

                <button
                    @click="show = false"
                    type="button"
                    class="w-full px-4 py-2.5 bg-[#3F51B5] text-white text-sm font-semibold rounded-lg hover:bg-[#303F9F] transition"
                >
                    Close
                </button>

                <button
                    @click="show = false"
                    type="button"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function contactForm() {
            return {
                loading: false,
                form: {
                    first_name: '',
                    last_name: '',
                    email: '{{ auth()->user()->email }}',
                    phone: '',
                    message: ''
                }
            }
        }

        @if(session('success'))
            window.addEventListener('DOMContentLoaded', () => {
                showToast('{{ session('success') }}', 'success');
            });
        @endif

        @if(session('error'))
            window.addEventListener('DOMContentLoaded', () => {
                showToast('{{ session('error') }}', 'error');
            });
        @endif
    </script>
    @endpush
</x-cabinet-layout>
