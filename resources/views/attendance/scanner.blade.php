@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    QR Scanner
</h2>
@endsection

@section('content')

<style>
    .glass-box {
        border-radius: 26px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(26px);
        border: 1px solid rgba(255,255,255,0.45);
        padding: 26px 32px;
        box-shadow: 0 18px 55px rgba(0,0,0,0.08);
    }

    select {
        background: rgba(255,255,255,0.75);
        border: 1px solid rgba(255,255,255,0.65);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 14px;
        width: 260px;
        outline: none;
    }

    #qr-reader {
        border-radius: 18px;
        overflow: hidden;
        background: #000;
        margin: 0 auto;
        max-width: 480px;
        padding: 10px;
    }

    #scanResult {
        margin-top: 14px;
        font-size: 14px;
        border-radius: 18px;
        padding: 12px 18px;
        backdrop-filter: blur(10px);
    }

    .success {
        background: rgba(187,247,208,0.85);
        color: #166534;
        border: 1px solid rgba(16,185,129,0.45);
    }
    .error {
        background: rgba(254,202,202,0.85);
        color: #991b1b;
        border: 1px solid rgba(239,68,68,0.45);
    }
    .neutral {
        background: rgba(255,255,255,0.75);
        color: #4b5563;
        border: 1px solid rgba(229,231,235,0.7);
    }
</style>


{{-- ============================================= --}}
{{-- QR Scanner Section --}}
{{-- ============================================= --}}
<div class="glass-box w-full max-w-4xl mx-auto space-y-6">

    {{-- Project Selection --}}
    <div class="flex items-center gap-4">
        <label class="font-semibold text-sm">Active Project:</label>

        <select id="projectSelect">
            <option value="">-- Select project --</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}">
                    {{ $project->project_name }} — {{ $project->client->name ?? '' }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Camera & Scanner --}}
    <div class="text-center w-full">
        <div id="qr-reader"></div>
        <div id="scanResult" class="neutral">Waiting for scan...</div>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const scanResult = document.getElementById('scanResult');
    const projectSelect = document.getElementById('projectSelect');
    const scanUrl = "{{ route('attendance.scan') }}";

    function showResult(msg, type = "neutral") {
        scanResult.textContent = msg;
        scanResult.className = type;
    }

    function onScanSuccess(decodedText) {

        if (!projectSelect.value) {
            showResult("⚠ Please select a project before scanning.", "error");
            return;
        }

        fetch(scanUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                qr_code: decodedText,
                project_id: projectSelect.value
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showResult("✔ " + data.message + " — " + data.employee, "success");
            } else {
                showResult("⚠ " + data.message, "error");
            }
        })
        .catch(() => {
            showResult("Server error while processing scan.", "error");
        });
    }


    // Init Scanner
    const html5QrCode = new Html5Qrcode("qr-reader");

    Html5Qrcode.getCameras().then(cameras => {
        if (cameras.length) {
            html5QrCode.start(
                cameras[0].id,
                { fps: 10, qrbox: { width: 300, height: 300 } },
                onScanSuccess
            );
        } else {
            showResult("No camera found on this device.", "error");
        }
    }).catch(err => {
        showResult("Camera access error: " + err, "error");
    });
</script>

@endsection
