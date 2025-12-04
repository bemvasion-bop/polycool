@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Add Employee</h2>
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


{{-- CANCEL CONFIRMATION MODAL --}}
<div id="cancelModal"
     class="fixed inset-0 bg-black/30 hidden items-center justify-center backdrop-blur-sm z-50">

    <div class="bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-2xl max-w-md text-center">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Cancel Creating?</h3>
        <p class="text-gray-600 mb-6 text-sm">All entered data will be lost.</p>

        <div class="flex justify-center gap-3">
            <button onclick="closeCancelModal()"
                class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium">
                Continue Filling
            </button>

            <button type="button"
                onclick="confirmCancel()"
                class="px-5 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white font-medium">
                Yes, Cancel
            </button>
        </div>
    </div>

</div>



<div class="max-w-6xl mx-auto pb-20">

    <div class="glass-card">

        <form action="{{ route('employees.store') }}" method="POST">
            @csrf

            {{-- ======================= PERSONAL INFO ======================= --}}
            <h4 class="section-title">Personal Information</h4>
            <div class="section-divider"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div class="space-y-5">
                    <div>
                        <label class="form-label">Given Name *</label>
                        <input class="input-field" name="given_name" type="text" required>
                    </div>
                    <div>
                        <label class="form-label">Middle Name</label>
                        <input class="input-field" name="middle_name" type="text">
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Last Name *</label>
                        <input class="input-field" name="last_name" type="text" required>
                    </div>
                    <div>
                        <label class="form-label">Gender</label>
                        <select class="input-field" name="gender">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Date of Birth</label>
                        <input class="input-field" name="date_of_birth" type="date">
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
                        <input class="input-field" name="email" type="email" required>
                    </div>
                    <div>
                        <label class="form-label">Phone Number</label>
                        <input class="input-field" name="phone_number" type="text">
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Street Address</label>
                        <input class="input-field" name="street_address" type="text">
                    </div>
                    <div>
                        <label class="form-label">City</label>
                        <input class="input-field" name="city" type="text">
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Province</label>
                        <input class="input-field" name="province" type="text">
                    </div>
                    <div>
                        <label class="form-label">Postal Code</label>
                        <input class="input-field" name="postal_code" type="text">
                    </div>
                </div>

            </div>


            {{-- ======================= EMPLOYMENT DETAILS ======================= --}}
            <h4 class="section-title">Employment Details</h4>
            <div class="section-divider"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Date Hired</label>
                        <input class="input-field" name="date_hired" type="date">
                    </div>
                    <div>
                        <label class="form-label">Password *</label>
                        <input class="input-field" name="password" type="password" required>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">System Role *</label>
                        <select class="input-field" name="system_role" required>
                            <option value="">Select</option>
                            <option value="employee">Employee</option>
                            <option value="manager">Manager</option>
                            <option value="accounting">Accounting</option>
                            <option value="audit">Audit</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Confirm Password *</label>
                        <input class="input-field" name="password_confirmation" type="password" required>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">Employment Type *</label>
                        <select class="input-field" name="employment_type" required>
                            <option value="">Select</option>
                            <option value="field_worker">Field Worker</option>
                            <option value="office_staff">Office Staff</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Employee Status</label>
                        <select class="input-field" name="employee_status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

            </div>


            {{-- ACTION BUTTONS --}}
            <div class="flex justify-end gap-4 mt-10">
                <button type="button" onclick="openCancelModal()" class="cancel-btn">
                    Cancel
                </button>
                <button type="submit" class="primary-btn">
                    Save Employee
                </button>
            </div>

        </form>

    </div>
</div>



{{-- This hidden form allows reliable redirect --}}
<form id="cancelRedirectForm"
      action="{{ route('employees.index') }}"
      method="GET"
      class="hidden"></form>



<script>
    function openCancelModal() {
        document.getElementById('cancelModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    function confirmCancel() {
        document.getElementById('cancelRedirectForm').submit();
    }

    // Close modal if clicking the overlay
    const modal = document.getElementById('cancelModal');
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeCancelModal();
        }
    });
</script>

@endsection
