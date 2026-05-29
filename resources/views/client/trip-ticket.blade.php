@extends('client.layout')

@section('content')
    <div class="w-full h-full flex flex-col">
        <flux:heading size="xl" level="1">Booked Trips</flux:heading>
        <flux:text class="mt-2 mb-6 text-base">View and manage your trip tickets</flux:text>

        <div class="flex-1 space-y-4">
            {{-- Search and Filter Bar --}}
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
                                                href="{{ route('client.travel-order.show', $ticket->id) }}">
                                                Travel Order
                                            </flux:menu.item>

                                            <flux:menu.separator />

                                            {{-- DELETE TRIGGER --}}
                                            <flux:modal.trigger :name="'delete-ticket-' . $ticket->id">
                                                <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                                            </flux:modal.trigger>
                                        </flux:menu>
                                    </flux:dropdown>

                                    <flux:modal :name="'track-ticket-' . $ticket->id" class="md:w-[700px]">
                                        <div class="space-y-6">
                                            <div class="flex items-center gap-3">
                                                <flux:icon name="document-check" class="size-6 text-emerald-500" />

                                                <div>
                                                    <flux:heading size="lg">Document Tracking</flux:heading>
                                                    <flux:text size="sm" class="text-zinc-500">Real-time processing
                                                        updates for your trip routing request.</flux:text>
                                                </div>
                                            </div>

                                            @php
                                                $definedRoutes = ['PMEU', 'BUDGET', 'ORD/OIC', 'ADMIN'];
                                            @endphp

                                            {{-- Horizontal Timeline Wrapper --}}
                                            <div class="relative flex items-start justify-between mt-8 z-0">
                                                @foreach ($definedRoutes as $index => $routeName)
                                                    @php
                                                        $stepData = $ticket->documentTrackings->firstWhere(
                                                            'route',
                                                            $routeName,
                                                        );
                                                        $hasStep = !is_null($stepData);

                                                        // Determine if the next step exists in the log to shade the connecting line
                                                        $nextRouteName = $definedRoutes[$index + 1] ?? null;
                                                        $nextStepExists = $nextRouteName
                                                            ? $ticket->documentTrackings->contains(
                                                                'route',
                                                                $nextRouteName,
                                                            )
                                                            : false;

                                                        // Connecting line color indicator
                                                        $lineColor =
                                                            $hasStep && ($stepData->date_released || $nextStepExists)
                                                                ? 'bg-emerald-500 dark:bg-emerald-500'
                                                                : 'bg-zinc-200 dark:bg-zinc-700';
                                                    @endphp

                                                    <div class="flex flex-col items-center flex-1 text-center relative">

                                                        {{-- Connecting Line Segments --}}
                                                        @if ($index < count($definedRoutes) - 1)
                                                            <div
                                                                class="absolute top-4 left-[50%] right-[-50%] h-0.5 {{ $lineColor }} -z-10 transition-colors duration-200">
                                                            </div>
                                                        @endif

                                                        {{-- Node Step Marker --}}
                                                        <div
                                                            class="flex items-center justify-center size-9 rounded-full border-2 transition-colors duration-200 shadow-sm bg-white dark:bg-zinc-800
                        {{ $hasStep ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-zinc-300 text-zinc-400' }}">

                                                            @if ($hasStep && $stepData->date_released)
                                                                {{-- Check icon shows it completed this specific routing node --}}
                                                                <flux:icon name="check" class="size-4 text-emerald-500" />
                                                            @else
                                                                <span
                                                                    class="text-sm font-semibold">{{ $index + 1 }}</span>
                                                            @endif
                                                        </div>

                                                        {{-- Department Label --}}
                                                        <span
                                                            class="mt-2 text-xs font-bold tracking-wide uppercase {{ $hasStep ? 'text-zinc-800 dark:text-zinc-200' : 'text-zinc-400' }}">
                                                            {{ $routeName }}
                                                        </span>

                                                        {{-- Timestamps Metadata --}}
                                                        <div
                                                            class="mt-3 space-y-1 text-[11px] px-1 w-full text-zinc-600 dark:text-zinc-400">
                                                            {{-- Received Entry --}}
                                                            <div
                                                                class="flex flex-col items-center bg-zinc-50 dark:bg-zinc-900/50 p-1.5 rounded border border-zinc-100 dark:border-zinc-800/60">
                                                                <span
                                                                    class="text-[9px] text-zinc-400 font-medium uppercase tracking-tight">Received</span>
                                                                @if ($hasStep && $stepData->date_received)
                                                                    <span
                                                                        class="font-medium text-zinc-800 dark:text-zinc-200 mt-0.5">
                                                                        {{ \Carbon\Carbon::parse($stepData->date_received)->format('M d, h:i A') }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-zinc-400 italic mt-0.5">--:--</span>
                                                                @endif
                                                            </div>

                                                            {{-- Released Entry --}}
                                                            <div
                                                                class="flex flex-col items-center bg-zinc-50 dark:bg-zinc-900/50 p-1.5 rounded border border-zinc-100 dark:border-zinc-800/60">
                                                                <span
                                                                    class="text-[9px] text-zinc-400 font-medium uppercase tracking-tight">Released</span>
                                                                @if ($hasStep && $stepData->date_released)
                                                                    <span
                                                                        class="font-medium text-zinc-800 dark:text-zinc-200 mt-0.5">
                                                                        {{ \Carbon\Carbon::parse($stepData->date_released)->format('M d, h:i A') }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-zinc-400 italic mt-0.5">--:--</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="flex justify-end pt-4">
                                                <flux:modal.close>
                                                    <flux:button variant="filled">Close Tracking</flux:button>
                                                </flux:modal.close>
                                            </div>
                                        </div>
                                    </flux:modal>

                                    <flux:modal :name="'view-ticket-' . $ticket->id" class="md:w-[600px]">
                                        <div class="space-y-6">
                                            <div class="flex items-center gap-3">
                                                <flux:icon name="information-circle" class="size-6 text-emerald-500" />
                                                <flux:heading size="lg">Trip Details</flux:heading>
                                            </div>

                                            <div class="grid grid-cols-2 gap-6">
                                                <flux:field>
                                                    <flux:label>Destination</flux:label>
                                                    <flux:text class="font-medium text-zinc-800 dark:text-white">
                                                        {{ $ticket->destination }}</flux:text>
                                                </flux:field>

                                                <flux:field>
                                                    <flux:label>Status</flux:label>
                                                    <div>
                                                        <flux:badge color="emerald" inset="top bottom">
                                                            {{ $ticket->status }}</flux:badge>
                                                    </div>
                                                </flux:field>

                                                <flux:field class="col-span-2">
                                                    <flux:label>Purpose</flux:label>
                                                    <flux:text>{{ $ticket->purpose ?? 'No purpose provided' }}</flux:text>
                                                </flux:field>

                                                <flux:field class="col-span-2">
                                                    <flux:label>Passenger(s)</flux:label>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @php
                                                            $passengers = is_array($ticket->passengers)
                                                                ? $ticket->passengers
                                                                : json_decode($ticket->passengers ?? '[]', true);
                                                        @endphp

                                                        @forelse($passengers as $passenger)
                                                            <flux:badge variant="outline" size="sm" icon="user">
                                                                {{ $passenger }}</flux:badge>
                                                        @empty
                                                            <flux:text size="sm" class="italic text-zinc-400">Driver
                                                                only</flux:text>
                                                        @endforelse
                                                    </div>
                                                </flux:field>

                                                <flux:field>
                                                    <flux:label>Departure</flux:label>
                                                    <flux:text>
                                                        {{ \Carbon\Carbon::parse($ticket->start_date)->format('M d, Y') }}
                                                    </flux:text>
                                                </flux:field>

                                                <flux:field>
                                                    <flux:label>Return</flux:label>
                                                    <flux:text>
                                                        {{ \Carbon\Carbon::parse($ticket->end_date)->format('M d, Y') }}
                                                    </flux:text>
                                                </flux:field>
                                            </div>

                                            <div class="flex justify-end">
                                                <flux:modal.close>
                                                    <flux:button variant="filled">Close</flux:button>
                                                </flux:modal.close>
                                            </div>
                                        </div>
                                    </flux:modal>

                                    {{-- DELETE MODAL --}}
                                    <flux:modal :name="'delete-ticket-' . $ticket->id" class="min-w-[400px]">
                                        <form action="{{ route('client.booking.destroy', $ticket->id) }}" method="POST"
                                            class="space-y-6">
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

    {{-- MODALS --}}

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
