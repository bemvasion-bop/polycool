@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
        Add Client
    </h2>
@endsection

@section('content')

<style>
    /* Glass card */
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.78);
        backdrop-filter: blur(22px);
        border: 1px solid rgba(255,255,255,0.45);
        padding: 40px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.10);
    }

    /* Glass input */
    .glass-input {
        background: rgba(255,255,255,0.65);
        border: 1px solid rgba(0,0,0,0.08);
        padding: 12px 16px;
        border-radius: 14px;
        width: 100%;
        transition: .2s ease;
    }
    .glass-input:focus {
        outline:none;
        border-color:#6366f1;
        box-shadow:0 0 0 2px #6366f130;
    }

    /* Buttons */
    .primary-btn {
        padding: 12px 22px;
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        border-radius: 14px;
        font-weight: 600;
        transition: .2s ease;
    }
    .primary-btn:hover { opacity:.9; transform:translateY(-2px); }

    .cancel-btn {
        padding: 12px 22px;
        background: rgba(255,255,255,0.55);
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,0.08);
        backdrop-filter: blur(12px);
        transition: .2s ease;
    }
    .cancel-btn:hover { background: rgba(255,255,255,0.8); }

    /* ===================== */
    /* PANEL-ONLY CANCEL MODAL */
    /* ===================== */
    .panel-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.35);
        backdrop-filter: blur(4px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 50;
        border-radius: 26px;
    }

    .cancel-modal {
        background: white;
        border-radius: 20px;
        padding: 28px;
        width: 380px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.20);
    }
</style>


<div class="relative max-w-4xl mx-auto pb-20">

    {{-- PANEL-ONLY OVERLAY + MODAL --}}
    <div id="cancelOverlay" class="panel-overlay">
        <div class="cancel-modal">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Discard changes?</h3>
            <p class="text-gray-600 text-sm mb-6">
                Your unsaved changes will be lost. Are you sure you want to cancel?
            </p>

            <div class="flex justify-end gap-3">
                <button onclick="closeCancelModal()"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
                    No, stay
                </button>

                <a href="{{ route('clients.index') }}"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                    Yes, cancel
                </a>
            </div>
        </div>
    </div>

    {{-- ======================= --}}
    {{-- FORM CARD --}}
    {{-- ======================= --}}
    <form id="createClientForm" action="{{ route('clients.store') }}" method="POST">
        @csrf

        <div class="glass-card mb-8">

            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Client Name</label>
                    <input type="text" name="name" class="glass-input" required>
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Contact Person</label>
                    <input type="text" name="contact_person" class="glass-input" required>
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="glass-input">
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" class="glass-input">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 font-medium text-gray-700">Address</label>
                    <textarea name="address" rows="3" class="glass-input"></textarea>
                </div>

            </div>

        </div> {{-- end glass-card --}}
    </form>

    {{-- ======================= --}}
    {{-- BUTTONS OUTSIDE CARD --}}
    {{-- ======================= --}}
    <div class="flex justify-end gap-3">

        {{-- CANCEL (opens panel modal only) --}}
        <button type="button"
            class="cancel-btn"
            onclick="openCancelModal()">
            Cancel
        </button>

        {{-- SAVE --}}
        <button form="createClientForm" class="primary-btn">
            Save Client
        </button>

    </div>

</div>

<script>
    function openCancelModal() {
        document.getElementById('cancelOverlay').style.display = 'flex';
    }

    function closeCancelModal() {
        document.getElementById('cancelOverlay').style.display = 'none';
    }
</script>

@endsection
