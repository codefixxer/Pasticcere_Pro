@extends('frontend.layouts.app')

@section('title', 'Il Mio Profilo')

@section('content')
<div class="container py-5 px-md-4">
  <div class="row mb-5 justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg rounded-3 border-0 overflow-hidden">
        <div class="card-body text-center pt-5">
          <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
          <p class="text-muted mb-3">{{ $user->email }}</p>
          <div class="mb-3">
            @forelse($user->roles as $role)
              <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
            @empty
              <span class="text-secondary">Nessun ruolo assegnato</span>
            @endforelse
          </div>

          <div class="mb-3">
            <p><strong>VAT:</strong> {{ $user->vat ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>
            @if($user->photo)
<img src="{{ asset('storage/photos/' . $user->photo) }}" alt="Profile Photo" class="img-fluid rounded-circle" width="100">
            @else
                <p>No profile photo uploaded</p>
            @endif
          </div>

          <a href="{{ route('users.edit', $user) }}" class="btn btn-gold btn-sm me-2" title="Modifica Profilo">
            <i class="bi bi-pencil me-1"></i>Modifica Profilo
          </a>

          <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="btn btn-red btn-sm" title="Esci">
            <i class="bi bi-box-arrow-right me-1"></i>Esci
          </a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .btn-gold {
    border: 1px solid #e2ae76 !important;
    color: #e2ae76 !important;
    background-color: transparent !important;
  }
  .btn-gold:hover {
    background-color: #e2ae76 !important;
    color: white !important;
  }

  .btn-gold-blue {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: 1px solid #e2ae76;
  }
  .btn-gold-blue:hover {
    background-color: #d89d5c !important;
    color: white !important;
  }

  .btn-deepblue {
    border: 1px solid #041930 !important;
    color: #041930 !important;
    background-color: transparent !important;
  }
  .btn-deepblue:hover {
    background-color: #041930 !important;
    color: white !important;
  }

  .btn-red {
    border: 1px solid red !important;
    color: red !important;
    background-color: transparent !important;
  }
  .btn-red:hover {
    background-color: red !important;
    color: white !important;
  }

  /* Blinking animation for expired badges */
  @keyframes blink {
    0%, 49%   { opacity: 1; }
    50%, 100% { opacity: 0; }
  }
  .blink {
    animation: blink 1s steps(1, end) infinite;
  }
</style>
@endsection
