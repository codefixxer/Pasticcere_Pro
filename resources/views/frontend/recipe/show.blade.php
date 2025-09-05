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

        // 2) Batch ingredient cost (sum of all lines as stored)
        $batchIngCost = $recipe->ingredients_cost_per_batch;

        // 3) Batch labor cost (rate chosen by mode)
        $user         = Auth::user();
        $groupRootId  = $user->created_by ?: $user->id;
        $laborRateRec = LaborCost::where('user_id', $groupRootId)->first();
        $rate         = $recipe->labor_cost_mode === 'external'
                        ? ($laborRateRec->external_cost_per_min ?? 0)
                        : ($laborRateRec->shop_cost_per_min     ?? 0);
        $batchLabCost = round($recipe->labour_time_min * $rate, 2);

        // 4) Per-unit costs
        if ($recipe->sell_mode === 'piece') {
          $pcs         = $recipe->total_pieces ?: 1;
          $unitIngCost = $batchIngCost / $pcs;
          $unitLabCost = $batchLabCost / $pcs;
        } else {
          $wLoss       = $recipe->recipe_weight
                         ? $recipe->recipe_weight
                         : $recipe->ingredients->sum(fn($i) => $i->quantity_g);
          $kg          = max($wLoss / 1000, 1);
          $unitIngCost = $batchIngCost / $kg;
          $unitLabCost = $batchLabCost / $kg;
        }

        // 5) Expense & margins (already computed server-side)
        $unitTotalCost = $recipe->total_expense;
        $unitMargin    = $recipe->potential_margin;
        $unitMarginPct = $recipe->potential_margin_pct;

        $ingPct   = $unitSell > 0 ? round(($unitIngCost  * 100) / $unitSell, 2) : 0;
        $labPct   = $unitSell > 0 ? round(($unitLabCost  * 100) / $unitSell, 2) : 0;
        $totalPct = $unitSell > 0 ? round(($unitTotalCost * 100) / $unitSell, 2) : 0;

        // 6) Base totals for the table (ingredients)
        $totalQty  = (int) ($recipe->recipe_weight ?: $recipe->ingredients->sum(fn($ri) => $ri->quantity_g));
        $totalCost = $recipe->ingredients->sum(fn($ri) => round(($ri->quantity_g/1000)*$ri->ingredient->price_per_kg, 2));

        // store originals for multiplier UI
        $origIng = (float) $totalCost;   // ingredients total €
        $origLab = (float) $batchLabCost; // labor total €
      @endphp

      {{-- KPIs --}}
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
        <table  data-page-length="25"class="table table-bordered mb-0" id="ingredients-detail-table">
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
                $lineBaseQty  = (float) $ri->quantity_g;
                $lineBaseCost = round(($lineBaseQty/1000) * $ri->ingredient->price_per_kg, 2);
              @endphp
              <tr>
                <td>{{ $ri->ingredient->ingredient_name }}</td>

                {{-- quantity cell with base value for JS --}}
                <td class="text-end qty-cell"
                    data-base-qty="{{ number_format($lineBaseQty, 3, '.', '') }}">
                  {{ number_format($lineBaseQty, 0) }}
                </td>

                {{-- cost cell with base value for JS --}}
                <td class="text-end cost-cell"
                    data-base-cost="{{ number_format($lineBaseCost,2,'.','') }}">
                  €{{ number_format($lineBaseCost,2) }}
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot class="table-warning">
            <tr>
              <td class="text-end"><strong>Totale:</strong></td>
              {{-- total qty cell with base value for JS --}}
              <td class="text-end">
                <strong id="qty-total"
                        data-base-qty="{{ number_format($totalQty, 3, '.', '') }}">
                  {{ number_format($totalQty, 0) }}
                </strong>
              </td>
              {{-- total ingredients € with base value for JS --}}
              <td class="text-end">
                <strong id="ingredients-total"
                        data-base-total="{{ number_format($origIng,2,'.','') }}">
                  €{{ number_format($origIng,2) }}
                </strong>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      {{-- Labor, grand total & multiplier (view-only) --}}
      <div class="mt-3">
        <p>
          <strong>Totale manodopera:</strong>
          €<span id="labor-total" data-base="{{ number_format($origLab,2,'.','') }}">
            {{ number_format($origLab,2) }}
          </span>
        </p>
        <p>
          <strong>Totale (ingredienti × moltiplicatore + manodopera):</strong>
          <span id="total-with-labor">
            {{ number_format($origIng + $origLab,2) }}
          </span>
        </p>
        <div class="d-flex align-items-center">
          <label for="multiplier" class="me-2 mb-0"><strong>Moltiplicatore:</strong></label>
          <input type="number" id="multiplier" step="0.01" value="1" style="width: 100px;">
        </div>
        <small class="text-muted d-block mt-1">
          Il moltiplicatore modifica solo i costi e le quantità degli ingredienti (non la manodopera).
          Solo visualizzazione: non viene salvato.
        </small>
      </div>

      {{-- Actions --}}
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
(function() {
  const mInput   = document.getElementById('multiplier');

  const qtyTds   = document.querySelectorAll('#ingredients-detail-table td.qty-cell');
  const costTds  = document.querySelectorAll('#ingredients-detail-table td.cost-cell');

  const qtyTotalEl = document.getElementById('qty-total');
  const ingTotalEl = document.getElementById('ingredients-total');

  const laborEl  = document.getElementById('labor-total');
  const totalEl  = document.getElementById('total-with-labor');

  function euro(n){ return '€' + Number(n).toFixed(2); }
  function i(n){ return Math.round(Number(n)); }

  function recalc() {
    const m        = parseFloat(mInput.value) || 1;
    const baseLab  = parseFloat(laborEl.dataset.base) || 0;

    // Update each ingredient qty and cost
    let newQtyTotal = 0;
    let newIngTotal = 0;

    qtyTds.forEach(td => {
      const baseQ = parseFloat(td.dataset.baseQty) || 0;
      const q     = baseQ * m;
      newQtyTotal += q;
      td.textContent = i(q);
    });

    costTds.forEach(td => {
      const baseC = parseFloat(td.dataset.baseCost) || 0;
      const c     = baseC * m;
      newIngTotal += c;
      td.textContent = euro(c);
    });

    // Totals
    if (qtyTotalEl) qtyTotalEl.textContent = i(newQtyTotal);
    if (ingTotalEl) ingTotalEl.textContent = euro(newIngTotal);

    // Grand total (ingredients * m + fixed labor)
    totalEl.textContent = euro(newIngTotal + baseLab);
  }

  // Initial render
  recalc();

  // Listeners
  mInput.addEventListener('input', recalc);
  mInput.addEventListener('change', recalc);
})();
</script>
@endsection
