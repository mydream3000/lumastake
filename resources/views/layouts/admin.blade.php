<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="/favicon.svg" type="image/svg+xml">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Scripts -->
    @php
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\Vite::useHotFile(base_path('no-vite-hot'));
            \Illuminate\Support\Facades\Vite::useBuildDirectory('build');
        }
    @endphp
    @vite(['resources/css/cabinet.css', 'resources/js/cabinet.js'])
</head>
<body class="font-sans antialiased overflow-x-hidden bg-cabinet-bg" x-data="{ mobileMenuOpen: false }">
<!-- Mobile Header (Fixed) -->
<div class="lg:hidden fixed top-0 left-0 right-0 bg-white border-b border-cabinet-border px-4 py-3 z-30">
    <div class="flex items-center justify-between">
        <div>
            <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-6 brightness-0">
            <p class="text-[10px] font-bold text-cabinet-blue uppercase mt-0.5">Admin Panel</p>
        </div>
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-cabinet-text-main">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
</div>

<!-- Mobile Sidebar Overlay -->
<div x-show="mobileMenuOpen"
     @click="mobileMenuOpen = false"
     x-transition:enter="transition-opacity ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden"
     style="display: none;">
</div>

<!-- Mobile Sidebar -->
<div x-show="mobileMenuOpen"
     @click.away="mobileMenuOpen = false"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed left-0 top-0 bottom-0 w-80 bg-gradient-to-b from-cabinet-sidebar-start to-cabinet-sidebar-end text-white z-50 lg:hidden shadow-2xl overflow-y-auto"
     style="display: none;">
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <div>
            <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-8">
            <p class="text-xs text-white/70 mt-2 font-bold uppercase">Admin Panel</p>
        </div>
        <button @click="mobileMenuOpen = false" class="text-white">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <nav class="p-4 space-y-1">
        @if(auth()->user()->is_closer && !auth()->user()->is_admin)
            {{-- Closer sees only Users --}}
            <a href="{{ route('admin.closer.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.closer.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-users w-5"></i>
                <span>Users</span>
            </a>
        @else
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-chart-line w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-users w-5"></i>
                <span>Users</span>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.payments.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-money-bill-wave w-5"></i>
                <span>Payments</span>
            </a>
            <a href="{{ route('admin.tiers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.tiers.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-layer-group w-5"></i>
                <span>Tiers</span>
            </a>
            <a href="{{ route('admin.pools.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.pools.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-coins w-5"></i>
                <span>Investment Pools</span>
            </a>
            <a href="{{ route('admin.referral-levels.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.referral-levels.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-gift w-5"></i>
                <span>Referral Levels</span>
            </a>
            <a href="{{ route('admin.promo.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.promo.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-tag w-5"></i>
                <span>Promo Codes</span>
            </a>
            <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.faqs.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-question-circle w-5"></i>
                <span>FAQ</span>
            </a>
            <a href="{{ route('admin.blog.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.blog.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-newspaper w-5"></i>
                <span>Blog</span>
            </a>
            <a href="{{ route('admin.bot-settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.bot-settings.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-robot w-5"></i>
                <span>Telegram Bot</span>
            </a>
            <a href="{{ route('admin.email-templates.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.email-templates.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-envelope w-5"></i>
                <span>Email Templates</span>
            </a>
            <a href="{{ route('admin.support-team.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.support-team.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-headset w-5"></i>
                <span>Support Team</span>
            </a>
            <a href="{{ route('admin.contact-submissions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.contact-submissions.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-inbox w-5"></i>
                <span>Contact Submissions</span>
            </a>
            <a href="{{ route('admin.social-links.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.social-links.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-share-alt w-5"></i>
                <span>Social Links</span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.analytics.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fas fa-chart-bar w-5"></i>
                <span>Analytics</span>
            </a>
            <a href="{{ route('admin.seo.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.seo.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80' }}">
                <i class="fa-solid fa-search w-5"></i>
                <span>SEO</span>
            </a>
        @endif
    </nav>

    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-4 py-3 bg-white/10 rounded-lg mb-2">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <p class="text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-white/60 uppercase font-bold">{{ auth()->user()->is_closer && !auth()->user()->is_admin ? 'Closer' : 'Administrator' }}</p>
            </div>
        </div>
        <a href="{{ route('cabinet.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-white/80 mb-1 hover:bg-white/10">
            <i class="fas fa-home w-5"></i>
            <span class="text-sm">User Cabinet</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-white/80 hover:bg-white/10">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="text-sm">Logout</span>
            </button>
        </form>
    </div>
