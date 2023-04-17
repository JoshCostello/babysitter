<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @vite('resources/css/app.css')

        <title>Babysitter Kata</title>
    </head>
    <body class="antialiased">
        <main class="px-24 py-12">
            <header class="mb-12">
                <h1 class="text-center text-3xl font-bold">Babysitter Kata</h1>
            </header>

            <form class="max-w-md mx-auto" action="{{ route('calculate') }}" method="POST">
                @csrf

                @error('calculation')
                    <p class="bg-red-100 font-bold text-red-600 px-4 py-2 rounded-lg mb-8">{{ $message }}</p>
                @enderror

                <fieldset class="mb-4">
                    <div class="mb-4">
                        <label class="block font-bold mb-1" for="arrival">Arrival Time <span class="text-gray-400">(Required)</span></label>

                        <input class="border-2 rounded-md px-2 py-1 text-lg" id="arrival" name="arrival" value="{{ old('arrival') }}" type="datetime-local">

                        @error('arrival')
                            <p class="text-red-400 font-bold text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold mb-1" for="bedtime">Bedtime</label>

                        <input class="border-2 rounded-md px-2 py-1 text-lg" id="bedtime" name="bedtime" value="{{ old('bedtime') }}" type="datetime-local">

                        @error('bedtime')
                            <p class="text-red-400 font-bold text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold mb-1" for="departure">Departure Time <span class="text-gray-400">(Required)</span></label>

                        <input class="border-2 rounded-md px-2 py-1 text-lg" id="departure" name="departure" value="{{ old('departure') }}" type="datetime-local">

                        @error('departure')
                            <p class="text-red-400 font-bold text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <button class="border-2 rounded-md px-3 py-1.5 font-bold transition hover:border-blue-500 hover:text-blue-500 hover:bg-blue-100">Calculate</button>
            </form>

            @if($earnings ?? false)
                <section class="max-w-md mx-auto mt-10 text-center border px-24 py-12 rounded-lg">
                    <p class="text-gray-400 font-bold">Total Earnings:</p>
                    <p class="text-3xl font-bold">{{ $earnings }}</p>
                </section>
            @endif
        </main>
    </body>
</html>
