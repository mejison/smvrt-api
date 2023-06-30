@extends('emails.layout')

@section('content')
<div style="max-width: 400px; margin: 0 auto;">
    <p style="color: #737373;  text-align: center;">
        <strong>{{ $user }}</strong> invited you to the Team.

        <ul>
            <li><strong>Team: </strong>{{ $team }}</li>
            <li><strong>Project Name</strong>: {{ $project_name }}</li>
            <li><strong>Document Type</<strong>: {{ $document_type }}</li>
        </ul>
    </p>
</div>
@endsection
