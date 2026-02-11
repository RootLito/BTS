@extends('client.layout')

@section('content')
<div class="w-full h-full flex flex-col">
    <flux:heading size="xl" level="1">Trip Tickets</flux:heading>
    <flux:text class="mt-2 mb-6 text-base">View and manage your trip tickets</flux:text>

    <div class="flex-1 space-y-4">
        {{-- Search and Filter Bar --}}
        <div class="flex gap-2">
            <flux:input icon="magnifying-glass" placeholder="Search trips..." class="flex-1" />

            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">Sort by</flux:button>
                <flux:menu>
                    <flux:menu.radio.group>
                        <flux:menu.radio checked>Latest activity</flux:menu.radio>
                        <flux:menu.radio>Date created</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>

            <flux:button variant="primary" color="emerald">Filter</flux:button>

            <flux:spacer />

            <flux:button href="{{ route('client.booking') }}" variant="primary" color="emerald" icon="plus">
                New Booking
            </flux:button>
        </div>

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
                        {{-- Destination & Purpose --}}
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-white">{{ $ticket->destination
                                    }}</span>
                                <span class="text-xs text-zinc-500 truncate max-w-[200px]">{{ $ticket->purpose }}</span>
                            </div>


                        </flux:table.cell>

                        {{-- Dates --}}
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

                        {{-- Driver & Vehicle --}}
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
                                {{-- First Passenger Name --}}
                                <span class="font-medium text-zinc-800 dark:text-white">
                                    {{ $passengerList[0] }}
                                </span>

                                {{-- Subtext with pluralization logic --}}
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

                        {{-- Status --}}
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

                        {{-- Actions --}}
                        {{-- <flux:dropdown>
                            <flux:button icon-trailing="chevron-down" variant="ghost">Actions</flux:button>

                            <flux:menu>
                                <flux:menu.item icon="eye" href="{{ route('trips.show', $trip->id) }}">
                                    View
                                </flux:menu.item>

                                <flux:menu.separator />

                                <flux:menu.item icon="ticket"
                                    href="{{ route('client.trip-ticket', ['id' => $trip->id]) }}">
                                    Trip Ticket
                                </flux:menu.item>

                                <flux:menu.item icon="document-text"
                                    href="{{ route('client.travel-order', ['id' => $trip->id]) }}">
                                    Travel Order
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown> --}}
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button icon-trailing="chevron-down" class="text-sm">Actions</flux:button>
                                <flux:menu>
                                    <flux:menu.item icon="eye">
                                        View
                                    </flux:menu.item>

                                    <flux:menu.item icon="ticket">
                                        Trip Ticket
                                    </flux:menu.item>

                                    <flux:menu.item icon="document-text">
                                        Travel Order
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
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
@endsection