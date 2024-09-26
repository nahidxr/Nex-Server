@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCORM Data Logger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<h1>SCORM Data Logger</h1>


{{--@dd($scorm->scos[0]['uuid'])--}}

<iframe src="{{ asset('storage/' . $scorm->uuid . '/' . $scorm->entry_url) }}" width="100%" height="600px" frameborder="0" allowfullscreen>
    Your browser does not support iframes.
</iframe>

<script type="text/javascript">
    console.log('SCORM API script loaded');

    let scormData = {};
    let scormKeys;
    let savedScormData = {};

    let scormVersion = '<?= $scorm->version ?>';

    fetch('/admin/get/scorm-data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            sco_id: '<?= $scorm->id ?>',
            user_id: '<?= Auth::guard('admin')->user()->id ?>',
        })
    })
        .then(response => response.json())
        .then(data => {
            savedScormData = JSON.parse(data.data);
            console.log('Loaded previous SCORM data:', savedScormData);
        })
        .catch(error => console.error('Error loading previous SCORM data:', error));

    // SCORM keys mapping based on version
    if (scormVersion === 'scorm_12') {
        scormKeys = [
            'cmi.suspend_data',
            'cmi.core.lesson_mode',
            'cmi.core.lesson_status',
            'cmi.core.score.raw',
            'cmi.core.score.min',
            'cmi.core.score.max',
            'cmi.core.session_time',
            'cmi.core.entry',
            'cmi.core.exit',
            'cmi.core.lesson_location',
            'cmi.core.total_time'
        ];
    } else if (scormVersion === 'scorm_2004') {
        scormKeys = [

            'cmi.suspend_data',
            'cmi.completion_status',
            'cmi.success_status',
            'cmi.score.raw',
            'cmi.score.min',
            'cmi.score.max',
            'cmi.score.scaled',
            'cmi.session_time',
            'cmi.entry',
            'cmi.exit',
            'cmi.location',
            'cmi.total_time',
            'cmi.progress_measure',
            'cmi.interactions._count',
            'cmi.interactions.0.id',
            'cmi.interactions.0.type',
            'cmi.interactions.0.objectives._count',
            'cmi.interactions.0.objectives.0.id',
            'cmi.interactions.0.correct_responses._count',
            'cmi.interactions.0.correct_responses.0.pattern',
            'cmi.interactions.0.student_response',
            'cmi.interactions.0.result'
        ];
    }

    var API = {
        LMSInitialize: function(param) {
            console.log('LMSInitialize called with param:', param);
            // Add more debug logs here if needed
            if (Object.keys(savedScormData).length > 0) {
                scormData = savedScormData; // Use the saved data instead of sessionStorage
                console.log('SCORM data restored from previous session:', savedScormData);
            }
            return "true";
        },

        LMSFinish: function(param) {
            console.log('LMSFinish called with param:', param);
            this._saveData();  // Save data when finishing
            return "true";
        },

        LMSSetValue: function(key, value) {
            console.log('LMSSetValue called with key:', key, 'and value:', value);
            let data = this._prepareData(key, value);
            this._sendData(data);  // Save the data to the backend
            return "true";
        },

        LMSGetValue: function(key) {
            console.log('LMSGetValue called with key:', key);
            return scormData[key] || "";
        },


        LMSCommit: function(param) {
            console.log('LMSCommit called with param:', param);
            return "true";
        },
        LMSGetLastError: function() {
            return "0";  // Use appropriate error code from SCORM 2004 if available
        },

        LMSGetErrorString: function(errorCode) {
            switch (errorCode) {
                case "0":
                    return "No error";
                // Add more error codes if needed
                default:
                    return "Unknown error";
            }
        },

        LMSGetDiagnostic: function(errorCode) {
            // Provide more detailed diagnostics if applicable
            return "No additional diagnostic information";
        },


        _prepareData: function(key, value) {
            let data = {};
            if (scormKeys.includes(key)) {
                data[key] = value;
                scormData[key] = value;
                console.log('Prepared Data:', data);
            } else {
                console.warn('Key not found in SCORM keys:', key);
            }
            return data;
        },


        _sendData: function(data) {
            console.log('Sending data:', data);

            // Send the SCORM data to the server to persist it
            fetch('/admin/save/scorm-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    uuid: '<?= $scorm->scos[0]['uuid'] ?>',
                    user_id: '<?= Auth::guard('admin')->user()->id ?>',
                    data: data
                })
            })
                .then(response => response.json())
                .then(data => console.log('Data saved successfully:', data))
                .catch(error => console.error('Error saving data:', error));
        },

        _saveData: function() {
            // When LMSFinish is called, save the final SCORM data to the server
            this._sendData(scormData);
        }
    };

    window.LMSInitialize = API.LMSInitialize;
    window.LMSFinish = API.LMSFinish;
    window.LMSSetValue = API.LMSSetValue;
    window.LMSGetValue = API.LMSGetValue;
    window.LMSCommit = API.LMSCommit;
    window.LMSGetLastError = API.LMSGetLastError;
    window.LMSGetErrorString = API.LMSGetErrorString;
    window.LMSGetDiagnostic = API.LMSGetDiagnostic;

    console.log('Custom SCORM API script loaded and overridden.');
