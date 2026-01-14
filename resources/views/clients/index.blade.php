@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Clients</h2>
@endsection

@section('content')

<style>
    .glass-panel {
    border-radius: 26px;
    background: rgba(255,255,255,0.45);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.55);
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    padding: 28px 32px;
}

.table-row {
    transition: .2s ease;
}
.table-row:hover {
    background: rgba(255,255,255,0.65);
}

</style>


</style>

            {{-- ============================================================
            🌈 ACTION BAR (New + Search + Sort + Filter)
            ============================================================ --}}
            <div class="flex flex-wrap items-center justify-between mb-6 gap-3">

                {{-- New Client --}}
                <a href="{{ route('clients.create') }}"
                class="glass-btn bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">
                    + New Client
                </a>

                {{-- Search --}}
                <input type="text"
                    id="searchInput"
                    placeholder="Search clients…"
                    class="rounded-full bg-white/60 px-4 py-2 text-sm border border-white/60 backdrop-blur-sm">
            </div>





            {{-- ======================= --}}
            {{-- TABLE (CLEAN + PREMIUM) --}}
            {{-- ======================= --}}
            <div class="glass-panel p-0 overflow-hidden">

                <table class="w-full border-collapse">
                    <thead>
                        <tr class="text-left border-b border-white/50">
                            <th class="p-4">Name</th>
                            <th class="p-4">Contact Person</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Phone</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($clients as $client)
                        <tr class="table-row border-b border-white/30">
                            <td class="p-4">{{ $client->name }}</td>
                            <td class="p-4">{{ $client->contact_person ?? '—' }}</td>
                            <td class="p-4">{{ $client->email ?? '—' }}</td>
                            <td class="p-4">{{ $client->phone ?? '—' }}</td>
                            <td class="p-4 text-right">
                                <a href="{{ route('clients.show', $client->id) }}"
                                class="text-blue-600 hover:underline">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-400 py-6">
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
