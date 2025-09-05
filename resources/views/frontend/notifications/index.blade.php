@extends('frontend.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-5">
    <h4 class="mb-4">Notifications</h4>

    @foreach($notifications as $notification)
    <div class="alert alert-info">
        <strong>{{ $notification->title }}</strong>
        <p>{{ $notification->message }}</p>
        <p class="text-muted">{{ $notification->created_at->diffForHumans() }}</p>

        <!-- Mark as Read Button -->
        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
            @csrf
            @method('POST')  <!-- This tells Laravel it's a POST request -->
            <button type="submit" class="btn btn-sm btn-primary">Mark as Read</button>
        </form>
    </div>
@endforeach


  
</div>
@endsection
