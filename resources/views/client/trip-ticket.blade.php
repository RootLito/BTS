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

                @if(request()->anyFilled(['search', 'sort']))
                <flux:button href="{{ route('client.trip-ticket') }}" variant="filled" color="zinc" icon="x-mark">
                    Clear
                </flux:button>
                @endif
            </div>


            <flux:button href="{{ route('client.booking') }}" variant="primary" color="emerald" icon="plus"
                class="ms-auto">
                New Booking
            </flux:button>
        </form>

        {{-- Table Card --}}
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
                                <span class="font-medium text-zinc-800 dark:text-white">{{ $ticket->destination
                                    }}</span>
                                <span class="text-xs text-zinc-500 truncate max-w-[200px]">{{ $ticket->purpose }}</span>
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
                                <span class="text-xs text-zinc-400">{{ $ticket->vehicle->vehicle ?? 'No Vehicle'
                                    }}</span>
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
                                @if(count($passengerList) > 0)
                                <span class="font-medium text-zinc-800 dark:text-white">
                                    {{ $passengerList[0] }}
                                </span>
                                @if($extraCount > 0)
                                <span class="text-xs text-zinc-400 cursor-help"
                                    title="{{ implode(', ', array_slice($passengerList, 1)) }}">
                                    +{{ $extraCount }} other {{ $extraCount > 1 ? 'passengers' : 'passenger' }}
                                </span>
                                @endif
                                @else
                                <span class="text-xs text-zinc-400 italic">Driver only</span>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @php
                            $statusColor = match($ticket->status) {
                            'Pending' => 'yellow',
                            'Approved' => 'emerald',
                            'Cancelled' => 'red',
                            'Completed' => 'blue',
                            default => 'zinc',
                            };
                            @endphp
                            <flux:badge color="{{ $statusColor }}" size="sm" inset="top bottom">{{ $ticket->status }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button icon-trailing="chevron-down" class="text-sm">Actions</flux:button>
                                <flux:menu>
                                    <flux:menu.item icon="eye"
                                        x-on:click="$dispatch('open-modal', { name: 'view-ticket', ticket: {{ json_encode($ticket) }} })">
                                        View
                                    </flux:menu.item>
                                    <flux:menu.item icon="ticket" href="{{ route('client.trip-ticket.ticket') }}">
                                        Trip Ticket
                                    </flux:menu.item>
                                    <flux:menu.item icon="document-text" href="{{ route('client.trip-ticket.to') }}">
                                        Travel Order
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item variant="danger" icon="trash"
                                        x-on:click="$dispatch('open-modal', { name: 'delete-ticket', id: {{ $ticket->id }} })">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <flux:icon name="map" class="size-8 text-zinc-400" />
                                <flux:text variant="strong" class="text-zinc-500">No trip tickets found</flux:text>
                                <flux:text size="sm" class="text-zinc-400">Your scheduled bookings will appear here.
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
                <flux:text x-text="ticket.destination" class="font-medium text-zinc-800" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <div>
                    <flux:badge x-text="ticket.status" color="emerald" inset="top bottom" />
                </div>
            </flux:field>

            <flux:field class="col-span-2">
                <flux:label>Purpose</flux:label>
                <flux:text x-text="ticket.purpose" />
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