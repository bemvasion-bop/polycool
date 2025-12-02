@extends('layouts.app')

@section('content')
<div class="p-10">
    <h2 class="text-2xl font-semibold mb-6">Projects</h2>

    <div class="bg-white p-6 rounded-lg shadow">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">Project Name</th>
                    <th class="p-3 border">Client</th>
                    <th class="p-3 border">Total Price</th>
                    <th class="p-3 border">Progress</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">Warnings</th>
                    <th class="p-3 border">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($projects as $project)
                <tr>

                    {{-- NAME --}}
                    <td class="p-3 border font-medium">
                        {{ $project->project_name }}
                    </td>

                    {{-- CLIENT --}}
                    <td class="p-3 border">
                        {{ $project->client->name ?? 'Unknown' }}
                    </td>

                    {{-- PRICE --}}
                    <td class="p-3 border">
                        ₱{{ number_format($project->final_project_price, 2) }}
                    </td>

                    {{-- PROGRESS --}}
                    <td class="p-3 border w-48">
                        <x-progress-bar :value="$project->progress" />
                    </td>

                    {{-- STATUS --}}
                    <td class="p-3 border">
                        @switch($project->status)
                            @case('pending')   <span class="px-3 py-1 text-xs bg-gray-300 rounded-full">Pending</span> @break
                            @case('active')    <span class="px-3 py-1 text-xs bg-blue-500 text-white rounded-full">Active</span> @break
                            @case('on_hold')   <span class="px-3 py-1 text-xs bg-yellow-400 rounded-full">On Hold</span> @break
                            @case('delayed')   <span class="px-3 py-1 text-xs bg-red-500 text-white rounded-full">Delayed</span> @break
                            @case('completed') <span class="px-3 py-1 text-xs bg-green-600 text-white rounded-full">Completed</span> @break
                        @endswitch
                    </td>

                    {{-- WARNINGS --}}
                    <td class="p-3 border">

                        @if($project->expense_warning['type'] === 'danger')
                            <span class="text-red-600 flex items-center">
                                ⚠️ {{ $project->expense_warning['text'] }}
                            </span>
                        @elseif($project->expense_warning['type'] === 'warning')
                            <span class="text-amber-600 flex items-center">
                                ⚠️ {{ $project->expense_warning['text'] }}
                            </span>
                        @else
                            <span class="text-green-600 flex items-center">
                                ✔ No Issues
                            </span>
                        @endif
                    </td>


                    {{-- ACTIONS --}}
                    <td class="p-3 border">
                        <a href="{{ route('projects.show', $project->id) }}"
                           class="text-blue-600 hover:underline">View</a>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
