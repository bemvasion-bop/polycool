@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Edit Employee</h2>
@endsection

@section('content')

<style>
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.78);
        backdrop-filter: blur(22px);
        border: 1px solid rgba(255,255,255,0.45);
        padding: 38px;
        box-shadow: 0 18px 60px rgba(0,0,0,0.12);
    }

    .form-label {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
        display: inline-block;
        color: #111;
    }

    .input-field {
        width: 100%;
        padding: 11px 14px;
        background: rgba(255,255,255,0.85);
        border: 1px solid rgba(210,210,210,0.8);
        border-radius: 14px;
        font-size: 14px;
    }

    /* Section Headings */
    .section-title {
        font-size: 17px;
        font-weight: 700;
        color: #111;
        margin-bottom: 6px;
    }
    .section-divider {
        height: 2px;
        background: rgba(200,200,200,0.4);
        border-radius: 6px;
        margin-bottom: 18px;
    }

    .primary-btn {
        padding: 12px 26px;
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        border-radius: 14px;
        font-weight: 600;
        transition: .2s ease;
    }
    .primary-btn:hover { transform: translateY(-2px); opacity: .9; }

    .cancel-btn {
        padding: 12px 26px;
        background: #f3f4f6;
        color: #374151;
        border-radius: 14px;
        font-weight: 600;
    }
    .cancel-btn:hover { background: #e5e7eb; }
</style>




{{-- CANCEL MODAL --}}
<div id="cancelModal"
     class="fixed inset-0 bg-black/30 hidden items-center justify-center backdrop-blur-sm z-50">

    <div class="bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-2xl max-w-md text-center">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Cancel Editing?</h3>
        <p class="text-gray-600 mb-6 text-sm">All unsaved changes will be lost.</p>

        <div class="flex justify-center gap-3">
            <button onclick="closeCancelModal()"
                class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium">
                Continue Editing
            </button>
            <a href="{{ route('employees.index') }}"
                class="px-5 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white font-medium">
                Yes, Cancel
            </a>
        </div>
    </div>

</div>



{{-- MAIN FORM --}}
<div class="max-w-6xl mx-auto pb-20">

    <div class="glass-card">

        <form action="{{ route('employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')


            {{-- ======================= PERSONAL INFO ======================= --}}
            <h4 class="section-title">Personal Information</h4>
            <div class="section-divider"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Given Name *</label>
                        <input class="input-field" type="text" name="given_name" value="{{ $employee->given_name }}" required>
                    </div>

                    <div>
                        <label class="form-label">Middle Name</label>
                        <input class="input-field" type="text" name="middle_name" value="{{ $employee->middle_name }}">
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Last Name *</label>
                        <input class="input-field" type="text" name="last_name" value="{{ $employee->last_name }}" required>
                    </div>

                    <div>
                        <label class="form-label">Gender</label>
                        <select class="input-field" name="gender">
                            <option value="">Select</option>
                            <option value="male"   {{ $employee->gender=='male'?'selected':'' }}>Male</option>
                            <option value="female" {{ $employee->gender=='female'?'selected':'' }}>Female</option>
                            <option value="other"  {{ $employee->gender=='other'?'selected':'' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Date of Birth</label>
                        <input class="input-field" type="date" name="date_of_birth" value="{{ $employee->date_of_birth }}">
                    </div>
                </div>

            </div>


            {{-- ======================= CONTACT INFO ======================= --}}
            <h4 class="section-title">Contact Information</h4>
            <div class="section-divider"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Email *</label>
                        <input class="input-field" type="email" name="email" value="{{ $employee->email }}" required>
                    </div>

                    <div>
                        <label class="form-label">Phone Number</label>
                        <input class="input-field" type="text" name="phone_number" value="{{ $employee->phone_number }}">
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Street Address</label>
                        <input class="input-field" type="text" name="street_address" value="{{ $employee->street_address }}">
                    </div>

                    <div>
                        <label class="form-label">City</label>
                        <input class="input-field" type="text" name="city" value="{{ $employee->city }}">
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Province</label>
                        <input class="input-field" type="text" name="province" value="{{ $employee->province }}">
                    </div>

                    <div>
                        <label class="form-label">Postal Code</label>
                        <input class="input-field" type="text" name="postal_code" value="{{ $employee->postal_code }}">
                    </div>
                </div>

            </div>


            {{-- ======================= EMPLOYMENT DETAILS ======================= --}}
            <h4 class="section-title">Employment Details</h4>
            <div class="section-divider"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Date Hired</label>
                        <input class="input-field" type="date" name="date_hired" value="{{ $employee->date_hired }}">
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">System Role *</label>
                        <select class="input-field" name="system_role" required>
                            <option value="employee"  {{ $employee->system_role=='employee'?'selected':'' }}>Employee</option>
                            <option value="manager"   {{ $employee->system_role=='manager'?'selected':'' }}>Manager</option>
                            <option value="accounting"{{ $employee->system_role=='accounting'?'selected':'' }}>Accounting</option>
                            <option value="audit"     {{ $employee->system_role=='audit'?'selected':'' }}>Audit</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Employment Type *</label>
                        <select class="input-field" name="employment_type" required>
                            <option value="field_worker" {{ $employee->employment_type=='field_worker'?'selected':'' }}>Field Worker</option>
                            <option value="office_staff" {{ $employee->employment_type=='office_staff'?'selected':'' }}>Office Staff</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Status</label>
                        <select class="input-field" name="employee_status">
                            <option value="active"   {{ $employee->employee_status=='active'?'selected':'' }}>Active</option>
                            <option value="inactive" {{ $employee->employee_status=='inactive'?'selected':'' }}>Inactive</option>
                        </select>
                    </div>
                </div>

            </div>


            {{-- ACTION BUTTONS --}}
            <div class="flex justify-end gap-4 mt-10">
                <button type="button" onclick="openCancelModal()" class="cancel-btn">Cancel</button>
                <button type="submit" class="primary-btn">Update Employee</button>
            </div>

        </form>

    </div>
</div>



<script>
function openCancelModal(){ document.getElementById('cancelModal').classList.remove('hidden'); }
function closeCancelModal(){ document.getElementById('cancelModal').classList.add('hidden'); }
</script>

@endsection
