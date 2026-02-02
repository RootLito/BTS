@extends('client.layout')

@section('content')
<flux:heading size="xl" level="1">Good afternoon, Janjan da great</flux:heading>
<flux:text class="mt-2 mb-6 text-base">Here's what's new today</flux:text>
<flux:separator variant="subtle" />

{{-- <flux:date-picker /> --}}

{{-- <flux:table :paginate="$this->orders">
    <flux:table.columns>
        <flux:table.column>Customer</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$sortDirection" wire:click="sort('date')">
            Date</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
            wire:click="sort('status')">Status</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'amount'" :direction="$sortDirection"
            wire:click="sort('amount')">Amount</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @foreach ($this->orders as $order)
        <flux:table.row :key="$order->id">
            <flux:table.cell class="flex items-center gap-3">
                <flux:avatar size="xs" src="{{ $order->customer_avatar }}" />

                {{ $order->customer }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">{{ $order->date }}</flux:table.cell>

            <flux:table.cell>
                <flux:badge size="sm" :color="$order->status_color" inset="top bottom">{{ $order->status }}</flux:badge>
            </flux:table.cell>

            <flux:table.cell variant="strong">{{ $order->amount }}</flux:table.cell>

            <flux:table.cell>
                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
            </flux:table.cell>
        </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table> --}}


@endsection