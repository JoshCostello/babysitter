<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Babysitter Kata</title>
    </head>
    <body class="antialiased">
        <main>
            <h1>Babysitter Kata</h1>
            <form action="{{ route('calculate') }}" method="POST">
                @csrf

                @error('calculation')
                    <p>{{ $message }}</p>
                @enderror

                <fieldset>
                    <div>
                        <label for="arrival">Arrival Time</label>

                        <input id="arrival" name="arrival" value="{{ old('arrival') }}" type="datetime-local">

                        @error('arrival')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bedtime">Bedtime</label>

                        <input id="bedtime" name="bedtime" value="{{ old('bedtime') }}" type="datetime-local">

                        @error('bedtime')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="departure">Departure Time</label>

                        <input id="departure" name="departure" value="{{ old('departure') }}" type="datetime-local">

                        @error('departure')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <button>Calculate</button>
            </form>

            @if('earnings')
                <section>
                    {{ $earnings ?? null }}
                </section>
            @endif
        </main>
    </body>
</html>
