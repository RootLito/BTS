@extends('admin.layout')

@section('content')
    <div class="flex items-center gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1">Booking Summary</flux:heading>
            <flux:text class="text-base">Reviewing details for Trip #{{ str_pad($tripTicket->id, 4, '0', STR_PAD_LEFT) }}
            </flux:text>
        </div>
    </div>

    <div class="mt-10 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <flux:card>
                    <div class="flex justify-between items-start mb-6">
                        <flux:heading size="lg">Trip Information</flux:heading>

                        @php
                            $statusData = match ($tripTicket->status) {
                                'Pending' => ['color' => 'yellow', 'icon' => 'clock'],
                                'Approved' => ['color' => 'emerald', 'icon' => 'check-circle'],
                                'Cancelled' => ['color' => 'red', 'icon' => 'x-circle'],
                                'Completed' => ['color' => 'blue', 'icon' => 'flag'],
                                default => ['color' => 'zinc', 'icon' => 'question-mark-circle'],
                            };
                        @endphp

                        <flux:badge :color="$statusData['color']" :icon="$statusData['icon']" inset="top bottom"
                            size="sm">
                            {{ $tripTicket->status }}
                        </flux:badge>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-4">
                        <div>
                            <flux:label>Destination</flux:label>
                            <flux:text variant="strong" class="block mt-1">{{ $tripTicket->destination }}</flux:text>
                        </div>

                        <div>
                            <flux:label>Purpose</flux:label>
                            <flux:text class="block mt-1 text-zinc-600 dark:text-zinc-400">{{ $tripTicket->purpose }}
                            </flux:text>
                        </div>

                        <div>
                            <flux:label>Departure Date</flux:label>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:icon name="calendar" variant="micro" class="text-zinc-400" />
                                <flux:text>{{ $tripTicket->start_date->format('M d, Y (l)') }}</flux:text>
                            </div>
                        </div>

                        <div>
                            <flux:label>Return Date</flux:label>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:icon name="calendar" variant="micro" class="text-zinc-400" />
                                <flux:text>{{ $tripTicket->end_date->format('M d, Y (l)') }}</flux:text>
                            </div>
                        </div>
                    </div>

                    <flux:separator class="my-6" />

                    <flux:heading size="md" class="mb-4">Passengers</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @forelse($tripTicket->passengers ?? [] as $passenger)
                            <div
                                class="flex items-center gap-2 p-2 rounded-md bg-zinc-50 dark:bg-white/5 border border-zinc-200 dark:border-white/10">
                                <flux:icon name="user" variant="micro" class="text-zinc-400" />
                                <flux:text size="sm">{{ $passenger }}</flux:text>
                            </div>
                        @empty
                            <flux:text italic>No passengers listed.</flux:text>
                        @endforelse
                    </div>
                </flux:card>

                <flux:card>
                    <div class="flex items-center justify-between mb-6">
                        <flux:heading size="md">Assigned Personnel</flux:heading>
                        @if ($tripTicket->driver_id)
                            <flux:badge size="sm" color="emerald" variant="subtle" icon="check-circle">Driver Assigned
                            </flux:badge>
                        @else
                            <flux:badge size="sm" color="yellow" variant="subtle" icon="exclamation-circle">Pending
                                Assignment
                            </flux:badge>
                        @endif
                    </div>

                    <div class="mb-8 p-4 rounded-xl bg-zinc-50 dark:bg-white/5 border border-zinc-200 dark:border-white/10">
                        <form action="{{ route('admin.booking.assign', $tripTicket->id) }}" method="POST"
                            class="flex items-end gap-3">
                            @csrf @method('PUT')

                            <div class="flex-1">
                                <flux:label size="sm" class="mb-2">Change or Assign Driver</flux:label>
                                <flux:select name="driver_id" searchable placeholder="Select driver..." size="sm">
                                    <flux:select.option value="" :selected="!$tripTicket->driver_id">
                                        Don't Assign
                                    </flux:select.option>

                                    @foreach ($drivers as $driver)
                                        @php
                                            $isAssigned = $tripTicket->driver_id == $driver->id;
                                            if ($driver->status !== 'Available' && !$isAssigned) {
                                                continue;
                                            }
                                        @endphp

                                        <flux:select.option value="{{ $driver->id }}" :selected="$isAssigned">
                                            {{ $driver->name }}
                                            — {{ $driver->vehicle->vehicle ?? 'No Vehicle' }}

                                            @if ($driver->status !== 'Available')
                                                ({{ $driver->status }})
                                            @endif
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <flux:button type="submit" variant="primary" color="emerald" icon="check" size="sm">
                                Update
                            </flux:button>
                        </form>

                    </div>

                    <div class="flex items-stretch gap-6">
                        <div class="flex-1">
                            <flux:text size="xs" class="uppercase tracking-wider font-semibold text-zinc-500 mb-2">
                                Current
                                Driver</flux:text>
                            <div class="flex items-center gap-3">
                                @php
                                    $initials = collect(explode(' ', $tripTicket->driver->name ?? 'N A'))
                                        ->map(fn($n) => $n[0])
                                        ->take(2)
                                        ->join('');
                                @endphp
                                <flux:avatar initials="{{ $initials }}" />
                                <div>
                                    <div class="font-medium text-zinc-800 dark:text-white">
                                        {{ $tripTicket->driver->name ?? 'No Driver Assigned' }}
                                    </div>
                                    <div class="text-xs text-zinc-500">
                                        {{ $tripTicket->driver->contact ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <flux:separator vertical />

                        <div class="flex-1">
                            <flux:text size="xs" class="uppercase tracking-wider font-semibold text-zinc-500 mb-2">
                                Current
                                Vehicle</flux:text>
                            <div class="mt-1">
                                <div class="font-medium text-emerald-600 dark:text-emerald-400">
                                    {{ $tripTicket->vehicle->vehicle ?? 'No Vehicle Assigned' }}
                                </div>
                                <div class="text-sm text-zinc-500">
                                    Plate: <span class="font-mono">{{ $tripTicket->vehicle->plate_no ?? 'N/A' }}</span>
                                </div>
                                <flux:badge size="sm" class="mt-2" variant="subtle">
                                    {{ $tripTicket->vehicle->type ?? 'Standard' }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>

            <div class="space-y-6">
                <flux:card>
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="sm">Documents</flux:heading>
                        <flux:badge size="sm" variant="subtle" color="zinc">Pending Release</flux:badge>
                    </div>

                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between p-3 border border-dashed rounded-lg border-zinc-300 dark:border-zinc-700 bg-zinc-50/50 dark:bg-white/5">
                            <div class="flex items-center gap-3">
                                <flux:icon name="document-text" class="text-zinc-400" />
                                <flux:text size="sm" class="font-medium">Travel Order</flux:text>
                            </div>
                            <flux:button variant="subtle" size="xs" icon="eye" />
                        </div>

                        <div
                            class="flex items-center justify-between p-3 border border-dashed rounded-lg border-zinc-300 dark:border-zinc-700 bg-zinc-50/50 dark:bg-white/5">
                            <div class="flex items-center gap-3">
                                <flux:icon name="ticket" class="text-zinc-400" />
                                <flux:text size="sm" class="font-medium">Trip Ticket</flux:text>
                            </div>
                            <flux:button variant="subtle" size="xs" icon="eye" />
                        </div>
                        <flux:button variant="filled" color="zinc" class="w-full" icon="paper-airplane">
                            Release Documents
                        </flux:button>
                    </div>
                </flux:card>

                <flux:card>
                    <flux:heading size="sm" class="mb-4">Actions</flux:heading>

                    <form action="{{ route('admin.booking.status', $tripTicket->id) }}" method="POST"
                        class="flex flex-col gap-2">
                        @csrf @method('PUT')

                        <flux:button type="submit" name="status" value="Approved" variant="primary" color="emerald"
                            icon="check" :disabled="$tripTicket->status === 'Approved'">
                            Approve Trip
                        </flux:button>

                        <flux:button type="submit" name="status" value="Cancelled" variant="filled" color="red"
                            icon="x-mark" :disabled="$tripTicket->status === 'Cancelled'">
                            Cancel Trip
                        </flux:button>
                    </form>
                </flux:card>
            </div>
        </div>
    </div>
@endsection
