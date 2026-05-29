@extends('client.layout')

@section('content')
    <div class="w-full h-full flex flex-col" x-data="{
        search: '',
        selectedOffice: 'Select recipient...',
        offices: {{ json_encode($offices) }},
        activeDocId: null,
        profile: null,
        history: [],
        loading: false,
        form: {
            forward_to: '',
            remarks: ''
        },
    
        async selectDocument(id) {
            this.activeDocId = id;
            this.loading = true;
    
            this.form.forward_to = '';
            this.form.remarks = '';
            this.selectedOffice = 'Select recipient...';
    
            try {
                let response = await fetch(`/client/document-tracking/${id}`);
    
                if (!response.ok) {
                    throw new Error(`Server responded with status ${response.status}`);
                }
    
                let data = await response.json();
                this.profile = data.profile;
                this.history = data.history;
            } catch (error) {
                console.error('Error fetching document track updates:', error);
            } finally {
                this.loading = false;
            }
        }
    }">
        <flux:heading size="xl" level="1">Document Tracking</flux:heading>
        <flux:text class="mt-2 mb-6 text-base">View and track document</flux:text>

        <div class="flex-1 flex gap-6 min-h-0">

            <flux:card class="w-96 flex flex-col h-[calc(100vh-220px)]">
                <div class="shrink-0 pb-4">
                    <flux:heading size="lg" level="1" class="mb-4">Forwarded Documents</flux:heading>
                    <flux:separator />
                </div>

                <div class="flex-1 overflow-y-auto pr-1 space-y-3">
                    @forelse($documents as $doc)
                        <div
                            class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 transition-all">
                            <div class="font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $doc->tripTicket->destination ?? 'No Destination' }}
                            </div>
                            <div class="text-xs text-zinc-400 mt-1 line-clamp-2">
                                {{ $doc->tripTicket->purpose ?? 'No purpose specified' }}
                            </div>
                            <div class="mt-3 flex justify-between items-center">
                                <span
                                    class="text-[10px] font-semibold tracking-wider px-2 py-0.5 bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 rounded">
                                    Doc #: {{ $doc->document_no }}
                                </span>

                                <flux:button size="sm" @click="selectDocument({{ $doc->trip_ticket_id }})"
                                    x-bind:variant="activeDocId == {{ $doc->trip_ticket_id }} ? 'primary' : 'subtle'">
                                    Select
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-zinc-400 py-8">
                            No active documents routed to your office.
                        </div>
                    @endforelse
                </div>
            </flux:card>

            <div class="flex-1 flex flex-col gap-6 h-[calc(100vh-220px)] overflow-y-auto">

                <flux:card class="shrink-0">
                    <flux:heading size="lg" level="1" class="mb-4">Document Profile</flux:heading>
                    <flux:separator />

                    <template x-if="profile">
                        <div class="mt-4 flex gap-x-4 text-sm text-zinc-600 dark:text-zinc-300">
                            <div class="w-48 shrink-0 space-y-3 font-medium text-zinc-400">
                                <div>Document No:</div>
                                <div>Date:</div>
                                <div>Passengers:</div>
                                <div>Office:</div>
                                <div>Inclusive Dates of Travel:</div>
                                <div>Purpose:</div>
                                <div>Destination:</div>
                            </div>

                            <div class="flex-1 space-y-3">
                                <div class="text-base font-semibold text-zinc-800 dark:text-zinc-100"
                                    x-text="profile.document_no"></div>
                                <div class="h-5 flex items-center" x-text="profile.date"></div>
                                <div class="h-5 flex items-center" x-text="profile.passengers"></div>
                                <div class="h-5 flex items-center" x-text="profile.office"></div>
                                <div class="h-5 flex items-center" x-text="profile.inclusive_dates"></div>
                                <div class="h-5 flex items-center" x-text="profile.purpose"></div>
                                <div class="h-5 flex items-center" x-text="profile.destination"></div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!profile && !loading">
                        <div class="p-6 text-center text-zinc-400 text-sm">
                            Select a document from the left list to see profile contents.
                        </div>
                    </template>
                </flux:card>

                <flux:card class="flex-1 flex flex-col min-h-0">
                    <div class="flex justify-between items-center mb-2">
                        <flux:heading size="lg" level="1" class="mb-4">Track of Document</flux:heading>
                        <template x-if="profile">
                            <flux:modal.trigger name="forward_document_modal">
                                <flux:button variant="primary" color="emerald" icon="document-text">Take Action
                                </flux:button>
                            </flux:modal.trigger>
                        </template>
                    </div>
                    <flux:separator />

                    <div class="flex-1 overflow-y-auto mt-6">
                        <table class="w-full border-collapse text-left text-sm text-zinc-600 dark:text-zinc-300">
                            <thead>
                                <tr
                                    class="text-xs font-bold uppercase tracking-wider text-zinc-400 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                                    <th class="p-3">Action</th>
                                    <th class="p-3">Date and Time</th>
                                    <th class="p-3">Office</th>
                                    <th class="p-3">Remarks</th>
                                    <th class="p-3">Forwarded</th>
                                    <th class="p-3">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(log, index) in history" :key="index">
                                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20">
                                        <td class="p-3">
                                            <span
                                                x-bind:class="log.status === 'Received' ?
                                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'"
                                                class="px-2 py-0.5 rounded text-xs font-medium"
                                                x-text="log.status === 'Received' ? 'Received' : 'Released'"></span>
                                        </td>
                                        <td class="p-3 text-zinc-500 dark:text-zinc-400" x-text="log.date_time"></td>
                                        <td class="p-3" x-text="log.office"></td>
                                        <td class="p-3 italic text-zinc-400" x-text="log.remarks"></td>
                                        <td class="p-3" x-text="log.forwarded_to"></td>
                                        <td class="p-3 font-mono text-xs" x-text="log.duration"></td>
                                    </tr>
                                </template>
                                <template x-if="history.length === 0">
                                    <tr>
                                        <td colspan="6" class="p-6 text-center text-zinc-400 text-sm">
                                            No activity history tracked for this entry.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </flux:card>
            </div>
        </div>

        <flux:modal name="forward_document_modal" class="md:w-[32rem] space-y-6">
            <div>
                <flux:heading size="lg">Forward Document Tracking</flux:heading>
                <flux:text>Choose the next organizational node to route this document profile.</flux:text>
            </div>

            <template x-if="profile">
                <form x-bind:action="'/client/document-tracking/' + profile.id + '/track'" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="document_no" x-bind:value="profile.document_no">
                    <input type="hidden" name="route" x-bind:value="form.forward_to" required>

                    <div class="space-y-2">
                        <flux:label>Forwarded to</flux:label>
                        <flux:dropdown class="w-full">
                            <flux:button icon:trailing="chevron-down" class="w-full [&>svg]:ml-auto" align="start">
                                <span x-text="selectedOffice"></span>
                            </flux:button>
                            <flux:menu class="max-h-60 overflow-y-auto !p-0">
                                <div
                                    class="p-2 sticky top-0 bg-white dark:bg-zinc-900 border-b border-zinc-100 dark:border-zinc-800 z-10">
                                    <flux:input x-model="search" placeholder="Search office..." icon="magnifying-glass"
                                        size="sm" clearable @click.stop="" @keydown.space.stop="" />
                                </div>

                                <template x-for="office in offices" :key="office">
                                    <flux:menu.item x-show="office.toLowerCase().includes(search.toLowerCase())"
                                        @click="form.forward_to = office; selectedOffice = office">
                                        <span x-text="office"></span>
                                    </flux:menu.item>
                                </template>

                                <div x-show="offices.filter(o => o.toLowerCase().includes(search.toLowerCase())).length === 0"
                                    class="p-3 text-sm text-zinc-400 text-center">
                                    No offices found.
                                </div>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <flux:textarea label="Remarks" name="remarks" x-model="form.remarks" rows="4"
                        placeholder="Provide tracking annotations (optional)..." />

                    <div class="flex gap-2 justify-end pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>

                        <flux:button type="submit" variant="primary" color="emerald" icon="check" loading="false"
                            x-bind:disabled="!form.forward_to">
                            Approve & Route
                        </flux:button>
                    </div>
                </form>
            </template>
        </flux:modal>
    </div>
@endsection
