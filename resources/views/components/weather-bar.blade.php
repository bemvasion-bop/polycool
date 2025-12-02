@if($weatherData)
    <x-weather-bar :weatherData="$weatherData" />
@else
    <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-6">
        ⚠️ Weather data unavailable — project location not found or API failed.
    </div>
@endif



    <div class="bg-white shadow-md rounded-lg p-5 mb-6">

        {{-- HEADER --}}
        <h3 class="text-xl font-semibold mb-3 flex items-center gap-2">
            🌤️ Weather Outlook (5 Days)
        </h3>

        {{-- RISK LEVEL BANNER --}}
        <div class="p-4 rounded mb-4 {{ $weather['recommendation']['color'] }}">
            <div class="flex items-center gap-2 text-lg">
                <span>{{ $weather['recommendation']['icon'] }}</span>
                <strong>{{ ucfirst($weather['risk']) }} Risk:</strong>
                <span>{{ $weather['recommendation']['text'] }}</span>
            </div>

            {{-- PAGASA Alert --}}
            @if(!empty($weather['pagasa_alert']))
                <div class="mt-2 bg-red-200 text-red-900 p-2 rounded text-sm font-bold">
                    ⚠️ PAGASA ALERT: {{ $weather['pagasa_alert'] }}
                </div>
            @endif
        </div>

        {{-- 5-DAY FORECAST --}}
        <div class="grid grid-cols-5 gap-3">
            @forelse ($weather['forecast'] as $day)
                <div class="bg-gray-100 p-3 rounded shadow-sm text-center">
                    <p class="font-semibold">{{ $day['day'] }}</p>

                    {{-- icon --}}
                    <img src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png"
                        class="mx-auto w-14 h-14" alt="icon">

                    <p class="text-2xl font-bold">{{ $day['temp'] }}°C</p>
                    <p class="text-sm text-gray-700">{{ $day['condition'] }}</p>

                    {{-- Rain Probability --}}
                    <p class="mt-1 text-sm font-semibold
                            {{ $day['rain'] >= 70 ? 'text-red-600' :
                                ($day['rain'] >= 40 ? 'text-yellow-600' : 'text-blue-600') }}">
                        🌧️ {{ $day['rain'] }}%
                    </p>
                </div>
            @empty
                <div class="col-span-5 text-center text-gray-500">
                    No forecast data available.
                </div>
            @endforelse
        </div>

    </div>

