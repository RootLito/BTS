@extends('admin.layout')

@section('content')
    <div class="w-full flex justify-between">
        <div>
            <flux:heading size="xl" level="1">Notifications</flux:heading>
            <flux:text class="mt-2 mb-6 text-base">Stay updated on recent trip activity.</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:modal.trigger name="mark-read-modal">
                <flux:button variant="subtle" size="sm" icon="envelope-open">Mark all as read</flux:button>
            </flux:modal.trigger>

            <flux:modal.trigger name="clear-notifications">
                <flux:button variant="subtle" size="sm" icon="trash" color="red">Clear notifications</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <flux:modal name="mark-read-modal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Mark all as read?</flux:heading>
                <flux:text class="mt-2">
                    This will mark all current admin notifications as viewed.
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <form action="{{ route('admin.notifications.markAllRead') }}" method="POST">
                    @csrf
                    <flux:button type="submit" color="emerald" variant="primary">Confirm</flux:button>
                </form>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="clear-notifications" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete all notifications?</flux:heading>
                <flux:text class="mt-2">
                    You're about to delete all notification records.<br>
                    This action cannot be reversed.
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <form action="{{ route('admin.notifications.clearAll') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="danger">Delete all</flux:button>
                </form>
            </div>
        </div>
    </flux:modal>

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
