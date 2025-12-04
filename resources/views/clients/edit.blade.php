@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Edit Client</h2>
@endsection


@section('content')

<style>
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.78);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.45);
        padding: 32px;
        box-shadow: 0 18px 60px rgba(0,0,0,0.12);
    }

    .glass-input {
        background: rgba(255,255,255,0.65);
        border: 1px solid rgba(0,0,0,0.08);
        padding: 12px 16px;
        border-radius: 14px;
        width: 100%;
        transition: .2s ease;
        backdrop-filter: blur(10px);
    }
    .glass-input:focus {
        outline: none;
        border-color: #7b5cf6;
        box-shadow: 0 0 0 2px #7b5cf63a;
    }

    .primary-btn {
        padding: 12px 22px;
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        border-radius: 14px;
        font-weight: 600;
        transition: .2s ease;
    }
    .primary-btn:hover {
        opacity: .85;
        transform: translateY(-2px);
    }

    .cancel-btn {
        padding: 12px 22px;
        background: rgba(200,200,200,0.5);
        border-radius: 14px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        transition: .2s ease;
    }
    .cancel-btn:hover {
        background: rgba(200,200,200,0.8);
    }

    .modal-bg {
        background: rgba(0,0,0,0.35);
        backdrop-filter: blur(4px);
    }
</style>


<div class="max-w-4xl mx-auto pb-20">

    <form action="{{ route('clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="glass-card">

            <h3 class="text-xl font-semibold mb-6">Client Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="font-medium text-gray-700">Client Name</label>
                    <input type="text" name="name"
                           value="{{ $client->name }}"
                           class="glass-input mt-1" required>
                </div>

                <div>
                    <label class="font-medium text-gray-700">Contact Person</label>
                    <input type="text" name="contact_person"
                           value="{{ $client->contact_person }}"
                           class="glass-input mt-1" required>
                </div>

                <div>
                    <label class="font-medium text-gray-700">Email</label>
                    <input type="email" name="email"
                           value="{{ $client->email }}"
                           class="glass-input mt-1">
                </div>

                <div>
                    <label class="font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone"
                           value="{{ $client->phone }}"
                           class="glass-input mt-1">
                </div>

                <div class="md:col-span-2">
                    <label class="font-medium text-gray-700">Address</label>
                    <textarea name="address" rows="3"
                              class="glass-input mt-1">{{ $client->address }}</textarea>
                </div>

            </div>

        </div>


        <div class="flex justify-end gap-4 mt-6">

            <button type="button" id="cancelBtn" class="cancel-btn">
                Cancel
            </button>

            <button type="submit" class="primary-btn">
                Update Client
            </button>
        </div>

    </form>
</div>



{{-- ======================== --}}
{{-- CONFIRM CANCEL MODAL --}}
{{-- ======================== --}}
<div id="cancelModal"
     class="fixed inset-0 hidden items-center justify-center modal-bg z-[999]">

    <div class="bg-white rounded-2xl shadow-xl p-8 w-[90%] max-w-md">

        <h3 class="text-xl font-semibold mb-2">Discard changes?</h3>
        <p class="text-gray-600 mb-6">
            Your unsaved changes will be lost. Are you sure you want to cancel?
        </p>

        <div class="flex justify-end gap-4">
            <button id="closeModal"
                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                No, stay
            </button>

            <a href="{{ route('clients.index') }}"
               class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600">
                Yes, cancel
            </a>
        </div>

    </div>

</div>



{{-- ======================== --}}
{{-- JS --}}
{{-- ======================== --}}
<script>
    const modal = document.getElementById('cancelModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');

    cancelBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    closeModal.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
</script>

@endsection
