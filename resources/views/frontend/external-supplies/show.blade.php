{{-- resources/views/frontend/external-supplies/show.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Fornitura Esterna — ' . $externalSupply->client->name)

@section('content')
<div class="container py-5 px-md-5">

  <div class="card border-primary shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #041930;">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-box-seam fs-4" style="color: #e2ae76;"></i>
        <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
          Fornitura Esterna — {{ $externalSupply->client->name }}
        </h5>
      </div>
      <small class="text-muted" style="font-size: 0.85rem;">
        Data: {{ $externalSupply->supply_date->format('Y-m-d') }}
      </small>
    </div>

    <div class="card-body">
      {{-- Riepilogo --}}
      <dl class="row mb-4">
        <dt class="col-sm-4 fw-semibold" style="font-size: 1.1rem;">Cliente:</dt>
        <dd class="col-sm-8" style="font-size: 1.1rem;">{{ $externalSupply->client->name }}</dd>

        <dt class="col-sm-4 fw-semibold" style="font-size: 1.1rem;">Nome Fornitura:</dt>
        <dd class="col-sm-8" style="font-size: 1.1rem;">{{ $externalSupply->supply_name }}</dd>

        <dt class="col-sm-4 fw-semibold" style="font-size: 1.1rem;">Data:</dt>
        <dd class="col-sm-8" style="font-size: 1.1rem;">{{ $externalSupply->supply_date->format('Y-m-d') }}</dd>

        <dt class="col-sm-4 fw-semibold" style="font-size: 1.1rem;">Ricavo (€):</dt>
        <dd class="col-sm-8" style="font-size: 1.1rem;">€{{ number_format($externalSupply->total_amount, 2) }}</dd>

        @php
          $cost = $externalSupply->recipes->sum(fn($line) =>
            ($line->recipe->production_cost_per_kg ?? 0) / 1000 * $line->qty
          );
          $profit = $externalSupply->total_amount - $cost;
        @endphp

        <dt class="col-sm-4 fw-semibold" style="font-size: 1.1rem;">Costo (€):</dt>
        <dd class="col-sm-8" style="font-size: 1.1rem;">€{{ number_format($cost, 2) }}</dd>

        <dt class="col-sm-4 fw-semibold" style="font-size: 1.1rem;">Profitto (€):</dt>
        <dd class="col-sm-8" style="font-size: 1.1rem;">
          <span class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
            €{{ number_format($profit, 2) }}
          </span>
        </dd>
      </dl>

      {{-- Linee Ricette --}}
      <h6 class="fw-semibold mb-3">Ricette Fornite</h6>
      <div class="table-responsive">
        <table  data-page-length="25"class="table table-bordered table-striped align-middle text-center">
          <thead>
            <tr>
              <th>Ricetta</th>
              <th>Qtà</th>
              <th>Ricavo Riga (€)</th>
              <th>Costo Riga (€)</th>
            </tr>
          </thead>
          <tbody>
            @php
              $totalQty = 0;
              $totalRev = 0;
              $totalCost = 0;
            @endphp

            @foreach($externalSupply->recipes as $line)
              @php
                $lineRev  = $line->total_amount;
                $lineCost = ($line->recipe->production_cost_per_kg ?? 0) / 1000 * $line->qty;
                $totalQty += $line->qty;
                $totalRev += $lineRev;
                $totalCost += $lineCost;
              @endphp
              <tr>
                <td>{{ $line->recipe->recipe_name ?? '—' }}</td>
                <td>{{ $line->qty }}</td>
                <td>€{{ number_format($lineRev, 2) }}</td>
                <td>€{{ number_format($lineCost, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>Total</th>
              <th>{{ $totalQty }}</th>
              <th>€{{ number_format($totalRev, 2) }}</th>
              <th>€{{ number_format($totalCost, 2) }}</th>
            </tr>
          </tfoot>
        </table>
      </div>

      {{-- Azioni --}}
      <div class="mt-4 text-end">
        <a href="{{ route('external-supplies.edit', $externalSupply) }}" class="btn btn-gold me-2">
          <i class="bi bi-pencil me-1"></i> Modifica
        </a>
        <a href="{{ route('external-supplies.index') }}" class="btn btn-deepblue me-2">
          <i class="bi bi-arrow-left me-1"></i> Indietro alla lista
        </a>
        <form action="{{ route('external-supplies.destroy', $externalSupply) }}"
              method="POST" class="d-inline"
              onsubmit="return confirm('Eliminare questa fornitura?');">
          @csrf
          @method('DELETE')
          <button class="btn btn-red" type="submit">
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
    color: #041930;
    background-color: transparent !important;
  }
  .btn-deepblue:hover {
    background-color: #041930 !important;
    color: white !important;
  }

  .btn-red {
    border: 1px solid #ff0000 !important;
    color: red;
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

  table th {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    text-align: center;
    vertical-align: middle;
  }
</style>
