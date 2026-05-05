<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/bfar.png') }}" type="image/png">
    <title>Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    <flux:sidebar sticky collapsible="mobile"
        class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:avatar size="xl" src="/images/bfar.png" class="bg-white mx-auto" />

        <h2 class="text-center font-bold text-3xl">T.S.</h2>


        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" :href="route('admin.dashboard')"
                :current="request()->routeIs('admin.dashboard')" class="py-4!">
                Dashboard
            </flux:sidebar.item>

            <flux:sidebar.item icon="calendar" :href="route('admin.book')"
                :current="request()->routeIs('admin.book')" class="py-4!">
                Booking
            </flux:sidebar.item>

            <flux:sidebar.item icon="ticket" :href="route('admin.booking')"
                :current="request()->routeIs('admin.booking*')" class="py-4!">
                Trips
            </flux:sidebar.item>

            <flux:sidebar.item icon="user" :href="route('admin.driver')"
                :current="request()->routeIs('admin.driver')" class="py-4!">
                Driver
            </flux:sidebar.item>

            <flux:sidebar.item icon="truck" :href="route('admin.vehicle')"
                :current="request()->routeIs('admin.vehicle')" class="py-4!">
                Vehicle
            </flux:sidebar.item>

            <flux:sidebar.item icon="bell" :href="route('admin.notification')"
                :current="request()->routeIs('admin.notification')" class="py-4!">
                <div class="flex items-center w-full">
                    <span>Notification</span>

                    @if ($unreadCount > 0)
                        <span
                            class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1.5 text-[10px] font-bold text-white shadow-sm flex-shrink-0">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </div>
            </flux:sidebar.item>
        </flux:sidebar.nav>


        <flux:sidebar.spacer />

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <flux:button type="submit" variant="filled" color="red" icon="arrow-left-start-on-rectangle"
                class="w-full">
                Logout
            </flux:button>
        </form>
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

    <flux:main class="bg-zinc-100">
        @yield('content')
    </flux:main>
    @fluxScripts
</body>

</html>
