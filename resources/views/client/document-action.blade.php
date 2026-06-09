@extends('client.layout')

@section('content')
    <div class="flex flex-col gap-6 w-full overflow-y-auto">
        <flux:button icon="arrow-uturn-left" variant="filled" color="red" href="{{ route('client.document-tracking') }}" class="w-32 mt-2">
            Back
        </flux:button>

        <div class="w-full">
            <flux:card class="space-y-4">
                <div>
                    <flux:heading size="lg" level="2">Travel Order</flux:heading>
                    <flux:text size="sm">Core overview profiles regarding this authorization log.</flux:text>
                </div>
                <div class="flex flex-col gap-4 mt-6">
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Document No.</flux:label>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $documentNo }}</p>
                    </div>
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Date</flux:label>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">
                            {{ $tripTicket->created_at ? $tripTicket->created_at->format('M j, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Name</flux:label>
                        <div class="flex flex-wrap gap-3 text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                            @if (is_array($tripTicket->passengers))
                                @foreach ($tripTicket->passengers as $passenger)
                                    <span class="inline-flex items-center gap-1.5">
                                        <flux:icon.user variant="micro" class="text-zinc-400 dark:text-zinc-500" />
                                        {{ $passenger }}
                                    </span>
                                @endforeach
                            @elseif (!empty($tripTicket->passengers))
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon.user variant="micro" class="text-zinc-400 dark:text-zinc-500" />
                                    {{ $tripTicket->passengers }}
                                </span>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Inclusive Dates</flux:label>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">
                            {{ $tripTicket->start_date ? $tripTicket->start_date->format('M j, Y') : 'N/A' }} —
                            {{ $tripTicket->end_date ? $tripTicket->end_date->format('M j, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Destination</flux:label>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                            {{ $tripTicket->destination ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Purpose</flux:label>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $tripTicket->purpose ?? 'No purpose specified.' }}
                        </p>
                    </div>
                </div>
            </flux:card>
        </div>

        <div class="w-full">
            <flux:card class="space-y-6">
                @php
                    $userOffice = is_object(auth()->user()->office)
                        ? auth()->user()->office->name
                        : auth()->user()->office;
                @endphp

                @if ($userOffice === 'FAS')
                    <div class="flex justify-between gap-4">
                        <div class="flex flex-col gap-1">
                            <flux:heading size="lg">Track of Document</flux:heading>
                            <flux:text size="sm">Manage incoming acceptances or dispatch items onward to neighboring
                                business departments.</flux:text>
                        </div>
                        @if ($tripTicket->to_no)
                            <flux:button disabled variant="filled" icon="check-circle">
                                TO GENERATED ({{ $tripTicket->to_no }})
                            </flux:button>
                        @else
                            <form action="{{ route('trip-tickets.generate-to', $tripTicket->id) }}" method="POST">
                                @csrf
                                <flux:button type="submit" variant="primary" color="emerald" icon="document-text">
                                    Generate TO
                                </flux:button>
                            </form>
                        @endif
                    </div>
                @else
                    <div class="flex flex-col gap-1">
                        <flux:heading size="lg">Track of Document</flux:heading>
                        <flux:text size="sm">Manage incoming acceptances or dispatch items onward to neighboring
                            business departments.</flux:text>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        @if ($tripTicket->client_id !== auth()->id())
                            @php
                                $latestLog = $trackings->first();
                                $isSenderPending =
                                    $latestLog &&
                                    $latestLog->status === 'Released' &&
                                    $latestLog->route_from === $userOffice;
                                $isAlreadyReceivedByUs =
                                    $latestLog &&
                                    $latestLog->status === 'Received' &&
                                    $latestLog->client_id == auth()->id();

                                $cannotReceive = $isSenderPending || $isAlreadyReceivedByUs;
                            @endphp

                            <form action="{{ route('client.document-tracking.receive', $tripTicket->id) }}" method="POST">
                                @csrf
                                <flux:button type="submit" variant="filled" color="emerald" icon="check-circle"
                                    :disabled="$cannotReceive">
                                    @if ($isSenderPending)
                                        Document Forwarded
                                    @elseif($isAlreadyReceivedByUs)
                                        Document Received
                                    @else
                                        Receive Document
                                    @endif
                                </flux:button>
                            </form>
                        @endif

                        <form action="{{ route('client.document-tracking.track', $tripTicket->id) }}" method="POST"
                            class="flex flex-wrap items-end gap-3 w-full sm:w-auto">
                            @csrf
                            <input type="hidden" name="document_no"
                                value="{{ $documentNo === 'N/A' ? '' : $documentNo }}">
                            <input type="hidden" name="route" id="forward_to_input" required>

                            <div class="w-56">
                                <flux:dropdown class="w-full">
                                    <flux:button id="office_dropdown_button" icon-trailing="chevron-down"
                                        class="w-full [&>svg]:ml-auto text-left justify-between">
                                        Forwarded to...
                                    </flux:button>
                                    <flux:menu class="max-h-60 overflow-y-auto">
                                        @foreach ($offices as $office)
                                            <flux:menu.item
                                                onclick="document.getElementById('forward_to_input').value = '{{ $office }}'; document.getElementById('office_dropdown_button').innerText = '{{ $office }}';">
                                                {{ $office }}
                                            </flux:menu.item>
                                        @endforeach
                                    </flux:menu>
                                </flux:dropdown>
                            </div>

                            <div class="flex-1 min-w-[250px]">
                                <flux:input name="remarks" placeholder="Remarks..." />
                            </div>

                            <flux:button type="submit" variant="primary" color="emerald" icon="paper-airplane">
                                Forward
                            </flux:button>
                        </form>
                    </div>
                @endif


                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Action</flux:table.column>
                        <flux:table.column>From</flux:table.column>
                        <flux:table.column>Forwarded</flux:table.column>
                        <flux:table.column>Office</flux:table.column>
                        <flux:table.column>Date and Time</flux:table.column>
                        <flux:table.column>Remarks</flux:table.column>
                        <flux:table.column>Duration</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($trackings as $track)
                            <flux:table.row>
                                <flux:table.cell>
                                    <span
                                        class="px-2 py-1 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        {{ $track->status }}
                                    </span>
                                </flux:table.cell>

                                <flux:table.cell class="text-zinc-700 dark:text-zinc-300 text-xs font-medium">
                                    {{ $track->route_from ?? 'Not Applicable' }}
                                </flux:table.cell>
                                <flux:table.cell class="text-zinc-700 dark:text-zinc-300 text-xs font-medium">
                                    {{ $track->route_to ?? 'Not Applicable' }}
                                </flux:table.cell>

                                <flux:table.cell class="text-zinc-700 dark:text-zinc-300 text-xs font-medium">
                                    {{ $track->client?->office ?? 'N/A' }}
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ $track->formatted_date }}
                                </flux:table.cell>

                                <flux:table.cell class="max-w-[180px] truncate italic text-zinc-500">
                                    {{ $track->remarks }}
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap font-mono text-xs">
                                    {{ $track->calculated_duration }}
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7" class="py-8 text-center text-zinc-400">
                                    No history records logged for this document yet.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>

                @foreach ($trackings as $track)
                    <flux:modal name="delete-tracking-{{ $track->id }}" class="min-w-[22rem]">
                        <form action="{{ route('client.document-tracking.destroy', $track->id) }}" method="POST"
                            class="m-0">
                            @csrf
                            @method('DELETE')

                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Delete Document Route?</flux:heading>
                                    <flux:text class="mt-2">
                                        You're about to delete this document route.<br>
                                        This action cannot be reversed.
                                    </flux:text>
                                </div>

                                <div class="flex gap-2">
                                    <flux:spacer />
                                    <flux:modal.close>
                                        <flux:button variant="ghost">Cancel</flux:button>
                                    </flux:modal.close>
                                    <flux:button type="submit" variant="danger">Delete</flux:button>
                                </div>
                            </div>
                        </form>
                    </flux:modal>
                @endforeach
            </flux:card>
        </div>
    </div>
@endsection
