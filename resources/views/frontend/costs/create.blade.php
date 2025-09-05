@extends('frontend.layouts.app')
@section('title', isset($cost) ? 'Modifica Costo' : 'Aggiungi Costo')

@section('content')
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-currency-dollar fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($cost) ? 'Modifica Costo' : 'Aggiungi Costo' }}
      </h5>
    </div>
    <div class="card-body">
      <form method="POST"
            action="{{ isset($cost) ? route('costs.update', $cost) : route('costs.store') }}"
            class="row g-3 needs-validation"
            novalidate>
        @csrf
        @if(isset($cost)) @method('PUT') @endif

        <div class="col-md-6">
          <label for="cost_identifier" class="form-label fw-semibold">
            Identificatore Costo <small class="text-muted">(facoltativo)</small>
          </label>
          <input type="text" name="cost_identifier" id="cost_identifier"
                 class="form-control form-control-lg"
                 placeholder="es. INV-2025-04-001"
                 value="{{ old('cost_identifier', $cost->cost_identifier ?? '') }}">
        </div>

        <div class="col-md-6">
          <label for="supplier" class="form-label fw-semibold">Fornitore</label>
          <input type="text" name="supplier" id="supplier"
                 class="form-control form-control-lg"
                 placeholder="es. ABC Srl"
                 value="{{ old('supplier', $cost->supplier ?? '') }}" required>
          <div class="invalid-feedback">Inserisci un fornitore.</div>
        </div>

        <div class="col-md-6">
          <label for="amount" class="form-label fw-semibold">Importo</label>
          <div class="input-group input-group-lg has-validation">
            <span class="input-group-text">€</span>
            <input type="number" step="0.01" name="amount" id="amount"
                   class="form-control"
                   value="{{ old('amount', $cost->amount ?? '') }}" required>
            <div class="invalid-feedback">Inserisci un importo valido.</div>
          </div>
        </div>

@php
    $dueDate = old('due_date')
        ?: (isset($cost) && $cost->due_date ? $cost->due_date->format('Y-m-d') : '');
@endphp
<div class="col-md-6">
  <label for="due_date" class="form-label fw-semibold">Data di scadenza</label>
  <input type="date" name="due_date" id="due_date"
         class="form-control form-control-lg"
         value="{{ $dueDate }}" required>
  <div class="invalid-feedback">Seleziona una data.</div>
</div>

        <div class="col-md-6">
          <label for="category_id" class="form-label fw-semibold">Categoria</label>
          <select name="category_id" id="category_id"
                  class="form-select form-select-lg" required>
            <option value="">Seleziona…</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}"
                {{ old('category_id', $cost->category_id ?? '') == $c->id ? 'selected' : '' }}>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
          <div class="invalid-feedback">Seleziona una categoria.</div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($cost) ? 'Aggiorna Costo' : 'Salva Costo' }}
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
