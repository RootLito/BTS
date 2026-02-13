@extends('client.layout')

@section('content')
<div class="fixed inset-0 top-[65px] overflow-y-auto">

    <div class="w-[8.5in] h-10 mx-auto my-4 flex justify-between items-center">
        <flux:button icon="arrow-uturn-left" variant="filled" color="red" href="{{ route('client.trip-ticket') }}">Back</flux:button>
        <flux:text class="text-base">Travel Order</flux:text>
        <flux:button icon="printer" variant="primary" color="emerald">Print</flux:button>
    </div>


    <div
        class="printable-folio bg-white mx-auto shadow-lg border border-gray-200 print:shadow-none print:border-none mb-4">
        <img src="{{ asset('images/top.png') }}" alt="Travel Order Header" class="top">
        <img src="{{ asset('images/bot.png') }}" alt="Travel Order Footer" class="bot">
    </div>

</div>
@endsection