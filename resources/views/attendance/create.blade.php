@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <br>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Add Attendance
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">
                        <a href="{{ route('user.attendance.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row" style="padding: 20px !important;">
                <div class="form-group col-sm-2">
                    <label>Latitude<span class="required_field" style="color: #e3001b;">*</span></label>
                    <input type="text" class="form-control" name="latitude" id="current-latitude" readonly>
                </div>
                <div class="form-group col-sm-2">
                    <label>Longitude<span class="required_field" style="color: #e3001b;">*</span></label>
                    <input type="text" class="form-control" name="longitude" id="current-longitude" readonly>
                </div>
                <div class="col-lg-4">
                    <video id="video" width="350" height="250" autoplay></video>
                    <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                    <br>
                    <div style="padding: 10px !important;">
                        <button type="button" id="capture" class="btn btn-outline-secondary">📸 Capture</button>
                        <button type="button" id="submitAttendance" class="btn btn-brand">Submit Attendance</button>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div id="preview"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureButton = document.getElementById('capture');
    const submitButton = document.getElementById('submitAttendance');
    const preview = document.getElementById('preview');
    let capturedBlob = null;

    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
    })
    .catch(err => {
        alert('Camera access denied: ' + err);
    });

    captureButton.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, 320, 240);

        canvas.toBlob(blob => {
            capturedBlob = blob;
            const imageUrl = URL.createObjectURL(blob);
            preview.innerHTML = `<img src="${imageUrl}" width="320" height="240"/>`;
        }, 'image/png');
    });

    submitButton.addEventListener('click', () => {
        if (!capturedBlob) {
            alert('Please capture a photo first.');
            return;
        }

        const latitude = document.getElementById('current-latitude').value;
        const longitude = document.getElementById('current-longitude').value;

        if (!latitude || !longitude) {
            alert('Unable to get your location. Please allow location access.');
            return;
        }

        const formData = new FormData();
        formData.append('photo', capturedBlob, 'attendance.png');
        formData.append('latitude', latitude);
        formData.append('longitude', longitude);

        fetch("{{ route('user.attendance.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location = "{{ route('user.attendance.index') }}";
            } else {
                alert(data.message);
                window.location = "{{ route('user.attendance.index') }}";
            }
        })
        .catch(err => {
            alert('Error submitting attendance: ' + err);
        });

    });

    setCurrentLocation();
    function setCurrentLocation() {
        const currentLatitude = document.getElementById("current-latitude");
        const currentLongitude = document.getElementById("current-longitude");

        function getLocation() 
        {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            }
        }

        function showPosition(position) {
            currentLatitude.value = position.coords.latitude;
            currentLongitude.value = position.coords.longitude;
        }
        getLocation();

    }
</script>
@endpush