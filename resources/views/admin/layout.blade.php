<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <title>Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    <flux:sidebar sticky collapsible="mobile"
        class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:brand href="#" logo="/images/bfar.png" name="BFAR" class="max-lg:hidden" />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        {{--
        <flux:sidebar.search placeholder="Search..." /> --}}

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="{{ route('admin.dashboard') }}">Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="ticket" href="{{ route('admin.booking') }}">Booking</flux:sidebar.item>
            <flux:sidebar.item icon="user" href="{{ route('admin.driver') }}">Driver</flux:sidebar.item>
            <flux:sidebar.item icon="truck" href="{{ route('admin.vehicle') }}">Vehicle</flux:sidebar.item>
        </flux:sidebar.nav>


        <flux:sidebar.spacer />

        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile avatar="https://fluxui.dev/img/demo/user.png" name="Olivia Martin" />

            <flux:menu>
                <flux:menu.item>
                    <flux:field variant="inline">
                        

                        <flux:switch wire:model.live="notifications" />

                        <flux:error name="notifications" />

                        <flux:label>Darkmode</flux:label>
                    </flux:field>
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" alignt="start">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png" />

            <flux:menu>
                <flux:menu.radio.group>
                    <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                    <flux:menu.radio>Truly Delta</flux:menu.radio>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <flux:main>
        @yield('content')
    </flux:main>
    @fluxScripts
</body>

</html>