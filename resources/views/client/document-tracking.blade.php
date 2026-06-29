@extends('client.layout')

@section('content')
    <flux:heading size="xl" level="1">Document Tracking</flux:heading>
    <flux:text class="mt-2 mb-6 text-base">View and track documents routed to your office.</flux:text>

    <div class="mb-4">
        <div class="flex items-center justify-between gap-2">
            <form action="{{ route('client.document-tracking') }}" method="GET" class="flex flex-1 items-center gap-2">
                <div class="w-100">
                    <flux:input name="search" value="{{ request('search') }}" icon="magnifying-glass"
                        placeholder="Search document no..." />
                </div>

                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        @if (request('to_type') === 'national')
                            National TO
                        @elseif(request('to_type') === 'local')
                            Local TO
                        @else
                            All TO
                        @endif
                    </flux:button>
                    <flux:menu class="max-h-60 overflow-y-auto">
                        <flux:menu.radio.group name="to_type" value="{{ request('to_type') }}">
                            <flux:menu.item
                                href="{{ route('client.document-tracking', array_merge(request()->query(), ['to_type' => ''])) }}">
                                All TO Types
                            </flux:menu.item>
                            <flux:menu.item
                                href="{{ route('client.document-tracking', array_merge(request()->query(), ['to_type' => 'national'])) }}">
                                National TO
                            </flux:menu.item>
                            <flux:menu.item
                                href="{{ route('client.document-tracking', array_merge(request()->query(), ['to_type' => 'local'])) }}">
                                Local TO
                            </flux:menu.item>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" color="emerald" icon="adjustments-horizontal">Filter</flux:button>

                    @if (request()->anyFilled(['search', 'to_type']))
                        <flux:button href="{{ route('client.document-tracking') }}" variant="filled" color="zinc"
                            icon="x-mark">
                            Clear
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="mt-10">
        <flux:card class="space-y-6 overflow-hidden mt-10">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Document No.</flux:table.column>
                    <flux:table.column>Subject/Purpose</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Remarks</flux:table.column>
                    <flux:table.column class="w-0 text-right">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($documents as $doc)
                        <flux:table.row>
                            <flux:table.cell class="font-bold text-zinc-800 dark:text-zinc-200">
                                <div class="relative inline-flex items-center gap-2">
                                    {{ $doc->document_no }}

                                    @if ($doc->is_new)
                                        <span class="flex h-2 w-2 relative">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                        </span>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-800 dark:text-white">
                                        @if ($doc->is_national)
                                            <span
                                                class="text-xs font-semibold px-1.5 py-0.5 rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 mr-1">National
                                                TO</span>
                                            {{ $doc->nationalTo->route ?? 'No Destination' }}
                                        @else
                                            {{ $doc->tripTicket->destination ?? 'No Destination' }}
                                        @endif
                                    </span>
                                    <span class="text-xs text-zinc-500 truncate max-w-[300px]">
                                        {{ $doc->is_national ? $doc->nationalTo->purpose ?? 'No purpose specified' : $doc->tripTicket->purpose ?? 'No purpose specified' }}
                                    </span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $doc->created_at ? $doc->created_at->format('M d, Y h:i A') : 'N/A' }}
                            </flux:table.cell>

                            <flux:table.cell class="italic text-zinc-500 max-w-[250px] truncate">
                                {{ $doc->remarks ?? 'No remarks' }}
                            </flux:table.cell>

                            <flux:table.cell class="text-right">
                                @if ($doc->is_national)
                                    <flux:button href="{{ route('client.document-tracking.show', $doc->national_to_id) }}"
                                        icon="eye" size="xs" variant="filled">
                                        View Details
                                    </flux:button>
                                @else
                                    <flux:button
                                        href="{{ route('client.document-tracking.show', $doc->trip_ticket_id ?? ($doc->national_to_id ?? 0)) }}"
                                        icon="eye" size="xs" variant="filled">
                                        View Details
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <flux:icon name="document-text" class="size-8 text-zinc-400" />
                                    <flux:text variant="strong" class="text-zinc-500">No active documents</flux:text>
                                    <flux:text size="sm" class="text-zinc-400">No active documents are currently routed
                                        to your office.</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
@endsection
