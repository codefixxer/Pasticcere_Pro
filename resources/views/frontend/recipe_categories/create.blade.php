@extends('frontend.layouts.app')

@section('title','Gestione Categorie Ricette')

@section('content')
<div class="container py-5 px-md-5">

  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-tags me-2 fs-4" style="color: #e2ae76;"></i>
      <h5 class="mb-0" style="color: #e2ae76;">
        {{ isset($category) ? 'Modifica Categoria Ricetta' : 'Aggiungi Categoria Ricetta' }}
      </h5>
    </div>

    <div class="card-body">
      <form
        action="{{ isset($category)
                  ? route('recipe-categories.update', $category->id)
                  : route('recipe-categories.store') }}"
        method="POST"
        class="row g-3 needs-validation"
        novalidate>
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div class="col-md-8">
          <label for="categoryName" class="form-label fw-semibold">Nome Categoria</label>
          <input
            type="text"
            id="categoryName"
            name="name"
            class="form-control form-control-lg"
            placeholder="es. Dolce"
            value="{{ old('name', $category->name ?? '') }}"
            required>
          <div class="invalid-feedback">Inserisci un nome per la categoria.</div>
        </div>

        <div class="col-12 text-end">
          <button type="submit"
                  class="btn btn-lg fw-semibold"
                  style="background-color: #e2ae76; color: #041930;">
            <i class="bi bi-save2 me-2" style="color: #041930;"></i>
            {{ isset($category) ? 'Aggiorna Categoria' : 'Salva Categoria' }}
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
  (() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>
@endsection
