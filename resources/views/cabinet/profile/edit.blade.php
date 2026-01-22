<x-cabinet-layout :showTopBar="false">
    <div class="flex flex-col items-center px-4">
        {{-- Header --}}
        <div class="w-full bg-[rgba(217,217,217,0.3)] rounded-t-[9px] px-4 py-3 md:py-4 xl:py-5 mb-4 md:mb-5 xl:mb-6">
            <div class="flex items-center gap-2 md:gap-3 xl:gap-4 max-w-[1030px] mx-auto">
                <a href="{{ route('cabinet.profile.show') }}" class="text-[#222222] hover:text-gray-600">
                    <svg class="w-6 h-4 md:w-8 md:h-5 xl:w-10 xl:h-6" fill="currentColor" viewBox="0 0 39 25" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.939339 10.9393C0.353554 11.5251 0.353554 12.4749 0.939339 13.0607L10.4853 22.6066C11.0711 23.1924 12.0208 23.1924 12.6066 22.6066C13.1924 22.0208 13.1924 21.0711 12.6066 20.4853L4.12132 12L12.6066 3.51472C13.1924 2.92893 13.1924 1.97919 12.6066 1.3934C12.0208 0.807611 11.0711 0.807611 10.4853 1.3934L0.939339 10.9393ZM39 10.5L2 10.5V13.5L39 13.5V10.5Z"/>
                    </svg>
                </a>
                <h1 class="font-poppins font-semibold text-lg md:text-xl xl:text-2xl text-[#222222]">Edit Profile</h1>
            </div>
        </div>

        <form action="{{ route('cabinet.profile.update') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-[1030px]">
            @csrf
            @method('PUT')

            {{-- Avatar Section --}}
            <div class="flex flex-col items-center mb-6 md:mb-8 xl:mb-10">
                <div class="relative mb-4 md:mb-6 xl:mb-8">
                    <img
                        src="{{ $user->avatar_url }}"
                        alt="Avatar"
                        id="avatar-preview"
                        class="w-32 h-32 md:w-44 md:h-44 xl:w-[211px] xl:h-[211px] rounded-full object-cover shadow-[0px_0px_10px_rgba(69,69,69,0.15)]"
                    >
                    <label for="avatar-upload" class="absolute bottom-0 right-0 w-10 h-10 md:w-12 md:h-12 xl:w-[46.25px] xl:h-[46.25px] bg-white rounded-full border border-[#CCCCCC] flex items-center justify-center cursor-pointer hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-cabinet-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </label>
                    <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/jpeg,image/png,image/gif,image/heic,image/heif,.jpg,.jpeg,.png,.gif,.heic,.heif">
                </div>
                @error('avatar')
                    <p class="text-cabinet-red text-base mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Form Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 md:gap-x-8 xl:gap-x-[51px] gap-y-4 md:gap-y-6 xl:gap-y-[39px] mb-8 md:mb-10 xl:mb-12">
                {{-- Full Name --}}
                <x-cabinet.input
                    label="Full Name"
                    name="name"
                    :value="$user->name"
                    required
                />

                {{-- Email --}}
                <x-cabinet.input
                    type="email"
                    label="Email"
                    name="email"
                    :value="$user->email"
                    required
                />

                {{-- Gender --}}
                <x-cabinet.select
                    label="Gender"
                    name="gender"
                    :value="$user->gender"
                    placeholder="Select gender"
                >
                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                </x-cabinet.select>

                {{-- Account Type --}}
                <div class="col-span-1">
                    <label class="block font-poppins font-normal text-base md:text-lg xl:text-[28px] text-[#CCCCCC] mb-1 md:mb-2">Account Type</label>
                    <input type="text" value="{{ ucfirst($user->account_type) }}" disabled
                           class="w-full py-2 md:py-3 xl:py-4 px-3 md:px-4 rounded-md bg-[#EFEFEF] border border-[rgba(68,68,68,0.3)] text-gray-500 font-poppins text-base md:text-base xl:text-lg cursor-not-allowed">
                </div>

                {{-- Phone Number (with country dial code selector) --}}
                <div class="col-span-1">
                    <label class="block font-poppins font-normal text-base md:text-lg xl:text-[28px] text-[#CCCCCC] mb-1 md:mb-2">Phone Number</label>
                    <div class="relative" x-data="phoneInputProfile()" data-dial-code="{{ $user->country_code }}" data-phone="{{ $user->phone }}" data-phone-country="{{ $user->phone_country }}">
                        <div class="flex items-stretch gap-0">
                            <!-- Country selector -->
                            <div class="relative w-30 shrink-0">
                                <button type="button" @click="open = !open"
                                        class="py-2 md:py-3 xl:py-4 w-30 px-3 md:px-4 rounded-l-md bg-[#F8F8F8] border border-[rgba(68,68,68,0.6)] border-r-0 text-gray-600 focus:border-cabinet-green focus:outline-none font-poppins text-base md:text-base xl:text-lg flex items-center justify-between transition">
                                    <div class="flex items-center gap-2">
                                        <span :class="selectedCountry.flag_class" class="text-base"></span>
                                        <span x-text="selectedCountry.phone_code"></span>
                                    </div>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <!-- Dropdown -->
                                <div x-show="open" @click.away="open = false" x-cloak
                                     class="absolute z-50 mt-1 w-[22rem] max-w-[calc(100vw-2rem)] bg-white border border-[rgba(68,68,68,0.6)] rounded-md shadow-lg max-h-80 overflow-y-auto">
                                    <template x-for="country in countries" :key="country.code">
                                        <button type="button" @click="selectCountry(country)" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-3 font-poppins">
                                            <span :class="country.flag_class" class="text-base"></span>
                                            <span class="font-medium" x-text="country.phone_code">+1</span>
                                            <span class="text-gray-600" x-text="country.name">United States</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <!-- Phone input (national number only) -->
                            <input type="text" x-model="phone" name="phone" inputmode="numeric" pattern="[0-9]*"
                                   class="py-2 md:py-3 xl:py-4 min-w-0 flex-1 px-3 md:px-4 rounded-r-md bg-[#F8F8F8] border border-[rgba(68,68,68,0.6)] border-l-0 text-gray-600 placeholder-gray-400 focus:border-cabinet-green focus:outline-none font-poppins text-base md:text-base xl:text-lg transition"
                                   placeholder="Phone number" />
                        </div>
                        <input type="hidden" name="country_code" x-model="selectedCountry.phone_code">
                        <input type="hidden" name="phone_country" x-model="selectedCountry.code">
                    </div>
                    @error('phone')
                        <p class="text-cabinet-red text-base mt-1">{{ $message }}</p>
                    @enderror
                    @error('country_code')
                        <p class="text-cabinet-red text-base mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Country --}}
                <div class="col-span-1" x-data="countrySelector()" data-country="{{ $user->country }}">
                    <label class="block font-poppins font-normal text-base md:text-lg xl:text-[28px] text-[#CCCCCC] mb-1 md:mb-2">Country</label>
                    <div class="relative">
                        <button type="button" @click="open = !open"
                                class="w-full bg-[#F8F8F8] border border-[rgba(68,68,68,0.6)] rounded-md px-3 py-2 md:px-4 md:py-3 xl:py-4 font-poppins text-sm md:text-base xl:text-lg flex items-center justify-between focus:outline-none focus:border-cabinet-green transition">
                            <div class="flex items-center gap-3">
                                <span :class="selected.flag_class" class="text-base"></span>
                                <span x-text="selected.name"></span>
                            </div>
                            <svg class="w-5 h-5 text-cabinet-orange transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute z-50 mt-1 w-full bg-white border border-[rgba(68,68,68,0.6)] rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="c in countries" :key="c.code">
                                <button type="button" @click="select(c)"
                                        class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-3 font-poppins transition-colors">
                                    <span :class="c.flag_class" class="text-base"></span>
                                    <span x-text="c.name"></span>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="country" :value="selected.code">
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="w-full max-w-[320px] md:max-w-[400px] xl:max-w-[453px] mx-auto mb-8 md:mb-10 xl:mb-12">
                <x-cabinet.form-button color="orange">
                    Save Profile
                </x-cabinet.form-button>
            </div>
        </form>

        {{-- Change Password Section --}}
        <form action="{{ route('cabinet.profile.change-password') }}" method="POST" class="w-full max-w-[1030px] mt-8 md:mt-10 xl:mt-12 border-t border-[rgba(217,217,217,0.5)] pt-8 md:pt-10 xl:pt-12">
            @csrf

            <h2 class="font-poppins font-semibold text-lg md:text-xl xl:text-2xl text-[#222222] mb-6 md:mb-8 xl:mb-10">Change Password</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 md:gap-x-8 xl:gap-x-[51px] gap-y-4 md:gap-y-6 xl:gap-y-[39px] mb-8 md:mb-10 xl:mb-12">
                {{-- Current Password --}}
                <x-cabinet.input
                    type="password"
                    label="Current Password"
                    name="current_password"
                    placeholder="Enter current password"
                    required
                />

                {{-- New Password --}}
                <x-cabinet.input
                    type="password"
                    label="New Password"
                    name="new_password"
                    placeholder="Enter new password"
                    required
                />

                {{-- Confirm New Password --}}
                <x-cabinet.input
                    type="password"
                    label="Confirm New Password"
                    name="new_password_confirmation"
                    placeholder="Confirm new password"
                    class="md:col-span-2"
                    required
                />
            </div>

            {{-- Submit Button --}}
            <div class="w-full max-w-[320px] md:max-w-[400px] xl:max-w-[453px] mx-auto mb-12 md:mb-16 xl:mb-20">
                <x-cabinet.form-button color="orange">
                    Change Password
                </x-cabinet.form-button>
            </div>
        </form>
    </div>

    <!-- Email Verification Modal -->
    @if(session('email_verification_required'))
    <div x-data="{
        open: true,
        code: '',
        loading: false,
        resendLoading: false,
        verified: false,
        statusMessage: '',

        async verifyCode() {
            this.loading = true;
            this.statusMessage = '';

            try {
                const response = await fetch('{{ route('cabinet.profile.verify-email-change') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: this.code })
                });

                const data = await response.json();

                if (response.ok) {
                    this.verified = true;
                    this.statusMessage = '✓ Code verified successfully!';
                    setTimeout(() => {
                        window.location.href = '{{ route('cabinet.profile.show') }}';
                    }, 1500);
                } else {
                    this.statusMessage = data.message || 'Invalid code';
                }
            } catch (error) {
                this.statusMessage = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        async resendCode() {
            this.resendLoading = true;
            this.statusMessage = '';

            try {
                const response = await fetch('{{ route('cabinet.profile.resend-email-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    window.showToast(data.message, 'success');
                } else {
                    window.showToast(data.message || 'Failed to resend code', 'error');
                }
            } catch (error) {
                window.showToast('An error occurred. Please try again.', 'error');
            } finally {
                this.resendLoading = false;
            }
        }
    }"
         x-show="open"
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="open = false"></div>

        <!-- Modal -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.away="open = false">
                <!-- Close button -->
                <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Title -->
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Verify Email Change</h3>
                <p class="text-base text-gray-600 mb-6">
                    We sent a 6-digit verification code to your <strong>new email</strong>. Please enter it below.
                </p>

                <!-- Code Input -->
                <div class="mb-4">
                    <label class="block text-base font-medium text-gray-700 mb-2">Verification Code</label>
                    <div class="relative">
                        <input
                            type="text"
                            x-model="code"
                            maxlength="6"
                            pattern="[0-9]*"
                            inputmode="numeric"
                            placeholder="Enter 6-digit code"
                            class="w-full px-4 py-3 pr-24 border border-gray-300 rounded-lg text-center text-2xl tracking-widest focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                            @input="code = code.replace(/[^0-9]/g, '')"
                        >
                        <button
                            type="button"
                            @click="async () => {
                                try {
                                    const text = await navigator.clipboard.readText();
                                    const digits = text.replace(/[^0-9]/g, '').slice(0, 6);
                                    if (digits.length === 6) {
                                        code = digits;
                                        window.dispatchEvent(new CustomEvent('show-toast', {
                                            detail: { message: 'Code inserted from clipboard', type: 'success' }
                                        }));
                                    } else {
                                        window.dispatchEvent(new CustomEvent('show-toast', {
                                            detail: { message: 'Invalid code format in clipboard', type: 'error' }
                                        }));
                                    }
                                } catch (err) {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { message: 'Failed to read clipboard', type: 'error' }
                                    }));
                                }
                            }"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-cabinet-green hover:bg-cabinet-green/90 text-white text-xs font-semibold rounded transition-colors"
                        >
                            Paste
                        </button>
                    </div>
                </div>

                <!-- Status Message -->
                <p x-show="statusMessage" x-text="statusMessage" :class="verified ? 'text-green-600' : 'text-red-600'" class="text-base mb-4"></p>

                <!-- Buttons -->
                <div class="flex gap-3 mb-4">
                    <button
                        @click="verifyCode()"
                        :disabled="code.length !== 6 || loading"
                        class="flex-1 bg-cabinet-orange text-white px-4 py-3 rounded-lg hover:bg-cabinet-orange/90 disabled:opacity-50 disabled:cursor-not-allowed transition font-semibold">
                        <span x-show="!loading">Verify Code</span>
                        <span x-show="loading">Verifying...</span>
                    </button>
                </div>

                <button
                    @click="resendCode()"
                    :disabled="resendLoading"
                    class="w-full text-cabinet-orange hover:underline text-base font-medium">
                    <span x-show="!resendLoading">Resend Code</span>
                    <span x-show="resendLoading">Sending...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        // Avatar preview with validation
        document.getElementById('avatar-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file size (2.5 MB = 2560 KB = 2,621,440 bytes)
            const maxSize = 2560 * 1024;
            if (file.size > maxSize) {
                window.showToast('File size must not exceed 2.5 MB', 'error');
                e.target.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/heic', 'image/heif'];
            const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.heic', '.heif'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                window.showToast('Only JPG, PNG, GIF, and HEIC images are allowed', 'error');
                e.target.value = '';
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        });
    </script>
    @endpush
</x-cabinet-layout>
