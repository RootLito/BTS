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
</head>

<body class="h-screen overflow-hidden bg-zinc-100 dark:bg-zinc-800 antialiased flex flex-col">
    <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:brand href="#" logo="/images/bfar.png" name="BFAR" class="max-lg:hidden" />

        <flux:navbar class="">
            <flux:navbar.item icon="home" href="{{ route('client.home') }}"
                :current="request()->routeIs('client.home')">
                Home
            </flux:navbar.item>

            <flux:navbar.item icon="user" href="{{ route('client.profile') }}"
                :current="request()->routeIs('client.profile')">
                Profile
            </flux:navbar.item>

            <flux:navbar.item icon="calendar" href="{{ route('client.booking') }}"
                :current="request()->routeIs('client.booking')">
                Booking
            </flux:navbar.item>

            <flux:navbar.item icon="map" href="{{ route('client.trip-ticket') }}"
                :current="request()->routeIs('client.trip-ticket*')">
                Trips
            </flux:navbar.item>

            {{-- <flux:navbar.item icon="ticket" href="{{ route('client.tt-print') }}"
                :current="request()->routeIs('client.tt-print')">
                Trip Ticket
            </flux:navbar.item> --}}

            {{-- <flux:navbar.item icon="document-text" href="{{ route('client.to-print') }}"
                :current="request()->routeIs('client.to-print')">
                National TO
            </flux:navbar.item> --}}

            <flux:navbar.item icon="document-text" href="{{ route('client.national-to') }}"
                :current="request()->routeIs('client.national-to*')">
                National TO
            </flux:navbar.item>


            @php
                $userOffice = auth()->user()->office;
                $incomingCount = \App\Models\DocumentTracking::from('document_trackings as dt')
                    ->where('dt.route_to', $userOffice)
                    ->where('dt.status', 'Released')
                    ->whereNotExists(function ($query) use ($userOffice) {
                        $query
                            ->select(DB::raw(1))
                            ->from('document_trackings')
                            ->whereRaw('document_trackings.trip_ticket_id = dt.trip_ticket_id')
                            ->where('route_from', $userOffice)
                            ->where('status', 'Received');
                    })
                    ->distinct('dt.trip_ticket_id')
                    ->count();
            @endphp

            <flux:navbar.item icon="document-magnifying-glass" href="{{ route('client.document-tracking') }}"
                :current="request()->routeIs('client.document-tracking*')">
                <span class="relative pr-2.5 inline-block">
                    Tracking

                    @php
                        $globalUnreadCount = 0;
                        if (auth()->check()) {
                            $rawOffice = auth()->user()->office ?? (auth()->user()->client->office ?? null);

                            if ($rawOffice) {
                                $userOffice = strtolower(trim($rawOffice));

                                $globalUnreadCount = \App\Models\DocumentTracking::where(function ($query) {
                                    $query
                                        ->whereHas('tripTicket', function ($q) {
                                            $q->where(function ($sub) {
                                                $sub->whereNull('to_no')->orWhere('to_no', '');
                                            })->where('status', 'NOT LIKE', '%cancel%');
                                        })
                                        ->orWhereHas('nationalTo', function ($q) {
                                            $q->where('status', 'NOT LIKE', '%cancel%');
                                        });
                                })
                                    ->whereRaw('LOWER(TRIM(route_to)) = ?', [$userOffice])
                                    ->whereRaw('LOWER(TRIM(route_from)) != ?', [$userOffice])

                                    ->whereNotIn('trip_ticket_id', function ($q) {
                                        $q->select('trip_ticket_id')
                                            ->from('document_trackings')
                                            ->where('status', 'LIKE', '%cancel%')
                                            ->whereNotNull('trip_ticket_id');
                                    })
                                    ->whereNotIn('national_to_id', function ($q) {
                                        $q->select('national_to_id')
                                            ->from('document_trackings')
                                            ->where('status', 'LIKE', '%cancel%')
                                            ->whereNotNull('national_to_id');
                                    })
                                    ->count();
                            }
                        }
                    @endphp

                    @if ($globalUnreadCount > 0)
                        <span class="absolute top-0 right-0 -mt-1 mr-0.5 flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    @endif
                </span>
            </flux:navbar.item>

            <flux:navbar.item icon="clipboard-document-check" href="{{ route('client.to-report') }}"
                :current="request()->routeIs('client.to-report*')">
                <span class="relative pr-2.5 inline-block">
                    TO Report

                    @php
                        $pendingReportsCount = 0;
                        if (auth()->check()) {
                            $clientId = auth()->id();
                            $today = now()->startOfDay();

                            $pendingTripTickets = \App\Models\TripTicket::where('client_id', $clientId)
                                ->where('end_date', '<', $today)
                                ->doesntHave('toReport')
                                ->count();

                            $pendingNationalTos = \App\Models\NationalTo::where('client_id', $clientId)
                                ->where('return_date', '<', $today)
                                ->doesntHave('toReport')
                                ->count();

                            $pendingReportsCount = $pendingTripTickets + $pendingNationalTos;
                        }
                    @endphp

                    @if ($pendingReportsCount > 0)
                        <span class="absolute top-0 right-0 -mt-1 mr-0.5 flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    @endif
                </span>
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />
        <flux:dropdown class="me-2">
            <div class="relative inline-block">
                <flux:button variant="subtle" icon="bell" />
                @if ($unreadClientCount > 0)
                    <span class="absolute top-2 right-2 -mt-1 -mr-1 flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span
                            class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white dark:border-zinc-900"></span>
                    </span>
                @endif
            </div>

            <flux:menu class="w-80">
                <flux:menu.item
                    class="font-bold border-b border-zinc-100 dark:border-zinc-700 text-zinc-800 dark:text-white">
                    Notifications ({{ $unreadClientCount }})
                </flux:menu.item>

                <div class="max-h-[240px] overflow-y-auto">
                    @forelse($clientNotifications as $notif)
                        <flux:modal.trigger :name="'notif-modal-' . $notif->id">
                            <flux:menu.item
                                class="flex flex-col items-start gap-0 py-3 bg-blue-50/50 dark:bg-blue-900/10">
                                <div class="flex justify-between w-full items-center">
                                    <span class="text-[10px] font-medium text-zinc-500 uppercase tracking-wider">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </span>
                                    {{-- Blue dot indicating it's unread --}}
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                </div>
                                <span class="text-sm text-zinc-700 dark:text-zinc-300 truncate w-full mt-1">
                                    {{ $notif->message }}
                                </span>
                            </flux:menu.item>
                        </flux:modal.trigger>
                    @empty
                        <div class="p-6 text-center">
                            <flux:text size="sm" class="text-zinc-500 italic">No new notifications.</flux:text>
                        </div>
                    @endforelse
                </div>
            </flux:menu>
        </flux:dropdown>

        @foreach ($clientNotifications as $notif)
            <flux:modal :name="'notif-modal-' . $notif->id" class="md:w-96">
                <div class="space-y-4">
                    <flux:heading size="lg">Trip Notification</flux:heading>
                    <flux:text>{{ $notif->message }}</flux:text>

                    <div class="flex justify-end">
                        <form action="{{ route('client.notification.read', $notif->id) }}" method="POST">
                            @csrf
                            <flux:button type="submit" variant="filled">Close</flux:button>
                        </form>
                    </div>
                </div>
            </flux:modal>
        @endforeach
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <flux:button type="submit" variant="filled" icon="arrow-left-start-on-rectangle" color="red">
                Logout
            </flux:button>
        </form>
    </flux:header>
    <flux:main container class="overflow-y-auto scrollbar-hidden">
        @yield('content')
    </flux:main>
    @fluxScripts
</body>

</html>
