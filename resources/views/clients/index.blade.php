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
            <div class="flex items-center justify-between mb-6">

                {{-- Button New Client --}}
                <a href="{{ route('clients.create') }}"
                    class="px-4 py-2 bg-purple-600 text-white rounded-xl shadow hover:bg-purple-700">
                    + New Client
                </a>

                {{-- Search Bar --}}
                <form action="{{ route('clients.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Search clients..."
                        class="w-64 px-4 py-2 rounded-full bg-white border border-white/40
                            backdrop-blur-sm placeholder-gray-400 text-sm">
                    @if(!empty($search))
                    <a href="{{ route('clients.index') }}" class="text-sm text-gray-500 hover:underline">
                        Clear
                    </a>
                    @endif
                </form>

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

                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td>{{ $client->name }}</td>
                                <td>{{ $client->contact_person ?? '—' }}</td>
                                <td>{{ $client->email ?? '—' }}</td>
                                <td>{{ $client->phone ?? '—' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('clients.show', $client->id) }}" class="text-indigo-600 font-medium hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-400 py-4">
                                    No clients found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

</div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.querySelector('input[name="search"]');
            const rows = document.querySelectorAll("tbody tr");

            searchInput.addEventListener("input", function () {
                const value = this.value.toLowerCase().trim();

                rows.forEach(row => {
                    let text = row.innerText.toLowerCase();
                    row.style.display = text.includes(value) ? "" : "none";
                });
            });
        });
    </script>


@endsection
