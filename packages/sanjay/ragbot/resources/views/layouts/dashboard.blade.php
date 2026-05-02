<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ragbot') }}</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased h-full">
    <div class="flex min-h-screen">
        <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.header>
                <flux:sidebar.brand
                    href="{{ route('ragbot.dashboard', ['project_slug' => app('ragbot.project')->slug]) }}"
                    name="Ragbot"
                />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="home" href="{{ route('ragbot.dashboard', ['project_slug' => app('ragbot.project')->slug]) }}" :current="request()->routeIs('ragbot.dashboard')">Dashboard</flux:sidebar.item>
                <flux:sidebar.item icon="document-text" href="{{ route('ragbot.documents', ['project_slug' => app('ragbot.project')->slug]) }}" :current="request()->routeIs('ragbot.documents')">Documents</flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="cog-6-tooth" href="{{ route('ragbot.settings', ['project_slug' => app('ragbot.project')->slug]) }}" :current="request()->routeIs('ragbot.settings')">Settings</flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile name="{{ Auth::guard('ragbot')->user()->name }}" />
                <flux:menu>
                    <flux:menu.item icon="arrow-right-start-on-rectangle" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <div class="flex-1 flex flex-col">
            <flux:header class="lg:hidden">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
                <flux:spacer />
                <flux:dropdown position="top" align="start">
                    <flux:profile name="{{ Auth::guard('ragbot')->user()->name }}" />
                    <flux:menu>
                        <flux:menu.item icon="arrow-right-start-on-rectangle" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>

            <flux:main class="flex-1">
                {{ $slot }}
            </flux:main>
        </div>
    </div>

    <form id="logout-form" action="{{ route('ragbot.logout', ['project_slug' => app('ragbot.project')->slug]) }}" method="POST" class="hidden">
        @csrf
    </form>

    @fluxScripts
</body>
</html>
