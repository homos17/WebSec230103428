<!DOCTYPE html>
<html lang="en">
<body>
    <p>Dear {{ $name }},</p>
    <p>Click on the following link to reset your password:</p>
    <p><a href="{{ $link }}" target="_blank">Reset Password Link</a></p>
    <p>{{ $link }}</p>
    <p>This link will expire in 60 minutes.</p>
    <p>Regards,<br>Your Application</p>
</body>
</html>