</script>



{{--<script>--}}
{{--    console.log('SCORM API script loaded');--}}

{{--    let scormData = {};--}}

{{--    // Define SCORM version--}}
{{--    let scormVersion = '<?= $scorm->version?>'; // or 'scorm_12'--}}

{{--    // Define SCORM keys for different versions--}}
{{--    const scormKeys = {--}}
{{--        'scorm_12': [--}}
{{--            'cmi.suspend_data',--}}
{{--            'cmi.core.lesson_mode',--}}
{{--            'cmi.core.lesson_status',--}}
{{--            'cmi.core.score.raw',--}}
{{--            'cmi.core.score.min',--}}
{{--            'cmi.core.score.max',--}}
{{--            'cmi.core.session_time',--}}
{{--            'cmi.core.entry',--}}
{{--            'cmi.core.exit',--}}
{{--            'cmi.core.lesson_location',--}}
{{--            'cmi.core.total_time'--}}
{{--        ],--}}
{{--        'scorm_2004': [--}}
{{--            'cmi.suspend_data',--}}
{{--            'cmi.completion_status',--}}
{{--            'cmi.success_status',--}}
{{--            'cmi.score.raw',--}}
{{--            'cmi.score.min',--}}
{{--            'cmi.score.max',--}}
{{--            'cmi.score.scaled',--}}
{{--            'cmi.session_time',--}}
{{--            'cmi.entry',--}}
{{--            'cmi.exit',--}}
{{--            'cmi.location',--}}
{{--            'cmi.total_time',--}}
{{--            'cmi.progress_measure'--}}
{{--        ]--}}
{{--    };--}}

{{--    // Function to handle SCORM API calls based on version--}}
{{--    function getScormAPI(version) {--}}

{{--        if (version === 'scorm_12') {--}}
{{--            return {--}}
{{--                LMSInitialize: function(param) {--}}
{{--                    console.log('LMSInitialize called with param:', param);--}}
{{--                    return "true";--}}
{{--                },--}}
{{--                LMSFinish: function(param) {--}}
{{--                    console.log('LMSFinish called with param:', param);--}}
{{--                    this._saveData();--}}
{{--                    return "true";--}}
{{--                },--}}
{{--                LMSSetValue: function(key, value) {--}}
{{--                    console.log('LMSSetValue called with key:', key, 'and value:', value);--}}
{{--                    let data = this._prepareData(key, value, scormKeys['scorm_12']);--}}
{{--                    this._sendData(data);--}}
{{--                    return "true";--}}
{{--                },--}}
{{--                LMSGetValue: function(key) {--}}
{{--                    console.log('LMSGetValue called with key:', key);--}}
{{--                    return ""; // Return a placeholder value--}}
{{--                },--}}
{{--                LMSCommit: function(param) {--}}
{{--                    console.log('LMSCommit called with param:', param);--}}
{{--                    return "true";--}}
{{--                },--}}
{{--                LMSGetLastError: function() {--}}
{{--                    return "0"; // No error--}}
{{--                },--}}
{{--                LMSGetErrorString: function(errorCode) {--}}
{{--                    return "No error"; // Return a generic error message--}}
{{--                },--}}
{{--                LMSGetDiagnostic: function(errorCode) {--}}
{{--                    return "No error diagnostic"; // Return diagnostic information--}}
{{--                },--}}
{{--                _prepareData: function(key, value, keys) {--}}
{{--                    let data = {};--}}
{{--                    if (keys.includes(key)) {--}}
{{--                        data[key] = value;--}}
{{--                    }--}}
{{--                    let existingData = JSON.parse(sessionStorage.getItem('scormData') || '{}');--}}
{{--                    existingData[key] = value;--}}
{{--                    sessionStorage.setItem('scormData', JSON.stringify(existingData));--}}
{{--                    return existingData;--}}
{{--                },--}}
{{--                _sendData: function(data) {--}}
{{--                    fetch('/save/scorm-data', {--}}
{{--                        method: 'POST',--}}
{{--                        headers: {--}}
{{--                            'Content-Type': 'application/json',--}}
{{--                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')--}}
{{--                        },--}}
{{--                        body: JSON.stringify({ uuid: '<?= $scorm->scos[0]['uuid']?>', user_id: '<?=auth()->user()->id?>', data: data })--}}
{{--                    })--}}
{{--                        .then(response => response.json())--}}
{{--                        .then(data => console.log('Data saved successfully:', data))--}}
{{--                        .catch(error => console.error('Error saving data:', error));--}}
{{--                },--}}
{{--                _saveData: function() {--}}
{{--                    let data = JSON.parse(sessionStorage.getItem('scormData') || '{}');--}}
{{--                    this._sendData(data);--}}
{{--                }--}}
{{--            };--}}
{{--        } else if (version === 'scorm_2004') {--}}

{{--            console.log(version)--}}
{{--            return {--}}
{{--                Initialize: function(param) {--}}
{{--                    console.log('Initialize called with param:', param);--}}
{{--                    return "true";--}}
{{--                },--}}
{{--                Terminate: function(param) {--}}
{{--                    console.log('Terminate called with param:', param);--}}
{{--                    this._saveData();--}}
{{--                    return "true";--}}
{{--                },--}}
{{--                SetValue: function(key, value) {--}}
{{--                    console.log('SetValue called with key:', key, 'and value:', value);--}}
{{--                    let data = this._prepareData(key, value, scormKeys['scorm_2004']);--}}
{{--                    this._sendData(data);--}}
{{--                    return "true";--}}
{{--                },--}}
{{--                GetValue: function(key) {--}}
{{--                    console.log('GetValue called with key:', key);--}}
{{--                    return ""; // Return a placeholder value--}}
{{--                },--}}
{{--                Commit: function(param) {--}}
{{--                    console.log('Commit called with param:', param);--}}
{{--                    return "true";--}}
{{--                },--}}
{{--                GetLastError: function() {--}}
{{--                    return "0"; // No error--}}
{{--                },--}}
{{--                GetErrorString: function(errorCode) {--}}
{{--                    return "No error"; // Return a generic error message--}}
{{--                },--}}
{{--                GetDiagnostic: function(errorCode) {--}}
{{--                    return "No error diagnostic"; // Return diagnostic information--}}
{{--                },--}}
{{--                _prepareData: function(key, value, keys) {--}}
{{--                    let data = {};--}}
{{--                    if (keys.includes(key)) {--}}
{{--                        data[key] = value;--}}
{{--                    }--}}
{{--                    let existingData = JSON.parse(sessionStorage.getItem('scormData') || '{}');--}}
{{--                    existingData[key] = value;--}}
{{--                    sessionStorage.setItem('scormData', JSON.stringify(existingData));--}}
{{--                    return existingData;--}}
{{--                },--}}
{{--                _sendData: function(data) {--}}
{{--                    fetch('/save/scorm-data', {--}}
{{--                        method: 'POST',--}}
{{--                        headers: {--}}
{{--                            'Content-Type': 'application/json',--}}
{{--                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')--}}
{{--                        },--}}
{{--                        body: JSON.stringify({ uuid: '<?= $scorm->scos[0]['uuid']?>', user_id: '<?=auth()->user()->id?>', data: data })--}}
{{--                    })--}}
{{--                        .then(response => response.json())--}}
{{--                        .then(data => console.log('Data saved successfully:', data))--}}
{{--                        .catch(error => console.error('Error saving data:', error));--}}
{{--                },--}}
{{--                _saveData: function() {--}}
{{--                    let data = JSON.parse(sessionStorage.getItem('scormData') || '{}');--}}
{{--                    this._sendData(data);--}}
{{--                }--}}
{{--            };--}}
{{--        } else {--}}
{{--            console.error('Unsupported SCORM version:', version);--}}
{{--            return null;--}}
{{--        }--}}
{{--    }--}}

{{--    // Initialize API based on SCORM version--}}
{{--    const API = getScormAPI(scormVersion);--}}

{{--    if (API) {--}}
{{--        if (scormVersion === 'scorm_12') {--}}
{{--            window.LMSInitialize = API.LMSInitialize;--}}
{{--            window.LMSFinish = API.LMSFinish;--}}
{{--            window.LMSSetValue = API.LMSSetValue;--}}
{{--            window.LMSGetValue = API.LMSGetValue;--}}
{{--            window.LMSCommit = API.LMSCommit;--}}
{{--            window.LMSGetLastError = API.LMSGetLastError;--}}
{{--            window.LMSGetErrorString = API.LMSGetErrorString;--}}
{{--            window.LMSGetDiagnostic = API.LMSGetDiagnostic;--}}
{{--        } else if (scormVersion === 'scorm_2004') {--}}
{{--            window.Initialize = API.Initialize;--}}
{{--            window.Terminate = API.Terminate;--}}
{{--            window.SetValue = API.SetValue;--}}
{{--            window.GetValue = API.GetValue;--}}
{{--            window.Commit = API.Commit;--}}
{{--            window.GetLastError = API.GetLastError;--}}
{{--            window.GetErrorString = API.GetErrorString;--}}
{{--            window.GetDiagnostic = API.GetDiagnostic;--}}
{{--        }--}}

{{--        console.log(`Custom SCORM ${scormVersion} API script loaded and overridden.`);--}}
{{--    }--}}
{{--</script>--}}





</body>
</html>
