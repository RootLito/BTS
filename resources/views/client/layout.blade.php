<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/bfar.png') }}" type="image/png">
    <title>Client</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="h-screen overflow-hidden bg-zinc-100 dark:bg-zinc-800 antialiased flex flex-col">
    <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:brand href="#" logo="/images/bfar.png" name="BFAR" class="max-lg:hidden" />

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="home" href="{{ route('client.home') }}"
                :current="request()->routeIs('client.home')">
                Home
            </flux:navbar.item>

            <flux:navbar.item icon="calendar" href="{{ route('client.booking') }}"
                :current="request()->routeIs('client.booking')">
                Booking
            </flux:navbar.item>

            <flux:navbar.item icon="ticket" href="{{ route('client.trip-ticket') }}"
                :current="request()->routeIs('client.trip-ticket*')">
                Trips
            </flux:navbar.item>
            {{-- <flux:navbar.item icon="document-text" href="">
                Documents
            </flux:navbar.item> --}}

            {{-- <flux:navbar.item icon="document-text" href="{{ route('client.travel-order') }}"
                :current="request()->routeIs('client.travel-order')">
                Travel Order
            </flux:navbar.item>

            <flux:navbar.item icon="clock" href="{{ route('client.trip-history') }}"
                :current="request()->routeIs('client.trip-history')">
                Trip History
            </flux:navbar.item> --}}
        </flux:navbar>
        <flux:spacer />
        <flux:icon.bell class="me-4"/>    
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <flux:button type="submit" variant="filled" icon="arrow-left-start-on-rectangle" color="red">
                Logout
            </flux:button>
        </form>
    </flux:header>
    <flux:main container>
        @yield('content')
    </flux:main>
    @fluxScripts
</body>

</html>