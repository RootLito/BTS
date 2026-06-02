@extends('admin.layout')

@section('content')
    <div class="h-full flex flex-col min-h-0">
        <div class="flex items-end justify-between mb-10">
            <div>
                <flux:heading size="xl" level="1">Admin Dashboard</flux:heading>
                <flux:text class="mt-2">Overview of trip schedules, vehicle availability, and driver readiness</flux:text>
            </div>
            <flux:modal.trigger name="export-report-modal">
                <flux:button icon="printer" variant="outline" size="sm">Export Report</flux:button>
            </flux:modal.trigger>
        </div>
        <flux:modal name="export-report-modal" class="md:max-w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Export Trip Tickets Report</flux:heading>
                    <flux:subheading>Filter your trip ticket logs before downloading the spreadsheet.</flux:subheading>
                </div>

                <form id="filterForm" action="{{ route('admin.booking.export') }}" method="GET" class="space-y-4">

                    <input type="hidden" name="office" id="selected_office_input" value="">
                    <input type="hidden" name="period" id="selected_period_input" value="">

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Filter by Office</label>
                        <flux:dropdown>
                            <flux:button id="office_btn" icon-trailing="chevron-down" class="w-full justify-between">
                                All Offices
                            </flux:button>

                            <flux:menu class="w-full">
                                <flux:menu.radio.group>
                                    <flux:menu.radio
                                        onclick="document.getElementById('selected_office_input').value = ''; document.getElementById('office_btn').innerText = 'All Offices'"
                                        checked>
                                        All Offices
                                    </flux:menu.radio>
                                    @foreach (\App\Models\Client::distinct()->pluck('office') as $officeName)
                                        <flux:menu.radio
                                            onclick="document.getElementById('selected_office_input').value = '{{ $officeName }}'; document.getElementById('office_btn').innerText = '{{ $officeName }}'">
                                            {{ $officeName }}
                                        </flux:menu.radio>
                                    @endforeach
                                </flux:menu.radio.group>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Time Frame Filter</label>
                        <flux:dropdown>
                            <flux:button id="period_btn" icon-trailing="chevron-down" class="w-full justify-between">
                                No Preset Period
                            </flux:button>

                            <flux:menu class="w-full">
                                <flux:menu.radio.group>
                                    <flux:menu.radio
                                        onclick="document.getElementById('selected_period_input').value = ''; document.getElementById('period_btn').innerText = 'No Preset Period'"
                                        checked>
                                        No Preset Period
                                    </flux:menu.radio>
                                    <flux:menu.radio
                                        onclick="document.getElementById('selected_period_input').value = 'day'; document.getElementById('period_btn').innerText = 'Today'">
                                        Today</flux:menu.radio>
                                    <flux:menu.radio
                                        onclick="document.getElementById('selected_period_input').value = 'week'; document.getElementById('period_btn').innerText = 'This Week'">
                                        This Week</flux:menu.radio>
                                    <flux:menu.radio
                                        onclick="document.getElementById('selected_period_input').value = 'month'; document.getElementById('period_btn').innerText = 'This Month'">
                                        This Month</flux:menu.radio>
                                    <flux:menu.radio
                                        onclick="document.getElementById('selected_period_input').value = 'year'; document.getElementById('period_btn').innerText = 'This Year'">
                                        This Year</flux:menu.radio>
                                </flux:menu.radio.group>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input type="date" name="custom_start_date" label="Start Date" />
                        <flux:input type="date" name="custom_end_date" label="End Date" />
                    </div>

                    <div class="flex justify-end space-x-2 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" color="emerald" icon="document-text">
                            Export Excel
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        <div class="flex-1 grid grid-cols-3 grid-rows-4 gap-6 min-h-0">

            <flux:card class="flex flex-col">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:heading size="sm" variant="subtle" class="uppercase tracking-wider">Total Vehicles
                        </flux:heading>
                        <flux:text class="text-3xl font-bold mt-1">{{ $totalVehicles }}</flux:text>
                    </div>
                    <div class="p-2 bg-zinc-100 rounded-lg">
                        <flux:icon.truck variant="outline" class="size-5 text-zinc-600" />
                    </div>
                </div>

                <div class="mt-auto space-y-2">
                    <div class="flex h-2 w-full rounded-full overflow-hidden bg-zinc-100">
                        <div class="bg-emerald-500"
                            style="width: {{ $totalVehicles > 0 ? ($availVehicles / $totalVehicles) * 100 : 0 }}%"
                            title="Available"></div>
                        <div class="bg-yellow-400"
                            style="width: {{ $totalVehicles > 0 ? ($tripVehicles / $totalVehicles) * 100 : 0 }}%"
                            title="On Trip"></div>
                        <div class="bg-red-500"
                            style="width: {{ $totalVehicles > 0 ? ($maintVehicles / $totalVehicles) * 100 : 0 }}%"
                            title="Maintenance"></div>
                    </div>
                    <div class="flex gap-2">
                        <flux:badge size="sm" color="emerald" inset="top bottom">{{ $availVehicles }} Available
                        </flux:badge>
                        <flux:badge size="sm" color="yellow" inset="top bottom">{{ $tripVehicles }} On Trip
                        </flux:badge>
                        <flux:badge size="sm" color="red" inset="top bottom">{{ $maintVehicles }} Maintenance
                        </flux:badge>
                    </div>
                </div>
            </flux:card>

            <flux:card class="flex flex-col">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:heading size="sm" variant="subtle" class="uppercase tracking-wider">
                            Total Drivers
                        </flux:heading>
                        <flux:text class="text-3xl font-bold mt-1">{{ $totalDrivers }}</flux:text>
                    </div>
                    <div class="p-2 bg-zinc-100 rounded-lg">
                        <flux:icon.users variant="outline" class="size-5 text-zinc-600" />
                    </div>
                </div>

                <div class="mt-auto space-y-2">
                    <div class="flex justify-between text-xs">
                        <flux:text size="sm">Duty Status</flux:text>
                    </div>
                    <div class="flex h-2 w-full rounded-full overflow-hidden bg-zinc-100">
                        <div class="bg-emerald-500"
                            style="width: {{ $totalDrivers > 0 ? ($availDrivers / $totalDrivers) * 100 : 0 }}%"
                            title="Available"></div>
                        <div class="bg-yellow-400"
                            style="width: {{ $totalDrivers > 0 ? ($tripDrivers / $totalDrivers) * 100 : 0 }}%"
                            title="On Duty"></div>
                    </div>
                    <div class="flex gap-2">
                        <flux:badge size="sm" color="emerald" inset="top bottom">{{ $availDrivers }} Available
                        </flux:badge>
                        <flux:badge size="sm" color="yellow" inset="top bottom">{{ $tripDrivers }} On Travel
                        </flux:badge>
                    </div>
                </div>
            </flux:card>

            <flux:card class="row-span-2 flex flex-col min-h-0">
                <flux:heading size="lg" class="mb-4">Driver Status</flux:heading>
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <flux:table>
                        <flux:table.rows>
                            @foreach ($driversList as $driver)
                                <flux:table.row>
                                    <flux:table.cell class="flex items-center gap-2">
                                        <flux:avatar
                                            initials="{{ collect(explode(' ', $driver->name))->map(fn($n) => $n[0])->join('') }}"
                                            size="xs" />
                                        <flux:text weight="medium">{{ $driver->name }}</flux:text>
                                    </flux:table.cell>

                                    <flux:table.cell align="end">
                                        @php
                                            $dColor = match ($driver->status) {
                                                'Available' => 'emerald',
                                                'On Trip' => 'yellow',
                                                default => 'zinc',
                                            };
                                        @endphp

                                        <flux:badge :color="$dColor" size="sm" variant="pill">
                                            {{ $driver->status }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:card>

            <flux:card class="col-span-2 row-span-3 flex flex-col overflow-hidden">
                <div class="flex flex-col h-full" x-data="{
                    activeEvent: null,
                    events: {{ $events->toJson() }},
                    initCalendar() {
                        let calendar = new FullCalendar(this.$refs.calendar, {
                            plugins: [dayGridPlugin, interactionPlugin],
                            initialView: 'dayGridMonth',
                            height: '100%',
                            selectable: true,
                            showNonCurrentDates: false,
                            fixedWeekCount: false,
                            events: this.events,
                            dayMaxEvents: 2,
                
                            headerToolbar: {
                                left: 'title',
                                center: '',
                                right: 'prev,next today'
                            },
                
                            eventClick: (info) => {
                                this.activeEvent = {
                                    title: info.event.title,
                                    // Use the formatted display dates from extendedProps
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
                    },
                }" x-init="initCalendar()">

                    <div class="flex justify-between items-center mb-4">
                        <flux:heading size="lg">Trip Schedules</flux:heading>
                        <flux:badge variant="solid" color="emerald">{{ $activeTripsCount }} Active This Month
                        </flux:badge>
                    </div>

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
                </div>
            </flux:card>

            <flux:card class="row-span-2 flex flex-col min-h-0">
                <flux:heading size="lg" class="mb-4">Fleet Status</flux:heading>
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <flux:table>
                        <flux:table.rows>
                            @foreach ($vehiclesList as $vehicle)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <flux:text weight="medium">{{ $vehicle->vehicle }}</flux:text>
                                        <flux:text size="xs" variant="subtle">Plate: {{ $vehicle->plate_no }}
                                        </flux:text>
                                    </flux:table.cell>

                                    <flux:table.cell align="end">
                                        @php
                                            $vColor = match ($vehicle->status) {
                                                'Available' => 'emerald',
                                                'On Trip' => 'yellow',
                                                'Maintenance' => 'red',
                                                default => 'zinc',
                                            };
                                        @endphp

                                        <flux:badge :color="$vColor" size="sm" variant="pill">
                                            {{ $vehicle->status }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:card>
        </div>
    </div>
@endsection
