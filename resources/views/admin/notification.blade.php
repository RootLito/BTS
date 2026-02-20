@extends('admin.layout')

@section('content')
    <flux:heading size="xl" level="1">Notifications</flux:heading>
    <flux:text class="mt-2 mb-6 text-base">Stay updated on recent trip activity.</flux:text>

    <div class="space-y-4">
        @forelse ($notifications as $notification)
            <flux:card class="flex items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <flux:heading size="sm">Trip #{{ $notification->trip_id }}</flux:heading>
                            <flux:text size="xs" class="text-zinc-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </flux:text>
                        </div>


                        @if ($notification->is_viewed)
                            <flux:badge size="sm" variant="subtle">Read</flux:badge>
                        @else
                            <flux:badge color="red" size="sm" variant="subtle">New</flux:badge>
                        @endif
                    </div>

                    <flux:text class="my-6 block font-bold">
                        {{ $notification->message }}
                    </flux:text>

                    {{-- <flux:link :href="route('admin.booking.show', $notification->trip_id)" underline>
                        <flux:text>View Trip</flux:text>
                    </flux:link> --}}
                    <flux:link :href="route('admin.notification.read', $notification->id)" underline>
                        <flux:text>View Trip</flux:text>
                    </flux:link>
                </div>
            </flux:card>
        @empty
            <flux:card class="text-center py-12">
                <flux:text class="text-zinc-500">No notification(s).</flux:text>
            </flux:card>
        @endforelse

        @if (method_exists($notifications, 'links'))
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
