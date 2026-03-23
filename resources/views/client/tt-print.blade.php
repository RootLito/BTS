@extends('client.layout')

@section('content')
    <div x-data="{
        form: {
            startDate: '',
            endDate: '',
            gas: '',
            driver: '',
            plate: '',
            passengers: '',
            destination: '',
            purpose: '',
            recommended: ''
        },
    
        // Helper to split comma-separated text into the numbered slots
        get pList() { return this.form.passengers.split(',').map(s => s.trim()) },
        get dList() { return this.form.destination.split(',').map(s => s.trim()) },
        get purpList() { return this.form.purpose.split(',').map(s => s.trim()) },
    
        // Logic for Sept. 1-3, 2025 OR Sept. 30 - Feb. 2, 2026
        get formattedDateRange() {
            if (!this.form.startDate) return '';
    
            const start = new Date(this.form.startDate);
            const end = this.form.endDate ? new Date(this.form.endDate) : null;
    
            const formatMonth = (date) => {
                let month = date.toLocaleDateString('en-US', { month: 'short' });
                // Custom handling for 'Sept.' abbreviation
                if (month === 'Sep') month = 'Sept';
                return month + '.';
            };
    
            const sMonth = formatMonth(start);
            const sDay = start.getDate();
            const sYear = start.getFullYear();
    
            // If no end date selected, or it's the same day
            if (!end || this.form.startDate === this.form.endDate) {
                return `${sMonth} ${sDay}, ${sYear}`;
            }
    
            const eMonth = formatMonth(end);
            const eDay = end.getDate();
            const eYear = end.getFullYear();
    
            // Scenario 1: Same Month (Sept. 1-3, 2025)
            if (sMonth === eMonth && sYear === eYear) {
                return `${sMonth} ${sDay}-${eDay}, ${sYear}`;
            }
    
            // Scenario 2: Cross Month/Year (Sept. 30 - Feb. 2, 2026)
            return `${sMonth} ${sDay} - ${eMonth} ${eDay}, ${eYear}`;
        }
    }">
        <div class="fixed inset-0 top-[65px] overflow-y-auto">

            <div class="w-[8.5in] h-10 mx-auto my-4 flex justify-between items-center">
                <div class="w-full flex gap-2 justify-between">
                    <div>
                        <flux:heading size="xl" level="1">Trip Ticket</flux:heading>
                    </div>
                    <div>
                        <flux:modal.trigger name="info">
                            <flux:button icon="document-text" variant="primary" color="sky">Update Info</flux:button>
                        </flux:modal.trigger>
                        <flux:button icon="printer" variant="primary" color="emerald" onclick="window.print()">Print
                        </flux:button>
                    </div>
                </div>
            </div>

            <div class="printable-folio bg-white mx-auto shadow-lg print:shadow-none mb-4">
                <img src="{{ asset('images/top.png') }}" alt="Travel Order Header" class="top">

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
                            <div class="font-bold flex items-end">DATE:</div>
                            <div class="font-bold flex items-end">DRIVER'S NAME:</div>
                        </div>

                        <div class="flex-1 flex gap-4 ms-4">
                            <div class="flex-1 flex flex-col">
                                <div class="flex-1 flex items-end">
                                    <span class="flex-1 border-b border-black leading-none min-h-[1.2em]"
                                        x-text="formattedDateRange"></span>
                                </div>
                                <div class="flex-1 flex items-end">
                                    <span class="flex-1 border-b border-black uppercase leading-none min-h-[1.2em]"
                                        x-text="form.driver"></span>
                                </div>
                            </div>

                            <div class="flex-1 flex flex-col">
                                <div class="flex-1 flex items-end">
                                    <span class="font-bold leading-none mr-1">GAS SLIP NO.:</span>
                                    <span class="flex-1 border-b border-black leading-none min-h-[1.2em]"
                                        x-text="form.gas"></span>
                                </div>
                                <div class="flex-1 flex items-end">
                                    <span class="font-bold me-4 leading-none">PLATE NO.:</span>
                                    <span class="flex-1 border-b border-black uppercase leading-none min-h-[1.2em]"
                                        x-text="form.plate"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col text-[10pt] mb-2">
                        <p class="font-bold">AUTHORIZED PASSENGER:</p>
                        <div class="flex-1 flex flex-col ms-30">
                            <div class="flex-1 flex gap-4 mb-2">
                                <div class="flex-1 flex items-end">
                                    <span class="font-bold mr-1 leading-none">1.</span>
                                    <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                        x-text="pList[0] || ''"></span>
                                </div>
                                <div class="flex-1 flex items-end">
                                    <span class="font-bold mr-1 leading-none">4.</span>
                                    <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                        x-text="pList[3] || ''"></span>
                                </div>
                            </div>
                            <div class="flex-1 flex gap-4 mb-2">
                                <div class="flex-1 flex items-end">
                                    <span class="font-bold mr-1 leading-none">2.</span>
                                    <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                        x-text="pList[1] || ''"></span>
                                </div>
                                <div class="flex-1 flex items-end">
                                    <span class="font-bold mr-1 leading-none">5.</span>
                                    <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                        x-text="pList[4] || ''"></span>
                                </div>
                            </div>
                            <div class="flex-1 flex gap-4 mb-2">
                                <div class="flex-1 flex items-end">
                                    <span class="font-bold mr-1 leading-none">3.</span>
                                    <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                        x-text="pList[2] || ''"></span>
                                </div>
                                <div class="flex-1 flex items-end">
                                    <span class="font-bold mr-1 leading-none">6.</span>
                                    <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                        x-text="pList[5] || ''"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex text-[10pt] mb-6 mt-4">
                        <p><strong>DESTINATION:</strong></p>
                        <div class="flex-1 flex flex-col ms-8">
                            <div class="flex-1 flex items-end mb-2">
                                <span class="font-bold mr-1 leading-none">1.</span>
                                <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                    x-text="dList[0] || ''"></span>
                            </div>
                            <div class="flex-1 flex items-end mb-2">
                                <span class="font-bold mr-1 leading-none">2.</span>
                                <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                    x-text="dList[1] || ''"></span>
                            </div>
                            <div class="flex-1 flex items-end">
                                <span class="font-bold mr-1 leading-none">3.</span>
                                <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                    x-text="dList[2] || ''"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex text-[10pt] mb-6 mt-4">
                        <p><strong>PURPOSE:</strong></p>
                        <div class="flex-1 flex flex-col ms-14">
                            <div class="flex-1 flex items-end mb-2">
                                <span class="font-bold mr-1 leading-none">1.</span>
                                <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                    x-text="purpList[0] || ''"></span>
                            </div>
                            <div class="flex-1 flex items-end mb-2">
                                <span class="font-bold mr-1 leading-none">2.</span>
                                <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                    x-text="purpList[1] || ''"></span>
                            </div>
                            <div class="flex-1 flex items-end">
                                <span class="font-bold mr-1 leading-none">3.</span>
                                <span class="flex-1 border-b border-black uppercase leading-none min-h-[1em]"
                                    x-text="purpList[2] || ''"></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-20 text-center text-[10pt] mb-6 px-4">
                        <div>
                            <p class="font-bold">RECOMMENDED BY:</p>
                            <div class="mt-4 border-b border-black font-bold uppercase min-h-[1.2em]"
                                x-text="form.recommended"></div>
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
                                    <p class="italic text-[10pt] text-left font-medium ">To be filled up only by the driver
                                        after each trip</p>
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
                            @for ($i = 0; $i < 8; $i++)
                                <tr>
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
                            </div>
                        </div>
                        <div class="text-center flex-1">
                            <p class="font-bold">CERTIFIED CORRECT:</p>
                            <div class="mt-6 font-bold">
                                <span class="flex-1 border-b border-black uppercase leading-none min-h-[1.2em]"
                                    x-text="form.driver"></span>
                            </div>
                            <p>DRIVER</p>
                            <p class="text-[12pt] mt-1 text-center leading-tight">
                                I hereby certify that the vehicle was used on official business as stated above.
                            </p>
                        </div>
                    </div>
                </div>
                <img src="{{ asset('images/bot.png') }}" alt="Travel Order Footer" class="bot">
            </div>
        </div>

        <flux:modal name="info" class="md:w-[450px]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Add Ticket Details</flux:heading>
                    <flux:text class="mt-2">Enter the details for this trip ticket.</flux:text>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Start Date" type="date" x-model="form.startDate" />
                    <flux:input label="End Date" type="date" x-model="form.endDate" />
                </div>

                <div class="w-full">
                    <flux:input label="Driver" x-model="form.driver" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Plate No." x-model="form.plate" />
                    <flux:input label="Gas Slip No." x-model="form.gas" />
                </div>

                <flux:input label="Authorized Passenger(s)" x-model="form.passengers"
                    placeholder="Use commas for multiple names" />
                <flux:input label="Destination(s)" x-model="form.destination"
                    placeholder="Use commas for multiple destinations" />
                <flux:input label="Purpose" x-model="form.purpose" placeholder="Enter trip purpose..." />
                <flux:input label="Immediate Supervisor" x-model="form.recommended"
                    placeholder="Enter supervisor name..." />

                <div class="flex">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="primary" color="emerald">Save Details</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>
    </div>
@endsection
