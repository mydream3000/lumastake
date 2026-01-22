<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    @php
        // Check if $seo array is provided (for blog posts with custom SEO)
        if (isset($seo) && is_array($seo)) {
            // Use SEO data from $seo array
            $pageTitle = $seo['title'] ?? 'Lumastake - USDT Staking Platform';
            $metaDescription = $seo['description'] ?? 'Earn passive income with USDT staking on Lumastake.';
            $metaKeywords = $seo['keywords'] ?? 'USDT, staking, cryptocurrency';
            $ogTitle = $seo['og_title'] ?? $pageTitle;
            $ogDescription = $seo['og_description'] ?? $metaDescription;
            // Ensure og:image is absolute URL even when passed from $seo
            $rawOgFromSeo = $seo['og_image'] ?? null;
            if ($rawOgFromSeo) {
                $ogImage = \Illuminate\Support\Str::startsWith($rawOgFromSeo, ['http://','https://']) ? $rawOgFromSeo : asset($rawOgFromSeo);
            } else {
                $ogImage = asset('images/og-image.jpg');
            }
            $twitterSite = $seo['twitter_site'] ?? '@lumastake';
            $schemaJson = null;
            $ogType = 'article'; // Blog article page
        } else {
            // Use SEO data from SeoSetting model
            $seoKey = $seoKey ?? 'home';
            $pageSeo = \App\Models\SeoSetting::getByKey($seoKey);

            $pageTitle = $pageSeo?->page_title ?? 'Lumastake - USD Staking Platform';
            $metaDescription = $pageSeo?->meta_description ?? 'Earn passive income with USDT staking on Lumastake. Secure, reliable, and profitable cryptocurrency staking platform.';
            $metaKeywords = $pageSeo?->meta_keywords ?? 'USD, staking, cryptocurrency, passive income, Tether';
            $ogTitle = $pageSeo?->og_title ?? $pageTitle;
            $ogDescription = $pageSeo?->og_description ?? $metaDescription;
            // Ensure og:image is absolute URL
            $rawOg = $pageSeo?->og_image;
            if ($rawOg) {
                $ogImage = \Illuminate\Support\Str::startsWith($rawOg, ['http://','https://']) ? $rawOg : asset($rawOg);
            } else {
                $ogImage = asset('images/og-image.jpg');
            }
            $twitterSite = $pageSeo?->twitter_site ?? '@lumastake';
            $schemaJson = $pageSeo?->schema_json;
            $ogType = 'website';
        }
    @endphp

    <!-- SEO Source: {{ isset($seo) && is_array($seo) ? 'Blog Post Custom SEO' : 'SeoSetting (' . ($seoKey ?? 'home') . ')' }} -->
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Lumastake">
    <meta property="og:locale" content="en_US">

    {{-- Open Graph Article specific --}}
    @if($ogType === 'article')
        @php
            $published = $seo['article_published_time'] ?? null;
            $modified = $seo['article_modified_time'] ?? null;
            $articleAuthor = $seo['article_author'] ?? null;
            $ogSection = $seo['og_section'] ?? 'Blog';
            $twitterCreator = $seo['twitter_creator'] ?? null;
        @endphp
        @if($published)
            <meta property="article:published_time" content="{{ $published }}">
        @endif
        @if($modified)
            <meta property="article:modified_time" content="{{ $modified }}">
        @endif
        @if($articleAuthor)
            <meta property="article:author" content="{{ $articleAuthor }}">
        @endif
        @if($ogSection)
            <meta property="article:section" content="{{ $ogSection }}">
        @endif
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ $twitterSite }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    @if(isset($twitterCreator) && $twitterCreator)
        <meta name="twitter:creator" content="{{ $twitterCreator }}">
    @endif

    {{-- Additional SEO --}}
    <meta name="author" content="Lumastake">
    <meta name="theme-color" content="#0F172A">

    {{-- Schema.org JSON-LD --}}
    @if($schemaJson)
        <script type="application/ld+json">{!! $schemaJson !!}</script>
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Audiowide&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="shortcut icon" href="/favicon.svg" type="image/svg+xml">

    @php
        // Safety fallback: on production force using built assets even if a stray hot file exists
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\Vite::useHotFile(base_path('no-vite-hot'));
            \Illuminate\Support\Facades\Vite::useBuildDirectory('build');
        }
    @endphp

    @vite(['resources/css/public.css','resources/js/public.js'])

