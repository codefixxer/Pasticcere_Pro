@extends('frontend.layouts.app')

@section('title', $cost->cost_identifier ?? 'Costo #' . $cost->id)

@section('content')
<div class="container py-5 px-md-5">
  <div class="card border-warning shadow-lg rounded-3 overflow-hidden">
    <!-- Header -->
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-currency-dollar fs-2 me-3" style="color: #e2ae76;"></i>
      <h4 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ $cost->cost_identifier ?? 'Costo #' . $cost->id }}
      </h4>
    </div>

    <div class="card-body">
      <!-- Details -->
      <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Fornitore</h6>
          <p class="fs-5 mb-0">{{ $cost->supplier }}</p>
        </div>
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Importo</h6>
          <p class="fs-5 mb-0">€{{ number_format($cost->amount, 2) }}</p>
        </div>
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Data di scadenza</h6>
          <p class="fs-5 mb-0">{{ optional($cost->due_date)->format('Y-m-d') }}</p>
        </div>
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Categoria</h6>
          <p class="fs-5 mb-0">{{ $cost->category->name ?? '–' }}</p>
        </div>
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Creato il</h6>
          <p class="fs-5 mb-0">{{ optional($cost->created_at)?->format('Y-m-d H:i') }}</p>
        </div>
        <div class="col">
          <h6 class="text-uppercase text-muted small mb-1">Ultimo aggiornamento</h6>
          <p class="fs-5 mb-0">{{ optional($cost->updated_at)?->format('Y-m-d H:i') }}</p>
        </div>
      </div>

      <hr class="border-secondary">

      <!-- Buttons -->
      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('costs.edit', $cost) }}" class="btn btn-gold btn-lg">
          <i class="bi bi-pencil me-1"></i>Modifica
        </a>

        <a href="{{ route('costs.index') }}" class="btn btn-deepblue btn-lg">
          <i class="bi bi-arrow-left me-1"></i>Indietro alla lista
        </a>

        <form action="{{ route('costs.destroy', $cost) }}" method="POST" onsubmit="return confirm('Eliminare questo costo?');" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-red btn-lg">
            <i class="bi bi-trash me-1"></i>Elimina
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
    border: 1px solid red !important;
    color: red !important;
    background-color: transparent !important;
  }
  .btn-red:hover {
    background-color: red !important;
    color: white !important;
  }
</style>
