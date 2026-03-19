@extends('client.layout')

@section('content')
    <div class="fixed inset-0 top-[65px] overflow-y-auto">

        {{-- Action Buttons --}}
        <div class="w-[8.5in] h-10 mx-auto my-4 flex justify-between items-center print:hidden">
            <div class="w-full flex justify-between gap-2">
                <div>
                    <flux:heading size="xl" level="1">Travel Order</flux:heading>
                </div>
                <div>
                    <flux:modal.trigger name="travel-order-info">
                        <flux:button icon="document-text" variant="primary" color="sky">Update Info</flux:button>
                    </flux:modal.trigger>
                    <flux:button icon="printer" variant="primary" color="emerald" onclick="window.print()">Print
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="printable-folio bg-white mx-auto shadow-lg print:shadow-none mb-4">
            <img src="{{ asset('images/top.png') }}" alt="Header" class="top">

            <div class="ticket-container">
                <div class="text-center mb-6">
                    <h1 class="text-[16pt] font-bold leading-none uppercase my-4">TRAVEL ORDER</h1>
                </div>

                <div class="flex justify-end text-[11pt] leading-none mb-8">
                    <div class="w-48">
                        <div class="flex mb-1 justify-between">
                            <span class="font-bold">TO No.:</span>
                            <span class="border-b border-black uppercase w-32 ms-auto"></span>
                        </div>
                        <div class="flex mb-1 justify-between">
                            <span class="font-bold">Date:</span>
                            <span class="border-b border-black font-bold w-32 ms-auto text-center">
                                {{-- {{ $travelOrder->date ? $travelOrder->date->format('F d, Y') : '' }} --}}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-[11pt] leading-none space-y-4">
                    {{-- PERSONNEL DETAILS --}}
                    <div class="grid grid-cols-12 gap-y-2">
                        <div class="col-span-2 font-bold">Name:</div>
                        <div class="col-span-5 flex flex-col space-y-1">
                            {{-- @foreach ($travelOrder->personnel ?? [] as $p)
                        <span class="border-b border-black uppercase min-h-[1.2rem]">{{ $p['name'] ?? '' }}</span>
                        @endforeach --}}
                        </div>

                        <div class="col-span-2 font-bold pl-4">Salary:</div>
                        <div class="col-span-3 flex flex-col space-y-1">
                            {{-- @foreach ($travelOrder->personnel ?? [] as $p)
                        <span class="border-b border-black min-h-[1.2rem]">{{ $p['salary'] ?? '' }}</span>
                        @endforeach --}}
                        </div>

                        <div class="col-span-2 font-bold mt-2">Position:</div>
                        <div class="col-span-5 flex flex-col space-y-1 mt-2">
                            {{-- @foreach ($travelOrder->personnel ?? [] as $p)
                        <span class="border-b border-black uppercase min-h-[1.2rem]">{{ $p['position'] ?? '' }}</span>
                        @endforeach --}}
                        </div>

                        <div class="col-span-2 font-bold pl-4 mt-2">Office:</div>
                        <div class="col-span-3 flex flex-col space-y-1 mt-2">
                            {{-- @foreach ($travelOrder->personnel ?? [] as $p)
                        <span class="border-b border-black uppercase min-h-[1.2rem]">{{ $p['office'] ?? '' }}</span>
                        @endforeach --}}
                        </div>
                    </div>

                    <div class="details-footer space-y-4 pt-6">
                        <div class="grid grid-cols-12 gap-y-2">
                            <div class="col-span-2 font-bold">Departure:</div>
                            <div class="col-span-4 border-b border-black uppercase"></div>
                            <div class="col-span-2 font-bold pl-4">Return Date:</div>
                            <div class="col-span-4 border-b border-black text-center uppercase"></div>
                        </div>

                        <div class="flex items-end">
                            <span class="font-bold w-[16.66%]">Destination:</span>
                            <span class="flex-1 border-b border-black uppercase"></span>
                        </div>

                        <div class="flex flex-col">
                            <span class="font-bold">Specific Purpose of the Trip:</span>
                            <div class="mt-1 border-b border-black py-1 uppercase ms-26 mt-2">
                            </div>
                            <div class="mt-1 border-b border-black py-1 uppercase ms-26">
                            </div>
                            <div class="mt-1 border-b border-black py-1 uppercase ms-26">
                            </div>
                            <div class="mt-1 border-b border-black py-1 uppercase ms-26">
                            </div>
                            <div class="mt-1 border-b border-black py-1 uppercase ms-26">
                            </div>
                        </div>


                        {{-- REMARKS & OBJECTIVES SECTION --}}
                        <div class="space-y-2">


                            <div class="flex items-end">
                                <span class="font-bold whitespace-nowrap mr-2"></span>
                                <span class="flex-1 border-b border-black h-5 uppercase"></span>
                            </div>
                        </div>

                        {{-- SIGNATORIES --}}
                        <div class="grid grid-cols-2 gap-20 text-center mt-12 px-10 leading-none">
                            <div>
                                <p class="text-[12pt] mb-10 text-left pl-4">Recommended by:</p>

                                <div class="mb-6 last:mb-0">
                                    <p class="font-bold underline text-[11pt] uppercase">
                                    </p>
                                    <p class="text-[12pt] mt-1">
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[12pt] mb-10 text-left pl-4">Approved by:</p>
                                <p class="font-bold underline text-[11pt] uppercase">RELLY B. GARCIA</p>
                                <p class="text-[12pt] mt-1 ">Regional Director</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <img src="{{ asset('images/bot.png') }}" alt="Footer" class="bot">
        </div>
    </div>

    {{-- MODAL SECTION --}}
    {{-- <flux:modal name="travel-order-info" class="w-300">
    <form action="{{ route('client.travel-order.store', $tripTicket->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-6">
            <flux:heading size="lg">Generate Travel Order</flux:heading>
            <flux:text class="mt-2">Enter the details for this order. Fields will be blank for new entries.</flux:text>
        </div>

        <flux:input type="date" label="Date" name="date" class="mb-4" />


        <div class="mb-4" x-data="{ rows: [0, 1, 2] }">
            <div class="flex justify-between items-center mb-2">
                <flux:label>Personnel Details</flux:label>
                <flux:button variant="ghost" size="sm" icon="plus" @click.prevent="rows.push(rows.length)">
                    Add Row
                </flux:button>
            </div>

            <div class="space-y-2">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="grid grid-cols-4 gap-2 items-end">
                        <flux:input ::label="index === 0 ? 'Name' : ''" ::name="`personnel[${index}][name]`"
                            placeholder="Name" />
                        <flux:input ::label="index === 0 ? 'Salary' : ''" ::name="`personnel[${index}][salary]`"
                            placeholder="0.00" />
                        <flux:input ::label="index === 0 ? 'Position' : ''" ::name="`personnel[${index}][position]`"
                            placeholder="Role" />
                        <flux:input ::label="index === 0 ? 'Office' : ''" ::name="`personnel[${index}][office]`"
                            placeholder="Office" />
                    </div>
                </template>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <flux:input label="Departure From" name="departure" placeholder="Origin" />
            <flux:input label="Return To" name="return" placeholder="Return point" />
        </div>
        <flux:input label="Destination" name="destination" placeholder="Specific location..." class="mb-4" />


        <flux:textarea label="Purpose" name="purpose" placeholder="Specific purpose of trip..." rows="2" class="mb-4">
        </flux:textarea>

        <div class="mb-4" x-data="{ recommendRows: [0] }">
            <div class="flex justify-between items-center mb-2">
                <flux:label>Recommended By</flux:label>
                <flux:button variant="ghost" size="sm" icon="plus"
                    @click.prevent="recommendRows.push(recommendRows.length)">
                    Add Row
                </flux:button>
            </div>

            <div class="space-y-2">
                <template x-for="(row, index) in recommendRows" :key="index">
                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <flux:input ::label="index === 0 ? 'Name' : ''" ::name="`recommended_by[${index}][name]`"
                                placeholder="Supervisor Name" />
                        </div>
                        <div class="w-52">
                            <flux:input ::label="index === 0 ? 'Position' : ''"
                                ::name="`recommended_by[${index}][position]`" placeholder="Role" />
                        </div>
                        <flux:button variant="ghost" size="sm" icon="trash"
                            @click.prevent="recommendRows.splice(index, 1)" class="mb-[2px] cusor-pointer"
                            x-show="recommendRows.length > 1" />
                    </div>
                </template>
            </div>
        </div>

        <div class="flex mt-2">
            <flux:spacer />
            <flux:button type="submit" variant="primary" color="emerald">Save and Generate</flux:button>
        </div>
    </form>
</flux:modal> --}}
@endsection