</head>
<body class="bg-white text-gray-900 font-poppins overflow-x-hidden selection:bg-lumastake-blue selection:text-white">

@include('layouts._partials.header')

<main class="relative overflow-x-clip">
    @yield('content')
</main>

<!-- Back to Top Button -->
<button id="backToTop" class="fixed z-50 bg-lumastake-blue text-white p-4 rounded-full shadow-2xl hover:bg-blue-700 transition-all duration-300 opacity-0 invisible translate-y-4 right-8 bottom-8">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
</button>

@include('layouts._partials.footer')

<script>
    // Back to Top Button functionality
    const backToTopButton = document.getElementById('backToTop');

    if (backToTopButton) {
        // Show/hide button on scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.remove('opacity-0', 'invisible', 'translate-y-4');
                backToTopButton.classList.add('opacity-100', 'visible', 'translate-y-0');
            } else {
                backToTopButton.classList.add('opacity-0', 'invisible', 'translate-y-4');
                backToTopButton.classList.remove('opacity-100', 'visible', 'translate-y-0');
            }
        });

        // Smooth scroll to top on button click
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
</script>

<script>
    // Global toast fallback to avoid "window.showToast is not a function" errors
    if (typeof window.showToast !== 'function') {
        window.showToast = function(message, type = 'info') {
            try {
                // De-duplicate identical toasts within 5 seconds
                const now = Date.now();
                const last = window.__lastToast || { message: null, time: 0 };
                if (last.message === String(message) && (now - last.time) < 5000) {
                    console.log('[Toast] Duplicate prevented:', message);
                    return; // skip duplicate
                }
                window.__lastToast = { message: String(message), time: now };
                console.log('[Toast] Showing:', message, 'Type:', type);

                const containerId = 'lumastake-toast-container';
                let container = document.getElementById(containerId);
                if (!container) {
                    container = document.createElement('div');
                    container.id = containerId;
                    container.style.position = 'fixed';
                    container.style.top = '20px';
                    container.style.right = '20px';
                    container.style.zIndex = '99999';
                    container.style.display = 'flex';
                    container.style.flexDirection = 'column';
                    container.style.gap = '8px';
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                toast.style.padding = '12px 16px';
                toast.style.borderRadius = '8px';
                toast.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)';
                toast.style.color = '#111928';
                toast.style.background = '#ffffff';
                toast.style.border = '1px solid #e5e7eb';
                toast.style.fontSize = '14px';
                toast.style.maxWidth = '360px';

                const colors = {
                    success: '#05C982',
                    error: '#F70808',
                    info: '#2563eb',
                    warning: '#f59e0b'
                };
                const bar = document.createElement('div');
                bar.style.height = '3px';
                bar.style.borderRadius = '3px';
                bar.style.marginTop = '8px';
                bar.style.background = colors[type] || colors.info;

                const inner = document.createElement('div');
                inner.textContent = message || '';

                toast.appendChild(inner);
                toast.appendChild(bar);
                container.appendChild(toast);

                setTimeout(() => {
                    toast.style.transition = 'opacity 300ms ease';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 2600);
            } catch (e) {
                // Final fallback
                alert(message);
            }
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            window.showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            window.showToast("{{ session('error') }}", 'error');
        @endif
        @if(session('status'))
            window.showToast("{{ session('status') }}", 'info');
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                window.showToast("{{ $error }}", 'error');
            @endforeach
        @endif
    });
</script>

{{-- Preload countries data for Alpine.js components --}}
<script>
    window.__GEOIP_COUNTRIES__ = @json(\App\Helpers\GeoIpHelper::getAllCountries());
</script>

@stack('scripts')

{{-- Cookie Consent Banner --}}
@include('layouts._partials.cookie-consent')

@if(config('services.intercom.app_id'))
{{-- Intercom Live Chat Widget --}}
<script>
  window.intercomSettings = {
    api_base: "https://api-iam.intercom.io",
    app_id: "{{ config('services.intercom.app_id') }}",
    @if(auth()->check())
    user_id: {{ auth()->id() }},
    name: @json(auth()->user()->name),
    email: @json(auth()->user()->email),
    @if(auth()->user()->phone)
    phone: @json(auth()->user()->phone),
    @endif
    created_at: {{ auth()->user()->created_at->timestamp }},
    @endif
  };
</script>
<script>
  (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/{{ config('services.intercom.app_id') }}';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
@endif

</body>
</html>
