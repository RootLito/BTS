<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Client Login</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/bfar.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body>
    <div class="w-full h-screen grid place-items-center bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('images/pic.jpg') }}');">
        <flux:card class="space-y-6 w-100">
            <img src="{{ asset('images/bfar.png') }}" alt="BFAR Logo" width="150" class="mx-auto">
            <div>
                <flux:heading size="lg" class="text-center">Booking and Ticketing System</flux:heading>
                <flux:text class="mt-2 text-center">BFAR Booking and Ticket Management</flux:text>
            </div>

            <flux:separator text="Client Login" />

            {{-- @if ($errors->any())
            <flux:callout variant="danger" icon="x-circle" heading="{{ $errors->first() }}" />
            @endif --}}

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="client">

                <div class="space-y-2">
                    <flux:input name="username" label="Username" type="text" placeholder="Your username" required />
                    <flux:input name="password" type="password" label="Password" placeholder="Your password" required />
                </div>

                <div class="space-y-2 mt-4">
                    <flux:button type="submit" variant="primary" color="sky" class="w-full">Log in</flux:button>
                    <flux:button href="/" type="button" variant="filled" color="red" class="w-full">
                        Back
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
    @fluxScripts
</body>

</html>