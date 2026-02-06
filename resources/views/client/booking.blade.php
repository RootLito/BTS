@extends('client.layout')

@section('content')
<div class="w-full h-full flex flex-col" x-data="{ 
        selection: {
            start: null,
            end: null,
            displayStart: '',
            displayEnd: '',
            totalDays: 0
        },
        initCalendar() {
            let calendar = new FullCalendar(this.$refs.calendar, {
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                height: '100%',
                selectable: true,
                unselectAuto: false,
                showNonCurrentDates: false,
                fixedWeekCount: false,
                headerToolbar: {
                    left: 'title',
                    center: '',
                    right: 'prev,next'
                },
                select: (info) => {
                    this.selection.start = info.start;
                    this.selection.end = info.end;
                    this.selection.displayStart = info.startStr;
                    let endAdjusted = new Date(info.end);
                    endAdjusted.setDate(endAdjusted.getDate() - 1);
                    let year = endAdjusted.getFullYear();
                    let month = String(endAdjusted.getMonth() + 1).padStart(2, '0');
                    let day = String(endAdjusted.getDate()).padStart(2, '0');
                    this.selection.displayEnd = `${year}-${month}-${day}`;
                    const diffTime = Math.abs(info.end - info.start);
                    this.selection.totalDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
                },
                selectAllow: (selectInfo) => {
                    return selectInfo.start >= new Date().setHours(0,0,0,0);
                }
            });
            calendar.render();
        }
    }" x-init="initCalendar()">
    <div class="w-full flex justify-between items-center mb-6">
        <div class="flex flex-col">
            <flux:heading size="xl" level="1">Booking</flux:heading>
            <flux:text class="mt-2text-base">Plan and schedule a trip</flux:text>
        </div>


        <div class="w-72">
            @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                <flux:callout color="emerald" icon="check-circle" heading="{{ session('status') }}" />
            </div>
            @endif
        </div>

    </div>

    <div class="flex-1 grid grid-cols-3 gap-10 min-h-0">
        <flux:card class="col-span-2 flex flex-col gap-6 overflow-hidden">
            <flux:heading size="lg">Calendar</flux:heading>
            <flux:callout variant="secondary" icon="information-circle"
                heading="Click and drag to select a date range" />
            <div class="flex-1 min-h-0">
                <div x-ref="calendar" class="h-full"></div>
            </div>
        </flux:card>

        <div class="col-span-1">
            <flux:card>
                <form action="{{ route('trips.store') }}" method="POST" class="flex flex-col h-full">
                    @csrf
                    <flux:heading size="lg" class="mb-6 flex justify-between items-start">Booking Summary
                        <template x-if="selection.totalDays > 0">
                            <flux:badge variant="pill" color="emerald" size="sm" icon="calendar">
                                <span x-text="selection.totalDays"></span>
                                <span class="ml-1" x-text="selection.totalDays === 1 ? 'day' : 'days'"></span>
                            </flux:badge>
                        </template>
                    </flux:heading>



                    <flux:textarea label="Purpose" resize="none" class="mb-2" name="purpose" />
                    <flux:input type="text" label="Destination" class="mb-2" name="destination" />
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
                        <flux:input type="date" label="Start date" x-model="selection.displayStart" name="start_date" />
                        <flux:input type="date" label="End date" x-model="selection.displayEnd" name="end_date" />
                    </div>



                    <div x-data="{ 
                passengers: [], 
                newName: '',
                add() {
                    if (this.newName.trim() !== '') {
                        this.passengers.push(this.newName.trim());
                        this.newName = '';
                    }
                },
                remove(index) {
                    this.passengers.splice(index, 1);
                }
            }" class="mb-2">
                        <flux:label class="mb-2">Authorized Passengers</flux:label>

                        <flux:modal.trigger name="passenger_modal">
                            <flux:button variant="outline" class="w-full">
                                <div class="flex items-center justify-between w-full gap-2">
                                    <span x-show="passengers.length === 0" class="text-zinc-400 font-normal">
                                        Add passengers
                                    </span>

                                    <span x-show="passengers.length > 0" class="truncate text-left flex-1">
                                        <span x-text="passengers[0]"></span>
                                        <template x-if="passengers.length > 1">
                                            <span class="text-zinc-500 text-sm italic">
                                                (+<span x-text="passengers.length - 1"></span> more...)
                                            </span>
                                        </template>
                                    </span>

                                    <flux:icon.users variant="micro" class="text-zinc-400 shrink-0" />
                                </div>
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal name="passenger_modal" class="md:w-[450px] space-y-6">
                            <div>
                                <flux:heading size="lg">Add Passengers</flux:heading>
                                <flux:subheading>Manage the authorized passengers for this booking.</flux:subheading>
                            </div>

                            <div class="flex gap-2">
                                <flux:input x-model="newName" @keydown.enter.prevent="add()" class="w-full"
                                    placeholder="Type name..." x-on:click.stop />
                                <flux:button @click="add()" variant="primary" color="emerald">Add</flux:button>
                            </div>

                            <flux:separator />

                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                <template x-if="passengers.length === 0">
                                    <div class="text-center py-4 text-zinc-400 text-sm">No passengers added yet.</div>
                                </template>

                                <template x-for="(name, index) in passengers" :key="index">
                                    <div
                                        class="flex justify-between items-center p-2 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                        <span x-text="name"></span>
                                        <flux:button variant="subtle" size="xs" color="red" icon="trash"
                                            @click="remove(index)" />
                                    </div>
                                </template>
                            </div>

                            <flux:modal.close>
                                <flux:button variant="primary" color="emerald" class="w-full">Done</flux:button>
                            </flux:modal.close>
                        </flux:modal>
                        <input type="hidden" name="passengers" :value="JSON.stringify(passengers)">
                    </div>

                    {{-- <div x-data="{ 
                    selectedId: '', 
                    selectedName: '',
                    select(id, name) {
                        this.selectedId = id;
                        this.selectedName = name;
                        $flux.modal('driver_modal').close();
                    },
                    clear() {
                        this.selectedId = '';
                        this.selectedName = '';
                        $flux.modal('driver_modal').close();
                    }
                }" class="mb-4">
                        <flux:label class="mb-2">Assigned Driver</flux:label>

                        <flux:modal.trigger name="driver_modal">
                            <flux:button variant="outline" class="w-full text-left">
                                <div class="flex items-center justify-between w-full gap-2">
                                    <span x-show="!selectedId" class="text-zinc-400 font-normal">Choose a
                                        driver...</span>
                                    <span x-show="selectedId" x-text="selectedName"
                                        class="truncate flex-1 font-medium"></span>
                                    <flux:icon.user variant="micro" class="text-zinc-400 shrink-0" />
                                </div>
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal name="driver_modal" class="md:w-[450px] flex flex-col gap-2">
                            <div>
                                <flux:heading size="lg">Select Driver</flux:heading>
                                <flux:subheading>Request a driver or not required.</flux:subheading>
                            </div>

                            <div class="space-y-2 max-h-80 overflow-y-auto mt-4">
                                @foreach($drivers as $driver)
                                <button type="button" @click="select('{{ $driver->id }}', '{{ $driver->name }}')"
                                    @disabled($driver->status !== 'Available')
                                    class="w-full flex justify-between items-center p-3 rounded-lg border text-left
                                    transition-colors
                                    {{ $driver->status === 'Available' ? 'border-zinc-200 hover:bg-zinc-50
                                    dark:border-zinc-700 dark:hover:bg-zinc-800' : 'opacity-50 cursor-not-allowed
                                    bg-zinc-100 border-zinc-200' }}">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-zinc-800 dark:text-white">{{ $driver->name
                                            }}</span>
                                        <span class="text-xs text-zinc-500 italic">{{ $driver->license_no }}</span>
                                    </div>
                                    @php
                                    $statusColor = match($driver->status) {
                                    'Available' => 'emerald',
                                    'On Trip' => 'yellow',
                                    'Leave' => 'red',
                                    default => 'zinc',
                                    };
                                    @endphp

                                    <flux:badge size="sm" color="{{ $statusColor }}" inset="top bottom">
                                        {{ $driver->status }}
                                    </flux:badge>
                                </button>
                                @endforeach
                                <button type="button" @click="clear()"
                                    class="w-full flex items-center justify-center gap-2 p-3 rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors group">
                                    <flux:icon.x-circle variant="micro"
                                        class="text-red-500 group-hover:text-red-600 dark:text-red-400" />
                                    <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                        No Driver Required
                                    </span>
                                </button>
                            </div>
                        </flux:modal>

                        <input type="hidden" name="driver_id" :value="selectedId">
                    </div> --}}

                    <flux:button type="submit" variant="primary" color="emerald" class="w-full mt-4">
                        Confirm
                    </flux:button>
                </form>
            </flux:card>
        </div>


    </div>
</div>
@endsection