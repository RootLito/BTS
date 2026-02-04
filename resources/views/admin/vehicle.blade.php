@extends('admin.layout')

@section('content')
<flux:heading size="xl" level="1">Vehicles</flux:heading>
<flux:text class="mt-2 mb-6 text-base">Manage your fleet of vehicles</flux:text>


<div class="my-10 flex gap-2">
    <flux:input icon="magnifying-glass" placeholder="Search vehicles" />
    {{-- <flux:dropdown>
        <flux:button icon:trailing="chevron-down">Sort by name</flux:button>

        <flux:menu>
            <flux:menu.radio.group wire:model="sortBy">
                <flux:menu.radio>Ascending</flux:menu.radio>
                <flux:menu.radio>Descending</flux:menu.radio>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
    <flux:dropdown>
        <flux:button icon:trailing="chevron-down">Sort by availability</flux:button>

        <flux:menu>
            <flux:menu.radio.group wire:model="sortBy">
                <flux:menu.radio checked>Available</flux:menu.radio>
                <flux:menu.radio>On Trip</flux:menu.radio>
                <flux:menu.radio>Maintainance</flux:menu.radio>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown> --}}
    <flux:modal.trigger name="add-vehicle">
        <flux:button variant="primary" color="emerald" icon="plus">
            Add Vehicle
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-vehicle" class="md:w-96">
        <form action="{{ route('vehicle.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <flux:heading size="lg">Add New Vehicle</flux:heading>
                <flux:text class="mt-2">Enter the details of the vehicle to add it to your fleet.</flux:text>
            </div>
            <flux:input label="Vehicle Name / Model" name="vehicle" placeholder="Enter vehicle name or model"
                required />
            <flux:input label="Plate Number" name="plate_no" placeholder="Enter plate number" required />
            <flux:select label="Type" name="type">
                <option value="Car">Car</option>
                <option value="Van">Van</option>
                <option value="Truck">Truck</option>
                <option value="SUV">SUV</option>
            </flux:select>

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary" color="emerald">Add Vehicle</flux:button>
            </div>
        </form>
    </flux:modal>

</div>


<flux:card class="row-span-2 col-start-3 row-start-1 space-y-6 overflow-y-auto">
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Vehicle</flux:table.column>
            <flux:table.column>Plate Number</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column class="w-0 text-right">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($vehicles as $vehicle)
            <flux:table.row>
                <flux:table.cell class="font-medium text-zinc-800">{{ $vehicle->vehicle }}</flux:table.cell>
                <flux:table.cell>{{ $vehicle->plate_no }}</flux:table.cell>
                <flux:table.cell>{{ $vehicle->type }}</flux:table.cell>
                <flux:table.cell>
                    <flux:badge
                        color="{{ $vehicle->status === 'Available' ? 'emerald' : ($vehicle->status === 'On Trip' ? 'yellow' : 'red') }}"
                        size="sm" inset="top bottom">
                        {{ $vehicle->status }}
                    </flux:badge>
                </flux:table.cell>
                <flux:table.cell class="shrink-0">
                    <div class="flex items-center gap-2">
                        <flux:modal.trigger name="edit-vehicle-{{ $vehicle->id }}">
                            <flux:button variant="primary" size="sm" icon="pencil-square" inset="top bottom">Update</flux:button>
                        </flux:modal.trigger>
                        <flux:modal.trigger name="confirm-delete-veh-{{ $vehicle->id }}">
                            <flux:button variant="primary" color="red" size="sm" icon="trash" inset="top bottom">Delete</flux:button>
                        </flux:modal.trigger>
                    </div>

                    {{-- Edit Modal --}}
                    <flux:modal name="edit-vehicle-{{ $vehicle->id }}" class="md:w-96">
                        <form action="{{ route('vehicle.update', $vehicle) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <flux:heading size="lg">Edit {{ $vehicle->vehicle }}</flux:heading>

                            <flux:input name="vehicle" label="Model" value="{{ $vehicle->vehicle }}" />
                            <flux:input name="plate_no" label="Plate Number" value="{{ $vehicle->plate_no }}" />

                            <flux:select name="type" label="Vehicle Type">
                                <option value="Car" {{ $vehicle->type == 'Car' ? 'selected' : '' }}>Car</option>
                                <option value="Van" {{ $vehicle->type == 'Van' ? 'selected' : '' }}>Van</option>
                                <option value="Truck" {{ $vehicle->type == 'Truck' ? 'selected' : '' }}>Truck</option>
                                <option value="SUV" {{ $vehicle->type == 'SUV' ? 'selected' : '' }}>SUV</option>
                            </flux:select>

                            <flux:select name="status" label="Current Status">
                                <option value="Available" {{ $vehicle->status == 'Available' ? 'selected' : ''
                                    }}>Available</option>
                                <option value="On Trip" {{ $vehicle->status == 'On Trip' ? 'selected' : '' }}>On Trip
                                </option>
                                <option value="Maintenance" {{ $vehicle->status == 'Maintenance' ? 'selected' : ''
                                    }}>Maintenance</option>
                            </flux:select>

                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary">Update</flux:button>
                            </div>
                        </form>
                    </flux:modal>

                    {{-- Delete Confirmation Modal --}}
                    <flux:modal name="confirm-delete-veh-{{ $vehicle->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Delete Vehicle?</flux:heading>
                                <flux:text class="mt-2">Are you sure you want to remove <b>{{ $vehicle->vehicle }}</b>
                                    ({{ $vehicle->plate_no }})? This action is permanent.</flux:text>
                            </div>
                            <div class="flex gap-2">
                                <flux:spacer />
                                <form action="{{ route('vehicle.destroy', $vehicle) }}" method="POST">
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
                <flux:table.cell colspan="5" class="py-10 text-center">
                    <div class="flex flex-col items-center justify-center space-y-2">
                        <flux:icon name="truck" class="size-10 text-zinc-500" />
                        <flux:text variant="strong" class="text-zinc-500">No vehicles found</flux:text>
                        <flux:text size="sm" class="text-zinc-400">Click "Add Vehicle" to start building your fleet.
                        </flux:text>
                    </div>
                </flux:table.cell>
            </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</flux:card>

@endsection