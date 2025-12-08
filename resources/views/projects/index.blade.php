@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Projects</h2>
@endsection

@section('content')

<style>
/* ========= Reveal Animation ========= */
.reveal {
    opacity:0;
    transform:translateY(25px);
    transition:.45s cubic-bezier(.21,.61,.35,1);
}
.reveal.visible {
    opacity:1;
    transform:translateY(0);
}

/* ========= Project Card ========= */
.project-card {
    border-radius:24px;
    background: rgba(237, 237, 245, 0.55);
    backdrop-filter: blur(26px);
    border:1px solid rgba(255,255,255,0.55);
    padding:20px 24px;
    min-height:180px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    transition:.35s ease;
    will-change: transform, opacity;
    box-shadow:0 15px 40px rgba(0,0,0,0.06);
}
.project-card:hover {
    transform:translateY(-6px);
    box-shadow:0 0 25px rgba(140,120,255,.45);
}

/* Smooth morph */
#projectsWrapper {
    transition:.35s ease;
}

/* LIST MODE */
#projectsWrapper.list-mode {
    display:flex;
    flex-direction:column;
    gap:18px;
}

/* GRID MODE */
#projectsWrapper.grid-mode {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(380px, 1fr));
    gap:22px;
}

.toggle-btn.active {
    background: rgba(180,200,255,0.65);
    color: #1e3a8a;
    font-weight:700;
}

/* Status */
.status-badge {
    padding:4px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:6px;
    background:rgba(255,255,255,0.80);
    backdrop-filter:blur(8px);
}

/* View button */
.view-btn {
    font-size:13px;
    padding:7px 16px;
    border-radius:14px;
    border:1px solid rgba(255,255,255,0.55);
    background:rgba(255,255,255,0.5);
    backdrop-filter:blur(12px);
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:4px;
    transition:.25s;
}
.view-btn:hover {
    background:white;
    transform:translateX(6px);
}

/* Search */
.search-bar {
    padding:8px 14px;
    border-radius:14px;
    background:white;
    border:1px solid rgba(0,0,0,0.08);
    min-width:220px;
}

/* KPI bubbles */
.mini-kpi {
    padding:8px 14px;
    background:rgba(255,255,255,0.75);
    border-radius:14px;
    font-size:13px;
    font-weight:600;
    backdrop-filter:blur(10px);
    white-space:nowrap;
}
</style>


{{-- ====================== Controls + KPI in 1 Row ====================== --}}
@php
$pending = $projects->where('status','pending')->count();
$active = $projects->where('status','active')->count();
$completed = $projects->where('status','completed')->count();
@endphp

<div class="flex items-center justify-between mb-4 px-1 gap-3">

    {{-- 🔥 MINI KPI moved here --}}
    <div class="flex gap-2 text-xs font-semibold">
        <span class="mini-kpi text-yellow-700">Pending: {{ $pending }}</span>
        <span class="mini-kpi text-blue-700">Active: {{ $active }}</span>
        <span class="mini-kpi text-green-700">Completed: {{ $completed }}</span>
    </div>

    {{-- 🔍 Search + Sort + Toggle Avoid Movement --}}
    <div class="flex items-center gap-2">
        <input type="text" id="searchInput" class="search-bar text-sm" placeholder="Search by name/client…">

        <select id="sortSelect" class="glass-btn text-sm">
            <option value="default">Sort: Default</option>
            <option value="price">Price</option>
            <option value="client">Client</option>
            <option value="status">Status</option>
            <option value="date">Date Created</option>
        </select>

        <div class="flex gap-1">
            <button id="listViewBtn" class="toggle-btn active"><i data-lucide="list"></i></button>
            <button id="gridViewBtn" class="toggle-btn"><i data-lucide="grid"></i></button>
        </div>
    </div>

</div>


{{-- ====================== Grouped Projects ====================== --}}
@php
$groups = [
    'pending' => 'Pending Projects',
    'active' => 'Active Projects',
    'completed' => 'Completed Projects',
];
@endphp

