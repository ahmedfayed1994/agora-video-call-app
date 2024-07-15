<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Call</title>
    <script src="https://cdn.agora.io/sdk/release/AgoraRTCSDK-3.4.0.js"></script>
</head>
<body>
<h1>Video Call</h1>
<div id="local-stream-container">
    <div id="local-stream" style="width: 400px; height: 300px; border: 1px solid black;"></div>
    <div id="local-name" style="text-align: center; font-weight: bold;"></div>
</div>
<div id="remote-stream-container">
    <div id="remote-stream" style="width: 400px; height: 300px; border: 1px solid black;"></div>
    <div id="remote-name" style="text-align: center; font-weight: bold;"></div>
</div>

<!-- قائمة اختيار المستخدمين -->
<select id="user-select">
    @foreach ($users as $user)
        <option value="{{ $user->id }}" data-name="{{ $user->name }}">{{ $user->name }}</option>
    @endforeach
</select>

<!-- زر بدء المكالمة -->
<button id="start-call">بدء المكالمة</button>

<!-- زر إنهاء المكالمة -->
<button id="end-call" style="display: none;">إنهاء المكالمة</button>

<script>
    let client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });

    client.init(`{{ env('AGORA_APP_ID') }}`, function () {
        console.log("AgoraRTC client initialized");

        let localStream;

        document.getElementById('start-call').onclick = function() {
            let userSelect = document.getElementById('user-select');
            let userId = userSelect.value;
            let userName = userSelect.options[userSelect.selectedIndex].getAttribute('data-name');
            let channelName = 'fb'; // يمكنك تعيين اسم القناة هنا أو بناءً على بعض الشروط

            // إرسال طلب لتوليد التوكن
            fetch('{{ route("generate.token") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    channelName: channelName,
                    user_id: userId
                })
            }).then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    let agoraToken = data.token;

                    // انضمام المستخدم إلى القناة باستخدام الـ agoraToken واسم القناة المحددة
                    client.join(agoraToken, channelName, null, (uid) => {
                        console.log("User " + uid + " join channel successfully");

                        localStream = AgoraRTC.createStream({
                            streamID: uid,
                            audio: true,
                            video: true,
                            screen: false
                        });

                        localStream.init(() => {
                            localStream.play('local-stream');
                            client.publish(localStream, (err) => {
                                console.log("Publish local stream error: " + err);
                            });

                            // عرض زر إنهاء المكالمة وإخفاء زر بدء المكالمة
                            document.getElementById('start-call').style.display = 'none';
                            document.getElementById('end-call').style.display = 'inline-block';

                            // عرض اسم المتصل عليه
                            document.getElementById('remote-name').textContent = 'المتحدث: ' + userName;
                        }, (err) => {
                            console.log("getUserMedia failed", err);
                        });
                    });
                })
                .catch(error => {
                    console.log('خرا');
                    console.error('Error:', error);
                });
        };

        document.getElementById('end-call').onclick = function() {
            if (localStream) {
                localStream.close();
                client.leave(() => {
                    console.log("Call ended.");

                    document.getElementById('start-call').style.display = 'inline-block';
                    document.getElementById('end-call').style.display = 'none';

                    document.getElementById('local-name').textContent = '';
                    document.getElementById('remote-name').textContent = '';
                }, (err) => {
                    console.error("Failed to leave the channel:", err);
                });
            }
        };
    }, function (err) {
        console.log("AgoraRTC client init failed", err);
    });

    client.on('stream-added', function (evt) {
        let stream = evt.stream;
        client.subscribe(stream, (err) => {
            console.log("Subscribe stream failed", err);
        });
    });

    client.on('stream-subscribed', function (evt) {
        let remoteStream = evt.stream;
        remoteStream.play('remote-stream');
    });
</script>
</body>
</html>
