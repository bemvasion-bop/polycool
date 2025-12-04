@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Clients</h2>
@endsection

@section('content')

<style>
    .glass-card {
         border-radius: 26px;
    background: rgba(255,255,255,0.45);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.55);
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    padding: 28px 32px;
    }
</style>


</style>

{{-- ============================================================
   🌈 ACTION BAR (New + Search + Sort + Filter)
============================================================ --}}
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">

    <a href="{{ route('clients.create') }}"
       class="glass-btn bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">
        + New Client
    </a>

    <div class="flex items-center gap-3">

        {{-- Search --}}
        <input type="text" id="searchInput"
               class="rounded-full bg-white/60 px-4 py-2 text-sm border border-white/60 backdrop-blur-sm"
               placeholder="Search…">

        {{-- Sort --}}
        <select id="sortSelect" class="glass-btn text-sm">
            <option value="default">Sort: Default</option>
            <option value="client">Client</option>
            <option value="price">Contract Price</option>
            <option value="date">Date</option>
            <option value="status">Status</option>
        </select>

        {{-- Filter Drawer Button --}}
        <button id="filterBtn" class="glass-btn flex items-center gap-2">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
        </button>
    </div>
</div>



{{-- ======================= --}}
{{-- TABLE (CLEAN + PREMIUM) --}}
{{-- ======================= --}}
<div class="glass-card p-0 overflow-hidden">

    <table class="w-full text-left">
        <thead>
            <tr class="bg-white/60 backdrop-blur-md text-gray-600 border-b border-gray-200">
                <th class="p-4 font-semibold">Name</th>
                <th class="p-4 font-semibold">Contact Person</th>
                <th class="p-4 font-semibold">Email</th>
                <th class="p-4 font-semibold">Phone</th>
                <th class="p-4 font-semibold text-right">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">

            @foreach($clients as $client)
            <tr class="hover:bg-white/50 transition-all">

                <td class="p-4 text-gray-900">
                    {{ $client->name }}
                </td>

                <td class="p-4 text-gray-700">
                    {{ $client->contact_person }}
                </td>

                <td class="p-4 text-gray-700">
                    {{ $client->email }}
                </td>

                <td class="p-4 text-gray-700">
                    {{ $client->phone }}
                </td>

                <td class="p-4 text-right space-x-4">
                    <a href="{{ route('clients.show', $client->id) }}"
                       class="text-blue-600 hover:text-blue-700 font-medium">
                        View
                    </a>

                    <a href="{{ route('clients.edit', $client->id) }}"
                       class="text-green-600 hover:text-green-700 font-medium">
                        Edit
                    </a>
                </td>

            </tr>
            @endforeach

        </tbody>
    </table>

</div>

</div>

@endsection
