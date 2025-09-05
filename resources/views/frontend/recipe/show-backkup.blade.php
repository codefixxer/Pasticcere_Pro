{{-- resources/views/frontend/recipe/show.blade.php --}}
@extends('frontend.layouts.app')

@section('title','Ricetta: '.$recipe->recipe_name)

@section('content')
<div class="container py-5">
  <div class="card shadow-lg">
    <div class="card-header bg-dark text-gold d-flex align-items-center">
      <h5 class="mb-0" style="color: #e2ae76;">Ricetta: {{ $recipe->recipe_name }}</h5>
    </div>

    <div class="card-body">
      @php
        use Illuminate\Support\Facades\Auth;
        use App\Models\LaborCost;

        // 1) Unit selling price
        $unitSell = $recipe->sell_mode === 'piece'
                    ? $recipe->selling_price_per_piece
                    : $recipe->selling_price_per_kg;

        // 2) Batch costs
        $batchIngCost = $recipe->ingredients_cost_per_batch;

        $user         = Auth::user();
        $groupRootId  = $user->created_by ?: $user->id;
        $laborRateRec = LaborCost::where('user_id', $groupRootId)->first();
        $rate         = $recipe->labor_cost_mode === 'external'
                        ? ($laborRateRec->external_cost_per_min ?? 0)
                        : ($laborRateRec->shop_cost_per_min     ?? 0);
        $batchLabCost = round($recipe->labour_time_min * $rate, 2);

        // 3) Per-unit costs
        if ($recipe->sell_mode === 'piece') {
          $pcs         = $recipe->total_pieces ?: 1;
          $unitIngCost = $batchIngCost / $pcs;
          $unitLabCost = $batchLabCost / $pcs;
        } else {
          $wLoss       = $recipe->recipe_weight
                         ? $recipe->recipe_weight
                         : $recipe->ingredients->sum(fn($i) => $i->quantity_g);
          $kg          = $wLoss / 1000 ?: 1;
          $unitIngCost = $batchIngCost / $kg;
          $unitLabCost = $batchLabCost / $kg;
        }

        // 4) Total expense per unit
        $unitTotalCost = $recipe->total_expense;

        // 5) Margins & percentages
        $unitMargin    = $recipe->potential_margin;
        $unitMarginPct = $recipe->potential_margin_pct;
        $ingPct        = $unitSell > 0 ? round(($unitIngCost  * 100) / $unitSell, 2) : 0;
        $labPct        = $unitSell > 0 ? round(($unitLabCost  * 100) / $unitSell, 2) : 0;
        $totalPct      = $unitSell > 0 ? round(($unitTotalCost * 100) / $unitSell, 2) : 0;

        // totals for ingredients – use stored weight after loss
        $totalQty  = $recipe->recipe_weight;
        $totalCost = $recipe->ingredients->sum(fn($ri) => round(($ri->quantity_g/1000)*$ri->ingredient->price_per_kg,2));
      @endphp

      <div class="row text-center mb-4">
        <div class="col-md-2">
          <strong>Prezzo</strong><br>
          <div class="d-flex flex-column align-items-center">
            <span>€{{ number_format($unitSell,2) }}</span>
            <small class="text-muted">(100%)</small>
          </div>
        </div>
        <div class="col-md-2">
          <strong>Costo ingr.</strong><br>
          <div class="d-flex flex-column align-items-center">
            <span>€{{ number_format($unitIngCost,2) }}</span>
            <small class="text-muted">({{ $ingPct }}%)</small>
          </div>
        </div>
        <div class="col-md-2">
          <strong>Costo lavoro</strong><br>
          <div class="d-flex flex-column align-items-center">
            <span>€{{ number_format($unitLabCost,2) }}</span>
            <small class="text-muted">({{ $labPct }}%)</small>
          </div>
        </div>
        <div class="col-md-2">
          <strong>Costo totale</strong><br>
          <div class="d-flex flex-column align-items-center">
            <span>€{{ number_format($unitTotalCost,2) }}</span>
            <small class="text-muted">({{ $totalPct }}%)</small>
          </div>
        </div>
        <div class="col-md-2">
          <strong>Margine</strong><br>
          <div class="d-flex flex-column align-items-center">
            <span class="{{ $unitMargin >= 0 ? 'text-success' : 'text-danger' }}">
              €{{ number_format($unitMargin,2) }}
            </span>
            <small class="text-muted">({{ number_format($unitMarginPct,2) }}%)</small>
          </div>
        </div>
        <div class="col-md-2">
          <strong>% margine</strong><br>
          {{ number_format($unitMarginPct,2) }}%
        </div>
      </div>

      <hr>

      {{-- Dettaglio ingredienti --}}
      <h6>Dettaglio ingredienti</h6>
      <div class="table-responsive">
        <table  data-page-length="25"class="table table-bordered mb-0">
          <thead class="table-light">
            <tr>
              <th>Ingrediente</th>
              <th class="text-end">Qtà (g)</th>
              <th class="text-end">Costo (€)</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recipe->ingredients as $ri)
              @php
                $cost = round(($ri->quantity_g/1000)*$ri->ingredient->price_per_kg,2);
              @endphp
              <tr>
                <td>{{ $ri->ingredient->ingredient_name }}</td>
                <td class="text-end">{{ $ri->quantity_g }}</td>
                <td class="text-end">€{{ number_format($cost,2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot class="table-warning">
            <tr>
              <td class="text-end"><strong>Totale:</strong></td>
              <td class="text-end"><strong>{{ $totalQty }}</strong></td>
              <td class="text-end"><strong>€{{ number_format($totalCost,2) }}</strong></td>
            </tr>
          </tfoot>
        </table>
      </div>

      {{-- New feature: batch labor, total w/loss & multiplier (view‐only) --}}
      @php
        $origIng = $totalCost;
        $origLab = $batchLabCost;
      @endphp
      <div class="mt-3">
        <p>
          <strong>Totale manodopera:</strong>
          €<span id="labor-total">{{ number_format($origLab,2) }}</span>
        </p>
        <p>
          <strong>Peso/perdita totale:</strong>
          €<span id="total-loss">{{ number_format($origIng + $origLab,2) }}</span>
        </p>
        <div class="d-flex align-items-center">
          <label for="multiplier" class="me-2 mb-0"><strong>Moltiplicatore:</strong></label>
          <input
            type="number"
            id="multiplier"
            step="0.01"
            value="1"
            style="width: 80px;"
          >
        </div>
      </div>

      {{-- Azioni --}}
      <div class="mt-4 text-end">
        <a href="{{ route('recipes.edit',$recipe->id) }}" class="btn btn-outline-gold">Modifica</a>
        <a href="{{ route('recipes.index') }}" class="btn btn-outline-deepblue">Indietro</a>
      </div>
    </div>
  </div>
</div>

<style>
  .btn-outline-gold {
    border: 1px solid #e2ae76;
    color: #e2ae76;
  }
  .btn-outline-gold:hover {
    background: #e2ae76;
    color: #fff;
  }
  .btn-outline-deepblue {
    border: 1px solid #041930;
    color: #041930;
  }
  .btn-outline-deepblue:hover {
    background: #041930;
    color: #fff;
  }
</style>

<script>
  document.getElementById('multiplier').addEventListener('input', function() {
    var m   = parseFloat(this.value) || 1;
    var ing = parseFloat("{{ number_format($origIng,2,'.','') }}");
    var lab = parseFloat("{{ number_format($origLab,2,'.','') }}");
    var newTotal = ing * m + lab;
    document.getElementById('labor-total').textContent = lab.toFixed(2);
    document.getElementById('total-loss').textContent  = newTotal.toFixed(2);
  });
</script>
@endsection
