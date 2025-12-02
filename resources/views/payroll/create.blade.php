@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Generate Payroll</h2>

    <div class="bg-white shadow rounded-lg p-6 max-w-3xl">

        <form action="{{ route('payroll.preview') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block font-medium mb-1">Start Date</label>
                    <input type="date" name="start_date"
                           class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block font-medium mb-1">End Date</label>
                    <input type="date" name="end_date"
                           class="w-full border rounded p-2">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-medium mb-1">Employee</label>
                    <select name="employee_id" class="w-full border rounded p-2">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->given_name }} {{ $emp->last_name }} 
                                ({{ ucfirst($emp->employment_type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    Generate Preview
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
