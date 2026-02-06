@extends('admin.layout')

@section('content')
<div class="h-full flex flex-col min-h-0">
    <div class="flex items-end justify-between mb-10">
        <div>
            <flux:heading size="xl" level="1">Admin Dashboard</flux:heading>
            <flux:text class="mt-2">Overview of trip schedules, vehicle availability, and driver readiness</flux:text>
        </div>
        <flux:button icon="printer" variant="outline" size="sm">Export Report</flux:button>
    </div>

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
                        style="width: {{ $totalVehicles > 0 ? ($availVehicles/$totalVehicles)*100 : 0 }}%"
                        title="Available"></div>
                    <div class="bg-yellow-400"
                        style="width: {{ $totalVehicles > 0 ? ($tripVehicles/$totalVehicles)*100 : 0 }}%"
                        title="On Trip"></div>
                    <div class="bg-red-500"
                        style="width: {{ $totalVehicles > 0 ? ($maintVehicles/$totalVehicles)*100 : 0 }}%"
                        title="Maintenance"></div>
                </div>
                <div class="flex gap-2">
                    <flux:badge size="sm" color="emerald" inset="top bottom">{{ $availVehicles }} Avail</flux:badge>
                    <flux:badge size="sm" color="yellow" inset="top bottom">{{ $tripVehicles }} Trip</flux:badge>
                    <flux:badge size="sm" color="red" inset="top bottom">{{ $maintVehicles }} Maint</flux:badge>
                </div>
            </div>
        </flux:card>

        <flux:card class="flex flex-col">
            <div class="flex items-start justify-between">
                <div>
                    <flux:heading size="sm" variant="subtle" class="uppercase tracking-wider">Total Drivers
                    </flux:heading>
                    <flux:text class="text-3xl font-bold mt-1">4</flux:text>
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
                    <div class="bg-emerald-500 w-1/2" title="Available"></div>
                    <div class="bg-yellow-400 w-1/2" title="On Duty"></div>
                </div>
                <div class="flex gap-2">
                    <flux:badge size="sm" color="emerald" inset="top bottom">2 Available</flux:badge>
                    <flux:badge size="sm" color="yellow" inset="top bottom">2 On Travel</flux:badge>
                </div>
            </div>
        </flux:card>

        <flux:card class="row-span-2 flex flex-col min-h-0">
            <flux:heading size="lg" class="mb-4">Driver Status</flux:heading>
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <flux:table>
                    <flux:table.rows>
                        @foreach($driversList as $driver)
                        <flux:table.row>
                            <flux:table.cell class="flex items-center gap-2">
                                <flux:avatar
                                    initials="{{ collect(explode(' ', $driver->name))->map(fn($n) => $n[0])->join('') }}"
                                    size="xs" />
                                <flux:text weight="medium">{{ $driver->name }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                @php $dColor = match($driver->status) { 'Available' => 'emerald', 'On Trip' => 'yellow',
                                default => 'zinc' }; @endphp
                                <flux:badge color="{{ $dColor }}" size="sm" variant="pill">{{ $driver->status }}
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
        selectedDate: '',
        {{-- 1. Inject the events from Laravel --}}
        events: {{ $events->toJson() }},
        
        initCalendar() {
            let calendar = new FullCalendar(this.$refs.calendar, {
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                height: '100%',
                selectable: true,
                showNonCurrentDates: false, 
                fixedWeekCount: false,
                {{-- 2. Add the events to the calendar --}}
                events: this.events, 
                
                headerToolbar: {
                    left: 'title',
                    center: '',
                    right: 'prev,next today'
                },
                dateClick: (info) => {
                    this.selectedDate = info.dateStr;
                    $flux.modal('trip_details_modal').show();
                }
            });
            calendar.render();
        }
    }" x-init="initCalendar()">

                <div class="flex justify-between items-center mb-4">
                    <flux:heading size="lg">Trip Schedules</flux:heading>
                    <flux:badge variant="solid" color="emerald">{{ $activeTripsCount }} Active This Month</flux:badge>
                </div>

                <div class="flex-1 min-h-0">
                    <div x-ref="calendar" class="h-full"></div>
                </div>

                <flux:modal name="trip_details_modal" class="min-w-[25rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Date: <span class="text-emerald-600" x-text="selectedDate"></span>
                            </flux:heading>
                            <flux:subheading>Daily trip management and dispatching.</flux:subheading>
                        </div>

                        {{-- 3. Dynamic Trip List for the Selected Date --}}
                        <div class="space-y-2">
                            <template x-for="event in events.filter(e => e.start === selectedDate)">
                                <div
                                    class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg flex justify-between items-center">
                                    <div>
                                        <flux:text weight="medium" x-text="event.title"></flux:text>
                                        <flux:text size="xs"
                                            x-text="'Driver: ' + (event.extendedProps.driver || 'Unassigned')">
                                        </flux:text>
                                    </div>
                                    <flux:badge size="sm" color="emerald" variant="subtle">Active</flux:badge>
                                </div>
                            </template>

                            {{-- 4. Empty State (Shows when no trips match selectedDate) --}}
                            <div x-show="events.filter(e => e.start === selectedDate).length === 0"
                                class="p-8 border-2 border-dashed border-zinc-200 rounded-xl text-center">
                                <flux:icon.calendar variant="outline" class="mx-auto size-8 text-zinc-300 mb-2" />
                                <flux:text variant="subtle">No trips scheduled for this date.</flux:text>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <flux:spacer />
                            <flux:button x-on:click="$flux.modal('trip_details_modal').close()" variant="ghost">Dismiss
                            </flux:button>
                            <flux:button variant="primary" icon="plus">Add Trip</flux:button>
                        </div>
                    </div>
                </flux:modal>
            </div>
        </flux:card>

        <flux:card class="row-span-2 flex flex-col min-h-0">
            <flux:heading size="lg" class="mb-4">Fleet Status</flux:heading>
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <flux:table>
                    <flux:table.rows>
                        @foreach($vehiclesList as $vehicle)
                        <flux:table.row>
                            <flux:table.cell>
                                <flux:text weight="medium">{{ $vehicle->vehicle }}</flux:text>
                                <flux:text size="xs" variant="subtle">Plate: {{ $vehicle->plate_no }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                @php $vColor = match($vehicle->status) { 'Available' => 'emerald', 'On Trip' =>
                                'yellow', 'Maintenance' => 'red', default => 'zinc' }; @endphp
                                <flux:badge color="{{ $vColor }}" size="sm">{{ $vehicle->status }}</flux:badge>
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