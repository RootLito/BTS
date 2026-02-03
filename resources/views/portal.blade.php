<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticketing System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/bfar.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body>
    <div class="w-full h-screen grid place-items-center">

        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">Log in to your account</flux:heading>
                <flux:text class="mt-2">Welcome back!</flux:text>
            </div>

            <div class="space-y-6">
                <flux:input label="Email" type="email" placeholder="Your email address" />

                <flux:field>
                    <div class="mb-3 flex justify-between">
                        <flux:label>Password</flux:label>

                        <flux:link href="#" variant="subtle" class="text-sm">Forgot password?</flux:link>
                    </div>

                    <flux:input type="password" placeholder="Your password" />

                    <flux:error name="password" />
                </flux:field>
            </div>

            <div class="space-y-2">
                <flux:button variant="primary" class="w-full">Log in</flux:button>

                <flux:button variant="ghost" class="w-full">Sign up for a new account</flux:button>
            </div>
        </flux:card>


        <div class="flex gap-4">
            <a href="{{ route('client.home') }}">Client</a>
            <a href="{{ route('admin.dashboard') }}">Admin</a>
        </div>
    </div>
    @fluxScripts
</body>

</html>