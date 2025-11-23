@extends('layouts.app')

@section('content')
<div class="p-10">

    <div class="bg-white rounded-lg shadow p-8 w-full lg:w-2/3">

        <h2 class="text-2xl font-semibold mb-6">Client Details</h2>

        <p><strong>Name:</strong> {{ $client->name }}</p>
        <p><strong>Contact Person:</strong> {{ $client->contact_person }}</p>
        <p><strong>Email:</strong> {{ $client->email }}</p>
        <p><strong>Phone:</strong> {{ $client->phone }}</p>
        <p><strong>Address:</strong> {{ $client->address }}</p>

        <div class="mt-6 space-x-4">
            <a href="{{ route('clients.edit', $client->id) }}"
               class="text-blue-600 hover:underline">
                Edit
            </a>

            <a href="{{ route('clients.index') }}"
               class="text-gray-600 hover:underline">
                Back
            </a>
        </div>

    </div>

</div>
@endsection
