{{-- resources/views/frontend/costs/dashboard.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Mensile Costi e Ricavi')

@section('content')
@php 
    use \Carbon\Carbon; 
    Carbon::setLocale('it');  // assicura Italiano per i mesi
@endphp

<style>
  /* Dashboard wrapper */
  .dashboard-container {
    background: #ffffff;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }

  /* Header */
  .page-header-custom {
    font-size: 1.5rem;
    font-weight: 600;
    color: #041930;
  }

  /* Month tabs */
  .month-tabs .nav-link {
    color: #041930;
    padding: 0.5rem 0.75rem;
    margin: 0 0.25rem;
    border-radius: 0.5rem;
    transition: background 0.2s;
  }
  .month-tabs .nav-link:hover {
    background: #ececec;
  }
  .month-tabs .nav-link.active {
    background: #e2ae76;
    color: #ffffff;
  }

  /* Mini-cards */
  .card-mini {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    padding: 1rem;
    margin: 0.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
    min-width: 140px;
    width: auto;
  }
  .card-mini:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
  }
  .card-mini-icon {
    font-size: 1.8rem;
    color: #e2ae76;
    margin-bottom: 0.5rem;
  }
  .card-mini-title {
    font-size: 0.85rem;
    color: #777;
    margin-bottom: 0.25rem;
    text-transform: capitalize;
  }
  .card-mini-value {
    font-size: 1.4rem;
    font-weight: 600;
    color: #041930;
    white-space: nowrap;
  }

  /* Table styling */
  .table thead th {
    background-color: #e2ae76;
    color: #041930;
    text-align: center;
  }
  .table td, .table th {
    text-align: center;
    vertical-align: middle;
  }

  /* Summary cards container */
  .summary-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
    margin-top: 1.5rem;
  }
</style>

<div class="container dashboard-container">

  <!-- Header + Year/Month Selector -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-header-custom mb-0">
      <i class="bi bi-speedometer2 me-2"></i>
      {{ Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
    </h4>
    <div class="d-flex">
      <select id="yearSelector" class="form-select form-select-sm me-2 w-auto">
        @foreach($availableYears as $availableYear)
          <option value="{{ $availableYear }}" {{ $availableYear == $year ? 'selected' : '' }}>
            {{ $availableYear }}
          </option>
        @endforeach
      </select>
      <input id="monthSelector"
             type="month"
             class="form-select form-select-sm w-auto"
             value="{{ sprintf('%04d-%02d',$year,$month) }}">
    </div>
  </div>

  <!-- Month Tabs -->
  <ul class="nav nav-pills mb-4 month-tabs justify-content-center">
    @for($m = 1; $m <= 12; $m++)
      <li class="nav-item">
        <a class="nav-link {{ $m == $month ? 'active' : '' }}"
           href="{{ route('costs.dashboard', ['y' => $year, 'm' => $m]) }}">
          {{-- Solo il nome del mese, es. "Maggio" --}}
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
          {{ $worstMonth
             ? Carbon::create($year, $worstMonth, 1)->translatedFormat('F')
             : '—' }} (€{{ number_format($worstNet, 2) }})
      </div>

      <div class="table-responsive small">
        <table  data-page-length="25"class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th>Mese</th>
              <th colspan="3">Anno Corrente ({{ $year }})</th>
              <th colspan="3">Anno Precedente ({{ $lastYear }})</th>
            </tr>
            <tr>
              <th></th>
              <th>Costo (€)</th><th>Ricavi (€)</th><th>Netto (€)</th>
              <th>Costo (€)</th><th>Ricavi (€)</th><th>Netto (€)</th>
            </tr>
          </thead>
          <tbody>
            @for($m = 1; $m <= 12; $m++)
              @php
                $c1 = $costsThisYear[$m] ?? 0;
                $i1 = $incomeThisYearMonthly[$m] ?? 0;
                $n1 = $i1 - $c1;
                $c2 = $costsLastYear[$m] ?? 0;
                $i2 = $incomeLastYearMonthly[$m] ?? 0;
                $n2 = $i2 - $c2;
              @endphp
              <tr>
                <td class="text-start">{{ Carbon::create($year, $m, 1)->translatedFormat('F') }}</td>
                <td>€{{ number_format($c1, 2) }}</td>
                <td>€{{ number_format($i1, 2) }}</td>
                <td class="{{ $n1 >= 0 ? 'text-success' : 'text-danger' }}">€{{ number_format($n1, 2) }}</td>
                <td>€{{ number_format($c2, 2) }}</td>
                <td>€{{ number_format($i2, 2) }}</td>
                <td class="{{ $n2 >= 0 ? 'text-success' : 'text-danger' }}">€{{ number_format($n2, 2) }}</td>
              </tr>
            @endfor
            <tr class="fw-bold bg-light">
              <td>Totale</td>
              <td>€{{ number_format($totalCostYear, 2) }}</td>
              <td>€{{ number_format($totalIncomeYear, 2) }}</td>
              <td class="{{ $netYear >= 0 ? 'text-success' : 'text-danger' }}">€{{ number_format($netYear, 2) }}</td>
              <td>€{{ number_format($totalCostLastYear, 2) }}</td>
              <td>€{{ number_format($totalIncomeLastYear, 2) }}</td>
              <td class="{{ $netLastYear >= 0 ? 'text-success' : 'text-danger' }}">€{{ number_format($netLastYear, 2) }}</td>
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
  const baseUrl       = '{{ route('costs.dashboard') }}';
  const yearSelector  = document.getElementById('yearSelector');
  const monthSelector = document.getElementById('monthSelector');

  function navigateTo(year, month) {
    window.location.href = `${baseUrl}?y=${year}&m=${month}`;
  }

  yearSelector.addEventListener('change', function() {
    const y = this.value;
    const m = monthSelector.value.split('-')[1];
    navigateTo(y, m);
  });

  monthSelector.addEventListener('change', function() {
    const [y, m] = this.value.split('-');
    navigateTo(y, m);
  });
});
</script>
@endsection
