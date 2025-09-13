{{-- resources/views/frontend/costs/dashboard.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Mensile Costi e Ricavi')

@section('content')
@php 
    use \Carbon\Carbon; 
    Carbon::setLocale('it');
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
  .dashboard-container{background:#fff;border-radius:.5rem;padding:1.5rem;box-shadow:0 2px 10px rgba(0,0,0,.05)}
  .page-header-custom{font-size:1.5rem;font-weight:600;color:#041930}
  .month-tabs .nav-link{color:#041930;padding:.5rem .75rem;margin:0 .25rem;border-radius:.5rem;transition:background .2s}
  .month-tabs .nav-link:hover{background:#ececec}
  .month-tabs .nav-link.active{background:#e2ae76;color:#fff}
  .card-mini{display:inline-flex;flex-direction:column;align-items:center;background:#fff;border-radius:.75rem;box-shadow:0 4px 8px rgba(0,0,0,.1);padding:1rem;margin:.5rem;transition:transform .2s,box-shadow .2s;min-width:140px}
  .card-mini:hover{transform:translateY(-4px);box-shadow:0 6px 12px rgba(0,0,0,.15)}
  .card-mini-icon{font-size:1.8rem;color:#e2ae76;margin-bottom:.5rem}
  .card-mini-title{font-size:.85rem;color:#777;margin-bottom:.25rem;text-transform:capitalize}
  .card-mini-value{font-size:1.4rem;font-weight:600;color:#041930;white-space:nowrap}
  .table thead th{background-color:#e2ae76;color:#041930;text-align:center}
  .table td,.table th{text-align:center;vertical-align:middle}
  .summary-container{display:flex;flex-wrap:wrap;justify-content:center;gap:1rem;margin-top:1.5rem}
  .open-days-input{width:84px}
  .bep-cell{min-width:110px;white-space:nowrap}
</style>

<div class="container dashboard-container">

  <!-- Header + Year Selector -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-header-custom mb-0">
      <i class="bi bi-speedometer2 me-2"></i>
      {{ Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
    </h4>
    <div class="d-flex">
      <select id="yearSelector" class="form-select form-select-sm w-auto">
        @foreach($availableYears as $availableYear)
          <option value="{{ $availableYear }}" {{ $availableYear == $year ? 'selected' : '' }}>
            {{ $availableYear }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <!-- Month Tabs -->
  <ul class="nav nav-pills mb-4 month-tabs justify-content-center">
    @for($m = 1; $m <= 12; $m++)
      <li class="nav-item">
        <a class="nav-link {{ $m == $month ? 'active' : '' }}"
           href="{{ route('costs.dashboard', ['y' => $year, 'm' => $m]) }}">
          {{ Carbon::create($year, $m, 1)->translatedFormat('F') }}
        </a>
      </li>
    @endfor
  </ul>

  <!-- Category Summary -->
  <div class="summary-container">
    @foreach($categories as $cat)
      <div class="card-mini">
        <i class="bi bi-tag card-mini-icon"></i>
        <div class="card-mini-title">{{ $cat->name }}</div>
        <div class="card-mini-value">€{{ number_format($raw[$cat->id] ?? 0, 2) }}</div>
      </div>
    @endforeach
  </div>

  <!-- Monthly Comparison -->
  <div class="card shadow-sm mb-4 mt-4">
    <div class="card-header bg-dark text-white">
      <i class="bi bi-bar-chart-line me-2"></i>Confronto Mensile ({{ $year }})
    </div>
    <div class="card-body p-3">
      <div class="alert alert-info mb-4 small">
        <strong>Miglior mese:</strong>
          {{ Carbon::create($year, $bestMonth, 1)->translatedFormat('F') }} (€{{ number_format($bestNet, 2) }})
        &nbsp;&nbsp;
        <strong>Peggior mese:</strong>
          {{ $worstMonth ? Carbon::create($year, $worstMonth, 1)->translatedFormat('F') : '—' }}
          (€{{ number_format($worstNet, 2) }})
      </div>

      <div class="table-responsive small">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th rowspan="2">Mese</th>
              <th colspan="5">Anno Corrente ({{ $year }})</th>
              <th colspan="5">Anno Precedente ({{ $lastYear }})</th>
            </tr>
            <tr>
              <th>Costo (€)</th>
              <th>Ricavi (€)</th>
              <th>Netto (€)</th>
              <th>Giorni apertura</th>
              <th>BEP (€/giorno)</th>

              <th>Costo (€)</th>
              <th>Ricavi (€)</th>
              <th>Netto (€)</th>
              <th>Giorni apertura</th>
              <th>BEP (€/giorno)</th>
            </tr>
          </thead>
          <tbody>
            @for($m = 1; $m <= 12; $m++)
              @php
                $c1    = (float) ($costsThisYear[$m] ?? 0);
                $i1    = (float) ($incomeThisYearMonthly[$m] ?? 0);
                $n1    = $i1 - $c1;
                $days1 = (int) ($openingDaysThisYear[$m] ?? 0);
                $b1    = (float) ($bepThisYear[$m] ?? 0.0);

                $c2    = (float) ($costsLastYear[$m] ?? 0);
                $i2    = (float) ($incomeLastYearMonthly[$m] ?? 0);
                $n2    = $i2 - $c2;
                $days2 = (int) ($openingDaysLastYear[$m] ?? 0);
                $b2    = (float) ($bepLastYear[$m] ?? 0.0);
              @endphp
              <tr data-cost-current="{{ $c1 }}" data-cost-previous="{{ $c2 }}">
                <td class="text-start">{{ Carbon::create($year, $m, 1)->translatedFormat('F') }}</td>

                {{-- CURRENT YEAR --}}
                <td>€{{ number_format($c1, 2) }}</td>
                <td>€{{ number_format($i1, 2) }}</td>
                <td class="{{ $n1 >= 0 ? 'text-success' : 'text-danger' }}">€{{ number_format($n1, 2) }}</td>
                <td>
                  <input type="number" min="0" max="31" step="1"
                         class="form-control form-control-sm open-days-input"
                         value="{{ $days1 ?: '' }}"
                         data-year="{{ $year }}" data-month="{{ $m }}" data-scope="current">
                </td>
                <td class="bep-cell" id="bep-{{ $year }}-{{ $m }}">€{{ number_format($b1, 2) }}</td>

                {{-- PREVIOUS YEAR --}}
                <td>€{{ number_format($c2, 2) }}</td>
                <td>€{{ number_format($i2, 2) }}</td>
                <td class="{{ $n2 >= 0 ? 'text-success' : 'text-danger' }}">€{{ number_format($n2, 2) }}</td>
                <td>
                  <input type="number" min="0" max="31" step="1"
                         class="form-control form-control-sm open-days-input"
                         value="{{ $days2 ?: '' }}"
                         data-year="{{ $lastYear }}" data-month="{{ $m }}" data-scope="previous">
                </td>
                <td class="bep-cell" id="bep-{{ $lastYear }}-{{ $m }}">€{{ number_format($b2, 2) }}</td>
              </tr>
            @endfor

            <tr class="fw-bold bg-light">
              <td>Totale</td>

              {{-- CURRENT YEAR TOTALS --}}
              <td>€{{ number_format($totalCostYear, 2) }}</td>
              <td>€{{ number_format($totalIncomeYear, 2) }}</td>
              <td class="{{ $netYear >= 0 ? 'text-success' : 'text-danger' }}">€{{ number_format($netYear, 2) }}</td>
              <td id="sum-days-{{ $year }}">{{ $sumDaysThisYear }}</td>
              <td id="sum-bep-{{ $year }}">€{{ number_format($overallBepThisYear, 2) }}</td>

              {{-- PREVIOUS YEAR TOTALS --}}
              <td>€{{ number_format($totalCostLastYear, 2) }}</td>
              <td>€{{ number_format($totalIncomeLastYear, 2) }}</td>
              <td class="{{ $netLastYear >= 0 ? 'text-success' : 'text-danger' }}">€{{ number_format($netLastYear, 2) }}</td>
              <td id="sum-days-{{ $lastYear }}">{{ $sumDaysLastYear }}</td>
              <td id="sum-bep-{{ $lastYear }}">€{{ number_format($overallBepLastYear, 2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Bottom Summary Mini-Cards -->
      <div class="summary-container">
        <div class="card-mini border border-success">
          <i class="bi bi-wallet2 card-mini-icon text-success"></i>
          <div class="card-mini-title">Ricavi ({{ Carbon::create($year, $month, 1)->translatedFormat('F Y') }})</div>
          <div class="card-mini-value">€{{ number_format($incomeThisMonth, 2) }}</div>
        </div>
        <div class="card-mini border border-secondary">
          <i class="bi bi-wallet card-mini-icon text-secondary"></i>
          <div class="card-mini-title">Ricavi ({{ Carbon::create($lastYear, $month, 1)->translatedFormat('F Y') }})</div>
          <div class="card-mini-value">€{{ number_format($incomeLastYearSame, 2) }}</div>
        </div>
        <div class="card-mini border border-primary">
          <i class="bi bi-receipt card-mini-icon text-primary"></i>
          <div class="card-mini-title">Costi Totali ({{ $year }})</div>
          <div class="card-mini-value">€{{ number_format($totalCostYear, 2) }}</div>
        </div>
        <div class="card-mini border border-success">
          <i class="bi bi-cash-stack card-mini-icon text-success"></i>
          <div class="card-mini-title">Ricavi Totali ({{ $year }})</div>
          <div class="card-mini-value">€{{ number_format($totalIncomeYear, 2) }}</div>
        </div>
        <div class="card-mini border border-danger">
          <i class="bi bi-percent card-mini-icon text-danger"></i>
          <div class="card-mini-title">Netto ({{ $year }})</div>
          @php $netVal = $totalIncomeYear - $totalCostYear; @endphp
          <div class="card-mini-value {{ $netVal >= 0 ? 'text-success' : 'text-danger' }}">
            €{{ number_format($netVal, 2) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const baseUrl     = '{{ route('costs.dashboard') }}';
  const saveDaysUrl = '{{ route('costs.opening-days.save') }}';
  const csrf        = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const yearSelector  = document.getElementById('yearSelector');

  function navigateTo(year, month){
    window.location.href = `${baseUrl}?y=${year}&m=${month}`;
  }

  // Year change keeps the same month
  yearSelector?.addEventListener('change', function(){
    navigateTo(this.value, {{ $month }});
  });

  const n2   = v => (isFinite(+v) ? (+v).toFixed(2) : '0.00');
  const euro = v => `€${n2(v)}`;

  function recomputeTotalsFor(year){
    // sum opening days
    const inputs = document.querySelectorAll(`.open-days-input[data-year="${year}"]`);
    let sumDays = 0; inputs.forEach(i => sumDays += (parseInt(i.value,10)||0));
    const sumEl = document.getElementById(`sum-days-${year}`); if (sumEl) sumEl.textContent = String(sumDays);

    // total costs for that year (already in the rows as data-attrs)
    let totalCost = 0;
    document.querySelectorAll('tbody tr[data-cost-current]').forEach(tr=>{
      const c = (String(year) === '{{ $year }}')
        ? parseFloat(tr.getAttribute('data-cost-current') || '0')
        : parseFloat(tr.getAttribute('data-cost-previous') || '0');
      totalCost += isFinite(c) ? c : 0;
    });

    const bep = sumDays > 0 ? (totalCost / sumDays) : 0;
    const bepEl = document.getElementById(`sum-bep-${year}`); if (bepEl) bepEl.textContent = euro(bep);
  }

  document.querySelectorAll('.open-days-input').forEach(input=>{
    input.addEventListener('change', async e=>{
      const el    = e.currentTarget;
      const year  = parseInt(el.dataset.year,10);
      const month = parseInt(el.dataset.month,10);
      const scope = el.dataset.scope;
      const days  = parseInt(el.value || '0', 10);

      // row cost
      const tr   = el.closest('tr');
      const cost = scope === 'current'
        ? parseFloat(tr.getAttribute('data-cost-current') || '0')
        : parseFloat(tr.getAttribute('data-cost-previous') || '0');

      // update BEP cell immediately
      const bepCell = document.getElementById(`bep-${year}-${month}`);
      if (bepCell) bepCell.textContent = euro(days > 0 ? (cost / days) : 0);

      // totals
      recomputeTotalsFor(year);

      // persist via AJAX
      try{
        await fetch(saveDaysUrl,{
          method:'POST',
          headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-Requested-With':'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify({ year, month, days })
        });
      }catch(err){ console.error(err); }
    });
  });
});
</script>
@endsection
