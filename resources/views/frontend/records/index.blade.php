{{-- resources/views/frontend/records.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Registri Showcase & Forniture Esterne')

@section('content')
<div class="container py-5">
  {{-- Page Title --}}
  <div class="text-center mb-4">
    <h2 class="d-inline-block px-4 py-2" style="background:#041930; color:#e2ae76; border-radius:.5rem;">
      Incassi Negozio e Forniture Esterne
    </h2>
  </div>

  {{-- Filtri --}}
  <div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-2">
          <label class="form-label">Da</label>
          <input id="filter-from" type="date" value="{{ $from }}" class="form-control">
        </div>
        <div class="col-md-2">
          <label class="form-label">A</label>
          <input id="filter-to" type="date" value="{{ $to }}" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Nome Ricetta</label>
          <input id="filter-recipe" type="text" class="form-control" placeholder="Inserisci ricetta…">
        </div>
        <div class="col-md-2">
          <label class="form-label">Categoria</label>
          <select id="filter-category" class="form-select">
            <option value="">Tutte le categorie</option>
            @php
              $cats = $showcaseGroups
                ->flatten(1)
                ->pluck('recipes')
                ->flatten(1)
                ->pluck('recipe.category.name')
                ->unique()
                ->filter();
            @endphp
            @foreach($cats as $cat)
              <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Reparto</label>
          <select id="filter-department" class="form-select">
            <option value="">Tutti i reparti</option>
            @php
              $depts = $showcaseGroups
                ->flatten(1)
                ->pluck('recipes')
                ->flatten(1)
                ->pluck('recipe.department.name')
                ->unique()
                ->filter();
            @endphp
            @foreach($depts as $dept)
              <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- Sommari --}}
  <div class="row mb-5 gx-4">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center">
          <i class="bi bi-graph-up display-4 text-primary mb-2"></i>
          <h5>Ricavi Totali Negozio</h5>
          <p id="summary-showcase" class="display-6 mb-1">€{{ number_format($totalShowcaseRevenue,2) }}</p>
          <small id="summary-showcase-pct" class="text-muted">0%</small>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center">
          <i class="bi bi-currency-euro display-4 text-danger mb-2"></i>
          <h5>Ricavi Totali Forniture Esterne</h5>
          <p id="summary-external" class="display-6 mb-1">€{{ number_format($totalExternalCost,2) }}</p>
          <small id="summary-external-pct" class="text-muted">0%</small>
        </div>
      </div>
    </div>
  </div>

  <div class="row gx-4">
    {{-- Showcase --}}
    <div class="col-lg-6 mb-5">
      <div class="d-flex align-items-center"
           style="background: #041930; color: #e2ae76; padding: .5rem; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
        <i class="bi bi-calendar2-check me-1" style="font-size:1.2rem;"></i>
         Lista Negozio
      </div>
      <table  data-page-length="25"class="table mb-0 border showcaseTable" id="showcaseTable">
        <thead class="table-light text-center">
          <tr>
            <th style="width:1%"></th>
            <th class="sortable">Data</th>
            <th class="sortable">Ricetta</th>
            <th class="sortable">Quantità</th>
            <th class="sortable">Venduto</th>
            <th class="sortable">Riutilizzo</th>
            <th class="sortable">Spreco</th>
            <th class="text-end sortable">Ricavo (€)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($showcaseGroups->sortKeysDesc() as $date => $group)
            @php
              $lines = collect();
              foreach($group as $sc) {
                $lines = $lines->merge($sc->recipes);
              }
              $sum = $lines->sum('actual_revenue');
            @endphp
            <tr class="bg-light group-header text-center" data-date="{{ $date }}">
              <td class="toggle-arrow" style="cursor:pointer">
                <i class="bi bi-caret-right-fill"></i>
              </td>
              <td colspan="6" class="text-start">{{ $date }} ({{ $lines->count() }} righe)</td>
              <td class="text-end fw-semibold">€{{ number_format($sum,2) }}</td>
            </tr>
            @foreach($group as $sc)
              @foreach($sc->recipes as $line)
                <tr class="group-{{ $date }} d-none text-center"
                    data-date="{{ $date }}"
                    data-recipe="{{ strtolower($line->recipe->recipe_name) }}"
                    data-category="{{ strtolower($line->recipe->category->name ?? '') }}"
                    data-department="{{ strtolower($line->recipe->department->name ?? '') }}"
                    data-qty="{{ $line->quantity }}"
                    data-sold="{{ $line->sold }}"
                    data-reuse="{{ $line->reuse }}"
                    data-waste="{{ $line->waste }}"
                    data-revenue="{{ $line->actual_revenue }}">
                  <td></td>
                  <td>{{ $sc->showcase_date->format('Y-m-d') }}</td>
                  <td>{{ $line->recipe->recipe_name }}</td>
                  <td>{{ $line->quantity }}</td>
                  <td>{{ $line->sold }}</td>
                  <td>{{ $line->reuse }}</td>
                  <td>{{ $line->waste }}</td>
                  <td class="text-end">€{{ number_format($line->actual_revenue,2) }}</td>
                </tr>
              @endforeach
            @endforeach
          @endforeach
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th colspan="3" class="text-end">Totale Generale:</th>
            <th id="showcaseQtyFooter" class="text-center">0</th>
            <th id="showcaseSoldFooter" class="text-center">0</th>
            <th id="showcaseReuseFooter" class="text-center">0</th>
            <th id="showcaseWasteFooter" class="text-center">0</th>
            <th id="showcaseFooter" class="text-end">0,00</th>
          </tr>
        </tfoot>
      </table>
    </div>

    {{-- Esterno --}}
    <div class="col-lg-6 mb-5">
      <div class="d-flex align-items-center"
           style="background: #041930; color: #e2ae76; padding: .5rem; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
        <i class="bi bi-box-seam me-1" style="font-size:1.2rem;"></i>
        Lista Forniture Esterne
      </div>
      <table  data-page-length="25"class="table mb-0 border externalTable" id="externalTable">
        <thead class="table-light text-center">
          <tr>
            <th style="width:1%"></th>
            <th class="sortable">Data</th>
            <th class="sortable">Cliente</th>
            <th class="sortable">Ricetta</th>
            <th class="sortable">Resi</th>
            <th class="sortable">Quantità</th>
            <th class="text-end sortable">Totale (€)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($externalGroups->sortKeysDesc() as $date => $group)
            @php
              $lines = collect();
              foreach($group as $es) {
                $lines = $lines->merge($es->recipes);
              }
              $sum = $lines->reduce(function($carry, $line){
                $unit     = $line->qty>0 ? $line->total_amount/$line->qty : 0;
                $returned = $line->returns->sum('qty') * $unit;
                return $carry + ($line->total_amount - $returned);
              }, 0);
            @endphp
            <tr class="bg-light group-header text-center" data-date="{{ $date }}">
              <td class="toggle-arrow" style="cursor:pointer">
                <i class="bi bi-caret-right-fill"></i>
              </td>
              <td colspan="5" class="text-start">{{ $date }} ({{ $lines->count() }} righe)</td>
              <td class="text-end fw-semibold">€{{ number_format($sum,2) }}</td>
            </tr>
            @foreach($group as $es)
              @foreach($es->recipes as $line)
                @php
                  $unit     = $line->qty>0 ? $line->total_amount/$line->qty : 0;
                  $rQty     = $line->returns->sum('qty');
                  $netTotal = $line->total_amount - $rQty * $unit;
                @endphp
                <tr class="group-{{ $date }} d-none text-center"
                    data-date="{{ $date }}"
                    data-recipe="{{ strtolower($line->recipe->recipe_name) }}"
                    data-category="{{ strtolower($line->recipe->category->name ?? '') }}"
                    data-department="{{ strtolower($line->recipe->department->name ?? '') }}"
                    data-returns="{{ $rQty }}"
                    data-qty="{{ $line->qty }}"
                    data-total="{{ $netTotal }}">
                  <td></td>
                  <td>{{ $es->supply_date->format('Y-m-d') }}</td>
                  <td>{{ $es->client->name }}</td>
                  <td>{{ $line->recipe->recipe_name }}</td>
                  <td>{{ $rQty }}</td>
                  <td>{{ $line->qty }}</td>
                  <td class="text-end">€{{ number_format($netTotal,2) }}</td>
                </tr>
              @endforeach
            @endforeach
          @endforeach
        </tbody>
        <tfoot class="table-light text-center">
          <tr>
            <th colspan="4" class="text-end">Totale Generale:</th>
            <th id="externalReturnsFooter" class="text-center">0</th>
            <th id="externalQtyFooter" class="text-center">0</th>
            <th id="externalFooter" class="text-end">0,00</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const fromIn   = document.getElementById('filter-from');
  const toIn     = document.getElementById('filter-to');
  const recIn    = document.getElementById('filter-recipe');
  const catIn    = document.getElementById('filter-category');
  const deptIn   = document.getElementById('filter-department');

  const sumShowEl    = document.getElementById('summary-showcase');
  const pctShowEl    = document.getElementById('summary-showcase-pct');
  const sumExtEl     = document.getElementById('summary-external');
  const pctExtEl     = document.getElementById('summary-external-pct');
  const footerShowEl = document.getElementById('showcaseFooter');
  const qtyShowEl    = document.getElementById('showcaseQtyFooter');
  const soldShowEl   = document.getElementById('showcaseSoldFooter');
  const reuseShowEl  = document.getElementById('showcaseReuseFooter');
  const wasteShowEl  = document.getElementById('showcaseWasteFooter');
  const footerExtEl  = document.getElementById('externalFooter');
  const retExtEl     = document.getElementById('externalReturnsFooter');
  const qtyExtEl     = document.getElementById('externalQtyFooter');

  function applyFilter() {
    const from  = fromIn.value;
    const to    = toIn.value;
    const rf    = recIn.value.trim().toLowerCase();
    const cf    = catIn.value.trim().toLowerCase();
    const df    = deptIn.value.trim().toLowerCase();

    let showSum   = 0,
        qtySum    = 0,
        soldSum   = 0,
        reuseSum  = 0,
        wasteSum  = 0,
        extSum    = 0,
        retSum    = 0,
        extQtySum = 0;

    function test(row, field, filterVal, exact = false) {
      const v = (row.dataset[field] || '').toLowerCase();
      if (!filterVal) return true;
      return exact ? (v === filterVal) : v.includes(filterVal);
    }

    // Showcase detail rows
    document.querySelectorAll('.showcaseTable .group-header').forEach(header => {
      const date = header.dataset.date;
      document.querySelectorAll(`.showcaseTable .group-${date}`).forEach(row => {
        const okDate = (!from || row.dataset.date >= from) &&
                       (!to   || row.dataset.date <= to);
        const okRec  = test(row, 'recipe', rf);
        const okCat  = test(row, 'category', cf, true);
        const okDep  = test(row, 'department', df, true);
        const show   = okDate && okRec && okCat && okDep;
        row.classList.toggle('d-none', !show);
        if (show) {
          qtySum   += +row.dataset.qty    || 0;
          soldSum  += +row.dataset.sold   || 0;
          reuseSum += +row.dataset.reuse  || 0;
            wasteSum += +row.dataset.waste  || 0;
          showSum  += +row.dataset.revenue|| 0;
        }
      });
    });

    // External detail rows
    document.querySelectorAll('.externalTable .group-header').forEach(header => {
      const date = header.dataset.date;
      document.querySelectorAll(`.externalTable .group-${date}`).forEach(row => {
        const okDate = (!from || row.dataset.date >= from) &&
                       (!to   || row.dataset.date <= to);
        const okRec  = test(row, 'recipe', rf);
        const okCat  = test(row, 'category', cf, true);
        const okDep  = test(row, 'department', df, true);
        const show   = okDate && okRec && okCat && okDep;
        row.classList.toggle('d-none', !show);
        if (show) {
          retSum    += +row.dataset.returns|| 0;
          extQtySum += +row.dataset.qty    || 0;
          extSum    += +row.dataset.total  || 0;
        }
      });
    });

    // Update summaries
    const grand = showSum + extSum;
    sumShowEl.textContent = showSum.toFixed(2);
    pctShowEl.textContent = grand ? Math.round(showSum * 100 / grand) + '%' : '0%';
    sumExtEl .textContent = extSum.toFixed(2);
    pctExtEl .textContent = grand ? Math.round(extSum * 100 / grand) + '%' : '0%';

    // Update footers
    qtyShowEl.textContent   = qtySum;
    soldShowEl.textContent  = soldSum;
    reuseShowEl.textContent = reuseSum;
    wasteShowEl.textContent = wasteSum;
    footerShowEl.textContent= showSum.toFixed(2);

    retExtEl .textContent   = retSum;
    qtyExtEl .textContent   = extQtySum;
    footerExtEl.textContent = extSum.toFixed(2);
  }

  // Wire up filter inputs
  [fromIn, toIn, recIn, catIn, deptIn].forEach(el => {
    el.addEventListener('input',  applyFilter);
    el.addEventListener('change', applyFilter);
  });

  // Initial run
  applyFilter();

  // Collapse detail‐rows by default
  document.querySelectorAll('.showcaseTable tr[class^="group-"]').forEach(r => r.classList.add('d-none'));
  document.querySelectorAll('.externalTable tr[class^="group-"]').forEach(r => r.classList.add('d-none'));

  // Toggle details when header clicked
  document.querySelectorAll('.toggle-arrow').forEach(btn => {
    btn.addEventListener('click', () => {
      const tr   = btn.closest('tr');
      const date = tr.dataset.date;
      const icon = btn.querySelector('i');
      document.querySelectorAll(`.group-${date}`).forEach(r => {
        r.classList.toggle('d-none');
      });
      icon.classList.toggle('bi-caret-right-fill');
      icon.classList.toggle('bi-caret-down-fill');
    });
  });

  // =========================
  // 2‑STATE SORT (asc/desc) + session persistence for BOTH tables
  // =========================
  function makeTwoStateSortable(tableId, storageKey) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');
    if (!thead || !tbody) return;

    function isDetailRow(tr) {
      return !tr.classList.contains('group-header');
    }

    function parseVal(txt) {
      const cleaned = txt.replace(/€/g,'').replace(/,/g,'').trim();
      const num = parseFloat(cleaned);
      if (!isNaN(num) && cleaned !== '') return num;
      return txt.toLowerCase();
    }

    function collectDetailRows() {
      return Array.from(tbody.querySelectorAll('tr')).filter(isDetailRow);
    }

    function sortRows(colIndex, dir) {
      const rows = collectDetailRows();
      rows.sort((a,b) => {
        const A = parseVal(a.children[colIndex].textContent);
        const B = parseVal(b.children[colIndex].textContent);
        if (typeof A === 'number' && typeof B === 'number') {
          return dir === 'asc' ? A - B : B - A;
        }
        if (A < B) return dir === 'asc' ? -1 : 1;
        if (A > B) return dir === 'asc' ? 1 : -1;
        return 0;
      });
      // Re-append ONLY detail rows in new order, keep group headers at top (their order untouched)
      const all = Array.from(tbody.querySelectorAll('tr'));
      const headers = all.filter(tr => !isDetailRow(tr));
      tbody.innerHTML = '';
      headers.forEach(h => tbody.appendChild(h));
      rows.forEach(r => tbody.appendChild(r));
    }

    // Restore previous state
    try {
      const saved = sessionStorage.getItem(storageKey);
      if (saved) {
        const { col, dir } = JSON.parse(saved);
        if (col != null && dir) {
          sortRows(col, dir);
          // Add indicator
          const th = thead.querySelectorAll('th')[col];
            if (th) th.setAttribute('data-sort-dir', dir);
        }
      }
    } catch(e){}

    thead.querySelectorAll('th').forEach((th, idx) => {
      if (!th.classList.contains('sortable')) return;
      th.style.cursor = 'pointer';
      th.addEventListener('click', () => {
        const current = th.getAttribute('data-sort-dir');
        const newDir = current === 'asc' ? 'desc' : 'asc';

        // Clear others
        thead.querySelectorAll('th.sortable').forEach(h => {
          if (h !== th) h.removeAttribute('data-sort-dir');
        });

        th.setAttribute('data-sort-dir', newDir);
        sortRows(idx, newDir);

        // Persist
        try {
          sessionStorage.setItem(storageKey, JSON.stringify({ col: idx, dir: newDir }));
        } catch(e){}
      });
    });
  }

  makeTwoStateSortable('showcaseTable', 'records_showcase_sort');
  makeTwoStateSortable('externalTable',  'records_external_sort');
});
</script>
@endsection
