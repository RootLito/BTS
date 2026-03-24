@extends('admin.layout')

@section('content')
    <div class="w-full flex-1 flex flex-col h-full">


        <div class="w-full flex justify-between">
            <div>
                <flux:heading size="xl" level="1">Booking</flux:heading>
                <flux:text class="mt-2 mb-6 text-base">Manage ticketing and travel orders efficiently</flux:text>
            </div>
            <div class="w-72">
                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                        <flux:callout color="emerald" icon="check-circle" heading="{{ session('status') }}" />
                    </div>
                @endif
            </div>
        </div>

        <div class="w-full h-full flex flex-col" x-data="{
            selection: {
                start: null,
                end: null,
                displayStart: '',
                displayEnd: '',
                totalDays: 0
            },
            activeEvent: null,
            events: {{ $events->toJson() }},
            availableDriversCount: {{ $drivers->where('status', 'Available')->count() }},
        
            initCalendar() {
                let calendar = new FullCalendar(this.$refs.calendar, {
                    plugins: [dayGridPlugin, interactionPlugin],
                    initialView: 'dayGridMonth',
                    height: '100%',
                    selectable: true,
                    unselectAuto: false,
                    showNonCurrentDates: false,
                    fixedWeekCount: false,
                    events: this.events,
                    dayMaxEvents: 2,
        
                    headerToolbar: {
                        left: 'title',
                        center: '',
                        right: 'prev,next today'
                    },
        
                    // --- SELECTION LOGIC (For Booking) ---
                    select: (info) => {
                        // Check if any drivers are available before allowing selection
                        if (this.availableDriversCount <= 0) {
                            calendar.unselect();
                            $flux.modal('no_drivers_modal').show();
                            return;
                        }
        
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
                        return selectInfo.start >= new Date().setHours(0, 0, 0, 0);
                    },
        
                    // --- CLICK LOGIC (For Viewing Existing Trips) ---
                    eventClick: (info) => {
                        this.activeEvent = {
                            title: info.event.title,
                            start: info.event.extendedProps.display_start,
                            end: info.event.extendedProps.display_end,
                            purpose: info.event.extendedProps.purpose,
                            driver: info.event.extendedProps.driver,
                            office: info.event.extendedProps.office,
                            status: info.event.extendedProps.status
                        };
                        $flux.modal('trip_details_modal').show();
                    }
                });
                calendar.render();
            }
        }" x-init="initCalendar()">


            <div class="flex-1 grid grid-cols-3 gap-8 min-h-0">
                <flux:card class="col-span-2 flex flex-col gap-6 overflow-hidden">
                    <flux:heading size="lg">Calendar</flux:heading>
                    <flux:callout variant="secondary" icon="information-circle"
                        heading="Click and drag to select a date range" />
                    <div class="flex-1 min-h-0">
                        <div x-ref="calendar" class="h-full"></div>
                    </div>
                    <flux:modal name="trip_details_modal" class="min-w-[25rem]">
                        <template x-if="activeEvent">
                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg" x-text="activeEvent.title"></flux:heading>
                                    <flux:text size="xs" variant="subtle" class="flex items-center gap-1">
                                        <flux:icon.calendar variant="mini" class="ml-1 size-4" />
                                        <span x-text="activeEvent.start"></span>

                                        <template x-if="activeEvent.start !== activeEvent.end">
                                            <span class="flex items-center gap-1">
                                                <span>-</span>
                                                <flux:icon.calendar variant="mini" class="ml-1 size-4" />
                                                <span x-text="activeEvent.end"></span>
                                            </span>
                                        </template>


                                    </flux:text>
                                </div>

                                <div class="space-y-3">
                                    {{-- Purpose - Full Width --}}
                                    <div
                                        class="p-4 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                                        <flux:text size="xs" variant="subtle"
                                            class="font-semibold uppercase tracking-wider text-zinc-500">
                                            Purpose
                                        </flux:text>
                                        <flux:text class="mt-1 block" x-text="activeEvent.purpose || 'Official Business'">
                                        </flux:text>
                                    </div>

                                    {{-- Driver & Office - Grid --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div
                                            class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                                            <flux:text size="xs" variant="subtle" class="uppercase">Driver
                                            </flux:text>
                                            <flux:text size="sm" weight="medium" class="block mt-0.5"
                                                x-text="activeEvent.driver || 'Unassigned'"></flux:text>
                                        </div>

                                        <div
                                            class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                                            <flux:text size="xs" variant="subtle" class="uppercase">Office
                                            </flux:text>
                                            <flux:text size="sm" weight="medium" class="block mt-0.5"
                                                x-text="activeEvent.office || 'N/A'"></flux:text>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <flux:spacer />
                                    <flux:button x-on:click="$flux.modal('trip_details_modal').close()" variant="ghost">
                                        Close
                                    </flux:button>
                                </div>
                            </div>
                        </template>
                    </flux:modal>
                </flux:card>

                <div class="col-span-1">
                    <flux:card>
                        <form action="{{ route('book.store') }}" method="POST" class="flex flex-col h-full">
                            @csrf

                            <flux:heading size="lg" class="mb-6 flex justify-between items-start">Booking Summary
                                <template x-if="selection.totalDays > 0">
                                    <flux:badge variant="pill" color="emerald" size="sm" icon="calendar">
                                        <span x-text="selection.totalDays"></span>
                                        <span class="ml-1" x-text="selection.totalDays === 1 ? 'day' : 'days'"></span>
                                    </flux:badge>
                                </template>
                            </flux:heading>

                            <flux:input type="text" label="Office" class="mb-2" name="office" />
                            <flux:textarea label="Purpose" resize="none" class="mb-2" name="purpose" />
                            <flux:input type="text" label="Destination" class="mb-2" name="destination"
                                placeholder="Ex: REGION XII, GENERAL SANTOS CITY" />
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
                                <flux:input type="date" label="Start date" x-model="selection.displayStart"
                                    name="start_date" />
                                <flux:input type="date" label="End date" x-model="selection.displayEnd"
                                    name="end_date" />
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
                                        <flux:subheading>Manage the authorized passengers for this booking.
                                        </flux:subheading>
                                    </div>

                                    <div class="flex gap-2">
                                        <flux:input x-model="newName" @keydown.enter.prevent="add()" class="w-full"
                                            placeholder="Type name, Position" x-on:click.stop />
                                        <flux:button @click="add()" variant="primary" color="emerald">Add</flux:button>
                                    </div>

                                    <flux:separator />

                                    <div class="space-y-2 max-h-64 overflow-y-auto">
                                        <template x-if="passengers.length === 0">
                                            <div class="text-center py-4 text-zinc-400 text-sm">No passengers added yet.
                                            </div>
                                        </template>

                                        <template x-for="(name, index) in passengers" :key="index">
                                            <div
                                                class="flex justify-between items-center p-2 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                                <span x-text="name"></span>
                                                <flux:button variant="subtle" size="xs" color="red"
                                                    icon="trash" @click="remove(index)" />
                                            </div>
                                        </template>
                                    </div>

                                    <flux:modal.close>
                                        <flux:button variant="primary" color="emerald" class="w-full">Done</flux:button>
                                    </flux:modal.close>
                                </flux:modal>
                                <input type="hidden" name="passengers" :value="JSON.stringify(passengers)">
                            </div>


                            <flux:button type="submit" variant="primary" color="emerald" class="w-full mt-4">
                                Confirm
                            </flux:button>
                        </form>
                    </flux:card>
                </div>


            </div>
        </div>
    </div>
@endsection
