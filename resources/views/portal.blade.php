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
    <div class="w-full h-screen grid place-items-center bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('images/pic.jpg') }}');">
        <flux:card class="space-y-6 w-100">
            <img src="{{ asset('images/bfar.png') }}" alt="BFAR Logo" width="150" class="mx-auto">
            <div>
                <flux:heading size="lg" class="text-center">Booking and Ticketing System</flux:heading>
                <flux:text class="mt-2" class="text-center">BFAR Booking and Ticket Management</flux:text>
            </div>
            <flux:separator text="Login as" />
            <div class="w-full flex gap-4">
                <flux:button href="{{ route('client.login') }}" variant="outline" class="flex-1">
                    Client
                </flux:button>

                <flux:button href="{{ route('admin.login') }}" variant="primary" color="sky" class="flex-1">
                    Admin
                </flux:button>
            </div>
        </flux:card>
    </div>
    @fluxScripts
</body>

</html>