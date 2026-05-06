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
                                'Approved' => ['color' => 'blue', 'icon' => 'check-circle'],
                                'Cancelled' => ['color' => 'red', 'icon' => 'x-circle'],
                                'Completed' => ['color' => 'emerald', 'icon' => 'flag'],
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

                                <div class="flex-1 flex gap-2">
                                    <flux:dropdown class="flex-1">
                                        <flux:button variant="filled" icon-trailing="chevron-down" size="sm"
                                            class="w-full justify-start gap-2">
                                            {{ $tripTicket->driver->name ?? 'Select a Driver...' }}
                                        </flux:button>

                                        <flux:menu class="min-w-[250px]">
                                            <form action="{{ route('admin.booking.assign', $tripTicket->id) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="driver_id" value="">
                                                <flux:menu.item type="submit" :selected="!$tripTicket->driver_id">
                                                    Select Driver
                                                </flux:menu.item>
                                            </form>

                                            <flux:menu.separator />

                                            @foreach ($drivers as $driver)
                                                @php
                                                    $isAssigned = $tripTicket->driver_id == $driver->id;
                                                    $isBusyOnTheseDates = $driver->is_busy && !$isAssigned;
                                                @endphp

                                                <form action="{{ route('admin.booking.assign', $tripTicket->id) }}"
                                                    method="POST">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="driver_id" value="{{ $driver->id }}">

                                                    <flux:menu.item type="submit" :disabled="$isBusyOnTheseDates"
                                                        :selected="$isAssigned">
                                                        <div class="flex flex-col">
                                                            <span>{{ $driver->name }}</span>
                                                            <span class="text-xs text-zinc-500">
                                                                {{ $driver->vehicle->vehicle ?? 'No Vehicle' }}
                                                                @if ($isBusyOnTheseDates)
                                                                    — <span
                                                                        class="text-red-500 font-semibold italic">Not available on this date(s)</span>
                                                                @elseif ($driver->status !== 'Available')
                                                                    — ({{ $driver->status }})
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </flux:menu.item>
                                                </form>
                                            @endforeach
                                        </flux:menu>
                                    </flux:dropdown>
                                    <flux:button type="submit" variant="primary" color="emerald" icon="check"
                                        size="sm" :disabled="in_array($tripTicket->status, ['Cancelled', 'Completed'])"
                                        class="w-64">
                                        Update
                                    </flux:button>
                                </div>
                            </div>
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
                    <flux:heading size="sm" class="mb-4">Actions</flux:heading>

                    <div class="flex flex-col gap-2">
                        <form action="{{ route('admin.booking.status', $tripTicket->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="Approved">
                            <flux:button type="submit" variant="primary" color="emerald" icon="check" class="w-full"
                                :disabled="in_array($tripTicket->status, ['Approved', 'Cancelled', 'Completed'])">
                                Approve Trip
                            </flux:button>
                        </form>

                        <flux:modal.trigger name="cancel-modal">
                            <flux:button variant="danger" icon="x-mark" class="w-full"
                                :disabled="in_array($tripTicket->status, ['Approved', 'Cancelled', 'Completed'])">
                                Cancel Trip
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                </flux:card>

                <flux:modal name="cancel-modal" class="md:w-[450px]">
                    <form action="{{ route('admin.booking.status', $tripTicket->id) }}" method="POST"
                        class="space-y-6">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="Cancelled">

                        <div>
                            <flux:heading size="lg">Cancel Trip</flux:heading>
                            <flux:text>Please provide a reason for cancelling Trip
                                #{{ str_pad($tripTicket->id, 4, '0', STR_PAD_LEFT) }}.</flux:text>
                        </div>

                        <flux:textarea label="Cancellation Note" name="note" placeholder="Reason for cancellation..."
                            required />

                        <div class="flex gap-2">
                            <flux:spacer />
                            <flux:modal.close>
                                <flux:button variant="ghost">Go Back</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" variant="danger">Confirm Cancellation
                            </flux:button>
                        </div>
                    </form>
                </flux:modal>
            </div>
        </div>
    </div>
@endsection
