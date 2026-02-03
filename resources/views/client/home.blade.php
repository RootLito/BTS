@extends('client.layout')

@section('content')
<div class="w-full h-full flex flex-col">
    <flux:heading size="xl" level="1">Good afternoon, Client</flux:heading>
    <flux:text class="mt-2 mb-6 text-base">Overview of trips, tickets, and travel activity</flux:text>



    <div class="flex-1 grid grid-cols-3 grid-rows-3 gap-10">
        <flux:card class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Total Trips</flux:heading>
                <flux:icon.map class="text-zinc-400" variant="outline" />
            </div>

            <div class="flex items-end gap-2">
                <flux:text class="text-4xl font-bold">142</flux:text>
                <flux:badge size="sm" color="emerald" variant="pill">+12%</flux:badge>
            </div>

            <flux:text size="sm" variant="subtle">Completed trips this year</flux:text>
            <flux:button variant="filled" size="sm" class="mt-auto">View All</flux:button>
        </flux:card>

        <flux:card class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Vehicle Trip Tickets</flux:heading>
                <flux:button variant="ghost" size="sm" icon="plus" square />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <flux:text size="xs" variant="subtle" class="uppercase tracking-wider">Active</flux:text>
                    <flux:text size="xl" weight="semibold">8</flux:text>
                </div>
                <div class="flex flex-col border-l border-zinc-200 pl-4">
                    <flux:text size="xs" variant="subtle" class="uppercase tracking-wider">Pending</flux:text>
                    <flux:text size="xl" weight="semibold">3</flux:text>
                </div>
            </div>

            <flux:button variant="filled" size="sm" class="mt-auto">View All Tickets</flux:button>
        </flux:card>

        <flux:card class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Travel Orders</flux:heading>
                <flux:badge color="yellow" size="sm" variant="solid">5 Awaiting Approval</flux:badge>
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                    <div class="flex-1">
                        <flux:text size="sm" weight="medium">TO-2026-001</flux:text>
                        <flux:text size="xs" variant="subtle">Regional Office Trip</flux:text>
                    </div>
                    <flux:icon.chevron-right size="sm" class="text-zinc-300" />
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                    <div class="flex-1">
                        <flux:text size="sm" weight="medium">TO-2026-004</flux:text>
                        <flux:text size="xs" variant="subtle">Supply Delivery</flux:text>
                    </div>
                    <flux:icon.chevron-right size="sm" class="text-zinc-300" />
                </div>
            </div>

            <flux:button variant="outline" size="sm" class="mt-auto">Manage Orders</flux:button>
        </flux:card>

        <flux:card class="row-span-2 col-span-2 flex flex-col gap-4">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <flux:heading size="lg">Monthly Trips Distribution</flux:heading>
                    <flux:subheading size="xs">Visualizing trip volume across the year</flux:subheading>
                </div>
                <flux:badge color="emerald" variant="pill" size="sm">Peak: Feb (19)</flux:badge>
            </div>

            <div class="flex-1 min-h-0" x-data="{
        initChart() {
            const ctx = this.$refs.canvas.getContext('2d');
            
            // Create a gradient for the fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                    datasets: [{
                        label: 'Trips',
                        data: [12, 19, 3, 5, 2, 3, 7, 10, 6, 8, 4, 9],
                        borderColor: '#10b981', 
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4, 
                        fill: true,
                        backgroundColor: gradient,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#18181b', // zinc-900
                            titleFont: { size: 14, weight: 'bold' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: (context) => ` Trips: ${context.raw}`
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            ticks: { color: '#a1a1aa', font: { size: 10 } },
                            grid: { color: '#f4f4f5' } // Very faint lines for tracking
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
            <div class="flex items-center justify-between mb-6">
                <flux:heading size="lg">Recent Trips</flux:heading>
                <flux:button variant="ghost" size="sm">View All</flux:button>
            </div>

            <div class="flex-1 overflow-y-auto pr-2 space-y-4 custom-scrollbar">
                <div class="group flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 rounded-lg">
                            <flux:icon.map-pin variant="outline" class="size-5 text-emerald-600" />
                        </div>
                        <div class="flex flex-col">
                            <flux:heading size="sm">Trip to Baguio</flux:heading>
                            <flux:text class="text-xs text-zinc-500">15 Jun 2024 • 1 Day</flux:text>
                        </div>
                    </div>
                    <flux:icon.check-circle variant="solid" class="size-6 text-emerald-500" />
                </div>

                <flux:separator variant="subtle" />

                <div class="group flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 rounded-lg">
                            <flux:icon.map-pin variant="outline" class="size-5 text-emerald-600" />
                        </div>
                        <div class="flex flex-col">
                            <flux:heading size="sm">Trip to Manila</flux:heading>
                            <flux:text class="text-xs text-zinc-500">12 Jun 2024 • 3 Days</flux:text>
                        </div>
                    </div>
                    <flux:icon.check-circle variant="solid" class="size-6 text-emerald-500" />
                </div>

                <flux:separator variant="subtle" />

                <div class="group flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 rounded-lg">
                            <flux:icon.map-pin variant="outline" class="size-5 text-emerald-600" />
                        </div>
                        <div class="flex flex-col">
                            <flux:heading size="sm">Trip to Vigan</flux:heading>
                            <flux:text class="text-xs text-zinc-500">10 Jun 2024 • 2 Days</flux:text>
                        </div>
                    </div>
                    <flux:icon.check-circle variant="solid" class="size-6 text-emerald-500" />
                </div>
            </div>
        </flux:card>
    </div>


</div>
@endsection