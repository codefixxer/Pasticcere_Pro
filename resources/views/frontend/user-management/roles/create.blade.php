@extends('frontend.layouts.app')

@section('title', $isEdit ? 'Modifica Ruolo' : 'Aggiungi Ruolo')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Intestazione -->
  <div class="page-header d-flex align-items-center mb-4"
       style="background-color: #041930; border-radius: 0.75rem; padding: 1rem 2rem;">
    <i class="bi bi-person-badge-fill me-2 fs-3" style="color: #e2ae76;"></i>
    <h4 class="mb-0 fw-bold" style="color: #e2ae76;">
      {{ $isEdit ? 'Modifica Ruolo' : 'Aggiungi Ruolo' }}
    </h4>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form action="{{ $isEdit ? route('roles.update', $role) : route('roles.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Nome del Ruolo --}}
        <div class="mb-4">
          <label class="form-label fw-semibold">Nome Ruolo</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 required
                 value="{{ old('name', $role->name ?? '') }}">
        </div>

        {{-- Permessi --}}
        <div class="mb-4">
          <label class="form-label fw-semibold">Assegna Permessi</label>
          <div class="row">
            @foreach($permissions as $perm)
              <div class="col-md-4 mb-2">
                <div class="form-check">
                  <input class="form-check-input"
                         type="checkbox"
                         name="permissions[]"
                         value="{{ $perm->id }}"
                         id="perm_{{ $perm->id }}"
                         {{ in_array(
                              $perm->id,
                              old('permissions', $isEdit ? $role->permissions->pluck('id')->toArray() : [])
                            ) ? 'checked' : '' }}>
                  <label class="form-check-label" for="perm_{{ $perm->id }}">
                    {{ ucfirst($perm->name) }}
                  </label>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Pulsante Invia --}}
        <div class="text-end">
          <button class="btn btn-gold-blue">
            <i class="bi bi-save2 me-1"></i> {{ $isEdit ? 'Aggiorna Ruolo' : 'Crea Ruolo' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

<style>
  .btn-gold-blue {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: 1px solid #e2ae76;
  }
  .btn-gold-blue:hover {
    background-color: #d89d5c !important;
    color: white !important;
  }
</style>
