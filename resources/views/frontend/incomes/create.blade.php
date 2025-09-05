@extends('frontend.layouts.app')

@section('title', isset($income) ? 'Modifica Entrata' : 'Aggiungi Entrata')

@section('content')
<div class="container py-5 px-md-5">
  <div class="card shadow-sm border-primary">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-currency-euro fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($income) ? 'Modifica Entrata' : 'Aggiungi Entrata' }}
      </h5>
    </div>
    <div class="card-body">
      <form
        action="{{ isset($income) ? route('incomes.update', $income) : route('incomes.store') }}"
        method="POST"
        class="row g-3 needs-validation"
        novalidate
      >
        @csrf
        @if(isset($income)) @method('PUT') @endif

        <div class="col-md-6">
          <label for="identifier" class="form-label fw-semibold">
            Identificatore Facoltativo <small class="text-muted">(facoltativo)</small>
          </label>
          <input
            type="text"
            name="identifier"
            id="identifier"
            class="form-control form-control-lg"
            value="{{ old('identifier', $income->identifier ?? '') }}"
          >
        </div>

        <div class="col-md-6">
          <label for="amount" class="form-label fw-semibold">Importo (€)</label>
          <div class="input-group input-group-lg has-validation">
            <span class="input-group-text"><i class="bi bi-currency-euro"></i></span>
            <input
              type="number"
              step="0.01"
              name="amount"
              id="amount"
              class="form-control"
              value="{{ old('amount', $income->amount ?? '') }}"
              required
            >
            <div class="invalid-feedback">
              {{ $errors->first('amount', 'Inserisci un importo valido.') }}
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <label for="date" class="form-label fw-semibold">Data</label>
          <input
            type="date"
            name="date"
            id="date"
            class="form-control form-control-lg"
            value="{{ old('date', isset($income) ? $income->date->format('Y-m-d') : '') }}"
            required
          >
          <div class="invalid-feedback">
            {{ $errors->first('date', 'Seleziona una data.') }}
          </div>
        </div>

        <!-- New Category Field -->
        <div class="col-md-6">
          <label for="category" class="form-label fw-semibold">Categoria Entrata</label>
          <select name="category" id="category" class="form-select form-select-lg">
            <option value="">Seleziona Categoria</option>
            <option value="Sales" @selected(old('category', $income->category ?? '') === 'Sales')>Vendite</option>
            <option value="Service" @selected(old('category', $income->category ?? '') === 'Service')>Servizi</option>
            <option value="Investment" @selected(old('category', $income->category ?? '') === 'Investment')>Investimenti</option>
            <option value="Other" @selected(old('category', $income->category ?? '') === 'Other')>Altro</option>
          </select>
          <div class="invalid-feedback">
            {{ $errors->first('category', 'Seleziona una categoria valida.') }}
          </div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-1"></i>
            {{ isset($income) ? 'Aggiorna Entrata' : 'Salva Entrata' }}
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
    font-weight: 600;
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
  document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });
})();
</script>
@endsection
