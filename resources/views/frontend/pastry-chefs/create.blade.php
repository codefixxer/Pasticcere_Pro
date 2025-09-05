@extends('frontend.layouts.app')

@section('title', isset($pastryChef) ? 'Modifica Pasticcere' : 'Aggiungi Pasticcere')

@section('content')
<div class="container py-5 px-md-5">
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-egg-fried fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($pastryChef) ? 'Modifica Pasticcere' : 'Aggiungi Pasticcere' }}
      </h5>
    </div>
    <div class="card-body">
      <form 
        action="{{ isset($pastryChef) ? route('pastry-chefs.update', $pastryChef->id) : route('pastry-chefs.store') }}" 
        method="POST" 
        class="row g-3 needs-validation" 
        novalidate
      >
        @csrf
        @if(isset($pastryChef)) @method('PUT') @endif

        <div class="col-md-6">
          <label for="Name" class="form-label fw-semibold">Nome Pasticceri</label>
          <input type="text"
                 id="Name"
                 name="name"
                 class="form-control form-control-lg"
                 value="{{ old('name', $pastryChef->name ?? '') }}"
                 placeholder="Nome Pasticceri"
                 required>
          <div class="invalid-feedback">
            Per favore inserisci il nome del pasticcere.
          </div>
        </div>

        <div class="col-md-6">
          <label for="Email" class="form-label fw-semibold">Email Chef</label>
          <input type="email"
                 id="Email"
                 name="email"
                 class="form-control form-control-lg"
                 value="{{ old('email', $pastryChef->email ?? '') }}"
                 placeholder="Inserisci l'email del pasticcere">
          <div class="invalid-feedback">
            Per favore inserisci l'email del pasticcere.
          </div>
        </div>

        <div class="col-md-6">
          <label for="phone" class="form-label fw-semibold">Telefono Chef</label>
          <input type="text"
                 id="phone"
                 name="phone"
                 class="form-control form-control-lg"
                 value="{{ old('phone', $pastryChef->phone ?? '') }}"
                 placeholder="Inserisci il numero di telefono del pasticcere">
          <div class="invalid-feedback">
            Per favore inserisci il numero di telefono del pasticcere.
          </div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($pastryChef) ? 'Aggiorna Pasticcere' : 'Salva Pasticcere' }}
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
