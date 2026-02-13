@extends('client.layout')

@section('content')
<div class="fixed inset-0 top-[65px] overflow-y-auto">

    <div class="w-[8.5in] h-10 mx-auto my-4 flex justify-between items-center">
        <flux:button icon="arrow-uturn-left" variant="filled" color="red" href="{{ route('client.trip-ticket') }}">Back
        </flux:button>
        <flux:text class="text-base">Trip Ticket</flux:text>
        <flux:button icon="printer" variant="primary" color="emerald" onclick="window.print()">Print</flux:button>
    </div>


    <div
        class="printable-folio bg-white mx-auto shadow-lg border border-gray-200 print:shadow-none print:border-none mb-4">
        <img src="{{ asset('images/top.png') }}" alt="Travel Order Header" class="top">
        <div class="printable-folio bg-white mx-auto shadow-lg print:shadow-none mb-4">
            <img src="{{ asset('images/top.png') }}" alt="Header" class="top">

            <div class="ticket-container">
                <h1 class="ticket-title">VEHICLE TRIP TICKET</h1>

                <div class="text-[9pt] mb-4 mt-2 leading-tight flex">
                    <strong>INSTRUCTIONS:</strong>
                    <ul class="list-none p-0 ms-6">
                        <li>1. To be filled up in four (4) copies by the person requesting use of Department vehicle.
                        </li>
                        <li>2. Original to driver to be returned to ICU upon completion.</li>
                        <li>3. Duplicate to Security Guard on duty for monitoring and gate passage.</li>
                    </ul>
                </div>

                <div class="flex text-[10pt] mb-2">
                    <div class="flex flex-col">
                        <div class="font-bold">DATE:</div>
                        <div class="font-bold">DRIVER'S NAME:</div>
                    </div>
                    <div class="flex-1 flex gap-4 ms-4 ">
                        <div class="flex-1 flex flex-col">
                            <span class="flex-1 border-b"></span>
                            <span class="flex-1 border-b"></span>
                        </div>
                        <div class="flex-1 flex flex-col">
                            <div class="flex">
                                <span class="font-bold">GAS SLOP NO.:</span><span class="flex-1 border-b"></span>
                            </div>
                            <div class="flex">
                                <span class="font-bold me-6">PLATE NO.:</span><span class="flex-1 border-b"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col text-[10pt] mb-2">
                    <p class="font-bold">AUTHORIZED PASSENGER:</p>
                    <div class="flex-1 flex flex-col ms-30">
                        <div class="flex-1 flex gap-4">
                            <div class="flex-1 flex">
                                <span class="font-bold mr-1">1.</span>
                                <span class="flex-1 border-b"></span>
                            </div>
                            <div class="flex-1 flex">
                                <span class="font-bold mr-1">4.</span>
                                <span class="flex-1 border-b"></span>
                            </div>
                        </div>

                        <div class="flex-1 flex gap-4">
                            <div class="flex-1 flex">
                                <span class="font-bold mr-1">2.</span>
                                <span class="flex-1 border-b"></span>
                            </div>
                            <div class="flex-1 flex">
                                <span class="font-bold mr-1">5.</span>
                                <span class="flex-1 border-b"></span>
                            </div>
                        </div>

                        <div class="flex-1 flex gap-4">
                            <div class="flex-1 flex">
                                <span class="font-bold mr-1">3.</span>
                                <span class="flex-1 border-b"></span>
                            </div>
                            <div class="flex-1 flex">
                                <span class="font-bold mr-1">6.</span>
                                <span class="flex-1 border-b"></span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex text-[10pt] mb-2 mt-4">
                    <p class="font-bold">DESTINATION:</p>
                    <div class="ms-8 flex-1 me-4 flex flex-col">
                        <div class="flex"><span class="font-bold">1.</span><span class="flex-1 border-b"></span></div>
                        <div class="flex"><span class="font-bold">2.</span><span class="flex-1 border-b"></span></div>
                        <div class="flex"><span class="font-bold">3.</span><span class="flex-1 border-b"></span></div>
                    </div>
                    <div class="flex-1"></div>
                </div>

                <div class="flex text-[10pt] mb-6 mt-4">
                    <p><strong>PURPOSE:</strong></p>
                    <div class="flex-1 flex flex-col ms-14">
                        <span class="flex-1 border-b">1 </span>
                        <span class="flex-1 border-b">2</span>
                        <span class="flex-1 border-b">3</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-20 text-center text-[10pt] mb-6 px-4">
                    <div>
                        <p class="font-bold">RECOMMENDED BY:</p>
                        <div class="mt-4 border-b border-black font-bold">RELLY B. GARCIA</div>
                        <p class="text-[10pt]">Immediate Supervisor</p>
                    </div>
                    <div>
                        <p class="font-bold">APPROVED BY:</p>
                        <div class="mt-4 border-b border-black font-bold">RELLY B. GARCIA</div>
                        <p class="text-[10pt]">Regional Director</p>
                    </div>
                </div>

                <table class="ticket-table mb-4">
                    <thead>
                        <tr>
                            <th colspan="5">
                                <p class="italic text-[10pt] text-left font-medium ">To be filled up only by the driver after each trip</p> 
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2">DEPARTURE</th>
                            <th colspan="2">ARRIVAL</th>
                            <th rowspan="2" width="120">SPEEDOMETER</th>
                        </tr>
                        <tr>
                            <th>TIME</th>
                            <th width="150">PLACE</th>
                            <th>TIME</th>
                            <th width="150">PLACE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=0; $i < 8; $i++) <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            </tr>
                            @endfor
                    </tbody>
                </table>

                <div class="flex text-[10pt]">
                    <div class="space-y-1 flex-1">
                        <p class="font-bold">GASOLINE USAGE:</p>
                        <div class="space-y-0 text-[12pt] font-bold leading-none mt-6">
                            <div class="flex items-end mb-1">
                                <span class="w-40">Balance in Tank</span>
                                <span class="w-12 border-b border-black"></span>
                                <span class="ml-1">ltrs.</span>
                            </div>
                            <div class="flex items-end mb-1">
                                <span class="w-40">Issued from stock</span>
                                <span class="w-12 border-b border-black"></span>
                                <span class="ml-1">ltrs.</span>
                            </div>
                            <div class="flex items-end mb-1 mt-4">
                                <span class="w-40">Purchase Outside</span>
                                <span class="w-12 border-b border-black"></span>
                                <span class="ml-1">ltrs.</span>
                            </div>
                            <div class="flex items-end mb-1">
                                <span class="w-40">Total</span>
                                <span class="w-12 border-b border-black"></span>
                                <span class="ml-1">ltrs.</span>
                            </div>
                            <div class="flex items-end mb-1 mt-4">
                                <span class="w-40">Gasoline Used</span>
                                <span class="w-12 border-b border-black"></span>
                                <span class="ml-1">ltrs.</span>
                            </div>
                            <div class="flex items-end mb-1">
                                <span class="w-40">Balance in Tank</span>
                                <span class="w-12 border-b border-black"></span>
                                <span class="ml-1">ltrs.</span>
                            </div>
                            <div class="flex items-end">
                                <span class="w-40">Distance Travelled</span>
                                <span class="w-12 border-b border-black"></span>
                                <span class="ml-1"></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center flex-1">
                        <p class="font-bold">CERTIFIED CORRECT:</p>
                        <div class="mt-6 font-bold underline">ROMEO P. DEGORIO JR.</div>
                        <p>DRIVER</p>
                        <p class="text-[12pt] mt-1 text-center leading-tight">
                            I hereby certify that the vehicle was used on official business as stated above.
                        </p>
                        <div class="flex-1 flex flex-col mt-1 text-[10pt] text-left ">
                            <div class="flex items-end mb-1">
                                <span class="font-bold mr-1">1.</span>
                                <span class="flex-1 border-b border-black"></span>
                            </div>
                            <div class="flex items-end mb-1">
                                <span class="font-bold mr-1">3.</span>
                                <span class="flex-1 border-b border-black"></span>
                            </div>
                            <div class="flex items-end mb-1">
                                <span class="font-bold mr-1">2.</span>
                                <span class="flex-1 border-b border-black"></span>
                            </div>
                            <div class="flex items-end">
                                <span class="font-bold mr-1">4.</span>
                                <span class="flex-1 border-b border-black"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <img src="{{ asset('images/bot.png') }}" alt="Travel Order Footer" class="bot">
        </div>

    </div>
    @endsection