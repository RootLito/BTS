@extends('admin.layout')

@section('content')
<flux:heading size="xl" level="1">Driver</flux:heading>
<flux:text class="mt-2 mb-6 text-base">Manage drivers information</flux:text>

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
    <flux:modal.trigger name="edit-profile">
        <flux:button variant="primary" color="emerald" icon="plus">Add Driver</flux:button>
    </flux:modal.trigger>
    <flux:modal name="edit-profile" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>
            <flux:input label="Name" placeholder="Your name" />
            <flux:input label="Date of birth" type="date" />
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </div>
    </flux:modal>
</div>


<flux:card class="row-span-2 col-start-3 row-start-1 space-y-6 overflow-y-auto">

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Driver</flux:table.column>
            <flux:table.column>Salary</flux:table.column>
            <flux:table.column>Availability</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            <flux:table.row>
                <flux:table.cell>John Smith</flux:table.cell>
                <flux:table.cell variant="strong">$3,200</flux:table.cell>
                <flux:table.cell>
                    <flux:badge color="green" size="sm" inset="top bottom">Available</flux:badge>
                </flux:table.cell>
            </flux:table.row>

            <flux:table.row>
                <flux:table.cell>Maria Lopez</flux:table.cell>
                <flux:table.cell variant="strong">$2,850</flux:table.cell>
                <flux:table.cell>
                    <flux:badge color="zinc" size="sm" inset="top bottom">On travel</flux:badge>
                </flux:table.cell>
            </flux:table.row>

            <flux:table.row>
                <flux:table.cell>David Kim</flux:table.cell>
                <flux:table.cell variant="strong">$3,100</flux:table.cell>
                <flux:table.cell>
                    <flux:badge color="green" size="sm" inset="top bottom">Available</flux:badge>
                </flux:table.cell>
            </flux:table.row>

            <flux:table.row>
                <flux:table.cell>Sophia Brown</flux:table.cell>
                <flux:table.cell variant="strong">$2,950</flux:table.cell>
                <flux:table.cell>
                    <flux:badge color="zinc" size="sm" inset="top bottom">On travel</flux:badge>
                </flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</flux:card>


@endsection