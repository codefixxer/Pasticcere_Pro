@extends('frontend.layouts.app')

@section('title', isset($equipment) ? 'Modifica Attrezzatura' : 'Aggiungi Attrezzatura')

@section('content')
<div class="container py-5 px-md-5">
  <div class="card border-primary shadow-sm rounded-3">
    
    <!-- Intestazione -->
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-tools fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($equipment) ? 'Modifica Attrezzatura' : 'Aggiungi Attrezzatura' }}
      </h5>
    </div>

    <div class="card-body">
      <form 
        action="{{ isset($equipment) ? route('equipment.update', $equipment->id) : route('equipment.store') }}" 
        method="POST" 
        class="needs-validation row g-3" 
        novalidate>
        
        @csrf
        @if(isset($equipment)) @method('PUT') @endif

        <div class="col-md-8">
          <label for="name" class="form-label fw-semibold">Nome Attrezzatura</label>
          <input 
            type="text"
            id="name"
            name="name"
            class="form-control form-control-lg"
            placeholder="es. Impastatrice, Forno"
            value="{{ old('name', $equipment->name ?? '') }}"
            required>
          <div class="invalid-feedback">Inserisci il nome dell’attrezzatura.</div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($equipment) ? 'Aggiorna Attrezzatura' : 'Salva Attrezzatura' }}
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
    padding: 10px 24px;
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
  // Validazione Bootstrap
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
