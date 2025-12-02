@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <h2 class="text-2xl font-bold mb-4">QR Scanner</h2>

    {{-- Project selector --}}
    <div class="bg-white p-4 rounded shadow mb-6 flex items-center space-x-4">
        <label class="font-semibold">Active Project:</label>

        <select id="projectSelect" class="border rounded px-3 py-2 w-80">
            <option value="">-- Select project --</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}">
                    {{ $project->project_name }} — {{ $project->client->name ?? '' }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Scanner --}}
    <div class="bg-white p-4 rounded shadow">
        <div id="qr-reader" style="width: 400px; max-width: 100%;"></div>

        <div id="scanResult"
             class="mt-4 text-sm p-3 rounded bg-gray-100 text-gray-800">
            Waiting for scan...
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const resultBox = document.getElementById('scanResult');
    const projectSelect = document.getElementById('projectSelect');
    const scanUrl = "{{ route('attendance.scan') }}";

    function logResult(msg, isError = false) {
        resultBox.textContent = msg;
        resultBox.className =
            "mt-4 text-sm p-3 rounded " +
            (isError ? "bg-red-100 text-red-700" : "bg-green-100 text-green-700");
    }

    function onScanSuccess(decodedText) {

        if (!projectSelect.value) {
            logResult("Please select a project before scanning.", true);
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
                logResult("✔ " + data.message + " — " + data.employee);
            } else {
                logResult("⚠ " + data.message, true);
            }
        })
        .catch(() => {
            logResult("Server error while processing scan.", true);
        });
    }


    // Start QR Scanner
    const html5QrCode = new Html5Qrcode("qr-reader");

    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            const cameraId = cameras[0].id;
            html5QrCode.start(
                cameraId,
                { fps: 10, qrbox: 250 },
                onScanSuccess,
                () => {}
            );
        } else {
            logMessage("No camera found on this device.", true);
        }
    }).catch(err => {
        logMessage("Camera access error: " + err, true);
    });
</script>

@endsection
