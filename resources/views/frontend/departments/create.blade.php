{{-- resources/views/frontend/departments/form.blade.php --}}
@extends('frontend.layouts.app')

@section('title', isset($department) ? 'Modifica Reparto' : 'Aggiungi Reparto')

@section('content')
<div class="container py-5 px-md-5">
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-building fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($department) ? 'Modifica Reparto' : 'Aggiungi Reparto' }}
      </h5>
    </div>

    <div class="card-body">
      <form
        action="{{ isset($department) ? route('departments.update', $department->id) : route('departments.store') }}"
        method="POST"
        class="needs-validation"
        novalidate
      >
        @csrf
        @if(isset($department)) @method('PUT') @endif

        <div class="mb-4">
          <label for="name" class="form-label fw-semibold">Nome Reparto</label>
          <input
            type="text"
            name="name"
            id="name"
            class="form-control form-control-lg"
            placeholder="es. Produzione, Logistica"
            value="{{ old('name', $department->name ?? '') }}"
            required
          >
          <div class="invalid-feedback">Per favore inserisci il nome del reparto.</div>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-1"></i>
            {{ isset($department) ? 'Aggiorna Reparto' : 'Salva Reparto' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

<style>
  .btn-gold-filled {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: none !important;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 12px;
    transition: background-color 0.2s ease;
  }

  .btn-gold-filled:hover {
    background-color: #d89d5c !important;
    color: white !important;
  }

  .btn-gold-filled i {
    color: inherit !important;
  }
</style>

@section('scripts')
<script>
  // Bootstrap validation
  (() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>
@endsection
