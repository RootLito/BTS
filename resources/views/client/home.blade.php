@extends('client.layout')

@section('content')
    <div class="w-full h-full flex flex-col">
        <flux:heading size="xl" level="1">Good afternoon, {{ Auth::user()->office }}</flux:heading>
        <flux:text class="mt-2 mb-6 text-base text-zinc-600">
            Overview of trip volume, current statuses, and monthly trip distribution for
            <span class="font-semibold text-zinc-900">{{ now()->year }}</span>
        </flux:text>

        <div class="flex-1 grid grid-cols-3 grid-rows-3 gap-6">
            <flux:card class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Total Trips</flux:heading>
                    <flux:icon.map class="text-zinc-400" variant="outline" />
                </div>

                <div class="flex items-end gap-2">
                    <flux:text class="text-4xl font-bold">{{ $totalTrips }}</flux:text>
                    <flux:badge size="sm" color="{{ $percentageChange >= 0 ? 'emerald' : 'rose' }}" variant="pill">
                        {{ $percentageChange >= 0 ? '+' : '' }}{{ number_format($percentageChange, 0) }}%
                    </flux:badge>
                </div>

                <flux:text size="sm" variant="subtle">Trips initiated this year</flux:text>
                <a href="{{ route('client.trip-ticket') }}" class="mt-auto self-start">
                    <flux:button variant="subtle" size="xs" icon:trailing="arrow-long-right" class="cursor-pointer">
                        View All</flux:button>
                </a>
            </flux:card>

            <flux:card class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Pending Trips</flux:heading>
                    <flux:icon.clock class="text-zinc-400" variant="outline" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:text class="text-4xl font-bold">{{ $pendingTrips }}</flux:text>
                </div>
                <flux:text size="sm" variant="subtle">Awaiting approval or dispatch</flux:text>
            </flux:card>

            <flux:card class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Completed Trips</flux:heading>
                    <flux:icon.check-badge class="text-zinc-400" variant="outline" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:text class="text-4xl font-bold">{{ $completedTripsCount }}</flux:text>
                </div>
                <flux:text size="sm" variant="subtle">Successfully arrived at destination</flux:text>
            </flux:card>

            <flux:card class="row-span-2 col-span-2 flex flex-col gap-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <flux:heading size="lg" class="mb-2">Monthly Trips Distribution</flux:heading>
                        <flux:text size="sm" variant="subtle">Trip volume for the current calendar year</flux:text>
                    </div>
                    <flux:badge color="emerald" variant="pill" size="sm">Peak: {{ $peakMonthName }}
                        ({{ $maxVal }})</flux:badge>
                </div>

                <div class="flex-1 min-h-0" x-data="{
                    initChart() {
                        const ctx = this.$refs.canvas.getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
                
                        new Chart(this.$refs.canvas, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                datasets: [{
                                    label: 'Trips',
                                    data: {{ json_encode($chartData) }},
                                    borderColor: '#10b981',
                                    borderWidth: 3,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#10b981',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    tension: 0.4,
                                    fill: true,
                                    backgroundColor: gradient,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { color: '#a1a1aa', font: { size: 10 }, maxTicksLimit: 5 },
                                        grid: { color: '#f4f4f5' }
                                    },
                                    x: {
                                        ticks: { color: '#a1a1aa', font: { size: 10 } },
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                    }
                }" x-init="initChart()">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </flux:card>

            <flux:card class="row-span-2 flex flex-col">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <flux:heading size="lg" class="mb-2">Recent Completed</flux:heading>
                        <flux:text size="sm" variant="subtle">Recent completed trips</flux:text>
                    </div>
                    <flux:icon.bookmark class="text-zinc-400" variant="outline" />
                </div>

                <div class="flex-1 overflow-y-auto space-y-4 mt-6">
                    @forelse($recentTrips as $trip)
                        <div x-data="{ isTruncated: false }" x-init="$nextTick(() => {
                            const el = $refs.destination;
                            isTruncated = el.scrollWidth > el.clientWidth;
                        })"
                            class="group flex items-center rounded-lg w-full">

                            <flux:modal.trigger name="details-{{ $trip->id }}">
                                <div class="flex items-center gap-3 min-w-0"
                                    :class="isTruncated ? 'cursor-pointer' : 'cursor-default'"
                                    x-on:click="if (!isTruncated) $event.preventDefault()">
                                    <div class="shrink-0 p-2 bg-emerald-50 rounded-lg">
                                        <flux:icon.map-pin variant="outline" class="size-5 text-emerald-600" />
                                    </div>

                                    <div class="flex flex-col min-w-0 text-left">
                                        <flux:heading x-ref="destination" size="sm" class="truncate block">
                                            {{ $trip->destination }}
                                        </flux:heading>
                                        <flux:text class="text-xs text-zinc-500 truncate">
                                            {{ $trip->start_date->format('d M Y') }}
                                        </flux:text>
                                    </div>
                                </div>
                            </flux:modal.trigger>

                            <div class="shrink-0">
                                <flux:icon.check-circle variant="solid" class="size-5 text-emerald-500" />
                            </div>

                            <flux:modal name="details-{{ $trip->id }}" class="min-w-[22rem]">
                                <div class="space-y-6">
                                    <flux:heading size="lg">Destination Details</flux:heading>
                                    <flux:text>{{ $trip->destination }}</flux:text>
                                    {{-- <flux:modal.close>
                                        <flux:button variant="ghost">Close</flux:button>
                                    </flux:modal.close> --}}
                                </div>
                            </flux:modal>
                        </div>
                        @if (!$loop->last)
                            <flux:separator variant="subtle" />
                        @endif
                    @empty
                        <flux:text class="text-center py-4 text-zinc-400">No completed trips yet.</flux:text>
                    @endforelse
                </div>
            </flux:card>
        </div>
    </div>
@endsection
