<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New website enquiry</title>
</head>
<body>
    <h2>New website enquiry</h2>
    <p><strong>Name:</strong> {{ $leadSubmission->name }}</p>
    <p><strong>Company:</strong> {{ $leadSubmission->company_name }}</p>
    <p><strong>City:</strong> {{ $leadSubmission->city }}</p>
    <p><strong>Phone:</strong> {{ $leadSubmission->phone }}</p>
    <p><strong>Email:</strong> {{ $leadSubmission->email }}</p>
    @if ($leadSubmission->message)
        <p><strong>Message:</strong><br>{{ $leadSubmission->message }}</p>
    @endif
    <p><strong>Submitted from:</strong> {{ $leadSubmission->source_url }}</p>
    <p><strong>Submitted at:</strong> {{ $leadSubmission->created_at }}</p>
</body>
</html>
