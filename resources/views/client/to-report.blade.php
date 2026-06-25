@extends('client.layout')

@section('content')
    <flux:heading size="xl" level="1">Travel Order Reports</flux:heading>
    <flux:text class="mt-2 mb-6 text-base">Manage and submit your post-travel compliance outputs.</flux:text>

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="mb-4 p-4 text-sm text-emerald-500 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 rounded-xl border border-emerald-200 dark:border-emerald-500">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 mb-4">
        <!-- Toggle Switch for Local vs National -->
        <div class="mb-4 inline-flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
            <a href="{{ route('client.to-report', array_merge(request()->query(), ['type' => 'local'])) }}"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $type === 'local' ? 'bg-white dark:bg-zinc-900 shadow text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                Local TO
            </a>
            <a href="{{ route('client.to-report', array_merge(request()->query(), ['type' => 'national'])) }}"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $type === 'national' ? 'bg-white dark:bg-zinc-900 shadow text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                National TO
            </a>
        </div>

        <div class="mb-4">
            <div class="flex items-center justify-between gap-2">
                <!-- Filter Form Items wrapper -->
                <form action="{{ route('client.to-report') }}" method="GET" class="flex flex-1 items-center gap-2">

                    <!-- Preserve the Type on Form Submit -->
                    <input type="hidden" name="type" value="{{ $type }}">

                    <!-- Search Input -->
                    <div class="w-full max-w-xs">
                        <flux:input name="search" value="{{ request('search') }}" icon="magnifying-glass"
                            placeholder="Search trips..." />
                    </div>

                    @if (auth()->guard('client')->user()->office === 'FAS')
                        <flux:dropdown>
                            <flux:button icon:trailing="chevron-down">
                                {{ request('office') ? request('office') : 'Select Office' }}
                            </flux:button>
                            <flux:menu class="max-h-60 overflow-y-auto">
                                <flux:menu.radio.group name="office" value="{{ request('office') }}">
                                    <flux:menu.item
                                        href="{{ route('client.to-report', array_merge(request()->query(), ['office' => ''])) }}">
                                        All Offices
                                    </flux:menu.item>
                                    @foreach ($offices as $officeOption)
                                        <flux:menu.item
                                            href="{{ route('client.to-report', array_merge(request()->query(), ['office' => $officeOption])) }}">
                                            {{ $officeOption }}
                                        </flux:menu.item>
                                    @endforeach
                                </flux:menu.radio.group>
                            </flux:menu>
                        </flux:dropdown>
                    @endif

                    <!-- Year Filter Dropdown -->
                    <flux:dropdown>
                        <flux:button icon:trailing="chevron-down">
                            {{ request('year') ? request('year') : 'Select Year' }}
                        </flux:button>
                        <flux:menu class="max-h-60 overflow-y-auto">
                            <flux:menu.radio.group name="year" value="{{ request('year') }}">
                                <flux:menu.item
                                    href="{{ route('client.to-report', array_merge(request()->query(), ['year' => ''])) }}">
                                    All Years
                                </flux:menu.item>
                                @foreach (range(date('Y'), 2025) as $year)
                                    <flux:menu.item
                                        href="{{ route('client.to-report', array_merge(request()->query(), ['year' => $year])) }}">
                                        {{ $year }}
                                    </flux:menu.item>
                                @endforeach
                            </flux:menu.radio.group>
                        </flux:menu>
                    </flux:dropdown>

                    <!-- Month Filter Dropdown -->
                    <flux:dropdown>
                        <flux:button icon:trailing="chevron-down">
                            @if (request('month'))
                                {{ DateTime::createFromFormat('!m', request('month'))->format('F') }}
                            @else
                                Select Month
                            @endif
                        </flux:button>
                        <flux:menu class="max-h-60 overflow-y-auto">
                            <flux:menu.radio.group name="month" value="{{ request('month') }}">
                                <flux:menu.item
                                    href="{{ route('client.to-report', array_merge(request()->query(), ['month' => ''])) }}">
                                    All Months
                                </flux:menu.item>
                                @foreach (range(1, 12) as $monthNum)
                                    @php
                                        $monthName = DateTime::createFromFormat('!m', $monthNum)->format('F');
                                    @endphp
                                    <flux:menu.item
                                        href="{{ route('client.to-report', array_merge(request()->query(), ['month' => $monthNum])) }}">
                                        {{ $monthName }}
                                    </flux:menu.item>
                                @endforeach
                            </flux:menu.radio.group>
                        </flux:menu>
                    </flux:dropdown>

                    <div class="flex gap-2">
                        <flux:button type="submit" variant="primary" color="emerald">Filter</flux:button>

                        @if (request()->anyFilled(['search', 'year', 'month']))
                            <flux:button href="{{ route('client.to-report', ['type' => $type]) }}" variant="filled"
                                color="zinc" icon="x-mark">
                                Clear
                            </flux:button>
                        @endif
                    </div>
                </form>

                <flux:button href="{{ route('to-report.export', request()->query()) }}" variant="filled" color="zinc"
                    icon="document-arrow-down">
                    Export Excel
                </flux:button>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <flux:card class="space-y-6 overflow-hidden">
            @if (auth()->user()->office === 'FAS')
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Travel Order No.</flux:table.column>
                        <flux:table.column>Name of Official/Personnel</flux:table.column>
                        <flux:table.column>Office</flux:table.column>
                        <flux:table.column>Travel Date</flux:table.column>
                        <flux:table.column>Purpose of Travel</flux:table.column>
                        <flux:table.column>Highlights/Outputs</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($tickets as $ticket)
                            <flux:table.row>
                                <flux:table.cell class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $ticket->to_no ?? '' }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if ($type === 'national')
                                        {{ is_array($ticket->personnel) ? implode(', ', $ticket->personnel) : $ticket->personnel }}
                                    @else
                                        {{ $ticket->user->name ?? '' }}
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $type === 'national' ? $ticket->client->office ?? '' : $ticket->user->office ?? 'FAS' }}
                                </flux:table.cell>

                                <flux:table.cell class="text-zinc-500 dark:text-zinc-400 text-xs">
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="calendar" class="size-4 text-zinc-400 shrink-0" />
                                        <span>
                                            @if ($type === 'national')
                                                {{ $ticket->departure?->format('M d, Y') }} -
                                                {{ $ticket->return_date?->format('M d, Y') }}
                                            @else
                                                {{ $ticket->start_date?->format('M d, Y') }} -
                                                {{ $ticket->end_date?->format('M d, Y') }}
                                            @endif
                                        </span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $type === 'national' ? $ticket->purpose : $ticket->purpose ?? $ticket->destination }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $ticket->toReport->outputs ?? '' }}
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <flux:icon name="clipboard-document-check" class="size-8 text-zinc-400" />
                                        <flux:text variant="strong" class="text-zinc-500">No travel orders or trip tickets
                                            found</flux:text>
                                        <flux:text size="sm" class="text-zinc-400">Completed structural office trips
                                            requiring post-travel reports will appear here.</flux:text>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>TO No.</flux:table.column>
                        <flux:table.column>Destination</flux:table.column>
                        <flux:table.column>Travel Date</flux:table.column>
                        <flux:table.column>Travel Status</flux:table.column>
                        <flux:table.column>Report Status</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($tickets as $ticket)
                            @php
                                $startDate = $type === 'national' ? $ticket->departure : $ticket->start_date;
                                $endDate = $type === 'national' ? $ticket->return_date : $ticket->end_date;
                                $destination = $type === 'national' ? $ticket->route : $ticket->destination;
                            @endphp
                            <flux:table.row>
                                <flux:table.cell class="font-bold text-zinc-800 dark:text-zinc-200">
                                    <div class="relative inline-flex items-center gap-2">
                                        {{ $ticket->to_no ?? '' }}

                                        @if (empty($ticket->toReport) && $endDate?->isPast())
                                            <span class="flex h-2 w-2 relative">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                            </span>
                                        @endif
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $destination }}
                                </flux:table.cell>

                                <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="calendar" class="size-4 text-zinc-400 shrink-0" />
                                        <span>
                                            {{ $startDate?->format('M d, Y') }} - {{ $endDate?->format('M d, Y') }}
                                        </span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if ($endDate && $endDate->isPast())
                                        <flux:badge color="emerald" size="sm" inset="top bottom">Completed</flux:badge>
                                    @else
                                        <flux:badge color="sky" size="sm" inset="top bottom">In Progress
                                        </flux:badge>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if ($ticket->toReport)
                                        <flux:badge color="emerald" size="sm" inset="top bottom">
                                            Submitted on {{ $ticket->toReport->created_at?->format('M d, Y g:i A') }}
                                        </flux:badge>
                                    @else
                                        <flux:badge color="amber" size="sm" inset="top bottom">Pending Report
                                        </flux:badge>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex items-center justify-start gap-2">
                                        @if ($ticket->toReport)
                                            <flux:modal.trigger name="edit-report-modal-{{ $ticket->id }}">
                                                <flux:button size="sm" variant="primary" color="sky"
                                                    icon="pencil-square">
                                                    Edit
                                                </flux:button>
                                            </flux:modal.trigger>

                                            <flux:modal.trigger name="delete-report-modal-{{ $ticket->id }}">
                                                <flux:button size="sm" variant="primary" color="red"
                                                    class="bg-red-500 hover:bg-red-600 text-white border-none"
                                                    icon="trash">
                                                    Delete
                                                </flux:button>
                                            </flux:modal.trigger>
                                        @else
                                            @if ($endDate?->isPast())
                                                <flux:modal.trigger name="create-report-modal-{{ $ticket->id }}">
                                                    <flux:button size="sm" variant="primary" color="emerald"
                                                        class="bg-emerald-600 hover:bg-emerald-700 text-white border-none"
                                                        icon="document-plus">
                                                        Create Report
                                                    </flux:button>
                                                </flux:modal.trigger>
                                            @else
                                                <flux:button size="sm" variant="primary" color="emerald"
                                                    class="bg-emerald-600/50 text-white/50 border-none cursor-not-allowed pointer-events-none"
                                                    icon="document-plus" disabled>
                                                    Create Report
                                                </flux:button>
                                            @endif
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <flux:icon name="clipboard-document-check" class="size-8 text-zinc-400" />
                                        <flux:text variant="strong" class="text-zinc-500">No travel orders or trip tickets
                                            found
                                        </flux:text>
                                        <flux:text size="sm" class="text-zinc-400">Your completed trips requiring
                                            post-travel reports will appear here.</flux:text>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            @endif
        </flux:card>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>

    @foreach ($tickets as $ticket)
        @php
            $startDate = $type === 'national' ? $ticket->departure : $ticket->start_date;
            $endDate = $type === 'national' ? $ticket->return_date : $ticket->end_date;
            $destination = $type === 'national' ? $ticket->route : $ticket->destination;
        @endphp

        @if ($ticket->toReport)
            <flux:modal name="edit-report-modal-{{ $ticket->id }}" class="md:w-[500px]">
                <form action="{{ route('to-report.update', $ticket->toReport->id) }}" method="POST"
                    class="space-y-6 m-0 p-0">
                    @csrf
                    @method('PUT')

                    <div>
                        <flux:heading size="lg">Update Travel Report</flux:heading>
                        <flux:text class="mt-1 text-sm">Modify your achievements and compliance outputs below.</flux:text>
                    </div>

                    <div>
                        <div>
                            <flux:text size="sm" class="text-zinc-400 block uppercase tracking-wider font-semibold">
                                Destination</flux:text>
                            <flux:text variant="strong" class="text-base text-zinc-900 dark:text-white mt-2">
                                {{ $destination }}</flux:text>
                        </div>
                        <div class="flex items-center gap-2 mt-1 text-zinc-600 dark:text-zinc-300">
                            <flux:icon name="calendar" class="size-4 text-zinc-400" />
                            <flux:text size="sm" class="font-medium">
                                {{ $startDate?->format('M d, Y') }} - {{ $endDate?->format('M d, Y') }}
                            </flux:text>
                        </div>
                    </div>

                    <flux:text size="sm" class="text-zinc-400 block uppercase tracking-wider font-semibold mb-2">
                        HIGHLIGHT / OUTPUTS</flux:text>
                    <flux:textarea name="outputs" rows="5" required>
                        {{ $ticket->toReport->outputs }}</flux:textarea>

                    <div class="flex justify-end gap-2 mt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" color="emerald"
                            class="bg-emerald-600 hover:bg-emerald-700 border-none">
                            Save Changes
                        </flux:button>
                    </div>
                </form>
            </flux:modal>

            <flux:modal name="delete-report-modal-{{ $ticket->id }}" class="md:w-[400px]">
                <form action="{{ route('to-report.destroy', $ticket->toReport->id) }}" method="POST"
                    class="space-y-6 m-0 p-0">
                    @csrf
                    @method('DELETE')

                    <div class="space-y-3">
                        <div>
                            <flux:heading size="lg">Delete Travel Report</flux:heading>
                            <flux:text class="mt-2 text-sm">
                                Are you sure you want to delete the report for <strong
                                    class="text-zinc-950 dark:text-white">{{ $destination }}</strong>? This action
                                cannot be undone.
                            </flux:text>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="w-24">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" color="red"
                            class="w-24 bg-red-500 hover:bg-red-600 text-white border-none">
                            Delete
                        </flux:button>
                    </div>
                </form>
            </flux:modal>
        @else
            <flux:modal name="create-report-modal-{{ $ticket->id }}" class="md:w-[500px]">
                <form action="{{ route('to-report.store') }}" method="POST" class="space-y-6 m-0 p-0">
                    @csrf

                    <input type="hidden" name="type" value="{{ $type }}">
                    @if ($type === 'local')
                        <input type="hidden" name="trip_ticket_id" value="{{ $ticket->id }}">
                    @else
                        <input type="hidden" name="national_to_id" value="{{ $ticket->id }}">
                    @endif

                    <div>
                        <flux:heading size="lg">Create Travel Report</flux:heading>
                        <flux:text class="mt-1 text-sm">Fill out the details below to complete your report.</flux:text>
                    </div>

                    <div>
                        <div>
                            <flux:text size="sm" class="text-zinc-400 block uppercase tracking-wider font-semibold">
                                Destination</flux:text>
                            <flux:text variant="strong" class="text-base text-zinc-900 dark:text-white mt-2">
                                {{ $destination }}</flux:text>
                        </div>
                        <div class="flex items-center gap-2 mt-2 text-zinc-600 dark:text-zinc-300">
                            <flux:icon name="calendar" class="size-4 text-zinc-400" />
                            <flux:text size="sm" class="font-medium">
                                {{ $startDate?->format('M d, Y') }} - {{ $endDate?->format('M d, Y') }}
                            </flux:text>
                        </div>
                    </div>

                    <div>
                        <flux:text size="sm" class="text-zinc-400 block uppercase tracking-wider font-semibold mb-2">
                            HIGHLIGHT / OUTPUTS</flux:text>
                        <flux:textarea name="outputs" class="text-zinc-400"
                            placeholder="Describe your achievements, outputs, and milestones from this trip..."
                            rows="5" required />
                    </div>

                    <div class="flex justify-end gap-2 mt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" color="emerald"
                            class="bg-emerald-600 hover:bg-emerald-700 border-none">
                            Save Report
                        </flux:button>
                    </div>
                </form>
            </flux:modal>
        @endif
    @endforeach
@endsection
