@extends('client.layout')

@section('content')
    <div class="flex flex-col gap-6 w-full overflow-y-auto">
        <flux:button icon="arrow-uturn-left" variant="filled" color="red" href="{{ route('client.document-tracking') }}"
            class="w-32 mt-2">
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
                            @if ($isNational)
                                {{ $nationalTo?->created_at ? $nationalTo->created_at->format('M j, Y') : 'N/A' }}
                            @else
                                {{ $tripTicket?->created_at ? $tripTicket->created_at->format('M j, Y') : 'N/A' }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Name / Personnel</flux:label>
                        <div class="flex flex-wrap gap-3 text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                            @if ($isNational)
                                @if (is_array($nationalTo?->personnel))
                                    @foreach ($nationalTo->personnel as $person)
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded">
                                            <flux:icon.user variant="micro" class="text-zinc-400 dark:text-zinc-500" />
                                            <span>
                                                {{ $person['name'] ?? 'Unknown' }}
                                                @if (!empty($person['position']))
                                                    <span
                                                        class="text-xs text-zinc-400 dark:text-zinc-500 font-normal">({{ $person['position'] }})</span>
                                                @endif
                                            </span>
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">N/A</span>
                                @endif
                            @else
                                @if ($tripTicket && is_array($tripTicket->passengers))
                                    @foreach ($tripTicket->passengers as $passenger)
                                        <span class="inline-flex items-center gap-1.5">
                                            <flux:icon.user variant="micro" class="text-zinc-400 dark:text-zinc-500" />
                                            {{ $passenger }}
                                        </span>
                                    @endforeach
                                @elseif ($tripTicket && !empty($tripTicket->passengers))
                                    <span class="inline-flex items-center gap-1.5">
                                        <flux:icon.user variant="micro" class="text-zinc-400 dark:text-zinc-500" />
                                        {{ $tripTicket->passengers }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">N/A</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Inclusive Dates</flux:label>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">
                            @if ($isNational)
                                {{ $nationalTo?->departure ? $nationalTo->departure->format('M j, Y') : 'N/A' }} —
                                {{ $nationalTo?->return_date ? $nationalTo->return_date->format('M j, Y') : 'N/A' }}
                            @else
                                {{ $tripTicket?->start_date ? $tripTicket->start_date->format('M j, Y') : 'N/A' }} —
                                {{ $tripTicket?->end_date ? $tripTicket->end_date->format('M j, Y') : 'N/A' }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Destination / Route</flux:label>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                            {{ $isNational ? $nationalTo?->route ?? 'N/A' : $tripTicket?->destination ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="flex items-baseline">
                        <flux:label class="w-52 shrink-0 whitespace-nowrap">Purpose</flux:label>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $isNational ? $nationalTo?->purpose ?? 'No purpose specified.' : $tripTicket?->purpose ?? 'No purpose specified.' }}
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
                        @if (!$isNational && $tripTicket?->to_no)
                            <flux:button disabled variant="filled" icon="check-circle">
                                TO GENERATED ({{ $tripTicket->to_no }})
                            </flux:button>
                        @elseif ($isNational)
                            <flux:button disabled variant="filled" icon="globe-alt">
                                NATIONAL TO TIMELINE
                            </flux:button>
                        @else
                            <form action="{{ route('trip-tickets.generate-to', $tripTicket?->id ?? 0) }}" method="POST">
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
                        @if (
                            ($isNational && $nationalTo?->client_id !== auth()->id()) ||
                                (!$isNational && $tripTicket?->client_id !== auth()->id()))
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
                                $targetId = $isNational ? $nationalTo?->id : $tripTicket?->id;
                            @endphp

                            @php
                                $userOffice = is_object(auth()->user()->office)
                                    ? auth()->user()->office->name
                                    : auth()->user()->office;

                                $latestLog = $trackings->first();

                                // 1. Did our office forward this document last?
                                $isSenderPending =
                                    $latestLog &&
                                    $latestLog->status === 'Released' &&
                                    $latestLog->route_from === $userOffice;

                                // 2. Has our office already received it, and no one has forwarded it yet?
                                $isAlreadyReceivedByUs =
                                    $latestLog &&
                                    $latestLog->status === 'Received' &&
                                    ($latestLog->route_to === $userOffice ||
                                        $trackings
                                            ->where('route_from', $userOffice)
                                            ->where('status', 'Received')
                                            ->isNotEmpty());

                                // 3. Is this document explicitly heading to our office right now?
                                $isIncomingToUs =
                                    $latestLog &&
                                    $latestLog->status === 'Released' &&
                                    $latestLog->route_to === $userOffice;

                                // Gray out the button if we aren't the ones who are supposed to receive it right now
                                $cannotReceive = !$isIncomingToUs || $isSenderPending || $isAlreadyReceivedByUs;
                            @endphp

                            <form action="{{ route('client.document-tracking.receive', $targetId ?? 0) }}" method="POST">
                                @csrf
                                <flux:button type="submit" variant="filled" color="emerald" icon="check-circle"
                                    :disabled="$cannotReceive">
                                    @if ($isSenderPending)
                                        Document Forwarded
                                    @elseif ($isAlreadyReceivedByUs)
                                        Document Received
                                    @elseif (!$isIncomingToUs && $latestLog && $latestLog->route_from !== $userOffice)
                                        In Transit (At {{ $latestLog->route_to ?? 'Other Office' }})
                                    @else
                                        Receive Document
                                    @endif
                                </flux:button>
                            </form>
                        @endif

                        <form
                            action="{{ route('client.document-tracking.track', ($isNational ? $nationalTo?->id : $tripTicket?->id) ?? 0) }}"
                            method="POST" class="flex items-end gap-3 w-full">
                            @csrf
                            <input type="hidden" name="document_no"
                                value="{{ $documentNo === 'N/A' ? '' : $documentNo }}">
                            <input type="hidden" name="route" id="forward_to_input" required>

                            <div class="w-56">
                                <flux:dropdown class="w-full">
                                    <flux:button id="office_dropdown_button" icon-trailing="chevron-down"
                                        class="w-full [&>svg]:ml-auto text-left justify-between"
                                        :disabled="$trackings->first()?->status === 'Cancelled'">
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

                            <div class="w-72">
                                <flux:input name="remarks" placeholder="Remarks..."
                                    :disabled="$trackings->first()?->status === 'Cancelled'" />
                            </div>

                            <flux:button type="submit" variant="primary" color="emerald" icon="paper-airplane"
                                :disabled="$trackings->first()?->status === 'Cancelled'">
                                Forward
                            </flux:button>


                            <flux:modal.trigger name="cancel-document-modal">
                                <flux:button type="button" variant="danger" icon="x-circle" class="ms-auto"
                                    :disabled="$trackings->first()?->status === 'Cancelled'">
                                    {{ $trackings->first()?->status === 'Cancelled' ? 'Cancelled' : 'Cancel' }}
                                </flux:button>
                            </flux:modal.trigger>
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
                                        class="px-2 py-1 rounded text-xs font-semibold 
                                        {{ $track->status === 'Cancelled'
                                            ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                                            : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' }}
                                    ">{{ $track->status }}</span>
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
    <flux:modal name="cancel-document-modal" class="min-w-[24rem]">
        <form action="{{ route('client.document-tracking.cancel', $isNational ? $nationalTo->id : $tripTicket->id) }}"
            method="POST" class="m-0">
            @csrf
            <input type="hidden" name="is_national" value="{{ $isNational ? '1' : '0' }}">

            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Cancel Travel Request?</flux:heading>
                    <flux:text class="mt-2">
                        Are you sure you want to change the status of this document to <strong>Cancelled</strong>?<br>
                        This will append a cancellation record to the system tracking trail.
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">No, Keep it</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="danger" icon="x-circle">Yes, Cancel Document</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
@endsection
