<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/bfar.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="antialiased">
    <div class="w-full h-screen grid place-items-center bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('images/pic.jpg') }}');">
        
        <flux:card class="space-y-6 w-100">
            <div class="text-center">
                <flux:heading size="lg">Reset Password</flux:heading>
                <flux:text class="mt-2">Verify your details to set a new password.</flux:text>
            </div>

            @if ($errors->any())
                <flux:text color="red" size="sm" class="text-center">{{ $errors->first() }}</flux:text>
            @endif

            <form id="password-reset-form" action="{{ route('client.password.reset.verify') }}" method="POST"
                class="space-y-4">
                @csrf
                <flux:input name="username" label="Confirm Username" value="{{ old('username') }}" required />
                <flux:input name="office" label="Confirm Registered Office" placeholder="e.g. Finance" required />

                <hr class="border-gray-200 dark:border-gray-700" />

                <flux:input name="password" type="password" label="New Password" required />
                <flux:input name="password_confirmation" type="password" label="Confirm New Password" required />

                <div class="space-y-2 mt-4">
                    <flux:modal.trigger name="confirm-reset">
                        <flux:button variant="primary" color="sky" class="w-full">Reset Password</flux:button>
                    </flux:modal.trigger>

                    <flux:button href="{{ route('client.login') }}" variant="filled" class="w-full mt-2">Back to Login
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>

    <flux:modal name="confirm-reset" class="md:w-[450px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="mb-4">Confirm Password Reset?</flux:heading>
                <flux:text>Are you sure you want to change your password? This will take effect immediately and you
                    will be redirected to login.</flux:text>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" form="password-reset-form" variant="primary" color="sky">Confirm
                    Reset</flux:button>
            </div>
        </div>
    </flux:modal>

    @fluxScripts
</body>

</html>