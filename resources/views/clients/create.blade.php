@extends('layouts.app')

@section('content')
<div class="p-10">
    <h2 class="text-2xl font-semibold mb-6">Add Client</h2>

    <form action="{{ route('clients.store') }}" method="POST"
          class="bg-white rounded-lg shadow p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block mb-1 font-medium">Client Name</label>
                <input type="text" name="name"
                       class="w-full border rounded-lg p-3"
                       required>
            </div>

            <div>
                <label class="block mb-1 font-medium">Contact Person</label>
                <input type="text" name="contact_person"
                       class="w-full border rounded-lg p-3" required>
            </div>

            <div>
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email"
                       class="w-full border rounded-lg p-3">
            </div>

            <div>
                <label class="block mb-1 font-medium">Phone</label>
                <input type="text" name="phone"
                       class="w-full border rounded-lg p-3">
            </div>

            <div class="col-span-2">
                <label class="block mb-1 font-medium">Address</label>
                <textarea name="address" rows="3"
                          class="w-full border rounded-lg p-3"></textarea>
            </div>
        </div>

        <button class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow">
            Save Client
        </button>
    </form>
</div>
@endsection
