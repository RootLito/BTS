@extends('client.layout')

@section('content')
    <div class="w-full h-full flex flex-col">
        <flux:card class="max-w-xl mx-auto mt-12">
            <form id="profile-form" action="{{ route('client.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')
                
                <div>
                    <flux:heading size="lg">Profile Information</flux:heading>
                    <flux:text size="sm">Update your account's profile information and office designation.</flux:text>
                </div>

                <flux:input label="Username" name="username" value="{{ old('username', $client->username) }}" required />

                <flux:input label="Office" name="office" value="{{ old('office', $client->office) }}" required />

                <hr class="border-gray-200 dark:border-gray-700" />

                <div>
                    <flux:heading size="lg">Update Password</flux:heading>
                    <flux:text size="sm">Leave blank if you do not want to change your password.</flux:text>
                </div>

                <flux:input label="New Password" name="password" type="password" />
                <flux:input label="Confirm Password" name="password_confirmation" type="password" />

                <div class="flex items-center justify-end gap-4">
                    @if (session('status') === 'profile-updated')
                        <flux:text color="green" size="sm" icon="check-circle">Saved successfully.</flux:text>
                    @endif

                    <flux:modal.trigger name="confirm-profile-update">
                        <flux:button variant="primary" color="emerald">Save Changes</flux:button>
                    </flux:modal.trigger>
                </div>
            </form>
        </flux:card>

        <flux:modal name="confirm-profile-update" class="md:w-[450px]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="mb-4">Confirm Changes?</flux:heading>
                    <flux:text>Are you sure you want to update your profile information? This will take effect immediately.</flux:text>
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" form="profile-form" variant="primary" color="emerald">Confirm Update</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
@endsection