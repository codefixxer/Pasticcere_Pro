{{-- resources/views/pastry-chefs/show.blade.php --}}
@extends('frontend.layouts.app')

@section('title', $pastryChef->name)

@section('content')
<div class="container py-5 px-md-5">
  <div class="card border-primary shadow-sm rounded-3 overflow-hidden">
    <!-- Header -->
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-person-fill fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">Pasticcere — {{ $pastryChef->name }}</h5>
    </div>

    <div class="card-body">
      <!-- Dettagli -->
      <div class="row row-cols-1 row-cols-md-2 g-4 mb-4" style="width: 70%;">
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1"> Nome Pasticceri</h6>
          <p class="fs-3 fw-bold mb-0">{{ $pastryChef->name }}</p>
        </div>
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Email</h6>
          <p class="fs-5 mb-0">{{ $pastryChef->email ?? '—' }}</p>
        </div>
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Telefono</h6>
          <p class="fs-5 mb-0">{{ $pastryChef->phone ?? '—' }}</p>
        </div>
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Ultimo Aggiornamento</h6>
          <p class="fs-5 mb-0">{{ optional($pastryChef->updated_at)?->format('Y-m-d H:i') ?? '—' }}</p>
        </div>
      </div>

      <hr class="border-secondary">

      <!-- Pulsanti Azione -->
      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('pastry-chefs.edit', $pastryChef) }}" class="btn btn-gold btn-lg">
          <i class="bi bi-pencil me-1"></i> Modifica
        </a>

        <a href="{{ route('pastry-chefs.index') }}" class="btn btn-deepblue btn-lg">
          <i class="bi bi-arrow-left me-1"></i> Torna alla Lista
        </a>

        <form action="{{ route('pastry-chefs.destroy', $pastryChef) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminare questo Chef?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-red btn-lg">
            <i class="bi bi-trash me-1"></i> Elimina
          </button>
        </form>
      </div>
    </div>
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
    color: white !important;
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

  .btn-red {
    border: 1px solid #ff0000 !important;
    color: red !important;
    background-color: transparent !important;
  }
  .btn-red:hover {
    background-color: #ff0000 !important;
    color: white !important;
  }

  .btn-gold i,
  .btn-deepblue i,
  .btn-red i {
    color: inherit !important;
  }
</style>