</div>

<div class="flex min-h-screen">
    <!-- Admin Sidebar -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-gradient-to-b from-cabinet-sidebar-start to-cabinet-sidebar-end text-white">
        <!-- Logo -->
        <div class="p-6 border-b border-white/10">
            <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake Admin" class="h-10 w-auto">
            <p class="text-[10px] text-white/70 font-bold uppercase mt-2">Admin Panel</p>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @if(auth()->user()->is_closer && !auth()->user()->is_admin)
                {{-- Closer sees only Users --}}
                <a href="{{ route('admin.closer.users.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.closer.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="font-medium">Users</span>
                </a>
            @else
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-chart-line w-5 text-center"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="font-medium">Users</span>
                </a>

                <a href="{{ route('admin.payments.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.payments.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-money-bill-wave w-5 text-center"></i>
                    <span class="font-medium">Payments</span>
                </a>

                <a href="{{ route('admin.tiers.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.tiers.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-layer-group w-5 text-center"></i>
                    <span class="font-medium">Tiers</span>
                </a>

                <a href="{{ route('admin.pools.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.pools.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-coins w-5 text-center"></i>
                    <span class="font-medium">Investment Pools</span>
                </a>

                <a href="{{ route('admin.referral-levels.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.referral-levels.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-gift w-5 text-center"></i>
                    <span class="font-medium">Referral Levels</span>
                </a>

                <a href="{{ route('admin.promo.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.promo.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-tag w-5 text-center"></i>
                    <span class="font-medium">Promo Codes</span>
                </a>

                <a href="{{ route('admin.faqs.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.faqs.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-question-circle w-5 text-center"></i>
                    <span class="font-medium">FAQ</span>
                </a>

                <a href="{{ route('admin.blog.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.blog.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-newspaper w-5 text-center"></i>
                    <span class="font-medium">Blog</span>
                </a>

                <a href="{{ route('admin.bot-settings.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.bot-settings.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-robot w-5 text-center"></i>
                    <span class="font-medium">Telegram Bot</span>
                </a>

                <a href="{{ route('admin.email-templates.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.email-templates.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-envelope w-5 text-center"></i>
                    <span class="font-medium">Email Templates</span>
                </a>

                <a href="{{ route('admin.support-team.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.support-team.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-headset w-5 text-center"></i>
                    <span class="font-medium">Support Team</span>
                </a>

                <a href="{{ route('admin.contact-submissions.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.contact-submissions.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-inbox w-5 text-center"></i>
                    <span class="font-medium">Contact Submissions</span>
                </a>

                <a href="{{ route('admin.social-links.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.social-links.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-share-alt w-5 text-center"></i>
                    <span class="font-medium">Social Links</span>
                </a>

                <a href="{{ route('admin.analytics.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.analytics.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span class="font-medium">Analytics</span>
                </a>
                <a href="{{ route('admin.seo.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.seo.*') ? 'bg-white/20 text-white font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-search w-5 text-center"></i>
                    <span class="font-medium">SEO</span>
                </a>
            @endif
        </nav>

        <!-- User Info & Logout -->
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-4 py-3 bg-white/10 rounded-lg mb-2">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-white/60 uppercase font-bold">{{ auth()->user()->is_closer && !auth()->user()->is_admin ? 'Closer' : 'Administrator' }}</p>
                </div>
            </div>

            <a href="{{ route('cabinet.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg text-white/80 hover:bg-white/10 hover:text-white transition-colors mb-1">
                <i class="fas fa-home w-5 text-center text-sm"></i>
                <span class="text-sm font-medium">User Cabinet</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-white/80 hover:bg-red-500/20 hover:text-red-400 transition-colors">
                    <i class="fas fa-sign-out-alt w-5 text-center text-sm"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Page Content -->
        <main class="flex-1 p-4 md:p-6 lg:p-8 lg:pt-8 pt-20 lg:pt-8">
            @php($__status = session('status'))
            {!! $__status ? '<div class="mb-4 p-4 rounded-lg bg-green-50 text-green-800 border border-green-200">'.e($__status).'</div>' : '' !!}
            @yield('content')
        </main>
    </div>
</div>

<div id="toast-app"></div>
<script src="https://cdn.tiny.cloud/1/8izs1yg67y0ctwch69rsv6f7cla2du96gtliyz59ya58bj15/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
@stack('scripts')
</body>
</html>
