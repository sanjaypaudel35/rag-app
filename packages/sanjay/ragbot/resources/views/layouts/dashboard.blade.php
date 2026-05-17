<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ragbot') }}</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 antialiased h-full font-sans text-zinc-900 dark:text-zinc-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <flux:sidebar sticky collapsible class="bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800">
            <flux:sidebar.header class="mb-6">
                <flux:sidebar.brand
                    href="{{ app()->bound('ragbot.project') ? route('ragbot.dashboard', ['project_slug' => app('ragbot.project')->slug]) : '#' }}"
                    name="{{ app()->bound('ragbot.project') ? app('ragbot.project')->name : 'Ragbot' }}"
                    logo="{{ (app()->bound('ragbot.project') && app('ragbot.project')->logo) ? asset('storage/'.app('ragbot.project')->logo) : 'https://fluxui.dev/img/demo/logo.png' }}"
                />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="squares-2x2" href="{{ app()->bound('ragbot.project') ? route('ragbot.dashboard', ['project_slug' => app('ragbot.project')->slug]) : '#' }}" :current="request()->routeIs('ragbot.dashboard')">Dashboard</flux:sidebar.item>
                <flux:sidebar.item icon="document-duplicate" href="{{ app()->bound('ragbot.project') ? route('ragbot.documents', ['project_slug' => app('ragbot.project')->slug]) : '#' }}" :current="request()->routeIs('ragbot.documents')">Documents</flux:sidebar.item>
                <flux:sidebar.item icon="rectangle-stack" href="#">Collections</flux:sidebar.item>
                <flux:sidebar.item icon="arrow-path" href="{{ app()->bound('ragbot.project') ? route('ragbot.processing-queue', ['project_slug' => app('ragbot.project')->slug]) : '#' }}" :current="request()->routeIs('ragbot.processing-queue')">Processing Queue</flux:sidebar.item>
                
                <flux:separator class="my-4 mx-4" />
                
                <flux:sidebar.item icon="cog-8-tooth" href="{{ app()->bound('ragbot.project') ? route('ragbot.settings', ['project_slug' => app('ragbot.project')->slug]) : '#' }}" :current="request()->routeIs('ragbot.settings')">Settings</flux:sidebar.item>
                <flux:sidebar.item icon="users" href="{{ app()->bound('ragbot.project') ? route('ragbot.team', ['project_slug' => app('ragbot.project')->slug]) : '#' }}" :current="request()->routeIs('ragbot.team')">Team</flux:sidebar.item>
                <flux:sidebar.item icon="chat-bubble-left-right" href="{{ app()->bound('ragbot.project') ? route('ragbot.settings.chatbots', ['project_slug' => app('ragbot.project')->slug]) : '#' }}" :current="request()->routeIs('ragbot.settings.chatbots')">Chatbots</flux:sidebar.item>
                <flux:sidebar.item icon="credit-card" href="#">Billing</flux:sidebar.item>
                <flux:sidebar.item icon="list-bullet" href="#">Activity Logs</flux:sidebar.item>

                <flux:separator class="my-4 mx-4" />

                <div class="px-4 mb-2">
                    <flux:text size="xs" class="font-bold uppercase tracking-widest text-zinc-400">Integration and Setup Guide</flux:text>
                </div>
                <flux:sidebar.item icon="code-bracket" href="{{ app()->bound('ragbot.project') ? route('ragbot.integration-guide', ['project_slug' => app('ragbot.project')->slug]) : '#' }}" :current="request()->routeIs('ragbot.integration-guide')">Integration Guide</flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <!-- Project Card at Bottom -->
            @if (app()->bound('ragbot.project'))
                <div class="p-4 mx-2 mb-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden in-data-flux-sidebar-collapsed-desktop:hidden">
                    <div class="flex items-center justify-between mb-2">
                        <flux:text size="xs" class="font-bold uppercase tracking-widest text-zinc-400">Current Project</flux:text>
                        <flux:badge size="sm" variant="success" inset="top bottom" class="text-[10px] px-1.5 py-0">Pro Plan</flux:badge>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(app('ragbot.project')->logo)
                            <img src="{{ asset('storage/'.app('ragbot.project')->logo) }}" class="h-6 w-6 rounded border border-zinc-200 dark:border-zinc-700">
                        @else
                            <div class="h-6 w-6 bg-indigo-100 dark:bg-indigo-900/30 rounded flex items-center justify-center">
                                <flux:icon icon="squares-2x2" variant="mini" class="text-indigo-600 dark:text-indigo-400 h-3 w-3" />
                            </div>
                        @endif
                        <flux:heading size="sm" class="truncate">{{ app('ragbot.project')->name }}</flux:heading>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <flux:text size="xs" class="text-zinc-500">{{ app('ragbot.project')->users()->count() }} Members</flux:text>
                    </div>
                </div>
            @endif

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile 
                    avatar="{{ Auth::guard('ragbot')->user()->profile_photo_path ? asset('storage/'.Auth::guard('ragbot')->user()->profile_photo_path) : '' }}"
                    name="{{ Auth::guard('ragbot')->user()->name ?? 'User' }}" 
                    initials="{{ strtoupper(substr(Auth::guard('ragbot')->user()->firstname ?? Auth::guard('ragbot')->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(Auth::guard('ragbot')->user()->lastname ?? '', 0, 1)) }}"
                />
                <flux:menu>
                    <flux:menu.item icon="user-circle" href="{{ route('ragbot.profile', ['project_slug' => app('ragbot.project')->slug]) }}">Profile Settings</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item icon="arrow-right-start-on-rectangle" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-10 px-6 py-4 md:px-10">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4 lg:hidden">
                        <flux:sidebar.toggle icon="bars-2" inset="left" />
                        <flux:brand 
                            name="{{ app()->bound('ragbot.project') ? app('ragbot.project')->name : 'Ragbot' }}" 
                            logo="{{ (app()->bound('ragbot.project') && app('ragbot.project')->logo) ? asset('storage/'.app('ragbot.project')->logo) : 'https://fluxui.dev/img/demo/logo.png' }}" 
                        />
                    </div>

                    <!-- Breadcrumbs & Titles -->
                    <div class="hidden md:block">
                        <div class="flex items-center gap-2 text-xs text-zinc-400 mb-1">
                            <span>Dashboard</span>
                            <flux:icon icon="chevron-right" variant="mini" class="w-3 h-3" />
                            <span class="text-zinc-900 dark:text-zinc-100 font-medium">
                                @if(request()->routeIs('ragbot.documents')) Documents 
                                @elseif(request()->routeIs('ragbot.integration-guide')) Integration Guide
                                @else Overview 
                                @endif
                            </span>
                        </div>
                        <flux:heading size="xl" level="1">
                            @if(request()->routeIs('ragbot.documents')) Documents 
                            @elseif(request()->routeIs('ragbot.integration-guide')) Integration Guide
                            @else Overview 
                            @endif
                        </flux:heading>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center gap-4 md:gap-6">
                        <flux:button icon="magnifying-glass" variant="ghost" size="sm" class="text-zinc-400" />
                        
                        <div class="relative">
                            <flux:button icon="bell" variant="ghost" size="sm" class="text-zinc-400" />
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-zinc-900"></span>
                        </div>

                        <div class="h-8 w-[1px] bg-zinc-200 dark:bg-zinc-800 mx-2 hidden sm:block"></div>

                        <div class="flex items-center gap-3">
                            <div class="hidden sm:block text-right">
                                <flux:text size="sm" class="font-semibold text-zinc-900 dark:text-zinc-100">{{ Auth::guard('ragbot')->user()->name ?? 'User' }}</flux:text>
                                <flux:text size="xs" class="text-zinc-500">Member</flux:text>
                            </div>
                            <flux:dropdown align="end">
                                <flux:avatar 
                                    src="{{ Auth::guard('ragbot')->user()->profile_photo_path ? asset('storage/'.Auth::guard('ragbot')->user()->profile_photo_path) : '' }}"
                                    size="sm" 
                                    initials="{{ strtoupper(substr(Auth::guard('ragbot')->user()->firstname ?? Auth::guard('ragbot')->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(Auth::guard('ragbot')->user()->lastname ?? '', 0, 1)) }}" 
                                    class="cursor-pointer" 
                                />
                                <flux:menu>
                                    <flux:menu.item icon="user" href="{{ route('ragbot.profile', ['project_slug' => app('ragbot.project')->slug]) }}">Account Settings</flux:menu.item>
                                    <flux:menu.item icon="cog-8-tooth">Preferences</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="arrow-right-start-on-rectangle" variant="danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>
                </div>
            </header>

            <flux:main class="flex-1 px-6 py-8 md:px-10 md:py-10 bg-zinc-50/50 dark:bg-zinc-950/50">
                {{ $slot }}
            </flux:main>
        </div>
    </div>

    @if (app()->bound('ragbot.project'))
    <form id="logout-form" action="{{ route('ragbot.tenant.logout', ['project_slug' => app('ragbot.project')->slug]) }}" method="POST" class="hidden">
        @csrf
    </form>
    @endif

    @fluxScripts
</body>
</html>
