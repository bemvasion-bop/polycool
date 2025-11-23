@extends('layouts.app')

@section('content')
<div class="p-8">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Clients</h2>

        <a href="{{ route('clients.create') }}"
           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow">
            + New Client
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b text-gray-600">
                    <th class="py-3">Name</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($clients as $client)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3">{{ $client->name }}</td>
                        <td>{{ $client->contact_person }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->phone }}</td>

                        <td class="space-x-3">
                            <a href="{{ route('clients.show', $client->id) }}"
                               class="text-blue-600 hover:underline">View</a>

                            <a href="{{ route('clients.edit', $client->id) }}"
                               class="text-green-600 hover:underline">Edit</a>

                            <form action="{{ route('clients.destroy', $client->id) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline"
                                        onclick="return confirm('Delete this client?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-5 text-center text-gray-500">No clients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
