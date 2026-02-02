@extends('admin.layout')

@section('content')
<flux:heading size="xl" level="1">Good afternoon, Boos janny</flux:heading>
<flux:text class="mt-2 mb-6 text-base">Here's what's new today</flux:text>
<flux:separator variant="subtle" />


<div class="my-10 flex gap-2">
    <flux:input icon="magnifying-glass" placeholder="Search orders" class="w-100" />
    <flux:dropdown>
        <flux:button icon:trailing="chevron-down">Sort by name</flux:button>

        <flux:menu>
            <flux:menu.radio.group wire:model="sortBy">
                <flux:menu.radio checked>Latest activity</flux:menu.radio>
                <flux:menu.radio>Date created</flux:menu.radio>
                <flux:menu.radio>Most popular</flux:menu.radio>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
    <flux:dropdown>
        <flux:button icon:trailing="chevron-down">Sort by availability</flux:button>

        <flux:menu>
            <flux:menu.radio.group wire:model="sortBy">
                <flux:menu.radio checked>Latest activity</flux:menu.radio>
                <flux:menu.radio>Date created</flux:menu.radio>
                <flux:menu.radio>Most popular</flux:menu.radio>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
    <flux:modal.trigger name="add-vehicle">
    <flux:button variant="primary" color="emerald" icon="plus">
        Add Vehicle
    </flux:button>
</flux:modal.trigger>

<flux:modal name="add-vehicle" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Add New Vehicle</flux:heading>
            <flux:text class="mt-2">Enter the details of the vehicle to add it to your fleet.</flux:text>
        </div>

        <flux:input label="Vehicle Name / Model" placeholder="Enter vehicle name or model" />

        <flux:input label="License Plate" placeholder="Enter license plate" />

        <flux:select label="Type">
            <option value="Car">Car</option>
            <option value="Van">Van</option>
            <option value="Truck">Truck</option>
            <option value="SUV">SUV</option>
        </flux:select>

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Add Vehicle</flux:button>
        </div>
    </div>
</flux:modal>

</div>

<flux:table>
    <flux:table.columns>
        <flux:table.column>Vehicle</flux:table.column>
        <flux:table.column>License Plate</flux:table.column>
        <flux:table.column>Type</flux:table.column>
        <flux:table.column>Status</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        <flux:table.row>
            <flux:table.cell>Ford Transit</flux:table.cell>
            <flux:table.cell>ABC-1234</flux:table.cell>
            <flux:table.cell>Van</flux:table.cell>
            <flux:table.cell>
                <flux:badge color="emerald" size="sm" inset="top bottom">Available</flux:badge>
            </flux:table.cell>
        </flux:table.row>

        <flux:table.row>
            <flux:table.cell>Toyota Corolla</flux:table.cell>
            <flux:table.cell>XYZ-5678</flux:table.cell>
            <flux:table.cell>Car</flux:table.cell>
            <flux:table.cell>
                <flux:badge color="yellow" size="sm" inset="top bottom">On Trip</flux:badge>
            </flux:table.cell>
        </flux:table.row>

        <flux:table.row>
            <flux:table.cell>Mercedes Actros</flux:table.cell>
            <flux:table.cell>LMN-9012</flux:table.cell>
            <flux:table.cell>Truck</flux:table.cell>
            <flux:table.cell>
                <flux:badge color="red" size="sm" inset="top bottom">Maintenance</flux:badge>
            </flux:table.cell>
        </flux:table.row>

        <flux:table.row>
            <flux:table.cell>Honda CR-V</flux:table.cell>
            <flux:table.cell>DEF-3456</flux:table.cell>
            <flux:table.cell>SUV</flux:table.cell>
            <flux:table.cell>
                <flux:badge color="emerald" size="sm" inset="top bottom">Available</flux:badge>
            </flux:table.cell>
        </flux:table.row>
    </flux:table.rows>
</flux:table>


@endsection