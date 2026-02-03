@extends('client.layout')

@section('content')
<div class="w-full h-full flex flex-col">
    <flux:heading size="xl" level="1">Trip Ticket</flux:heading>
    <flux:text class="mt-2 mb-6 text-base">View and manage your trip tickets</flux:text>


    <div class="flex-1 ">
        <div class="flex gap-2">
            <flux:input icon="magnifying-glass" placeholder="Search orders" />
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">Sort by</flux:button>

                <flux:menu>
                    <flux:menu.radio.group wire:model="sortBy">
                        <flux:menu.radio checked>Latest activity</flux:menu.radio>
                        <flux:menu.radio>Date created</flux:menu.radio>
                        <flux:menu.radio>Most popular</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">Sort by</flux:button>

                <flux:menu>
                    <flux:menu.radio.group wire:model="sortBy">
                        <flux:menu.radio checked>Latest activity</flux:menu.radio>
                        <flux:menu.radio>Date created</flux:menu.radio>
                        <flux:menu.radio>Most popular</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>
            <flux:button variant="primary" color="emerald">Filter</flux:button>
        </div>
    </div>
</div>
@endsection
