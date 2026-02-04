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

                    // 1. Set the Start Date string directly from FullCalendar
                    this.selection.displayStart = info.startStr;

                    // 2. Handle the End Date
                    // info.end is the day AFTER the selection. 
                    // We subtract 1 day to show the inclusive date in the UI.
                    let endAdjusted = new Date(info.end);
                    endAdjusted.setDate(endAdjusted.getDate() - 1);

                    // Format to YYYY-MM-DD manually to avoid Timezone/ISO shifts
                    let year = endAdjusted.getFullYear();
                    let month = String(endAdjusted.getMonth() + 1).padStart(2, '0');
                    let day = String(endAdjusted.getDate()).padStart(2, '0');
                    
                    this.selection.displayEnd = `${year}-${month}-${day}`;

                    // 3. Calculate Total Days correctly
                    // Since FC end is exclusive, (End - Start) already gives the correct count
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
    <flux:heading size="xl" level="1">Booking</flux:heading>
    <flux:text class="mt-2 mb-6 text-base">Plan and schedule a trip</flux:text>

    <div class="flex-1 grid grid-cols-3 gap-10 min-h-0">
        <flux:card class="col-span-2 flex flex-col gap-6 overflow-hidden">
            <flux:heading size="lg">Calendar</flux:heading>
            <flux:callout variant="secondary" icon="information-circle"
                heading="Click and drag to select a date range" />



            <div class="flex-1 min-h-0">
                <div x-ref="calendar" class="h-full"></div>
            </div>
        </flux:card>

        <flux:card class="col-span-1 flex flex-col">
            <flux:heading size="lg" class="mb-6 flex justify-between items-start">Booking Summary
                <template x-if="selection.totalDays > 0">
                    <flux:badge variant="pill" color="emerald" size="sm" icon="calendar">
                        <span x-text="selection.totalDays"></span>
                        <span class="ml-1" x-text="selection.totalDays === 1 ? 'day' : 'days'"></span>
                    </flux:badge>
                </template>
            </flux:heading>

            <flux:textarea label="Purpose" resize="none" class="mb-2" />
            <flux:input type="text" label="Destination" class="mb-2" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
                <flux:input type="date" label="Start date" x-model="selection.displayStart" />
                <flux:input type="date" label="End date" x-model="selection.displayEnd" />
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

            <flux:select label="Select Driver" placeholder="Choose a driver..." class="mb-2">
                @foreach($drivers as $driver)
                <flux:select.option value="{{ $driver->id }}" :disabled="$driver->status !== 'Available'">
                    {{ $driver->name }}
                    @if($driver->status !== 'Available')
                    ({{ $driver->status }})
                    @endif
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select label="Select Vehicle" placeholder="Choose a vehicle...">
                @foreach($vehicles as $vehicle)
                <flux:select.option value="{{ $vehicle->id }}" :disabled="$vehicle->status !== 'Available'">
                    {{ $vehicle->vehicle }} ({{ $vehicle->plate_no }})
                    @if($vehicle->status !== 'Available')
                    — {{ $vehicle->status }}
                    @endif
                </flux:select.option> @endforeach
            </flux:select>

            <flux:button variant="primary" color="emerald" class="mt-auto w-full"
                x-bind:disabled="!selection.displayStart">
                Confirm
            </flux:button>
        </flux:card>
    </div>
</div>
@endsection