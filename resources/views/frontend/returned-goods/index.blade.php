@extends('frontend.layouts.app')

@section('title','Prodotti  Forniture Esterne & Resi
')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Header -->
  <div class="page-header d-flex align-items-center mb-4"
       style="background-color: #041930; border-radius: .75rem; padding: 1rem 2rem;">
    <iconify-icon
      icon="mdi:truck-delivery"
      class="me-2"
      style="width: 1.5em; height: 1.0em; color: #e2ae76; font-size: 1.7vw;">
    </iconify-icon>
    <h4 class="mb-0 fw-bold" style="color: #e2ae76;"> Forniture Esterne & Resi </h4>
  </div>

  {{-- Filters --}}
  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
      <label class="form-label">Cliente</label>
      <select name="client_id" class="form-select">
        <option value="">Tutti i clienti</option>
        @foreach($clients as $c)
          <option value="{{ $c->id }}" {{ request('client_id')==$c->id?'selected':'' }}>
            {{ $c->name }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Da</label>
      <input type="date" name="start_date" class="form-control"
             value="{{ request('start_date') }}">
    </div>
    <div class="col-md-3">
      <label class="form-label">A</label>
      <input type="date" name="end_date" class="form-control"
             value="{{ request('end_date') }}">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button
        type="submit"
        class="btn w-100"
        style="
          background-color: #e2ae76;
          border: 2px solid #e2ae76;
          color: #041930;
        "
      >
        <i class="bi bi-funnel me-1" style="color: #041930;"></i>
        Applica Filtri
      </button>
    </div>
  </form>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card h-100" style="background-color: #e2ae76;">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-box-seam fs-2 me-3" style="color: #041930;"></i>
            <div>
              <div class="fs-5" style="color: #041930;">Totale Forniture</div>
              <div class="fs-4" style="color: #041930;">€{{ number_format($grandSupply,2) }}</div>
            </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100" style="background-color: #dc3545;">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-arrow-counterclockwise fs-2 me-3" style="color: #ffffff;"></i>
          <div>
            <div class="fs-5" style="color: #ffffff;">Totale Resi</div>
            <div class="fs-4" style="color: #ffffff;">€{{ number_format($grandReturn, 2) }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100" style="background-color: #041930;">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-cash-stack fs-2 me-3" style="color: #e2ae76;"></i>
          <div>
            <div class="fs-5" style="color: #e2ae76;">Incasso Totale</div>
            <div class="fs-4" style="color: #e2ae76;">€{{ number_format($grandNet,2) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- All Supplies vs Returns --}}
  <div class="row g-4">
    {{-- A) Supplies Table --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-dark text-gold fw-bold" style="color: #e2ae76">Tutte le Forniture</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table  data-page-length="25"id="suppliesTable" class="table table-striped mb-0">
              <thead class="bg-gold text-dark">
                <tr>
                  <th>Data</th><th>Cliente</th><th>Fornitura (€)</th>
                  <th>Reso (€)</th><th class="text-end">Azioni</th>
                </tr>
              </thead>
              <tbody>
                @forelse($supplies as $s)
                  <tr>
                    <td>{{ $s->supply_date->format('Y-m-d') }}</td>
                    <td>{{ $s->client->name }}</td>
                    <td>€{{ number_format($s->total_amount,2) }}</td>
                    <td>€{{ number_format($returnsBySupply[$s->id] ?? 0,2) }}</td>
                    <td class="text-end">
                      <a href="{{ route('returned-goods.create',['external_supply_id'=>$s->id]) }}"
                         class="btn btn-sm btn-outline-warning me-1" title="Aggiungi Reso">
                        <i class="bi bi-arrow-counterclockwise"></i>
                      </a>
                      <a href="{{ route('external-supplies.show',$s->id) }}"
                         class="btn btn-sm btn-outline-info me-1" title="Visualizza">
                        <i class="bi bi-eye"></i>
                      </a>
                      <a href="{{ route('external-supplies.edit',$s->id) }}"
                         class="btn btn-sm btn-outline-primary me-1" title="Modifica">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <form action="{{ route('external-supplies.destroy',$s->id) }}"
                            method="POST" class="d-inline"
                            onsubmit="return confirm('Eliminare questa fornitura?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Elimina">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">Nessuna fornitura trovata.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- B) Daily Comparison --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-dark text-gold fw-bold" style="color:#e2ae76">
          Confronto Giornaliero (Fornito - Reso)
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table  data-page-length="25"id="dailyCompareTable" class="table table-striped mb-0">
              <thead class="bg-gold text-dark">
                <tr>
                  <th>Data</th>
                  <th>Fornitura (€)</th>
                  <th>Reso (€)</th>
                  <th>Netto (€)</th>
                </tr>
              </thead>
              <tbody>
                @forelse($supsByDate as $d)
                  @php
                    $net = $d->total_supply - $d->total_return;
                  @endphp
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($d->date)->format('Y-m-d') }}</td>
                    <td>€{{ number_format($d->total_supply, 2) }}</td>
                    <td class="{{ $d->total_return > 0 ? 'text-danger' : 'text-success' }}">
                      €{{ number_format($d->total_return, 2) }}
                    </td>
                    <td class="{{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                      €{{ number_format($net, 2) }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted">Nessun dato da confrontare.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<style>
  .btn-gold-blue {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: 1px solid #e2ae76;
  }
  .btn-gold-blue:hover {
    background-color: #d89d5c !important;
    color: white !important;
  }
  /* Make headers look clickable */
  #suppliesTable thead th,
  #dailyCompareTable thead th {
    cursor: pointer;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const clientSelect = document.querySelector('[name="client_id"]');
  const startDate    = document.querySelector('[name="start_date"]');
  const endDate      = document.querySelector('[name="end_date"]');

  const updateFilters = () => {
    const params = new URLSearchParams(window.location.search);
    params.set('client_id', clientSelect.value || '');
    params.set('start_date', startDate.value || '');
    params.set('end_date', endDate.value || '');
    window.location.search = params.toString();
  };

  clientSelect.addEventListener('change', updateFilters);
  startDate.   addEventListener('change', updateFilters);
  endDate.     addEventListener('change', updateFilters);

  // =========================
  // 2-STATE SORT + SESSION KEEP
  // =========================
  // Generic sorter applied to both tables (without any external libs).
  function makeTwoStateSortable(tableId, storageKey) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    function parseCell(text) {
      const clean = text.replace(/€/g,'').replace(/,/g,'').trim();
      const num = parseFloat(clean);
      return isNaN(num) ? text.trim().toLowerCase() : num;
    }

    function sortBy(colIndex, dir) {
      const rows = Array.from(tbody.querySelectorAll('tr'));
      rows.sort((a,b) => {
        const A = parseCell(a.children[colIndex].textContent);
        const B = parseCell(b.children[colIndex].textContent);
        if (typeof A === 'number' && typeof B === 'number') {
          return dir === 'asc' ? A - B : B - A;
        }
        if (A < B) return dir === 'asc' ? -1 : 1;
        if (A > B) return dir === 'asc' ? 1 : -1;
        return 0;
      });
      rows.forEach(r => tbody.appendChild(r));
    }

    // Restore previous sort
    try {
      const saved = sessionStorage.getItem(storageKey);
      if (saved) {
        const { col, dir } = JSON.parse(saved);
        if (col != null && dir) sortBy(col, dir);
      }
    } catch(e){}

    table.querySelectorAll('thead th').forEach((th, index) => {
      // Skip "Azioni" columns (right-aligned actions)
      if (th.classList.contains('text-end')) return;

      th.addEventListener('click', () => {
        const current = th.getAttribute('data-sort-dir');
        const newDir = current === 'asc' ? 'desc' : 'asc';

        // Clear other headers' indicators
        table.querySelectorAll('thead th').forEach(h => {
          if (h !== th) h.removeAttribute('data-sort-dir');
        });

        th.setAttribute('data-sort-dir', newDir);
        sortBy(index, newDir);

        // Persist
        try {
          sessionStorage.setItem(storageKey, JSON.stringify({ col:index, dir:newDir }));
        } catch(e){}
      });
    });
  }

  makeTwoStateSortable('suppliesTable', 'supplies_sort_state');
  makeTwoStateSortable('dailyCompareTable', 'daily_compare_sort_state');
});
</script>
@endsection
