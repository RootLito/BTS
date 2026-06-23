@extends('client.layout')

@section('content')
    <div x-data="{
        form: {
            to_no: '{{ $travelOrder->to_no ?? '' }}',
            date: '{{ $travelOrder->date ? $travelOrder->date->format('Y-m-d') : '' }}',
            personnel: [
                @foreach ($travelOrder->personnel ?? [] as $p)
                { 
                    name: '{{ $p['name'] ?? '' }}', 
                    position: '{{ $p['position'] ?? '' }}' 
                }, @endforeach
            ],
            route: '{{ $travelOrder->route ?? '' }}',
            departure: '{{ $travelOrder->departure ? $travelOrder->departure->format('Y-m-d') : '' }}',
            return_date: '{{ $travelOrder->return_date ? $travelOrder->return_date->format('Y-m-d') : '' }}',
            purpose: '{{ str_replace(["\r", "\n", "'"], [' ', ' ', "\'"], $travelOrder->purpose ?? '') }}',
            rd: '{{ $travelOrder->rd ?? 'RELLY B. GARCIA' }}',
            oic: '{{ $travelOrder->oic ?? 'EMMILOU J. UY, CPA, MBA' }}'
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
                        href="{{ route('client.national-to') }}">
                        Back
                    </flux:button>

                    <div class="flex flex-col">
                        @if ($latestTracking && $latestTracking->status === 'Released' && !is_null($latestTracking->date_released))
                            <div class="flex items-center gap-1.5 text-sky-600 font-medium text-sm">
                                <flux:icon name="check-circle" variant="solid" class="w-4 h-4 text-sky-600" />
                                <span>Forwarded</span>
                            </div>
                            <div class="flex items-center gap-1 text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                                <flux:icon name="calendar" variant="solid" class="w-3.5 h-3.5 text-zinc-400" />
                                <span
                                    class="text-xs">{{ \Carbon\Carbon::parse($latestTracking->date_released)->format('M d, Y h:i a') }}</span>
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

            <div
                class="printable-folio bg-white mx-auto shadow-lg print:shadow-none mb-4 print:fixed print:top-0 print:left-0">
                <img src="{{ asset('images/top.png') }}" alt="Header" class="top">

                <div class="ticket-container leading-[1.2] text-justify text-[12pt]">
                    <div class="flex justify-end  leading-none my-6">
                        <div class="w-44">
                            <div class="flex mb-1 justify-between">
                                <span class="font-bold">Date:</span>
                                <span class="font-bold w-32 ms-auto"
                                    x-text="form.date ? new Date(form.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : ''">
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-8">
                        <h1 class="text-[16pt] font-bold leading-none uppercase my-4">TRAVEL ORDER NO. <span
                                x-text="form.to_no" class="underline"></span></h1>
                    </div>

                    <div class="leading-none mb-8">
                        <template x-for="(p, index) in form.personnel" :key="index">
                            <h2 class="font-bold leading-none grid grid-cols-[40px_1fr]">
                                <span class="uppercase">
                                    <span x-text="index === 0 ? 'TO:' : ''"></span>
                                </span>

                                <div>
                                    <span class="uppercase"
                                        x-text="p.name || 'Name'"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span class="italic Capitalize" x-text="p.position || 'Position'"></span>
                                </div>
                            </h2>
                        </template>
                    </div>

                    <div class="text-[12pt]">
                        <p class="indent-10 mb-6">
                            You are hereby directed to travel on Official Business via Cebu Pacific or Philippine Airlines,
                            and Ferry Boats to <span class="underline font-bold uppercase" x-text="form.route"></span> on
                            <span class="underline font-bold"
                                x-text="(() => {
                                    if (!form.departure) return '';
                                    let start = new Date(form.departure);
                                    let end = form.return_date ? new Date(form.return_date) : start;
                                    
                                    if (start.getMonth() === end.getMonth() && start.getFullYear() === end.getFullYear()) {
                                        let monthYear = start.toLocaleDateString('en-US', { month: 'long', year: 'numeric' }).split(' ');
                                        return start.getDate() === end.getDate() 
                                            ? `${monthYear[0]} ${start.getDate()}, ${monthYear[1]}`
                                            : `${monthYear[0]} ${start.getDate()}-${end.getDate()}, ${monthYear[1]}`;
                                    } else {
                                        return `${start.toLocaleDateString('en-US', { month: 'long', day: 'numeric' })} - ${end.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`;
                                    }
                                })()"></span>.
                        </p>

                        <p class="mb-10">
                            Purpose&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<span x-text="form.purpose"></span>
                        </p>

                        <p class="mb-8">
                            It is understood that a report shall be submitted on your completion on this travel.
                        </p>

                        <div class="w-40 ms-auto mb-8">
                            <p class="mb-8">Approved by:</p>
                            <p class="underline font-bold leading-none" x-text="form.rd"></p>
                            <p>Regional Director</p>
                        </div>

                        <div class="w-full flex items-center mb-6">
                            <span class="w-full border-t-1 border-dashed "></span>
                        </div>

                        <p>Transportation Order</p>
                        <div class="w-full flex justify-between mb-8">
                            <p>Expiry Date _______________</p>
                            <p class="w-64">Validation Stamp</p>
                        </div>

                        <p class="mb-6">
                            Please issue plane ticket to
                            <template x-for="(p, index) in form.personnel" :key="index">
                                <span class="uppercase font-bold"
                                    x-text="p.name + (index < form.personnel.length - 1 ? ', ' : '')"></span>
                            </template>
                            from <span x-text="form.route" class="uppercase underline font-bold"></span> in the amount of
                            __________________________ chargeable against Bureau of Fisheries and Aquatic
                            Resources, Regional Office XI, Davao City as per Travel Order.
                        </p>

                        <p class="mb-8">
                            The mode of payment for this particular travel is:______________________________________
                        </p>

                        <div class="w-40 ms-auto mb-6">
                            <p class="underline font-bold leading-none" x-text="form.rd"></p>
                            <p>Regional Director</p>
                        </div>

                        <p>In case of credit </p>
                        <div class="w-full flex justify-between mb-6">
                            <p>Certifications as to Funds:</p>
                            <p class="w-64">Ticket Issued by:</p>
                        </div>

                        <div class="w-52 flex flex-col justify-center ms-auto text-center">
                            <p>___________________</p>
                            <p>(Representative)</p>
                        </div>

                        <div class="w-53 flex flex-col justify-center text-center">
                            <p class="underline font-bold leading-none" x-text="form.oic"></p>
                            <p>OIC, Accounting Unit</p>
                        </div>

                        <div class="ms-auto w-54 flex flex-col">
                            <div class="flex justify-between">
                                <span>Plane Ticket No:</span>
                                <span>____________</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Date of Issue:</span>
                                <span>____________</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Place of Issue:</span>
                                <span>____________</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sales Report No:</span>
                                <span>____________</span>
                            </div>
                        </div>
                    </div>
                </div>

                <img src="{{ asset('images/bot.png') }}" alt="Footer" class="bot">
            </div>
        </div>

        {{-- FORWARD DOCUMENT MODAL --}}
        <flux:modal name="forward-document-modal" class="md:w-[500px]">
            <form action="{{ route('client.national-to.track', $travelOrder->id) }}" method="POST"
                class="space-y-6 w-full">
                @csrf
                <div>
                    <flux:heading size="lg">Track Document</flux:heading>
                </div>

                <flux:input label="Document Number" name="document_no" required />

                <div class="w-full flex flex-col gap-2">
                    <flux:label>Forwarded to</flux:label>
                    <input type="hidden" name="route" id="forward_to_input" required>
                    <flux:dropdown class="w-full">
                        <flux:button id="office_dropdown_button" icon-trailing="chevron-down"
                            class="w-full [&>svg]:ml-auto" align="start">
                            Select recipient...
                        </flux:button>

                        <flux:menu class="max-h-60 overflow-y-auto">
                            @foreach ($offices as $office)
                                <flux:menu.item
                                    onclick="
                                    document.getElementById('forward_to_input').value = '{{ $office }}';
                                    document.getElementById('office_dropdown_button').innerText = '{{ $office }}';
                                ">
                                    {{ $office }}
                                </flux:menu.item>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <flux:textarea label="Remarks" name="remarks" rows="4" placeholder="Add remarks here..." />

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost" type="button">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" color="emerald" type="submit">Forward</flux:button>
                </div>
            </form>
        </flux:modal>

        {{-- UPDATE DETAILS MODAL --}}
        <flux:modal name="travel-order-info" class="md:w-[700px]">
            <form action="{{ route('client.national-to.update', $travelOrder->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <input type="hidden" name="to_no" x-bind:value="form.to_no">
                <input type="hidden" name="date" x-bind:value="form.date">
                <input type="hidden" name="route" x-bind:value="form.route">
                <input type="hidden" name="departure" x-bind:value="form.departure">
                <input type="hidden" name="return_date" x-bind:value="form.return_date">
                <input type="hidden" name="purpose" x-bind:value="form.purpose">
                <input type="hidden" name="rd" x-bind:value="form.rd">
                <input type="hidden" name="oic" x-bind:value="form.oic">
                <input type="hidden" name="personnel" :value="JSON.stringify(form.personnel)">

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
                        <flux:button variant="ghost" size="sm" icon="plus" type="button"
                            @click="form.personnel.push({name: '', position: ''})">Add Personnel
                        </flux:button>
                    </div>
                    <template x-for="(p, index) in form.personnel" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-end border-b border-gray-100 pb-2">
                            <div class="col-span-6">
                                <flux:input x-model="p.name" placeholder="Name" />
                            </div>
                            <div class="col-span-5">
                                <flux:input x-model="p.position" placeholder="Position" />
                            </div>
                            <div class="col-span-1">
                                <flux:button variant="ghost" icon="trash" size="sm" type="button"
                                    @click="form.personnel.splice(index, 1)" x-show="form.personnel.length > 1" />
                            </div>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <flux:input label="Route" x-model="form.route" />
                    <flux:input type="date" label="Departure Date" x-model="form.departure" />
                    <flux:input type="date" label="Return Date" x-model="form.return_date" />
                </div>

                <flux:textarea label="Purpose" x-model="form.purpose" rows="3" />

                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Regional Director (RD)" x-model="form.rd" />
                    <flux:input label="OIC, Accounting Unit" x-model="form.oic" />
                </div>

                <div class="flex">
                    <flux:spacer />
                    <flux:button variant="primary" color="emerald" type="submit">Save Details</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>
@endsection
