<x-cabinet-layout>
    <div class="flex flex-col items-center px-4">
        {{-- Header --}}
        <div class="w-full bg-[rgba(217,217,217,0.3)] rounded-t-[9px] px-4 py-3 md:py-4 xl:py-5 mb-4 md:mb-5 xl:mb-6">
            <div class="flex items-center justify-between max-w-[1030px] mx-auto">
                <h1 class="font-poppins font-semibold text-lg md:text-xl xl:text-2xl text-[#222222]">Profile</h1>
                <a href="{{ route('cabinet.profile.edit') }}" class="font-poppins font-semibold text-base md:text-lg xl:text-[22px] text-cabinet-green hover:underline">
                    Edit Profile
                </a>
            </div>
        </div>

        {{-- Avatar + Verification Section --}}
        <div class="flex flex-col items-center mb-6 md:mb-7 xl:mb-8">
            <div class="relative">
                <img
                    src="{{ $user->avatar_url }}"
                    alt="Avatar"
                    class="w-36 h-36 md:w-48 md:h-48 xl:w-[231px] xl:h-[231px] rounded-full object-cover shadow-[0px_0px_10px_rgba(69,69,69,0.15)]"
                >
                <button class="absolute bottom-0 right-0 w-11 h-11 md:w-12 md:h-12 xl:w-[50.63px] xl:h-[50.63px] bg-white rounded-full border border-[#CCCCCC] flex items-center justify-center hover:bg-gray-50 transition" title="Change avatar">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-cabinet-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>

            {{-- Verification status badge under avatar --}}
            <div class="mt-3">
                @php $v = $user->verification_status; @endphp
                @if($v === 'verified')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">verified user</span>
                @elseif($v === 'pending')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">verification pending</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">unverified user</span>
                @endif
            </div>

            {{-- Start verification button + SDK modal trigger --}}
            @if($v !== 'verified' && $v !== 'pending')
                <div class="mt-4 w-full flex flex-col items-center">
                    <button id="start-veriff" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md bg-cabinet-orange text-white font-medium hover:opacity-90 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5H4.75a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                        <span>Verify identity</span>
                    </button>
                </div>

                {{-- Veriff Modal --}}
                <x-cabinet.modal name="veriff-modal">
                    <div class="bg-white">
                        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-[#222222]">Identity verification</h3>
                            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="window.dispatchEvent(new CustomEvent('close-modal'))" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <div class="rounded-md border border-gray-200 p-3">
                                <div id="veriff-root" class="w-full"></div>
                            </div>
                        </div>
                    </div>
                </x-cabinet.modal>
            @endif
        </div>

        {{-- User Info --}}
        <div class="text-center mb-6 md:mb-8 xl:mb-10">
            <h2 class="font-poppins font-semibold text-xl md:text-2xl text-cabinet-orange mb-1">{{ $user->name }}</h2>
            @php
                $currentTier = \App\Models\Tier::find($user->current_tier);
            @endphp
            <p class="font-poppins font-medium text-base md:text-lg text-[#444444]">{{ $currentTier->name ?? 'Starter' }}</p>
        </div>

        {{-- Details Card --}}

        <div class="w-full max-w-[1030px] bg-[#F8F8F8] border border-[#DADADA] rounded-md p-6 md:p-8 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-1 gap-x-20 gap-y-6 md:gap-y-8">

                {{-- Email --}}
                <div class="flex items-center justify-between gap-4">
                    <span class="shrink-0 font-poppins text-sm md:text-base xl:text-xl text-[#444444]">Email:</span>

                    <div class="min-w-0 grow text-right">
                        <div class="inline-flex items-center justify-end gap-2 max-w-full">
                            <span class="font-poppins text-sm md:text-base xl:text-xl text-black truncate">{{ $user->email }}</span>

                            @if($user->google_id)
                                <span class="bg-green-700 w-5 h-5 rounded-full text-white text-[13px] flex items-center justify-center" title="Signed in with Google">✓</span>
                            @endif
                        </div>

                        @if(!$user->google_id)
                            @if($user->email_verified_at)
                                <span class="ml-auto mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.42 0l-3.25-3.25a1 1 0 111.42-1.42l2.54 2.54 6.54-6.54a1 1 0 011.42 0z" clip-rule="evenodd" />
              </svg>
              <span class="font-poppins font-medium text-[10px] text-green-700">Verified</span>
            </span>
                            @else
                                <form method="POST" action="{{ route('cabinet.profile.send-email-verification') }}" class="mt-1 inline-block">
                                    @csrf
                                    <button type="submit" class="font-poppins font-medium text-xs md:text-sm text-cabinet-green hover:underline">
                                        Verify Now
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Gender --}}
                <div class="flex items-center justify-between gap-4">
                    <span class="shrink-0 font-poppins text-sm md:text-base xl:text-xl text-[#444444]">Gender:</span>
                    <span class="min-w-0 grow text-right font-poppins text-sm md:text-base xl:text-xl text-black">
        {{ ucfirst($user->gender ?? 'Not specified') }}
      </span>
                </div>

                {{-- Account Type --}}
                <div class="flex items-center justify-between gap-4">
                    <span class="shrink-0 font-poppins text-sm md:text-base xl:text-xl text-[#444444]">Account Type:</span>
                    <span class="min-w-0 grow text-right font-poppins text-sm md:text-base xl:text-xl text-black">
                        {{ ucfirst($user->account_type) }}
                    </span>
                </div>

                {{-- Phone --}}
                <div class="flex items-center justify-between gap-4">
                    <span class="shrink-0 font-poppins text-sm md:text-base xl:text-xl text-[#444444]">Phone:</span>
                    <span class="min-w-0 grow text-right font-poppins text-sm md:text-base xl:text-xl text-black">
                        @php
                            $phone = trim((string) ($user->phone ?? ''));
                            $allCountries = \App\Helpers\GeoIpHelper::getAllCountries();
                            $phoneCountry = collect($allCountries)->firstWhere('phone_code', $user->country_code);
                        @endphp
                        @if($phone !== '')
                            @if($phoneCountry) {{ $phoneCountry['flag'] }} @endif
                            @if(Str::startsWith($phone, '+'))
                                {{ $phone }}
                            @else
                                {{ trim(($user->country_code ? $user->country_code.' ' : '').$phone) }}
                            @endif
                        @else
                            Not specified
                        @endif
                    </span>
                </div>

                {{-- Country --}}
                <div class="flex items-center justify-between gap-4">
                    <span class="shrink-0 font-poppins text-sm md:text-base xl:text-xl text-[#444444]">Country:</span>
                    <span class="min-w-0 grow text-right font-poppins text-sm md:text-base xl:text-xl text-black">
                        @if($country)
                            {{ $country['flag'] }} {{ $country['name'] }}
                        @else
                            {{ $user->country ?? 'Not specified' }}
                        @endif
                    </span>
                </div>

            </div>
        </div>



        {{-- Logout Button --}}
        <div class="w-full max-w-[320px] md:max-w-[400px] xl:max-w-[453px] mb-12 md:mb-16 xl:mb-20">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-cabinet.form-button color="orange" type="submit">
                    Logout
                </x-cabinet.form-button>
            </form>
        </div>
    </div>

    @if($user->verification_status !== 'verified')
        @push('scripts')
            <script src="https://cdn.veriff.me/sdk/js/1.5/veriff.min.js"></script>
            <script src="https://cdn.veriff.me/incontext/js/v1/veriff.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var btn = document.getElementById('start-veriff');
                    if (!btn) return;

                    var veriffInstance = null;
                    function initVeriff() {
                        if (veriffInstance) return veriffInstance;
                        veriffInstance = Veriff({
                            host: '{{ config('services.veriff.base_url', 'https://stationapi.veriff.com') }}',
                            apiKey: '{{ config('services.veriff.api_key') }}',
                            parentId: 'veriff-root',
                            onSession: function (err, response) {
                                if (err) {
                                    console.error('Veriff session error', err);
                                    return;
                                }
                                if (window.veriffSDK && response && response.verification && response.verification.url) {
                                    window.veriffSDK.createVeriffFrame({url: response.verification.url});
                                }
                            }
                        });
                        return veriffInstance;
                    }

                    btn.addEventListener('click', function () {
                        // Open modal first
                        try {
                            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'veriff-modal' } }));
                        } catch (e) {}

                        var v = initVeriff();
                        try {
                            v.mount({
                                formLabel: {
                                    vendorData: 'Your e-mail'
                                },
                                vendorData: '{{ $user->email }}'
                            });
                        } catch (e) {
                            console.error('Veriff mount error', e);
                        }
                    });
                });
            </script>
        @endpush
    @endif
</x-cabinet-layout>
