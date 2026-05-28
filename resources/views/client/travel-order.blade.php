@extends('client.layout')

@section('content')
    <div x-data="{
        form: {
            to_no: '{{ $travelOrder->to_no ?? '' }}',
            date: '{{ $travelOrder->date ? $travelOrder->date->format('Y-m-d') : now()->format('Y-m-d') }}',
            personnel: [
                @foreach ($tripTicket->passengers as $name)
                { 
                    name: '{{ $name }}', 
                    salary: '{{ $travelOrder->personnel[$loop->index]['salary'] ?? '' }}', 
                    position: '{{ $travelOrder->personnel[$loop->index]['position'] ?? '' }}', 
                    office: '{{ $travelOrder->personnel[$loop->index]['office'] ?? '' }}' 
                }, @endforeach
            ],
    
            departure: '{{ $tripTicket->start_date ? $tripTicket->start_date->format('Y-m-d') : '' }}',
            return_date: '{{ $tripTicket->end_date ? $tripTicket->end_date->format('Y-m-d') : '' }}',
            destination: '{{ $tripTicket->destination }}',
            purpose: '{{ str_replace(["\r", "\n"], ' ', $tripTicket->purpose) }}',
            recommended: {
                name: '{{ $travelOrder->recommended_by[0]['name'] ?? '' }}',
                position: '{{ $travelOrder->recommended_by[0]['position'] ?? '' }}'
            },
            forward_to: '',
            remarks: '',
        },
        get purposeLines() {
            let lines = this.form.purpose.match(/.{1,75}(\s|$)/g) || [''];
            return lines;
        }
    }">
        <div class="fixed inset-0 top-[65px] overflow-y-auto">

            <div class="w-[8.5in] h-10 mx-auto my-4 flex justify-between items-center print:hidden">
                <div class="flex gap-2 items-center">
                    <flux:button icon="arrow-uturn-left" variant="filled" color="red"
                        href="{{ route('client.trip-ticket') }}">
                        Back
                    </flux:button>

                    <div class="flex flex-col">
                        @if ($latestTracking && $latestTracking->status === 'Forwarded')
                            <div class="flex items-center gap-1.5 text-emerald-600 font-medium text-sm">
                                <flux:icon name="check-circle" variant="solid" class="w-4 h-4 text-emerald-600" />
                                <span>Forwarded</span>
                            </div>
                            <div class="flex items-center gap-1 text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                                <flux:icon name="calendar" variant="solid" class="w-3.5 h-3.5 text-zinc-400" />
                                <span class="text-xs">{{ $latestTracking->date_released->format('M d, Y h:i a') }}</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-amber-600 font-medium text-sm">
                                <flux:icon name="exclamation-triangle" variant="solid" class="w-4 h-4 text-amber-500" />
                                <span>Not Forwarded</span>
                            </div>
                            <div class="flex items-center gap-1 text-sm text-zinc-400 dark:text-zinc-500 mt-0.5">
                                <flux:icon name="calendar" variant="solid" class="w-3.5 h-3.5 text-zinc-400/70" />
                                <span class="text-xs">Pending...</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <flux:modal.trigger name="forward-document-modal">
                        <flux:button icon="arrows-right-left" variant="primary" color="amber">Forward</flux:button>
                    </flux:modal.trigger>
                    <flux:modal.trigger name="travel-order-info">
                        <flux:button icon="document-text" variant="primary" color="sky">Update Info</flux:button>
                    </flux:modal.trigger>
                    <flux:button icon="printer" variant="primary" color="emerald" onclick="window.print()">Print
                    </flux:button>
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
                            <div class="flex mb-2 justify-between">
                                <span class="font-bold">TO No.:</span>
                                <span class="border-b border-black w-32 ms-auto uppercase" x-text="form.to_no"></span>
                            </div>
                            <div class="flex mb-1 justify-between">
                                <span class="font-bold">Date:</span>
                                <span class="border-b border-black font-bold w-32 ms-auto "
                                    x-text="form.date ? new Date(form.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : ''">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-[11pt] leading-none">
                        <div class="w-full flex mb-4">
                            <div class="flex">
                                <div class="font-bold w-22">Name:</div>
                                <div class="flex flex-col gap-1">
                                    <template x-for="(p, index) in form.personnel" :key="index">
                                        <span class="w-56 mb-1 border-b border-black min-h-[1em] uppercase"
                                            x-text="p.name"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="flex-1 flex ms-4">
                                <div class="font-bold">Salary per Month:</div>
                                <div class="flex flex-col gap-1 ms-auto">
                                    <template x-for="(p, index) in form.personnel" :key="index">
                                        <span class="w-40 mb-1 border-b border-black min-h-[1em]" x-text="p.salary"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="w-full flex mb-8">
                            <div class="flex">
                                <div class="font-bold w-22">Position:</div>
                                <div class="flex flex-col gap-1">
                                    <template x-for="(p, index) in form.personnel" :key="index">
                                        <span class="w-56 mb-1 border-b border-black min-h-[1em]"
                                            x-text="p.position"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="flex-1 flex ms-4">
                                <div class="font-bold">Office Station:</div>
                                <div class="flex flex-col gap-1 ms-auto">
                                    <template x-for="(p, index) in form.personnel" :key="index">
                                        <span class="w-40 mb-1 border-b border-black min-h-[1em]" x-text="p.office"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="w-full flex mb-3">
                            <div class="flex">
                                <div class="font-bold w-22">Departure:</div>
                                <div class="flex flex-col gap-1">
                                    <span class="w-56 border-b border-black min-h-[1em]"
                                        x-text="form.departure ? new Date(form.departure).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : ''">
                                    </span>
                                </div>
                            </div>

                            <div class="flex-1 flex ms-4">
                                <div class="font-bold">Return Date:</div>
                                <div class="flex flex-col gap-1 ms-auto">
                                    <span class="w-40 border-b border-black min-h-[1em]"
                                        x-text="form.return_date ? new Date(form.return_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : ''">
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex mb-2">
                            <div class="font-bold w-22">Destination:</div>
                            <div class="flex-1">
                                <span class="inline-block w-full border-b border-black min-h-[1em]"
                                    x-text="form.destination"></span>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <span class="font-bold">Specific Purpose of the Trip:</span>
                            <template x-for="(line, index) in purposeLines">
                                <div class="flex items-end border-b border-black min-h-[1.5rem] leading-none ms-26"
                                    x-text="line"></div>
                            </template>
                        </div>

                        <div class="flex mb-2 mt-3">
                            <div class="font-bold w-26">Objective(s):</div>
                            <div class="flex-1 border-b border-black min-h-[1em]"></div>
                        </div>

                        <div class="flex mb-2">
                            <div class="font-bold w-36">Per Diems Allowed:</div>
                            <div class="flex-1 border-b border-black min-h-[1em]"></div>
                        </div>

                        <div class="flex mb-2">
                            <div class="font-bold w-50">Assistant Laborers Allowed:</div>
                            <div class="flex-1 border-b border-black min-h-[1em]"></div>
                        </div>

                        <div class="flex mb-2">
                            <div class="font-bold w-84">Appropriation to which travel should be charged:</div>
                            <div class="flex-1 border-b border-black min-h-[1em]"></div>
                        </div>

                        <div class="flex mb-2">
                            <div class="font-bold w-60">Remarks or Special Instructions:</div>
                            <div class="flex-1 border-b border-black min-h-[1em]"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-20 text-center mt-12 px-10 leading-none">
                            <div>
                                <p class="text-[12pt] mb-10 text-left pl-4">Recommended by:</p>
                                <div class="mb-6">
                                    <p class="font-bold underline text-[11pt] uppercase" x-text="form.recommended.name">
                                    </p>
                                    <p class="text-[12pt] mt-1" x-text="form.recommended.position"></p>
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

                <img src="{{ asset('images/bot.png') }}" alt="Footer" class="bot">
            </div>
        </div>

        <flux:modal name="forward-document-modal" class="md:w-[500px]">
            <form action="{{ route('client.travel-order.track', $tripTicket->id) }}" method="POST"
                class="space-y-6 w-full">
                @csrf
                <div>
                    <flux:heading size="lg">Track Document</flux:heading>
                </div>

                <div class="w-full flex flex-col gap-2" x-data="{
                    search: '',
                    selectedOffice: 'Select recipient...',
                    offices: {{ json_encode($offices) }}
                }">
                    <flux:label>Forwarded to</flux:label>

                    <input type="hidden" name="route" x-model="form.forward_to" required>

                    <div class="w-full flex gap-2">
                        <div class="flex-1">
                            <flux:dropdown class="w-full">
                                <flux:button icon:trailing="chevron-down" class="w-full [&>svg]:ml-auto" align="start">
                                    <span x-text="selectedOffice"></span>
                                </flux:button>
                                <flux:menu class="max-h-60 overflow-y-auto !p-0">
                                    <div
                                        class="p-2 sticky top-0 bg-white dark:bg-zinc-900 border-b border-zinc-100 dark:border-zinc-800 z-10">
                                        <flux:input x-model="search" placeholder="Search office" icon="magnifying-glass"
                                            size="sm" clearable @click.stop @keydown.space.stop />
                                    </div>

                                    <template x-for="office in offices" :key="office">
                                        <flux:menu.item x-show="office.toLowerCase().includes(search.toLowerCase())"
                                            @click="form.forward_to = office; selectedOffice = office">
                                            <span x-text="office"></span>
                                        </flux:menu.item>
                                    </template>

                                    <div x-show="offices.filter(o => o.toLowerCase().includes(search.toLowerCase())).length === 0"
                                        class="p-3 text-sm text-zinc-400 text-center">
                                        No offices found.
                                    </div>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>
                </div>

                <flux:textarea label="Remarks" name="remarks" x-model="form.remarks" rows="4"
                    placeholder="Add remarks here..." />

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost" type="button">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" color="emerald" type="submit">Forward</flux:button>
                </div>
            </form>
        </flux:modal>

        {{-- MODAL SECTION --}}
        <flux:modal name="travel-order-info" class="md:w-[800px]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Update Travel Order Info</flux:heading>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="TO No." x-model="form.to_no" />
                    <flux:input type="date" label="Date" x-model="form.date" />
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <flux:label>Personnel Details</flux:label>
                        <flux:button variant="ghost" size="sm" icon="plus"
                            @click="form.personnel.push({name: '', salary: '', position: '', office: ''})">Add Personnel
                        </flux:button>
                    </div>

                    <div class="grid grid-cols-12 gap-2 px-1">
                        <div class="col-span-4">
                            <flux:label>Name</flux:label>
                        </div>
                        <div class="col-span-2">
                            <flux:label>Salary</flux:label>
                        </div>
                        <div class="col-span-3">
                            <flux:label>Position</flux:label>
                        </div>
                        <div class="col-span-2">
                            <flux:label>Office</flux:label>
                        </div>
                        <div class="col-span-1"></div>
                    </div>

                    <template x-for="(p, index) in form.personnel" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-center border-b border-gray-100 pb-2">
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
                            <div class="col-span-1 flex justify-center">
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

                <div class="space-y-2">
                    <flux:label>Recommended By</flux:label>
                    <div class="flex gap-2">
                        <flux:input class="flex-1" x-model="form.recommended.name" placeholder="Name" />
                        <flux:input class="flex-1" x-model="form.recommended.position" placeholder="Position" />
                    </div>
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
    