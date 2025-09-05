{{-- resources/views/frontend/users/show.blade.php --}}
@extends('frontend.layouts.app')

@section('title','Dettagli Utente')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Intestazione -->
  <div class="page-header d-flex align-items-center mb-4"
       style="background-color: #041930; border-radius: 0.75rem; padding: 1rem 2rem;">
    <i class="bi bi-person-lines-fill me-2 fs-3" style="color: #e2ae76;"></i>
    <h4 class="mb-0 fw-bold" style="color: #e2ae76;">
      Utente: {{ $user->name }}
    </h4>
  </div>

  <div class="card shadow-sm mb-4 border-0">
    <div class="card-body px-4 py-3">
      <div class="mb-3">
        <strong class="text-muted">Email:</strong>
        <div class="fs-5">{{ $user->email }}</div>
      </div>

      <div>
        <strong class="text-muted">Ruoli:</strong>
        <div>
          @forelse($user->roles as $r)
            <span class="badge rounded-pill bg-secondary px-3 py-2 me-1">
              {{ ucfirst($r->name) }}
            </span>
          @empty
            <span class="text-muted">Nessun ruolo assegnato</span>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-end gap-2">
    <a href="{{ route('users.edit', $user) }}" class="btn btn-gold">
      <i class="bi bi-pencil me-1"></i> Modifica
    </a>
    <a href="{{ route('users.index') }}" class="btn btn-deepblue">
      <i class="bi bi-arrow-left me-1"></i> Torna alla Lista
    </a>
  </div>
</div>
@endsection

<style>
  .btn-gold {
    border: 1px solid #e2ae76 !important;
    color: #e2ae76 !important;
    background-color: transparent !important;
  }
  .btn-gold:hover {
    background-color: #e2ae76 !important;
    color: #041930 !important;
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
</style>
