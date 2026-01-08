@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center h-full">

        <div class="bg-white p-6 sm:p-10 rounded-xl shadow-lg w-full max-w-md text-center">


        <h2 class="text-2xl font-bold mb-4">My QR Code</h2>

        <p class="text-gray-600 mb-6">
            This QR code will be used for time-in and time-out.
        </p>

        {{-- Employee Details --}}
        <div class="mb-6">
            <p class="text-lg font-semibold">
                {{ $user->full_name }}
            </p>
            <p class="text-gray-500">
                {{ ucfirst($user->system_role) }}
            </p>
            <p class="text-gray-500 text-sm">
                Employee ID: {{ $user->id }}
            </p>
        </div>


        {{-- QR CODE --}}
        <div class="flex justify-center mb-6">

            @if($user->system_role === 'employee')
                {!! QrCode::size(180)->generate($user->qr_code) !!}
            @else
                <p class="text-gray-500">QR Code not available for this role.</p>
            @endif

        </div>

        <p class="text-gray-600 text-sm mb-4">
            QR Value: <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                {{ $user->qr_code }}
            </span>
        </p>



        {{-- DOWNLOAD BUTTON (SVG — FIXED) --}}
        <a href="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(300)->generate($user->qr_code)) }}"
        download="QR-{{ $user->id }}.svg"
        class="px-5 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            Download QR Code
        </a>

    </div>

</div>
@endsection
