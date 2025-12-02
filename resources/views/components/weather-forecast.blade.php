@if($weatherData)

<div class="bg-white shadow p-6 rounded-lg mb-6">
    <h3 class="text-xl font-semibold mb-4">
        5-Day Forecast for: {{ $weatherData['location'] }}
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

        @foreach($weatherData['forecast'] as $day)
            <div class="border rounded-lg p-4 flex flex-col items-center bg-gray-50">

                <p class="font-semibold">
                    {{ \Carbon\Carbon::parse($day['date'])->format('D, M d') }}
                </p>

                <img src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png"
                     class="w-16 h-16" />

                <p class="text-lg font-bold">
                    {{ $day['temp_min'] }}°C / {{ $day['temp_max'] }}°C
                </p>

                <p class="text-sm text-gray-600">
                    {{ ucfirst($day['condition']) }}
                </p>

            </div>
        @endforeach

    </div>

    {{-- OPTIONAL WARNING BANNER --}}
    @if($weatherData['risk'] === 'high')
        <div class="mt-4 p-3 bg-red-200 text-red-900 font-medium rounded">
            ⛈️ High chance of rain — avoid spraying.
        </div>
    @elseif($weatherData['risk'] === 'moderate')
        <div class="mt-4 p-3 bg-yellow-200 text-yellow-900 font-medium rounded">
            🌦️ Moderate risk — evaluate conditions carefully.
        </div>
    @else
        <div class="mt-4 p-3 bg-green-200 text-green-900 font-medium rounded">
            ☀️ Low rain chance — good spraying weather.
        </div>
    @endif

</div>

@endif