<div id="projectsWrapper" class="grid-mode reveal">

@foreach($groups as $status => $label)
    @php $filtered = $projects->where('status',$status); @endphp
    @if($filtered->count() > 0)
        <h3 class="text-sm font-bold text-gray-900 col-span-full mt-6 mb-1">{{ $label }}</h3>

        @foreach($filtered as $project)
        <div class="project-card reveal project-item"
            data-status="{{ $project->status }}"
            data-progress="{{ $project->progress }}"
            data-client="{{ $project->client->name ?? 'Unknown' }}"
            data-price="{{ $project->final_project_price }}"
            data-date="{{ $project->created_at }}"
            data-text="{{ strtolower($project->project_name.' '.$project->client->name ?? '') }}"
        >

            <div class="flex justify-between">
                <div>
                    <p class="text-xl font-semibold text-gray-900">{{ $project->project_name }}</p>
                    <p class="text-gray-600 text-sm mt-1">
                        <strong>Client:</strong> {{ $project->client->name ?? 'Unknown' }}
                    </p>
                </div>

                <div class="status-badge">
                    @if($project->status === 'pending') ⏳ Pending
                    @elseif($project->status === 'active') 🔵 Active
                    @elseif($project->status === 'completed') ✔ Completed
                    @endif
                </div>
            </div>

            <div class="mt-2 text-sm">
                <p class="text-xs text-gray-500">Price</p>
                <p class="text-lg font-bold">₱{{ number_format($project->final_project_price,2) }}</p>
            </div>

            <div class="mt-2">
                <p class="text-xs text-gray-500">Progress</p>
                <x-progress-bar :value="$project->progress"/>
            </div>

            <div class="mt-3 flex justify-end">
                <a href="{{ route('projects.show', $project) }}" class="view-btn">
                View <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
            </div>

        </div>
        @endforeach
    @endif
@endforeach

</div>


<script>
document.addEventListener("DOMContentLoaded", () => {

    const wrapper = document.getElementById("projectsWrapper");
    const cards = document.querySelectorAll(".project-item");
    const listBtn = document.getElementById("listViewBtn");
    const gridBtn = document.getElementById("gridViewBtn");
    const searchInput = document.getElementById("searchInput");

    // Reveal animation
    const obs = new IntersectionObserver((items)=>{
        items.forEach(i=>{
            if(i.isIntersecting){
                i.target.classList.add("visible");
                obs.unobserve(i.target);
            }
        })
    },{threshold:.15});
    document.querySelectorAll(".reveal").forEach(el => obs.observe(el));


    // Search
    searchInput.addEventListener("input",()=> {
        let term = searchInput.value.toLowerCase();
        cards.forEach(c=>{
            c.style.display = c.dataset.text.includes(term) ? "flex" : "none";
        });
    });


    // Sorting
    document.getElementById("sortSelect").onchange = (e) => {
        let arr=[...cards];
        switch(e.target.value){
            case "price": arr.sort((a,b)=>a.dataset.price-b.dataset.price); break;
            case "client": arr.sort((a,b)=>a.dataset.client.localeCompare(b.dataset.client)); break;
            case "status": arr.sort((a,b)=>a.dataset.status.localeCompare(b.dataset.status)); break;
            case "date": arr.sort((a,b)=>new Date(a.dataset.date)-new Date(b.dataset.date)); break;
        }
        arr.forEach(c=>wrapper.appendChild(c));
    };


    // View mode saver
    function applyView(mode){
        wrapper.classList.toggle("list-mode", mode==="list");
        wrapper.classList.toggle("grid-mode", mode==="grid");
        listBtn.classList.toggle("active", mode==="list");
        gridBtn.classList.toggle("active", mode==="grid");
        localStorage.setItem("project_view",mode);
    }

    const saved = localStorage.getItem("project_view") || "grid";
    applyView(saved);

    listBtn.onclick = ()=>applyView("list");
    gridBtn.onclick = ()=>applyView("grid");
});
</script>

@endsection
