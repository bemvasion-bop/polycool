@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">QR Code Scanner</h2>

    <div class="bg-white p-6 shadow rounded-lg">
        <div id="reader" style="width: 350px;"></div>

        <p id="resultBox" class="mt-4 text-lg font-semibold text-blue-600">
            Scan a QR code to Time In / Time Out
        </p>
    </div>

</div>

{{-- Html5Qrcode CDN --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
function onScanSuccess(decodedText, decodedResult) {

    // Display scanned QR value
    document.getElementById('resultBox').innerHTML =
        "Scanned: " + decodedText + "<br>Processing attendance...";

    // Redirect to Laravel route
    window.location.href = "/attendance/qr-process/" + decodedText;
}

let html5QrCode = new Html5Qrcode("reader");

Html5Qrcode.getCameras().then(cameras => {
    if (cameras && cameras.length) {
        html5QrCode.start(
            cameras[0].id,
            {
                fps: 10,
                qrbox: 250
            },
            onScanSuccess,
            errorMessage => {}
        );
    }
}).catch(err => {
    alert("Camera access failed: " + err);
});
</script>
@endsection
