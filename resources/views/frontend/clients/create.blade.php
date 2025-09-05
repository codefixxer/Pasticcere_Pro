{{-- resources/views/frontend/clients/form.blade.php --}}
@extends('frontend.layouts.app')

@section('title', isset($client) ? 'Modifica Cliente' : 'Aggiungi Cliente')

@section('content')
<div class="container py-5 px-md-5">
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-person-lines-fill fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($client) ? 'Modifica Cliente' : 'Aggiungi Cliente' }}
      </h5>
    </div>

    <div class="card-body">
      <form action="{{ isset($client) ? route('clients.update', $client->id) : route('clients.store') }}"
            method="POST"
            class="row g-4 needs-validation"
            novalidate>
        @csrf
        @if(isset($client))
          @method('PUT')
        @endif

        <div class="col-md-6">
          <label for="name" class="form-label fw-semibold">Nome Cliente</label>
          <input type="text"
                 name="name"
                 id="name"
                 class="form-control form-control-lg"
                 value="{{ old('name', $client->name ?? '') }}"
                 required>
          <div class="invalid-feedback">Inserisci il nome del cliente.</div>
        </div>

        <div class="col-md-6">
          <label for="location" class="form-label fw-semibold">Sede</label>
          <input type="text"
                 name="location"
                 id="location"
                 class="form-control form-control-lg"
                 value="{{ old('location', $client->location ?? '') }}">
        </div>

        <div class="col-md-4">
          <label for="phone" class="form-label fw-semibold">Telefono</label>
          <input type="text"
                 name="phone"
                 id="phone"
                 class="form-control form-control-lg"
                 value="{{ old('phone', $client->phone ?? '') }}">
        </div>

        <div class="col-md-4">
          <label for="email" class="form-label fw-semibold">Email</label>
          <input type="email"
                 name="email"
                 id="email"
                 class="form-control form-control-lg"
                 value="{{ old('email', $client->email ?? '') }}">
        </div>

        <div class="col-md-4">
          <label for="notes" class="form-label fw-semibold">Note</label>
          <input type="text"
                 name="notes"
                 id="notes"
                 class="form-control form-control-lg"
                 value="{{ old('notes', $client->notes ?? '') }}">
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold btn-lg">
            <i class="bi bi-save2 me-1"></i>
            {{ isset($client) ? 'Aggiorna Cliente' : 'Salva Cliente' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

<style>
  .btn-gold {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: 1px solid #e2ae76 !important;
  }
  .btn-gold:hover {
    background-color: #d89d5c !important;
    color: white !important;
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
  document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  });
</script>
@endsection
