@extends('client.layout')

@section('content')
    <div class="w-full h-full flex flex-col">
        <flux:heading size="xl" level="1">National Travel Orders</flux:heading>
        <flux:text class="mt-2 mb-6 text-base">View and manage your national travel order documents</flux:text>

        <div class="flex-1 space-y-4">
            {{-- Filter Form --}}
            <form action="{{ route('client.national-to') }}" method="GET" class="flex items-center gap-2">
                <div class="w-100">
                    <flux:input name="search" value="{{ request('search') }}" icon="magnifying-glass"
                        placeholder="Search travel orders..." />
                </div>

                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        {{ request('sort') === 'oldest' ? 'Old - New' : 'New - Old' }}
                    </flux:button>

                    <flux:menu>
                        <flux:menu.radio.group name="sort" value="{{ request('sort', 'latest') }}">
                            <flux:menu.item
                                href="{{ route('client.national-to', ['sort' => 'latest', 'search' => request('search')]) }}">
                                New - Old
                            </flux:menu.item>
                            <flux:menu.item
                                href="{{ route('client.national-to', ['sort' => 'oldest', 'search' => request('search')]) }}">
                                Old - New
                            </flux:menu.item>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" color="emerald" icon="adjustments-horizontal">Filter
                    </flux:button>

                    @if (request()->anyFilled(['search', 'sort']))
                        <flux:button href="{{ route('client.national-to') }}" variant="filled" color="zinc"
                            icon="x-mark">
                            Clear
                        </flux:button>
                    @endif
                </div>

                {{-- Trigger to open the Create Modal --}}
                <flux:modal.trigger name="create-travel-order">
                    <flux:button variant="primary" color="emerald" icon="plus" class="ms-auto">
                        New Travel Order
                    </flux:button>
                </flux:modal.trigger>
            </form>

            <flux:card class="space-y-6 overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>TO Number</flux:table.column>
                        <flux:table.column>Route/Destination</flux:table.column>
                        <flux:table.column>Dates</flux:table.column>
                        <flux:table.column>Personnel</flux:table.column>
                        <flux:table.column class="w-0 text-right">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($travelOrders as $order)
                            <flux:table.row>
                                <flux:table.cell class="font-semibold text-zinc-800 dark:text-white">
                                    {{ $order->to_no ?? 'PENDING' }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span class="font-medium text-zinc-800 dark:text-white">{{ $order->route }}</span>
                                        <span
                                            class="text-xs text-zinc-500 truncate max-w-[200px]">{{ $order->purpose }}</span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap">
                                    <div class="flex flex-col text-sm">
                                        <span>
                                            {{ $order->departure ? \Carbon\Carbon::parse($order->departure)->format('M d') : 'N/A' }}
                                            –
                                            {{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->format('M d, Y') : 'N/A' }}
                                        </span>
                                        <span class="text-xs text-zinc-400">
                                            {{ $order->departure ? \Carbon\Carbon::parse($order->departure)->format('l') : '' }}
                                        </span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    @php
                                        $personnelList = is_array($order->personnel)
                                            ? $order->personnel
                                            : json_decode($order->personnel ?? '[]', true);
                                        $extraCount = count($personnelList) - 1;
                                    @endphp

                                    <div class="flex flex-col text-sm">
                                        @if (count($personnelList) > 0)
                                            <span class="font-medium text-zinc-800 dark:text-white">
                                                {{ $personnelList[0]['name'] ?? 'Unknown' }}
                                            </span>
                                            @if ($extraCount > 0)
                                                @php
                                                    $namesOnly = array_map(
                                                        fn($p) => $p['name'] ?? '',
                                                        array_slice($personnelList, 1),
                                                    );
                                                @endphp
                                                <span class="text-xs text-zinc-400 cursor-help"
                                                    title="{{ implode(', ', $namesOnly) }}">
                                                    +{{ $extraCount }} other {{ $extraCount > 1 ? 'people' : 'person' }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-xs text-zinc-400 italic">None Assigned</span>
                                        @endif
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex gap-2 items-center">
                                        <flux:modal.trigger :name="'track-ticket-' . $order->id">
                                            <flux:button icon="map">Track</flux:button>
                                        </flux:modal.trigger>

                                        <flux:dropdown>
                                            <flux:button icon-trailing="chevron-down" class="text-sm">Actions</flux:button>
                                            <flux:menu>
                                                <flux:modal.trigger :name="'view-order-' . $order->id">
                                                    <flux:menu.item icon="eye">View Details</flux:menu.item>
                                                </flux:modal.trigger>
                                                <flux:modal.trigger :name="'edit-order-' . $order->id">
                                                    <flux:menu.item icon="pencil">Edit / Update</flux:menu.item>
                                                </flux:modal.trigger>
                                                <flux:menu.item icon="document-text"
                                                    href="{{ route('client.national-to.show', $order->id) }}">
                                                    Print TO
                                                </flux:menu.item>
                                                <flux:menu.separator />
                                                <flux:modal.trigger :name="'delete-order-' . $order->id">
                                                    <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                                                </flux:modal.trigger>
                                            </flux:menu>
                                        </flux:dropdown>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <flux:icon name="document-text" class="size-8 text-zinc-400" />
                                        <flux:text variant="strong" class="text-zinc-500">No travel orders found</flux:text>
                                        <flux:text size="sm" class="text-zinc-400">Your created national travel orders
                                            will show up here.</flux:text>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            @if (method_exists($travelOrders, 'links'))
                {{ $travelOrders->links() }}
            @endif
        </div>
    </div>

    {{-- GLOBAL / CREATE MODAL --}}
    <flux:modal name="create-travel-order" class="md:w-[650px]">
        <form action="{{ route('client.national-to.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <flux:heading size="lg">Create National Travel Order</flux:heading>
                <flux:text>Fill out the parameters below to draft your travel authorization document.</flux:text>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input label="TO Number (Optional)" name="to_no" />
                <flux:input type="date" label="Document Date" name="date" required value="{{ date('Y-m-d') }}" />
                <div class="md:col-span-2">
                    <flux:input label="Route / Destination" name="route" required placeholder="Ex: Davao-Manila-Davao" />
                </div>
                <flux:input type="date" label="Departure Date" name="departure" required />
                <flux:input type="date" label="Return Date" name="return_date" required />
                <div class="md:col-span-2">
                    <flux:textarea label="Purpose of Travel" name="purpose" rows="3" required
                        placeholder="State objective clearly..." />
                </div>

            </div>

            <flux:separator />

            {{-- Personnel UI Section --}}
            <div x-data="{ personnel: [{ name: '', position: '' }] }" class="space-y-2">
                <div class="flex justify-between items-center">
                    <flux:label>Personnel Details</flux:label>
                    <flux:button variant="ghost" size="sm" icon="plus"
                        @click="personnel.push({name: '', position: ''})">Add Personnel
                    </flux:button>
                </div>
                <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                    <template x-for="(p, index) in personnel" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-end border-b border-gray-100 dark:border-zinc-800 pb-2">
                            <div class="col-span-6">
                                <flux:input ::name="'personnel['+index+'][name]'" x-model="p.name" placeholder="Name"
                                    required />
                            </div>
                            <div class="col-span-5">
                                <flux:input ::name="'personnel['+index+'][position]'" x-model="p.position"
                                    placeholder="Position" required />
                            </div>
                            <div class="col-span-1 flex justify-center">
                                <flux:button variant="ghost" icon="trash" size="sm"
                                    @click="personnel.splice(index, 1)" x-show="personnel.length > 1" />
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" color="emerald">Save Order</flux:button>
            </div>
        </form>
    </flux:modal>


    @foreach ($travelOrders as $order)
        @php
            $personnelList = is_array($order->personnel)
                ? $order->personnel
                : json_decode($order->personnel ?? '[]', true);
        @endphp

        <flux:modal :name="'view-order-' . $order->id" class="md:w-[600px]">
            <div class="space-y-6">
                <flux:heading size="lg" class="font-semibold text-zinc-900 dark:text-white">Travel Order Information
                </flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-6">
                    <flux:field>
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 font-medium">TO Number
                        </flux:label>
                        <flux:text variant="strong" size="lg" class="block mt-1 text-zinc-900 dark:text-white">
                            {{ $order->to_no ?? 'Unassigned' }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 font-medium">Date Documented
                        </flux:label>
                        <flux:text class="block mt-1 text-zinc-800 dark:text-zinc-200">
                            {{ $order->date ? \Carbon\Carbon::parse($order->date)->format('M d, Y') : 'N/A' }}
                        </flux:text>
                    </flux:field>

                    <flux:field class="col-span-2">
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 font-medium">Route/Destination
                        </flux:label>
                        <flux:text font="medium" class="block mt-1 text-zinc-900 dark:text-white">
                            {{ $order->route }}
                        </flux:text>
                    </flux:field>

                    <flux:field class="col-span-2">
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 font-medium">Purpose of Travel
                        </flux:label>
                        <flux:text class="block mt-1 text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            {{ $order->purpose ?? 'No purpose provided' }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 font-medium">Departure Date
                        </flux:label>
                        <div class="flex items-center gap-2 mt-1.5 text-zinc-800 dark:text-zinc-200">
                            <flux:icon name="calendar" variant="micro" class="text-zinc-400" />
                            <flux:text font="medium">
                                {{ $order->departure ? \Carbon\Carbon::parse($order->departure)->format('M d, Y (l)') : 'N/A' }}
                            </flux:text>
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs uppercase tracking-wider text-zinc-400 font-medium">Return Date
                        </flux:label>
                        <div class="flex items-center gap-2 mt-1.5 text-zinc-800 dark:text-zinc-200">
                            <flux:icon name="calendar" variant="micro" class="text-zinc-400" />
                            <flux:text font="medium">
                                {{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->format('M d, Y (l)') : 'N/A' }}
                            </flux:text>
                        </div>
                    </flux:field>
                </div>

                <flux:separator class="my-6" />

                <div>
                    <flux:heading size="sm" class="font-semibold text-zinc-800 dark:text-zinc-200 mb-3">Traveling
                        Personnel ({{ count($personnelList) }})</flux:heading>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @forelse($personnelList as $person)
                            <div
                                class="flex flex-col p-2.5 rounded-lg bg-zinc-50 dark:bg-white/5 border border-zinc-200/60 dark:border-white/10">
                                <div class="flex items-center gap-2">
                                    <flux:icon name="user" variant="micro" class="text-zinc-400" />
                                    <flux:text size="sm" class="font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ $person['name'] ?? 'N/A' }}</flux:text>
                                </div>
                                <span
                                    class="text-[11px] text-zinc-400 pl-6">{{ $person['position'] ?? 'No Position' }}</span>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-4 text-zinc-400 text-xs italic">No personnel listed.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </flux:modal>

        {{-- UPDATE/EDIT MODAL --}}
        <flux:modal :name="'edit-order-' . $order->id" class="md:w-[650px]">
            <form action="{{ route('client.national-to.update', $order->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <flux:heading size="lg">Modify Travel Order</flux:heading>
                    <flux:text>Update parameters and structural changes for this specific document track record.</flux:text>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input label="TO Number" name="to_no" value="{{ $order->to_no }}" />
                    <flux:input type="date" label="Document Date" name="date" required
                        value="{{ $order->date ? \Carbon\Carbon::parse($order->date)->format('Y-m-d') : '' }}" />
                    <div class="md:col-span-2">
                        <flux:input label="Route / Destination" name="route" required value="{{ $order->route }}" />
                    </div>
                    <div class="md:col-span-2">
                        <flux:textarea label="Purpose of Travel" name="purpose" rows="3" required>
                            {{ $order->purpose }}</flux:textarea>
                    </div>
                    <flux:input type="date" label="Departure Date" name="departure" required
                        value="{{ $order->departure ? \Carbon\Carbon::parse($order->departure)->format('Y-m-d') : '' }}" />
                    <flux:input type="date" label="Return Date" name="return_date" required
                        value="{{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->format('Y-m-d') : '' }}" />
                </div>

                <flux:separator />

                {{-- Personnel UI Section for Editing --}}
                <div x-data="{ personnel: {{ json_encode(count($personnelList) > 0 ? $personnelList : [['name' => '', 'position' => '']]) }} }" class="space-y-2">
                    <div class="flex justify-between items-center">
                        <flux:label>Personnel Details</flux:label>
                        <flux:button variant="ghost" size="sm" icon="plus"
                            @click="personnel.push({name: '', position: ''})">Add Personnel
                        </flux:button>
                    </div>
                    <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                        <template x-for="(p, index) in personnel" :key="index">
                            <div
                                class="grid grid-cols-12 gap-2 items-end border-b border-gray-100 dark:border-zinc-800 pb-2">
                                <div class="col-span-6">
                                    <flux:input ::name="'personnel['+index+'][name]'" x-model="p.name" placeholder="Name"
                                        required />
                                </div>
                                <div class="col-span-5">
                                    <flux:input ::name="'personnel['+index+'][position]'" x-model="p.position"
                                        placeholder="Position" required />
                                </div>
                                <div class="col-span-1 flex justify-center">
                                    <flux:button variant="ghost" icon="trash" size="sm"
                                        @click="personnel.splice(index, 1)" x-show="personnel.length > 1" />
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" color="emerald">Update Details</flux:button>
                </div>
            </form>
        </flux:modal>

        {{-- TRACKING MODAL --}}
        <flux:modal :name="'track-ticket-' . $order->id" class="max-w-none!">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Document Tracking</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                        Routing history and office processing logs for your travel order document
                        #{{ $order->to_no ?? 'PENDING' }}.
                    </flux:text>
                </div>

                @if (!empty($order->stepper_steps) && count($order->stepper_steps) > 0)
                    <div class="relative flex items-start justify-between mt-8 z-0 pb-4 overflow-x-auto">
                        @foreach ($order->stepper_steps as $step)
                            <div
                                class="flex flex-col items-center flex-1 min-w-[150px] max-w-xl text-center relative px-2">

                                @if (isset($step['has_next_line']) && $step['has_next_line'])
                                    <div
                                        class="absolute top-[18px] left-[50%] right-[-50%] h-[3px] {{ !empty($step['is_released']) ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-zinc-200 dark:bg-zinc-700' }} -z-10 transition-colors duration-200">
                                    </div>
                                @endif

                                <div
                                    class="flex items-center justify-center size-9 rounded-full border transition-colors duration-200 shadow-none
                        {{ !empty($step['is_released'])
                            ? 'bg-emerald-500 border-emerald-500 text-white font-bold'
                            : 'bg-zinc-50 dark:bg-zinc-800 border-zinc-300 text-zinc-400 dark:border-zinc-600 dark:text-zinc-500' }}">
                                    @if (!empty($step['is_released']))
                                        <flux:icon name="check" class="size-6 text-white" variant="micro" />
                                    @else
                                        <span class="text-xs font-semibold">{{ $loop->iteration }}</span>
                                    @endif
                                </div>

                                <span
                                    class="mt-3 text-xs font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                                    {{ $step['name'] ?? 'Unknown Step' }}
                                </span>

                                <div
                                    class="mt-3 w-full bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200/60 dark:border-zinc-800/80 rounded-lg p-2 text-left space-y-1.5">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 tracking-wider">RECEIVED</span>
                                        <span
                                            class="text-[11px] font-medium text-zinc-700 dark:text-zinc-300 {{ ($step['received_at'] ?? '') === 'Not Applicable' ? 'italic text-zinc-400 dark:text-zinc-500' : '' }}">
                                            {{ $step['received_at'] ?? 'Pending' }}
                                        </span>
                                    </div>

                                    <div class="h-[1px] bg-zinc-200/60 dark:bg-zinc-800/80 w-full"></div>

                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 tracking-wider">RELEASED</span>
                                        <span
                                            class="text-[11px] font-medium text-zinc-700 dark:text-zinc-300 {{ ($step['released_at'] ?? '') === 'Not Applicable' ? 'italic text-zinc-400 dark:text-zinc-500' : '' }}">
                                            {{ $step['released_at'] ?? 'Pending' }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div
                        class="flex flex-col items-center justify-center py-12 text-center border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/20">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No History Logged</span>
                        <span class="text-xs text-zinc-400 dark:text-zinc-500 max-w-xs mt-1">This document hasn't completed
                            any tracking movements yet.</span>
                    </div>
                @endif

                <div class="flex justify-end mt-4">
                    <flux:modal.close>
                        <flux:button variant="filled">Close Tracker</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>

        {{-- DELETE MODAL --}}
        <flux:modal :name="'delete-order-' . $order->id" class="min-w-[400px]">
            <form action="{{ route('client.national-to.destroy', $order->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('DELETE')

                <div class="space-y-2">
                    <flux:heading size="lg">Delete Travel Order?</flux:heading>
                    <flux:text>
                        Are you sure you want to delete this travel order destination to
                        <strong>{{ $order->route }}</strong>? This action cannot be reversed.
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" color="red">Confirm Delete</flux:button>
                </div>
            </form>
        </flux:modal>
    @endforeach
@endsection
