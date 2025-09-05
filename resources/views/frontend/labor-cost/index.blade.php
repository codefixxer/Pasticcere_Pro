@extends('frontend.layouts.app')

@section('title','Calcolatore del Costo del Lavoro e BEP')

@php
  $lc = optional($laborCost);
  $ALL_BUCKETS = [
    'electricity'      => 'Elettricità (€)',
    'ingredients'      => 'Materie Prime (€)',
    'leasing_loan'     => 'Affitto/Mutuo/Prestito (€)',
    'packaging'        => 'Imballaggio (€)',
    'owner'            => 'Proprietario (€)',
    'van_rental'       => 'Noleggio Furgone (€)',
    'chefs'            => 'Pasticceri (€)',
    'shop_assistants'  => 'Addetti alla Vendita (€)',
    'other_salaries'   => 'Altri Stipendi (€)',
    'taxes'            => 'Tasse (€)',
    'other_categories' => 'Altre Categorie (€)',
    'driver_salary'    => 'Stipendi fornitura esterna (€)',
  ];
  $SHARED_ONLY = [
    'electricity','leasing_loan','owner','van_rental','taxes','shop_assistants',
  ];
@endphp

@section('content')
<div class="container py-5">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger mb-4">
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form id="laborCostForm" method="POST" action="{{ route('labor-cost.store') }}">
    @csrf
    @if(!empty($editingId))
      <input type="hidden" name="editing_id" id="editing_id" value="{{ $editingId }}">
    @else
      <input type="hidden" name="editing_id" id="editing_id" value="">
    @endif

    <div class="card shadow-sm">
      <div class="card-header d-flex align-items-center" style="background-color:#041930; color:#e2ae76;">
        <i class="bi bi-calculator fs-5 me-2"></i>
        <h5 class="mb-0 fw-bold">
          {{ $editingId ? 'Modifica Costo Lavoro' : 'Calcolatore del Costo del Lavoro e Punto di Pareggio' }}
        </h5>
      </div>

      <div class="card-body">

        <div class="alert alert-info d-flex align-items-center" role="alert">
          <i class="bi bi-graph-up-arrow me-2"></i>
          <div>
            <strong>BEP Globale</strong>: Mensile €{{ number_format($globalMonthlyBEP ?? 0, 2) }}
            — Giornaliero €{{ number_format($globalDailyBEP ?? 0, 2) }}
          </div>
        </div>

        @if(isset($departments) && $departments->count())
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Ambito</label>
              <select name="department_id" id="departmentSelect" class="form-select">
                <option value="">— Condiviso per tutto il gruppo —</option>
                @foreach ($departments as $dept)
                  <option value="{{ $dept->id }}"
                    @selected(old('department_id', $lc->department_id) == $dept->id)>
                    {{ $dept->name }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">
                Scegli <em>Condiviso</em> per modificare i costi globali; scegli un reparto per salvare un override.
              </small>
            </div>

            <div class="col-md-6">
              <label class="form-label">Incidenza reparto (%) — opzionale</label>
              <div class="input-group">
                <input type="number" step="0.01" min="0" max="100"
                       id="incidencePct"
                       name="incidence_pct"
                       class="form-control"
                       value="{{ old('incidence_pct', optional($lc->department)->share_percent) }}">
                <span class="input-group-text">%</span>
              </div>
              <small class="text-muted">
                Se vuoto: 100%.
              </small>
            </div>
          </div>
          <hr>
        @endif

<div class="row g-3 mb-3">
  <div class="col-md-6">
    <label class="form-label">Giorni di Apertura (questo mese)</label>
    <input type="number" id="openDays" name="opening_days" class="form-control" min="1"
           value="{{ old('opening_days', $lc->opening_days ?? 22) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">Ore di Apertura / Giorno</label>
    <input type="number" id="hoursPerDay" name="hours_per_day" class="form-control" min="0"
           value="{{ old('hours_per_day', $lc->hours_per_day ?? 8) }}">
  </div>
</div>
<hr>

<div class="mb-3">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h6 class="mb-0">Voci di Reparto</h6>
    <small class="text-muted">Specifiche del reparto</small>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Numero di Pasticceri</label>
      <div class="input-group">
        <input type="number" id="numChefs" name="num_chefs" class="form-control"
               min="0.1" step="0.1"
               value="{{ old('num_chefs', $lc->num_chefs ?? 1) }}">
      </div>
      <small class="text-muted">Specifico del reparto</small>
    </div>

    @foreach ($ALL_BUCKETS as $field => $label)
      @continue(in_array($field, $SHARED_ONLY, true))
      @php
        $isShared = false;
        $value = old($field, $lc->$field ?? 0);
        $sharedValue = optional($sharedCost)->$field ?? 0;
      @endphp
      <div class="col-md-4">
        <label class="form-label">{{ $label }}</label>
        <div class="input-group">
          <input type="number" step="0.01"
                 id="{{ $field }}"
                 name="{{ $field }}"
                 class="form-control cost-input dept-bucket"
                 data-shared="0"
                 value="{{ $value }}"
                 data-shared-default="{{ $sharedValue }}">
          <span class="input-group-text">€</span>
        </div>
      </div>
    @endforeach

  </div>
</div>

        <div class="mb-3 p-3 border rounded-3 bg-light-subtle">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0">Voci Condivise <small class="text-secondary">(comuni al gruppo)</small></h6>
            <small class="text-muted">
              Modificabili solo in ambito Condiviso.
            </small>
          </div>
          <div class="row g-3">
            @foreach ($ALL_BUCKETS as $field => $label)
              @continue(!in_array($field, $SHARED_ONLY, true))
              @php
                $isShared = true;
                $value = old($field, $lc->$field ?? 0);
                $sharedValue = optional($sharedCost)->$field ?? 0;
              @endphp
              <div class="col-md-4">
                <label class="form-label">{{ $label }}</label>
                <div class="input-group">
                  <input type="number" step="0.01"
                        id="{{ $field }}"
                        name="{{ $field }}"
                        class="form-control cost-input shared-bucket"
                        data-shared="1"
                        value="{{ $value }}"
                        data-shared-default="{{ $sharedValue }}">
                  <span class="input-group-text">€</span>
                </div>
                <small class="text-muted">Campo condiviso</small>
              </div>
            @endforeach
          </div>
        </div>

        <hr>

        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label">BEP Mensile (Globale)</label>
            <div class="input-group">
              <input type="text" class="form-control bg-light" readonly
                     value="{{ number_format($globalMonthlyBEP ?? 0, 2) }}">
              <span class="input-group-text">€</span>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">BEP Giornaliero (Globale)</label>
            <div class="input-group">
              <input type="text" class="form-control bg-light" readonly
                     value="{{ number_format($globalDailyBEP ?? 0, 2) }}">
              <span class="input-group-text">€</span>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Costo del lavoro al minuto (Interno)</label>
            <div class="input-group">
              <input type="text" id="shopCostPerMin" name="shop_cost_per_min" class="form-control bg-light" readonly
                     value="{{ old('shop_cost_per_min', $lc->shop_cost_per_min) }}">
              <span class="input-group-text">€</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Costo del lavoro al minuto (Esterno)</label>
            <div class="input-group">
              <input type="text" id="externalCostPerMin" name="external_cost_per_min" class="form-control bg-light" readonly
                     value="{{ old('external_cost_per_min', $lc->external_cost_per_min) }}">
              <span class="input-group-text">€</span>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-gold-blue btn-lg">
            <i class="bi bi-save2 me-1"></i> {{ $editingId ? 'Aggiorna' : 'Salva' }}
          </button>
          @if($editingId)
            <a href="{{ route('labor-cost.index') }}#laborCostForm" class="btn btn-outline-secondary btn-lg">
              Annulla modifica
            </a>
          @endif
        </div>

      </div>
    </div>
  </form>

  <div class="card mt-4 shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color:#041930; color:#e2ae76;">
      <i class="bi bi-list-check fs-5 me-2"></i>
      <h5 class="mb-0 fw-bold">Tutti i Costo Lavoro (questo gruppo)</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table  data-page-length="25"class="table table-hover align-middle">
          <thead>
            <tr>
              <th style="width:22%">Reparto</th>
              <th class="text-center">Incidenza (%)</th>
              <th class="text-center">Giorni</th>
              <th class="text-center">Ore/gg</th>
              <th class="text-center">Chef</th>
              <th class="text-end">€/min (Interno)</th>
              <th class="text-end">€/min (Esterno)</th>
              <th class="text-end">Aggiornato</th>
              <th class="text-center" style="width: 180px;">Azioni</th>
            </tr>
          </thead>
          <tbody>
            @forelse($allCosts as $row)
              @php $dept = $row->department; @endphp
              <tr>
                <td>
                  @if ($row->department_id)
                    <span class="badge bg-info-subtle text-dark me-1">Override</span>
                    {{ $dept?->name ?? '—' }}
                  @else
                    <span class="badge bg-primary-subtle text-dark me-1">Condiviso</span>
                    <em>Per tutto il gruppo</em>
                  @endif
                </td>
                <td class="text-center">
                  {{ optional($dept)->share_percent !== null ? number_format($dept->share_percent,2) : '—' }}
                </td>
                <td class="text-center">{{ $row->opening_days }}</td>
                <td class="text-center">{{ $row->hours_per_day }}</td>
                <td class="text-center">{{ $row->num_chefs }}</td>
                <td class="text-end">{{ number_format($row->shop_cost_per_min ?? 0, 4) }}</td>
                <td class="text-end">{{ number_format($row->external_cost_per_min ?? 0, 4) }}</td>
                <td class="text-end">{{ $row->updated_at?->format('Y-m-d H:i') }}</td>
                <td class="text-center">
                  <a href="{{ route('labor-cost.index', ['edit' => $row->id]) }}#laborCostForm"
                     class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil-square"></i> Modifica
                  </a>
                  <form action="{{ route('labor-cost.destroy', $row) }}" method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Eliminare questo record?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i> Elimina
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">Nessun record ancora.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

<style>
  .btn-gold-blue {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: 1px solid #e2ae76 !important;
  }
  .btn-gold-blue i { color: #041930 !important; }
  .btn-gold-blue:hover {
    background-color: #d89d5c !important;
    color: white !important;
  }
  .form-control[disabled] {
    background-color: #f6f7fb !important;
    opacity: 1;
  }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const byId = id => document.getElementById(id);
  const deptSelect   = byId('departmentSelect');
  const incidencePct = byId('incidencePct');
  const numChefs     = byId('numChefs');
  const openDays     = byId('openDays');
  const hoursPerDay  = byId('hoursPerDay');
  const editingId    = byId('editing_id');
  const costInputs  = Array.from(document.querySelectorAll('.cost-input'));
  const shopEl      = byId('shopCostPerMin');
  const externalEl  = byId('externalCostPerMin');
  const SHARED_KEYS = ['electricity','leasing_loan','owner','van_rental','taxes','shop_assistants'];
  const FIELD_NAMES = [
    'num_chefs','opening_days','hours_per_day',
    'electricity','ingredients','leasing_loan','packaging','owner','van_rental','chefs',
    'shop_assistants','other_salaries','taxes','other_categories','driver_salary'
  ];
  const getSharedDefaults = () => {
    const map = {};
    costInputs.forEach(el => {
      const key = el.id;
      const val = parseFloat(el.dataset.sharedDefault || '0') || 0;
      map[key] = val;
    });
    return map;
  };
  const SHARED_DEFAULTS = getSharedDefaults();
  const isDeptSelected = () => !!(deptSelect && deptSelect.value);
  function applySharedLock(){
    const deptMode = isDeptSelected();
    costInputs.forEach(el => {
      const isShared = SHARED_KEYS.includes(el.id);
      if (deptMode && isShared) {
        el.value = (SHARED_DEFAULTS[el.id] ?? 0).toFixed(2);
        el.disabled = true;
      } else {
        el.disabled = false;
      }
    });
    recalcRates();
  }
  function recalcRates(){
    const days  = Math.max(1, parseInt(openDays.value)||1);
    const mins  = days * (parseFloat(hoursPerDay.value)||0) * 60;
    const chefs = Math.max(0.1, parseFloat(numChefs.value)||0.1);
    const deptOnly = costInputs.reduce((sum, el) => sum + (el.disabled ? 0 : (parseFloat(el.value)||0)), 0);
    let sharedShare = 0;
    if (isDeptSelected()) {
      const share = Math.max(0, parseFloat(incidencePct.value || '100')) / 100;
      SHARED_KEYS.forEach(k => { sharedShare += (SHARED_DEFAULTS[k] || 0) * share; });
    }
    const total = deptOnly + sharedShare;
    const getEnableVal = key => {
      const el = byId(key);
      if (!el) return 0;
      if (isDeptSelected() && SHARED_KEYS.includes(key)) return 0;
      return parseFloat(el.value)||0;
    };
    const ing = getEnableVal('ingredients'),
          van = getEnableVal('van_rental'),
          drv = getEnableVal('driver_salary');
    const saShare = isDeptSelected()
      ? (SHARED_DEFAULTS['shop_assistants'] || 0) * (Math.max(0, parseFloat(incidencePct.value || '100')) / 100)
      : getEnableVal('shop_assistants');
    const shopOfficial     = mins > 0 ? (total - ing - van - drv) / mins / chefs : 0;
    const externalOfficial = mins > 0 ? (total - ing - saShare) / mins / chefs : 0;
    shopEl.value     = (shopOfficial / 3 * 4).toFixed(4);
    externalEl.value = (externalOfficial / 3 * 4).toFixed(4);
  }
  async function fetchAndFill(deptId){
    const url = new URL('{{ route('labor-cost.show', 0) }}', window.location.origin);
    if (deptId) url.searchParams.set('department_id', deptId);
    try {
      const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      byId('numChefs').value    = data.num_chefs ?? 1;
      byId('openDays').value    = data.opening_days ?? 22;
      byId('hoursPerDay').value = data.hours_per_day ?? 8;
      incidencePct.value        = (data.incidence_pct ?? '');
      FIELD_NAMES.forEach(f => {
        const el = byId(f);
        if (el && f !== 'num_chefs' && f !== 'opening_days' && f !== 'hours_per_day') {
          el.value = (data[f] ?? 0);
        }
      });
      editingId.value = data.id || '';
      applySharedLock();
      recalcRates();
    } catch (e) {
      console.error(e);
    }
  }
  [deptSelect, incidencePct, numChefs, openDays, hoursPerDay, ...costInputs].forEach(el => {
    if (!el) return;
    el.addEventListener('input', () => {
      if (el === deptSelect) { fetchAndFill(deptSelect.value || null); return; }
      recalcRates();
    });
    el.addEventListener('change', () => {
      if (el === deptSelect) { fetchAndFill(deptSelect.value || null); return; }
      recalcRates();
    });
  });
  applySharedLock();
  recalcRates();
  if (!'{{ $editingId ?? "" }}' && deptSelect && deptSelect.value) {
    fetchAndFill(deptSelect.value);
  }
  if (window.location.hash === '#laborCostForm') {
    document.getElementById('laborCostForm')?.scrollIntoView({behavior:'smooth'});
  }
});
</script>
@endsection
