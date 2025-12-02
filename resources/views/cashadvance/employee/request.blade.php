@extends('layouts.app')

@section('content')

<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-semibold mb-4">Request Cash Advance</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cashadvance.submit') }}" method="POST">
        @csrf

        <label class="block mb-2 font-medium">Amount (PHP):</label>
        <input type="number" name="amount" class="w-full border p-2 rounded mb-4"
               placeholder="Enter amount" required>

        <label class="block mb-2 font-medium">Reason:</label>
        <textarea name="reason" class="w-full border p-2 rounded mb-4"
                  placeholder="Why do you need this cash advance?" required></textarea>

        <button class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700">
            Submit Request
        </button>
    </form>

</div>

@endsection
