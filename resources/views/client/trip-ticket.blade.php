@extends('client.layout')

@section('content')
    <div class="w-full h-full flex flex-col">
        <flux:heading size="xl" level="1">Booked Trips</flux:heading>
        <flux:text class="mt-2 mb-6 text-base">View and manage your trip tickets</flux:text>

        <div class="flex-1 space-y-4">
            <form action="{{ route('client.trip-ticket') }}" method="GET" class="flex items-center gap-2">
                <div class="w-100">
                    <flux:input name="search" value="{{ request('search') }}" icon="magnifying-glass"
                        placeholder="Search trips..." />
                </div>

                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        {{ request('sort') === 'oldest' ? 'Old - New' : 'New - Old' }}
                    </flux:button>

                    <flux:menu>
                        <flux:menu.radio.group name="sort" value="{{ request('sort', 'latest') }}">
                            <flux:menu.item
                                href="{{ route('client.trip-ticket', ['sort' => 'latest', 'search' => request('search')]) }}">
                                New - Old
                            </flux:menu.item>
                            <flux:menu.item
                                href="{{ route('client.trip-ticket', ['sort' => 'oldest', 'search' => request('search')]) }}">
                                Old - New
                            </flux:menu.item>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" color="emerald">Filter</flux:button>

                    @if (request()->anyFilled(['search', 'sort']))
                        <flux:button href="{{ route('client.trip-ticket') }}" variant="filled" color="zinc"
                            icon="x-mark">
                            Clear
                        </flux:button>
                    @endif
                </div>

                <flux:button href="{{ route('client.booking') }}" variant="primary" color="emerald" icon="plus"
                    class="ms-auto">
                    New Booking
                </flux:button>
            </form>

            <flux:card class="space-y-6 overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Destination</flux:table.column>
                        <flux:table.column>Dates</flux:table.column>
                        <flux:table.column>Driver & Vehicle</flux:table.column>
                        <flux:table.column>Passenger(s)</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column class="w-0 text-right">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($tickets as $ticket)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span
                                            class="font-medium text-zinc-800 dark:text-white">{{ $ticket->destination }}</span>
                                        <span
                                            class="text-xs text-zinc-500 truncate max-w-[200px]">{{ $ticket->purpose }}</span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap">
                                    <div class="flex flex-col text-sm">
                                        <span>
                                            {{ \Carbon\Carbon::parse($ticket->start_date)->format('M d') }}
                                            –
                                            {{ \Carbon\Carbon::parse($ticket->end_date)->format('M d, Y') }}
                                        </span>
                                        <span class="text-xs text-zinc-400">
                                            {{ \Carbon\Carbon::parse($ticket->start_date)->format('l') }}
                                            –
                                            {{ \Carbon\Carbon::parse($ticket->end_date)->format('l') }}
                                        </span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex flex-col text-sm">
                                        <span>{{ $ticket->driver->name ?? 'No Driver' }}</span>
                                        <span
                                            class="text-xs text-zinc-400">{{ $ticket->vehicle->vehicle ?? 'No Vehicle' }}</span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    @php
                                        $passengerList = is_array($ticket->passengers)
                                            ? $ticket->passengers
                                            : json_decode($ticket->passengers ?? '[]', true);
                                        $extraCount = count($passengerList) - 1;
                                    @endphp

                                    <div class="flex flex-col text-sm">
                                        @if (count($passengerList) > 0)
                                            <span class="font-medium text-zinc-800 dark:text-white">
                                                {{ $passengerList[0] }}
                                            </span>
                                            @if ($extraCount > 0)
                                                <span class="text-xs text-zinc-400 cursor-help"
                                                    title="{{ implode(', ', array_slice($passengerList, 1)) }}">
                                                    +{{ $extraCount }} other
                                                    {{ $extraCount > 1 ? 'passengers' : 'passenger' }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-xs text-zinc-400 italic">Driver only</span>
                                        @endif
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    @php
                                        $statusColor = match ($ticket->status) {
                                            'Pending' => 'yellow',
                                            'Approved' => 'blue',
                                            'Cancelled' => 'red',
                                            'Completed' => 'emerald',
                                            default => 'zinc',
                                        };
                                    @endphp
                                    <flux:badge color="{{ $statusColor }}" size="sm" inset="top bottom">
                                        {{ $ticket->status }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:modal.trigger :name="'track-ticket-' . $ticket->id">
                                        <flux:button icon="map">Track Status</flux:button>
                                    </flux:modal.trigger>
                                    <flux:dropdown>
                                        <flux:button icon-trailing="chevron-down" class="text-sm">Actions</flux:button>
                                        <flux:menu>
                                            {{-- VIEW TRIGGER --}}
                                            <flux:modal.trigger :name="'view-ticket-' . $ticket->id">
                                                <flux:menu.item icon="eye">View</flux:menu.item>
                                            </flux:modal.trigger>

                                            <flux:menu.item icon="ticket"
                                                href="{{ route('client.trip-ticket.ticket', $ticket->id) }}">
                                                Trip Ticket
                                            </flux:menu.item>

                                            <flux:menu.item icon="document-text"
                                                href="{{ route('client.trip-ticket.travel-order.show', $ticket->id) }}">
                                                Travel Order
                                            </flux:menu.item>

                                            <flux:menu.separator />

                                            {{-- DELETE TRIGGER --}}
                                            <flux:modal.trigger :name="'delete-ticket-' . $ticket->id">
                                                <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                                            </flux:modal.trigger>
                                        </flux:menu>
                                    </flux:dropdown>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <flux:icon name="map" class="size-8 text-zinc-400" />
                                        <flux:text variant="strong" class="text-zinc-500">No trip tickets found
                                        </flux:text>
                                        <flux:text size="sm" class="text-zinc-400">Your scheduled bookings will
                                            appear
                                            here.
                                        </flux:text>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>
            {{ $tickets->links() }}
        </div>
    </div>


    {{-- LOOP-DEPENDENT MODALS --}}
    @foreach ($tickets as $ticket)
        <flux:modal :name="'track-ticket-' . $ticket->id" class="max-w-none!">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Document Tracking</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Routing history and office processing
                        logs for your travel order
                        document.</flux:text>
                </div>

                @if (count($ticket->stepper_steps) > 0)
                    <div class="relative flex items-start justify-between mt-8 z-0 pb-4">
                        @foreach ($ticket->stepper_steps as $step)
                            <div class="flex flex-col items-center flex-1 min-w-[150px] max-w-xl text-center relative px-2">

                                @if ($step['has_next_line'])
                                    <div
                                        class="absolute top-[18px] left-[50%] right-[-50%] h-[3px] {{ $step['is_released'] ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-zinc-200 dark:bg-zinc-700' }} -z-10 transition-colors duration-200">
                                    </div>
                                @endif

                                <div
                                    class="flex items-center justify-center size-9 rounded-full border transition-colors duration-200 shadow-none
                                {{ $step['is_released']
                                    ? 'bg-emerald-500 border-emerald-500 text-white font-bold'
                                    : 'bg-zinc-50 dark:bg-zinc-800 border-zinc-300 text-zinc-400 dark:border-zinc-600 dark:text-zinc-500' }}">
                                    @if ($step['is_released'])
                                        <flux:icon name="check" class="size-6 text-white" variant="micro" />
                                    @else
                                        <span class="text-xs font-semibold">{{ $loop->iteration }}</span>
                                    @endif
                                </div>

                                <span
                                    class="mt-3 text-xs font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                                    {{ $step['name'] }}
                                </span>

                                <div
                                    class="mt-3 w-full bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200/60 dark:border-zinc-800/80 rounded-lg p-2 text-left space-y-1.5">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 tracking-wider">RECEIVED</span>
                                        <span
                                            class="text-[11px] font-medium text-zinc-700 dark:text-zinc-300 {{ $step['received_at'] === 'Not Applicable' ? 'italic text-zinc-400 dark:text-zinc-500' : '' }}">
                                            {{ $step['received_at'] }}
                                        </span>
                                    </div>

                                    <div class="h-[1px] bg-zinc-200/60 dark:bg-zinc-800/80 w-full">
                                    </div>

                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 tracking-wider">RELEASED</span>
                                        <span
                                            class="text-[11px] font-medium text-zinc-700 dark:text-zinc-300 {{ $step['released_at'] === 'Not Applicable' ? 'italic text-zinc-400 dark:text-zinc-500' : '' }}">
                                            {{ $step['released_at'] }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div
                        class="flex flex-col items-center justify-center py-12 text-center border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/20">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No
                            History Logged</span>
                        <span class="text-xs text-zinc-400 dark:text-zinc-500 max-w-xs mt-1">This
                            document hasn't completed any tracking movements yet.</span>
                    </div>
                @endif
            </div>
        </flux:modal>

        <flux:modal :name="'view-ticket-' . $ticket->id" class="md:w-[600px]">
            <div>
                <div class="flex gap-4 items-start mb-6">
                    <div>
                        <flux:heading size="lg" class="font-semibold text-zinc-900 dark:text-white">Trip Details
                        </flux:heading>
                    </div>

                    @php
                        $statusData = match ($ticket->status) {
                            'Pending' => ['color' => 'yellow', 'icon' => 'clock'],
                            'Approved' => ['color' => 'blue', 'icon' => 'check-circle'],
                            'Cancelled' => ['color' => 'red', 'icon' => 'x-circle'],
                            'Completed' => ['color' => 'emerald', 'icon' => 'flag'],
                            default => ['color' => 'zinc', 'icon' => 'question-mark-circle'],
                        };
                    @endphp

                    <flux:badge :color="$statusData['color']" :icon="$statusData['icon']" variant="pill" size="sm"
                        class="px-2.5 py-0.5 font-medium">
                        {{ $ticket->status }}
                    </flux:badge>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-6">
                    <flux:field>
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-medium">
                            Destination</flux:label>
                        <flux:text variant="strong" size="lg" class="block mt-1 text-zinc-900 dark:text-white">
                            {{ $ticket->destination }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-medium">
                            Purpose</flux:label>
                        <flux:text class="block mt-1 text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            {{ $ticket->purpose ?? 'No purpose provided' }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-medium">
                            Departure</flux:label>
                        <div class="flex items-center gap-2 mt-1.5 text-zinc-800 dark:text-zinc-200">
                            <flux:icon name="calendar" variant="micro" class="text-zinc-400 dark:text-zinc-500" />
                            <flux:text font="medium">
                                {{ \Carbon\Carbon::parse($ticket->start_date)->format('M d, Y (l)') }}</flux:text>
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-medium">
                            Return</flux:label>
                        <div class="flex items-center gap-2 mt-1.5 text-zinc-800 dark:text-zinc-200">
                            <flux:icon name="calendar" variant="micro" class="text-zinc-400 dark:text-zinc-500" />
                            <flux:text font="medium">{{ \Carbon\Carbon::parse($ticket->end_date)->format('M d, Y (l)') }}
                            </flux:text>
                        </div>
                    </flux:field>
                </div>

                <flux:separator class="my-6" />

                <div>
                    @php
                        $passengers = is_array($ticket->passengers)
                            ? $ticket->passengers
                            : json_decode($ticket->passengers ?? '[]', true);
                    @endphp

                    <div class="flex items-center gap-2 mb-4">
                        <flux:heading size="sm" class="font-semibold text-zinc-800 dark:text-zinc-200">Passenger(s)
                        </flux:heading>
                        <flux:badge size="sm" variant="subtle" class="rounded-full px-2 text-xs">
                            {{ count($passengers) }}
                        </flux:badge>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @forelse($passengers as $passenger)
                            <div
                                class="flex items-center gap-2.5 p-2.5 rounded-lg bg-zinc-50 dark:bg-white/5 border border-zinc-200/60 dark:border-white/10 transition-colors hover:bg-zinc-100/50 dark:hover:bg-white/[0.07]">
                                <flux:icon name="user" variant="micro" class="text-zinc-400 dark:text-zinc-500" />
                                <flux:text size="sm" class="font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ $passenger }}</flux:text>
                            </div>
                        @empty
                            <div
                                class="col-span-2 flex items-center justify-center p-6 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/20">
                                <flux:icon name="user" variant="micro" class="text-zinc-400 mr-1" />
                                <flux:text italic size="sm" class="text-zinc-400 dark:text-zinc-500">Driver only
                                </flux:text>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </flux:modal>

        {{-- DELETE MODAL --}}
        <flux:modal :name="'delete-ticket-' . $ticket->id" class="min-w-[400px]">
            <form action="{{ route('client.booking.destroy', $ticket->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('DELETE')

                <div class="space-y-2">
                    <flux:heading size="lg">Delete Trip Ticket?</flux:heading>
                    <flux:text>
                        Are you sure you want to delete the trip to
                        <strong>{{ $ticket->destination }}</strong>?
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" color="red">
                        Confirm Delete
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endforeach

    {{-- TRIP TICKET --}}
    <flux:modal name="view-ticket" class="md:w-[600px]">
        <div x-data="{ ticket: {} }"
            x-on:open-modal.window="if($event.detail.name === 'view-ticket') ticket = $event.detail.ticket">
            <div class="flex items-center gap-3 mb-6">
                <flux:icon name="information-circle" class="size-6 text-emerald-500" />
                <flux:heading size="lg">Trip Details</flux:heading>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Destination</flux:label>
                    <flux:text x-text="ticket.destination || 'N/A'" class="font-medium text-zinc-800" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <div>
                        <flux:badge x-text="ticket.status" color="emerald" inset="top bottom" />
                    </div>
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>Purpose</flux:label>
                    <flux:text x-text="ticket.purpose || 'No purpose provided'" />
                </flux:field>

                <flux:field>
                    <flux:label>Departure</flux:label>
                    <flux:text x-text="ticket.start_date" />
                </flux:field>

                <flux:field>
                    <flux:label>Return</flux:label>
                    <flux:text x-text="ticket.end_date" />
                </flux:field>
            </div>

            <div class="mt-8 flex justify-end">
                <flux:modal.close>
                    <flux:button variant="filled">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="delete-ticket" class="min-w-[400px]">
        <div x-data="{ deleteRoute: '' }"
            x-on:open-modal.window="if($event.detail.name === 'delete-ticket') deleteRoute = '/client/booking/' + $event.detail.id">
            <form :action="deleteRoute" method="POST" class="space-y-6">
                @csrf
                @method('DELETE')

                <div class="space-y-2">
                    <flux:heading size="lg">Delete Trip Ticket?</flux:heading>
                    <flux:text>This action cannot be undone. This will permanently delete the booking record.</flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" color="red">
                        Confirm Delete
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
@endsection
