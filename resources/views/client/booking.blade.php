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
                    
  
                    let endDate = new Date(info.end);
                    endDate.setDate(endDate.getDate() - 1);
                    this.selection.displayEnd = endDate.toISOString().split('T')[0];

 
                    const diffTime = Math.abs(info.end - info.start);
                    this.selection.totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
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
            <flux:callout variant="secondary" icon="information-circle" heading="Click and drag to select a date range" />



            <div class="flex-1 min-h-0">
                <div x-ref="calendar" class="h-full"></div>
            </div>
        </flux:card>

        <flux:card class="col-span-1 flex flex-col">
            <flux:heading size="lg" class="mb-6">Booking Summary</flux:heading>

            <div class="flex-1 space-y-6">
                <div class="space-y-4" x-show="selection.displayStart" x-cloak>
                    <div class="flex justify-between items-center">
                        <flux:text variant="subtle">Start Date:</flux:text>
                        <flux:badge color="zinc" inset="top bottom" x-text="selection.displayStart"></flux:badge>
                    </div>

                    <div class="flex justify-between items-center">
                        <flux:text variant="subtle">End Date:</flux:text>
                        <flux:badge color="zinc" inset="top bottom" x-text="selection.displayEnd"></flux:badge>
                    </div>

                    <flux:separator variant="dashed" />

                    <div class="flex justify-between items-center">
                        <flux:heading size="sm">Total Duration</flux:heading>
                        <flux:text weight="semibold" x-text="selection.totalDays + ' Day(s)'"></flux:text>
                    </div>
                </div>

                <div x-show="!selection.displayStart"
                    class="h-32 flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 rounded-xl">
                    <flux:text variant="subtle">Please select a date on the calendar</flux:text>
                </div>
            </div>

            <flux:button variant="primary" color="emerald" class="mt-auto w-full" x-bind:disabled="!selection.displayStart">
                Confirm
            </flux:button>
        </flux:card>
    </div>
</div>
@endsection