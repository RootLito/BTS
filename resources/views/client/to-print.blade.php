@extends('client.layout')

@section('content')
    <div x-data="{
        form: {
            to_no: '',
            date: '',
            personnel: [{ name: '', salary: '', position: '', office: '' }],
            departure: '',
            return_date: '',
            destination: '',
            purpose: '',
            recommended: [{ name: '', position: '' }]
        },
        // Helper to split purpose into multiple lines if it gets long
        get purposeLines() {
            let lines = this.form.purpose.match(/.{1,80}(\s|$)/g) || [''];
            return lines.concat(Array(5).fill('')).slice(0, 5);
        }
    }">
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
                                <span class="border-b border-black uppercase w-32 ms-auto" x-text="form.to_no"></span>
                            </div>
                            <div class="flex mb-1 justify-between">
                                <span class="font-bold">Date:</span>
                                <span class="border-b border-black font-bold w-32 ms-auto" x-text="form.date"></span>
                            </div>
                        </div>
                    </div>

                    <div class="text-[11pt] leading-none space-y-4">
                        {{-- PERSONNEL DETAILS --}}
                        {{-- <div class="grid grid-cols-12">
                            <div class="col-span-2 font-bold">Name:</div>
                            <div class="col-span-5 flex flex-col space-y-1">
                                <template x-for="(p, index) in form.personnel" :key="index">
                                    <span class="border-b border-black uppercase min-h-[1.2rem]" x-text="p.name"></span>
                                </template>
                            </div>

                            <div class="col-span-2 font-bold pl-4">Salary:</div>
                            <div class="col-span-3 flex flex-col space-y-1">
                                <template x-for="(p, index) in form.personnel" :key="index">
                                    <span class="border-b border-black min-h-[1.2rem]" x-text="p.salary"></span>
                                </template>
                            </div>

                            <div class="col-span-2 font-bold mt-1">Position:</div>
                            <div class="col-span-5 flex flex-col space-y-1 mt-1">
                                <template x-for="(p, index) in form.personnel" :key="index">
                                    <span class="border-b border-black uppercase min-h-[1.2rem]" x-text="p.position"></span>
                                </template>
                            </div>

                            <div class="col-span-2 font-bold pl-4 mt-1">Office:</div>
                            <div class="col-span-3 flex flex-col space-y-1 mt-1">
                                <template x-for="(p, index) in form.personnel" :key="index">
                                    <span class="border-b border-black uppercase min-h-[1.2rem]" x-text="p.office"></span>
                                </template>
                            </div>
                        </div> --}}


                        <div class="w-full flex mb-4">
                            <div class="flex">
                                <div class="font-bold w-22">Name:</div>
                                <div class="flex flex-col gap-1">
                                    <template x-for="(p, index) in form.personnel" :key="index">
                                        <span class="w-56 border-b border-black uppercase min-h-[1em]" x-text="p.name"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="flex-1 flex ms-4">
                                <div class="font-bold">Salary per Month:</div>
                                <div class="flex flex-col gap-1 ms-auto">
                                    <template x-for="(p, index) in form.personnel" :key="index">
                                        <span class="w-40 border-b border-black min-h-[1em]" x-text="p.salary"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="w-full flex mb-2">
                            <div class="flex">
                                <div class="font-bold w-22">Position:</div>
                                <div class="flex flex-col gap-1">
                                    <template x-for="(p, index) in form.personnel" :key="index">
                                        <span class="w-56 border-b border-black uppercase min-h-[1em]"  x-text="p.position"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="flex-1 flex ms-4">
                                <div class="font-bold">Office Station:</div>
                                <div class="flex flex-col gap-1 ms-auto">
                                    <template x-for="(p, index) in form.personnel" :key="index">
                                        <span class="w-40 border-b border-black min-h-[1em]" x-text="p.office"></span>
                                    </template>
                                </div>
                            </div>
                        </div>












                        <div class="details-footer space-y-4 pt-6">
                            <div class="grid grid-cols-12 gap-y-2">
                                <div class="col-span-2 font-bold">Departure:</div>
                                <div class="col-span-4 border-b border-black uppercase text-center" x-text="form.departure">
                                </div>
                                <div class="col-span-2 font-bold pl-4">Return Date:</div>
                                <div class="col-span-4 border-b border-black text-center uppercase"
                                    x-text="form.return_date"></div>
                            </div>

                            <div class="flex items-end">
                                <span class="font-bold w-[16.66%]">Destination:</span>
                                <span class="flex-1 border-b border-black uppercase text-center"
                                    x-text="form.destination"></span>
                            </div>

                            <div class="flex flex-col">
                                <span class="font-bold">Specific Purpose of the Trip:</span>
                                <template x-for="line in purposeLines">
                                    <div class="mt-1 border-b border-black py-1 uppercase ms-26 min-h-[1.5rem]"
                                        x-text="line"></div>
                                </template>
                            </div>

                            {{-- SIGNATORIES --}}
                            <div class="grid grid-cols-2 gap-20 text-center mt-12 px-10 leading-none">
                                <div>
                                    <p class="text-[12pt] mb-10 text-left pl-4">Recommended by:</p>
                                    <template x-for="(r, index) in form.recommended" :key="index">
                                        <div class="mb-6 last:mb-0">
                                            <p class="font-bold underline text-[11pt] uppercase" x-text="r.name"></p>
                                            <p class="text-[12pt] mt-1" x-text="r.position"></p>
                                        </div>
                                    </template>
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
        <flux:modal name="travel-order-info" class="md:w-[700px]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Update Travel Order Info</flux:heading>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="TO No." x-model="form.to_no" />
                    <flux:input type="date" label="Date" x-model="form.date" />
                </div>

                {{-- Dynamic Personnel --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <flux:label>Personnel Details</flux:label>
                        <flux:button variant="ghost" size="sm" icon="plus"
                            @click="form.personnel.push({name: '', salary: '', position: '', office: ''})">Add Personnel
                        </flux:button>
                    </div>
                    <template x-for="(p, index) in form.personnel" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-end border-b border-gray-100 pb-2">
                            <div class="col-span-4">
                                <flux:input x-model="p.name" placeholder="Name" />
                            </div>
                            <div class="col-span-2">
                                <flux:input x-model="p.salary" placeholder="Salary" />
                            </div>
                            <div class="col-span-3">
                                <flux:input x-model="p.position" placeholder="Position" />
                            </div>
                            <div class="col-span-2">
                                <flux:input x-model="p.office" placeholder="Office" />
                            </div>
                            <div class="col-span-1">
                                <flux:button variant="ghost" icon="trash" size="sm"
                                    @click="form.personnel.splice(index, 1)" x-show="form.personnel.length > 1" />
                            </div>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input type="date" label="Departure Date" x-model="form.departure" />
                    <flux:input type="date" label="Return Date" x-model="form.return_date" />
                </div>

                <flux:input label="Destination" x-model="form.destination" />
                <flux:textarea label="Purpose" x-model="form.purpose" rows="3" />

                {{-- Dynamic Recommended By --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <flux:label>Recommended By</flux:label>
                        <flux:button variant="ghost" size="sm" icon="plus"
                            @click="form.recommended.push({name: '', position: ''})">Add Row</flux:button>
                    </div>
                    <template x-for="(r, index) in form.recommended" :key="index">
                        <div class="flex gap-2 items-end">
                            <flux:input class="flex-1" x-model="r.name" placeholder="Name" />
                            <flux:input class="flex-1" x-model="r.position" placeholder="Position" />
                            <flux:button variant="ghost" icon="trash" size="sm"
                                @click="form.recommended.splice(index, 1)" x-show="form.recommended.length > 1" />
                        </div>
                    </template>
                </div>

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
