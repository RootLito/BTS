@extends('admin.layout')

@section('content')
<flux:heading size="xl" level="1">Drivers</flux:heading>
<flux:text class="mt-2 mb-6 text-base">Manage drivers information</flux:text>

<div class="my-10 flex gap-2">
    <div class="w-100">
        <flux:input icon="magnifying-glass" placeholder="Search drivers" />
    </div>
    <flux:spacer />

    {{-- CREATE MODAL --}}
    <flux:modal.trigger name="add-driver">
        <flux:button variant="primary" color="emerald" icon="plus">Add Driver</flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-driver" class="md:w-96">
        <form action="{{ route('driver.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <flux:heading size="lg">Add New Driver</flux:heading>
                <flux:text class="mt-2">Enter driver details below.</flux:text>
            </div>
            <flux:input label="Name" name="name" placeholder="Full Name" required />
            <flux:input label="Contact Number" type="tel" name="contact" placeholder="09XXXXXXXXX" required />
            <flux:select label="Assign Vehicle" name="vehicle_id" placeholder="Select a vehicle...">
                <option value="">No Vehicle Assigned</option>
                @foreach($vehicles as $vehicle)
                @php
                $isAssigned = in_array($vehicle->id, $assignedVehicleIds);
                @endphp
                <option value="{{ $vehicle->id }}" {{ $isAssigned ? 'disabled' : '' }}>
                    {{ $vehicle->vehicle }} ({{ $vehicle->plate_no }}) {{ $isAssigned ? '— Already Assigned' : '' }}
                </option>
                @endforeach
            </flux:select>

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary" color="emerald">Register Driver</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

<flux:card class="space-y-6 overflow-y-auto">
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Contact</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column class="w-0 text-right">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($drivers as $driver)
            <flux:table.row>
                <flux:table.cell>
                    <div class="flex flex-col">
                        <span class="font-medium text-zinc-800 dark:text-white">{{ $driver->name }}</span>
                        @if($driver->vehicle)
                        <span class="text-xs text-zinc-500">
                            {{ $driver->vehicle->vehicle }} — {{ $driver->vehicle->plate_no }}
                        </span>
                        @else
                        <span class="text-xs text-zinc-400 italic">No vehicle assigned</span>
                        @endif
                    </div>
                </flux:table.cell>
                <flux:table.cell variant="strong">{{ $driver->contact }}</flux:table.cell>
                <flux:table.cell>
                    @php
                    $color = match($driver->status) {
                    'Available' => 'emerald',
                    'On Trip' => 'yellow',
                    'Leave' => 'red',
                    default => 'zinc'
                    };
                    @endphp
                    <flux:badge color="{{ $color }}" size="sm" inset="top bottom">{{ $driver->status }}</flux:badge>
                </flux:table.cell>
                <flux:table.cell class="shrink-0">
                    <div class="flex items-center justify-end gap-2">
                        <flux:modal.trigger name="edit-driver-{{ $driver->id }}">
                            <flux:button variant="primary" size="sm" icon="pencil-square" inset="top bottom">
                                Update
                            </flux:button>
                        </flux:modal.trigger>
                        <flux:modal.trigger name="delete-driver-{{ $driver->id }}">
                            <flux:button variant="primary" color="red" size="sm" icon="trash" inset="top bottom">
                                Delete
                            </flux:button>
                        </flux:modal.trigger>
                    </div>

                    {{-- Modals remain inside the loop to capture $driver context --}}
                    <flux:modal name="edit-driver-{{ $driver->id }}" class="md:w-96">
                        <form action="{{ route('driver.update', $driver) }}" method="POST" class="space-y-6">
                            @csrf @method('PUT')
                            <flux:heading size="lg">Update Driver</flux:heading>
                            <flux:input label="Name" name="name" value="{{ $driver->name }}" />
                            <flux:input label="Contact" name="contact" value="{{ $driver->contact }}" />
                            <flux:select label="Assign Vehicle" name="vehicle_id">
                                <option value="">No Vehicle Assigned</option>
                                @foreach($vehicles as $vehicle)
                                @php
                                $isAssignedToOthers = in_array($vehicle->id, $assignedVehicleIds) && $driver->vehicle_id
                                != $vehicle->id;
                                @endphp
                                <option value="{{ $vehicle->id }}" {{ $driver->vehicle_id == $vehicle->id ? 'selected' :
                                    '' }}
                                    {{ $isAssignedToOthers ? 'disabled' : '' }}>

                                    {{ $vehicle->vehicle }} ({{ $vehicle->plate_no }})
                                    {{ $isAssignedToOthers ? '— Already Assigned' : '' }}
                                </option>
                                @endforeach
                            </flux:select>
                            <flux:select label="Status" name="status">
                                <option value="Available" {{ $driver->status == 'Available' ? 'selected' : ''
                                    }}>Available</option>
                                <option value="On Trip" {{ $driver->status == 'On Trip' ? 'selected' : '' }}>On Trip
                                </option>
                                <option value="Leave" {{ $driver->status == 'Leave' ? 'selected' : '' }}>Leave</option>
                            </flux:select>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary">Save Changes</flux:button>
                            </div>
                        </form>
                    </flux:modal>

                    <flux:modal name="delete-driver-{{ $driver->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <flux:heading size="lg">Delete Driver?</flux:heading>
                            <flux:text>Are you sure you want to remove <b>{{ $driver->name }}</b>?</flux:text>
                            <div class="flex gap-2">
                                <flux:spacer />
                                <form action="{{ route('driver.destroy', $driver) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <flux:button type="submit" color="red" variant="primary">Confirm Delete
                                    </flux:button>
                                </form>
                            </div>
                        </div>
                    </flux:modal>
                </flux:table.cell>
            </flux:table.row>
            @empty
            {{-- EMPTY STATE ROW --}}
            <flux:table.row>
                <flux:table.cell colspan="4" class="py-10 text-center">
                    <div class="flex flex-col items-center justify-center space-y-2">
                        <flux:icon name="users" class="size-10 text-zinc-500" />
                        <flux:text variant="strong" class="text-zinc-500">No drivers saved yet</flux:text>
                        <flux:text size="sm" class="text-zinc-400">Click "Add Driver" to start building your team.
                        </flux:text>
                    </div>
                </flux:table.cell>
            </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</flux:card>
@endsection