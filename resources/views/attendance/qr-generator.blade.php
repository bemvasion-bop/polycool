@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4">QR Generator</h2>

    <table class="w-full border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 border">Project</th>
                <th class="p-3 border">Client</th>
                <th class="p-3 border">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($projects as $project)
            <tr class="border-b">
                <td class="p-3">{{ $project->project_name }}</td>
                <td class="p-3">{{ $project->client->name }}</td>
                <td class="p-3">
                    <a href="{{ route('projects.qr', $project->id) }}"
                       class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Generate QR
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
