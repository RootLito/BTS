@extends('admin.layout')

@section('content')
<flux:heading size="xl" level="1">Booking</flux:heading>
<flux:text class="mt-2 mb-6 text-base">Manage ticketing and travel orders efficiently</flux:text>

<div class="mt-10">
    <form action="{{ route('admin.booking') }}" method="GET">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-100">
                <flux:input name="search" value="{{ request('search') }}" icon="magnifying-glass"
                    placeholder="Search Office or Destination..." />
            </div>
            <div class="flex gap-2 items-center">
                <flux:input type="date" name="start_date" value="{{ request('start_date') }}" />
                <flux:input type="date" name="end_date" value="{{ request('end_date') }}" />
            </div>
            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" color="emerald" icon="adjustments-horizontal">
                    Filter
                </flux:button>
                @if(request()->anyFilled(['search', 'start_date', 'end_date']))
                <flux:button href="{{ route('admin.booking') }}" variant="filled" color="zinc" icon="x-mark">
                    Clear
                </flux:button>
                @endif
            </div>
        </div>
    </form>
    <flux:card class="space-y-6 overflow-hidden mt-10">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Office</flux:table.column>
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
                    <flux:table.cell></flux:table.cell>
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
                        <flux:button href="{{ route('admin.booking.show', $ticket->id) }}" icon="eye" size="xs"
                            variant="filled">Full Details</flux:button>
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
@endsection