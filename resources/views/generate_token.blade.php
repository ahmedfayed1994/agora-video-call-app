<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Agora Token</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2>Generate Agora Token</h2>
            <form id="generateTokenForm">
                <div class="form-group">
                    <label for="channelName">Channel Name</label>
                    <input type="text" class="form-control" id="channelName" name="channelName" required>
                </div>
                <div class="form-group">
                    <label for="userId">User ID</label>
                    <input type="text" class="form-control" id="userId" name="userId" required>
                </div>
                <button type="submit" class="btn btn-primary">Generate Token</button>
            </form>
            <div id="tokenResult" class="mt-3"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#generateTokenForm').submit(function(e) {
            e.preventDefault();

            var formData = {
                channelName: $('#channelName').val(),
                user_id: $('#userId').val()
            };

            $.ajax({
                type: 'POST',
                url: '{{ route('generate.token') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $('#tokenResult').html('<div class="alert alert-success">Token: ' + response.token + '</div>');
                },
                error: function(xhr, status, error) {
                    var err = JSON.parse(xhr.responseText);
                    $('#tokenResult').html('<div class="alert alert-danger">Error: ' + err.error + '</div>');
                }
            });
        });
    });
</script>
</body>
</html>
