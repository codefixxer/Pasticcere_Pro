{{-- resources/views/frontend/cost_categories/form.blade.php --}}
@extends('frontend.layouts.app')

@section('title', isset($category) ? 'Modifica Categoria' : 'Aggiungi Categoria')

@section('content')
<div class="container py-5 px-md-5">
  <div class="card border-primary shadow-sm">
    <!-- Header -->
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-tags fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($category) ? 'Modifica Categoria di Costo' : 'Aggiungi Categoria di Costo' }}
      </h5>
    </div>

    <div class="card-body">
      <form
        action="{{ isset($category) ? route('cost_categories.update', $category->id) : route('cost_categories.store') }}"
        method="POST"
        class="needs-validation row g-3"
        novalidate
      >
        @csrf
        @if (isset($category)) @method('PUT') @endif

        <!-- Nome Categoria -->
        <div class="col-md-8">
          <label for="name" class="form-label fw-semibold">Nome Categoria</label>
          <input
            type="text"
            name="name"
            id="name"
            class="form-control form-control-lg"
            placeholder="es. Utenze, Affitto, Imballaggio"
            value="{{ old('name', $category->name ?? '') }}"
            required
          >
          <div class="invalid-feedback">Per favore inserisci un nome di categoria.</div>
        </div>

        <!-- Pulsante Salva/Aggiorna -->
        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold btn-lg">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($category) ? 'Aggiorna Categoria' : 'Salva Categoria' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

<style>
  .btn-gold {
    border: 1px solid #e2ae76 !important;
    color: #041930 !important;
    background-color: e2ae76 !important;
  }

  .btn-deepblue {
    border: 1px solid #041930 !important;
    color: #041930 !important;
    background-color: transparent !important;
  }

  .btn-gold i,
  .btn-deepblue i {
    color: inherit !important;
  }
</style>

@section('scripts')
<script>
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
