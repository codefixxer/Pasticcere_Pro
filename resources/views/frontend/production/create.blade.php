{{-- resources/views/frontend/production/create.blade.php --}}
@extends('frontend.layouts.app')

@section('title', isset($production) ? 'Modifica Produzione' : 'Nuova Voce di Produzione')

@section('content')
<div class="container py-5">
  <div class="card border-primary shadow-sm">
    <!-- Header with custom blue background and gold text -->
    <div class="card-header d-flex align-items-center"
     style="background-color: #041930; color: #e2ae76; padding: .5rem; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
      <iconify-icon
        icon="mdi:factory"
        class="me-3"
        style=" height: 1.1em; color: #e2ae76; font-size:1.5vw;">
      </iconify-icon>
      <h5 class="mb-0" style="color: #e2ae76;">
        {{ isset($production) ? 'Modifica' : 'Crea' }} Voce di Produzione
      </h5>
    </div>

    <div class="card-body">
      <form method="POST"
            action="{{ isset($production) ? route('production.update', $production->id) : route('production.store') }}"
            id="productionForm">
        @csrf
        @if(isset($production)) @method('PUT') @endif

        {{-- Production Name --}}
        <div class="mb-4">
          <label for="production_name" class="form-label fw-semibold" id="productionNameLabel">Nome Produzione</label>
          <input type="text"
                 id="production_name"
                 name="production_name"
                 class="form-control"
                 value="{{ old('production_name', isset($production) ? $production->production_name : '') }}">
        </div>

        {{-- Template Selection --}}
        @if (!isset($production))
        <div class="mb-4">
          <label for="template_select" class="form-label fw-semibold">Scegli Modello</label>
          <select id="template_select" name="template_id" class="form-select">
            <option value="">-- Seleziona Modello --</option>
            @foreach($templates as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>
        @endif

        {{-- Production Date --}}
        <div class="mb-4">
          <label for="production_date" class="form-label fw-semibold">Data Produzione</label>
          <input type="date"
                 id="production_date"
                 name="production_date"
                 class="form-control"
                 value="{{ old('production_date', isset($production) ? $production->production_date : date('Y-m-d')) }}"
                 required>
        </div>

        {{-- Save As --}}
        <div class="mb-4 col-md-4 px-0">
          <label for="template_action" class="form-label fw-semibold">Salva come</label>
          <select id="template_action" name="template_action" class="form-select">
            @php
              $default = old('template_action', isset($production) ? ($production->save_template ? 'template' : 'none') : 'none');
            @endphp
            <option value="none"     {{ $default=='none' ? 'selected' : '' }}>Solo Salva</option>
            <option value="template" {{ $default=='template' ? 'selected' : '' }}>Salva come Modello</option>
           
          </select>
        </div>

        {{-- Details Table --}}
        <table  data-page-length="25"class="table table-bordered align-middle" id="recipeTable">
          <thead class="table-light">
            <tr>
              <th>Ricetta</th>
              <th> Pasticcere</th>
              <th>Qtà</th>
              <th>Tempo (min)</th>
              <th>Attrezzatura</th>
              <th>Ricavo (€)</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @php $details = $production->details ?? collect([null]); @endphp
            @foreach($details as $index => $d)
            <tr>
              <td>
                <select name="recipe_id[]" class="form-select recipe-select" required>
                  <option value="">Seleziona Ricetta</option>
                  @foreach($recipes as $r)
                  <option value="{{ $r->id }}"
                          data-price-kg="{{ $r->selling_price_per_kg }}"
                          data-price-piece="{{ $r->selling_price_per_piece }}"
                          data-sell-mode="{{ $r->sell_mode }}"
                          {{ isset($d) && $r->id == $d->recipe_id ? 'selected' : '' }}>
                    {{ $r->recipe_name }}
                  </option>
                  @endforeach
                </select>
              </td>
              <td>
                <select name="pastry_chef_id[]" class="form-select" required>
                  <option value="">Seleziona Chef</option>
                  @foreach($chefs as $c)
                    <option value="{{ $c->id }}" {{ isset($d) && $c->id == $d->pastry_chef_id ? 'selected' : '' }}>{{ $c->name }}</option>
                  @endforeach
                </select>
              </td>
              <td>
                <div class="input-group">
                  <input type="number"
                         name="quantity[]"
                         class="form-control quantity-input"
                         value="{{ $d->quantity ?? 1 }}"
                         min="1"
                         required>
                  <span class="input-group-text unit-label">/unità</span>
                </div>
              </td>
              <td>
                <input type="number"
                       name="execution_time[]"
                       class="form-control"
                       value="{{ $d->execution_time ?? '' }}"
                       min="1"
                       required>
              </td>
              <td>
                <div class="multi-equipment">
                  <div class="selected-equipment d-flex flex-wrap mb-2"></div>
                  <div class="input-group">
                    <select class="form-select equipment-select">
                      <option value="">Seleziona Attrezzatura</option>
                      @foreach($equipments as $e)
                        <option value="{{ $e->id }}">{{ $e->name }}</option>
                      @endforeach
                    </select>
                    <button type="button" class="btn btn-outline-secondary add-equipment-btn">Aggiungi</button>
                  </div>
                  <input type="hidden" name="equipment_ids[{{ $loop->index }}][]" class="equipment-hidden" value="{{ $d->equipment_ids ?? '' }}">
                </div>
              </td>
              <td>
                <input type="text"
                       name="potential_revenue[]"
                       class="form-control revenue-field"
                       value="{{ $d->potential_revenue ?? '0.00' }}"
                       readonly>
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>

        <div class="mb-3">
          <!-- Add Row button with gold border and blue text -->
          <button type="button"
                  id="addRowBtn"
                  class="btn"
                  style="border: 1px solid #e2ae76; color: #041930; background-color: transparent;">
            + Aggiungi Riga
          </button>
        </div>

        {{-- Total Revenue --}}
        <div class="mb-4">
          <label class="form-label fw-semibold">Ricavo Potenziale Totale (€)</label>
          <input type="text"
                 id="totalRevenue"
                 name="total_revenue"
                 class="form-control"
                 value="{{ old('total_revenue', isset($production) ? $production->total_potential_revenue : '0.00') }}"
                 readonly>
        </div>

        <div class="text-end">
          <!-- Save Production button with gold background and blue text -->
          <button type="submit"
                  class="btn btn-lg"
                  style="background-color: #e2ae76; color: #041930; border: none;">
            <i class="bi bi-save2 me-1"></i>
            {{ isset($production) ? 'Aggiorna' : 'Salva' }} Produzione
          </button>
        </div>
      </form>
    </div>
  </div>
</div>



<style>
  /* keep the Qty & Time columns from wrapping and respect the fixed width */
  #recipeTable th:nth-child(3),
  #recipeTable td:nth-child(3),
  #recipeTable th:nth-child(4),
  #recipeTable td:nth-child(4) {
    min-width: 10px;
    white-space: nowrap;
  }

  /* Recipe column min width */
  #recipeTable th:first-child,
  #recipeTable td:first-child {
    white-space: nowrap;
    min-width: 300px;
  }
  .selected-equipment span {
    background: #e9ecef;
    border-radius: 15px;
    padding: 4px 10px;
    margin-right: 5px;
    margin-bottom: 5px;
    font-size: 0.875rem;
  }
  .selected-equipment .remove-tag {
    color: #888;
    margin-left: 6px;
    cursor: pointer;
  }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tableBody         = document.querySelector('#recipeTable tbody');
  const addRowBtn         = document.getElementById('addRowBtn');
  const totalRevenueField = document.getElementById('totalRevenue');
  const templateSelect    = document.getElementById('template_select');
  const actionSelect      = document.getElementById('template_action');
  const nameLabel         = document.getElementById('productionNameLabel');
  const nameInput         = document.getElementById('production_name');
  const dateInput         = document.getElementById('production_date');

  let blankRow = tableBody.rows[0].cloneNode(true);
  let rowIndex = 0;

  function updateNameRequirement() {
    const v = actionSelect.value;
    nameLabel.textContent = (v === 'template' || v === 'both') ? 'Nome Modello' : 'Nome Produzione';
    nameInput.required = (v === 'template' || v === 'both');
  }

  actionSelect.addEventListener('change', updateNameRequirement);
  updateNameRequirement();

  function calculateRowRevenue(row) {
    const recipe     = row.querySelector('.recipe-select');
    const qtyInput   = row.querySelector('.quantity-input');
    const unitLabel  = row.querySelector('.unit-label');
    const priceKg    = +recipe.selectedOptions[0]?.dataset.priceKg || 0;
    const pricePiece = +recipe.selectedOptions[0]?.dataset.pricePiece || 0;
    const mode       = recipe.selectedOptions[0]?.dataset.sellMode || 'piece';
    const qty        = +qtyInput.value || 0;
    const price      = mode === 'kg' ? priceKg : pricePiece;

    unitLabel.textContent = '/' + (mode === 'kg' ? 'kg' : 'pz');
    row.querySelector('.revenue-field').value = (qty * price).toFixed(2);
    calculateTotalRevenue();
  }

  function calculateTotalRevenue() {
    let sum = 0;
    document.querySelectorAll('.revenue-field').forEach(i => sum += +i.value || 0);
    totalRevenueField.value = sum.toFixed(2);
  }

  function attachRowEvents(row, idx) {
    const recipeSelect = row.querySelector('.recipe-select');
    const qtyInput     = row.querySelector('.quantity-input');
    const removeBtn    = row.querySelector('.remove-row');
    const equipmentSel = row.querySelector('.equipment-select');
    const equipmentBtn = row.querySelector('.add-equipment-btn');
    const selectedCont = row.querySelector('.selected-equipment');
    const hiddenInput  = row.querySelector('.equipment-hidden');

    function renderTags() {
      selectedCont.innerHTML = '';
      (hiddenInput.value ? hiddenInput.value.split(',') : []).forEach(id => {
        const opt = equipmentSel.querySelector(`option[value="${id}"]`);
        const label = opt ? opt.text : '—';
        const span = document.createElement('span');
        span.innerHTML = `${label} <span class="remove-tag" data-id="${id}">&times;</span>`;
        selectedCont.append(span);
      });
    }

    function addEquipment() {
      const id = equipmentSel.value;
      if (!id) return;
      const list = hiddenInput.value ? hiddenInput.value.split(',') : [];
      if (!list.includes(id)) {
        list.push(id);
        hiddenInput.value = list.join(',');
        renderTags();
      }
      equipmentSel.selectedIndex = 0;
    }

    recipeSelect?.addEventListener('change', () => calculateRowRevenue(row));
    qtyInput?.addEventListener('input', () => calculateRowRevenue(row));
    removeBtn?.addEventListener('click', () => {
      if (tableBody.rows.length > 1) {
        row.remove();
        calculateTotalRevenue();
      }
    });
    equipmentSel?.addEventListener('change', addEquipment);
    equipmentBtn?.addEventListener('click', addEquipment);
    selectedCont?.addEventListener('click', e => {
      if (e.target.classList.contains('remove-tag')) {
        hiddenInput.value = hiddenInput.value
          .split(',')
          .filter(v => v !== e.target.dataset.id)
          .join(',');
        renderTags();
      }
    });

    renderTags();
    calculateRowRevenue(row);
  }

  tableBody.querySelectorAll('tr').forEach((r, i) => {
    attachRowEvents(r, i);
    rowIndex++;
  });

  addRowBtn.addEventListener('click', () => {
    const idx = rowIndex++;
    const clone = blankRow.cloneNode(true);
    clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    clone.querySelectorAll('input').forEach(i => {
      if (i.classList.contains('quantity-input')) i.value = 1;
      else if (i.classList.contains('revenue-field')) i.value = '0.00';
      else if (i.classList.contains('equipment-hidden')) {
        i.value = '';
        i.name = `equipment_ids[${idx}][]`;
      } else {
        i.value = '';
      }
    });
    clone.querySelector('.selected-equipment').innerHTML = '';
    tableBody.appendChild(clone);
    attachRowEvents(clone, idx);
  });

  templateSelect?.addEventListener('change', function () {
    const templateId = this.value;
    if (!templateId) return;

    fetch(`/production/template/${templateId}`)
      .then(res => res.json())
      .then(data => {
        // Fill name and reset date
        nameInput.value = (data.production_name || 'Modello');
        dateInput.value = new Date().toISOString().split('T')[0];
        actionSelect.value = 'template';
        updateNameRequirement();

        // Clear old rows
        tableBody.innerHTML = '';
        rowIndex = 0;

        data.details.forEach((d, i) => {
          const row = blankRow.cloneNode(true);
          row.querySelector('.recipe-select').value = d.recipe_id;
          row.querySelector('select[name="pastry_chef_id[]"]').value = d.chef_id;
          row.querySelector('.quantity-input').value = d.quantity;
          row.querySelector('input[name="execution_time[]"]').value = d.execution_time;

          const hidden = row.querySelector('.equipment-hidden');
          hidden.name = `equipment_ids[${i}][]`;
          hidden.value = d.equipment_ids.join(',');

          tableBody.appendChild(row);
          attachRowEvents(row, i);
          rowIndex++;
        });

        calculateTotalRevenue();
      })
      .catch(console.error);
  });

});
</script>
@endsection
