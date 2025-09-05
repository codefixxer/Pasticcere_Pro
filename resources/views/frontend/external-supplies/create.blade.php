@extends('frontend.layouts.app')

@section('title', isset($externalSupply) ? 'Modifica Fornitura Esterna' : 'Crea Fornitura Esterna')

@section('content')
<div class="container py-5">
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex align-items-center gap-2"
         style="background-color:#041930;color:#e2ae76;padding:.5rem;border-top-left-radius:.5rem;border-top-right-radius:.5rem;">
      <iconify-icon icon="mdi:warehouse" class="me-2" style="font-size:35px;color:#e2ae76;"></iconify-icon>
      <h5 class="mb-0" style="color:#e2ae76;font-size:1.6vw;">
        {{ isset($externalSupply) ? 'Modifica Fornitura Esterna' : 'Crea Fornitura Esterna' }}
      </h5>
    </div>

    <div class="card-body">
      <form method="POST"
            action="{{ isset($externalSupply) ? route('external-supplies.update', $externalSupply->id) : route('external-supplies.store') }}"
            class="row g-3 needs-validation"
            novalidate>
        @csrf
        @if(isset($externalSupply))
          @method('PUT')
        @endif

        <!-- Supply Name -->
        <div class="col-md-6">
          <label id="supplyNameLabel" for="supply_name" class="form-label fw-semibold">Nome Fornitura</label>
          <input type="text" id="supply_name" name="supply_name" class="form-control form-control-lg"
                 value="{{ old('supply_name', $externalSupply->supply_name ?? '') }}">
          <div class="invalid-feedback">Inserisci un nome per la fornitura.</div>
        </div>

        <!-- Client -->
        <div class="col-md-6">
          <label for="client_id" class="form-label fw-semibold">Cliente</label>
          <select id="client_id" name="client_id" class="form-select form-control-lg" required>
            <option value="">Seleziona Cliente</option>
            @foreach($clients as $client)
              <option value="{{ $client->id }}"
                {{ old('client_id', $externalSupply->client_id ?? '') == $client->id ? 'selected' : '' }}>
                {{ $client->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Supply Date -->
        <div class="col-md-6">
          <label for="supply_date" class="form-label fw-semibold">Data Fornitura</label>
          <input type="date" id="supply_date" name="supply_date" class="form-control form-control-lg" required
                 value="{{ old('supply_date', isset($externalSupply) ? $externalSupply->supply_date->format('Y-m-d') : '') }}">
        </div>

        <!-- Template Selection (only on create) -->
        @if(!isset($externalSupply))
        <div class="col-md-6">
          <label for="template_select" class="form-label fw-semibold">Scegli Modello</label>
          <select id="template_select" name="template_id" class="form-select form-control-lg">
            <option value="">-- Seleziona Modello --</option>
            @foreach($templates as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>
        @endif

        <!-- Save As -->
        <div class="col-md-6">
          <label for="template_action" class="form-label fw-semibold">Salva come</label>
          @php
            $default = old('template_action',
              isset($externalSupply) && $externalSupply->save_template ? 'template' : 'none'
            );
          @endphp
          <select id="template_action" name="template_action" class="form-select form-control-lg">
            <option value="none"     {{ $default=='none' ? 'selected' : '' }}>Solo Salva</option>
            <option value="template" {{ $default=='template' ? 'selected' : '' }}>Salva come Modello</option>
          </select>
        </div>

        <!-- Supplied Products -->
        <div class="col-12">
          <div class="card border-primary shadow-sm">
            <div class="card-header d-flex align-items-center" style="background-color:#041930;">
              <strong style="color:#e2ae76;font-size:1.1rem;">Prodotti Forniti</strong>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table  data-page-length="25"class="table table-hover align-middle mb-0" id="supplyTable">
                  <thead class="table-light">
                    <tr>
                      <th style="width:40%;">Ricetta</th>
                      <th style="width:150px;">Prezzo (€)</th>
                      <th style="width:80px;">Qtà</th>
                      <th style="width:120px;">Totale (€)</th>
                      <th style="width:60px;">Azione</th>
                    </tr>
                  </thead>
                  <tbody id="supplyTableBody">
                    @foreach(old('recipes', $externalSupply->recipes ?? [null]) as $index => $item)
                      <tr class="supply-row">
                        <td>
                          <select name="recipes[{{ $index }}][id]" class="form-select recipe-select" required>
                            <option value="">Seleziona Ricetta</option>
                            @foreach($recipes as $rec)
                              <option value="{{ $rec->id }}"
                                      data-price="{{ $rec->sell_mode==='kg' ? $rec->selling_price_per_kg : $rec->selling_price_per_piece }}"
                                      data-sell-mode="{{ $rec->sell_mode }}"
                                      {{ old("recipes.$index.id", $item->recipe_id ?? '') == $rec->id ? 'selected' : '' }}>
                                {{ $rec->recipe_name }}
                              </option>
                            @endforeach
                          </select>
                        </td>
                        <td>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">€</span>
                            <input type="text"
                                   name="recipes[{{ $index }}][price]"
                                   class="form-control text-end price-field"
                                   readonly
                                   value="{{ old("recipes.$index.price", $item->price ?? '') }}">
                            <span class="input-group-text unit-field">/pz</span>
                          </div>
                        </td>
                        <td>
                          <input type="number"
                                 name="recipes[{{ $index }}][qty]"
                                 class="form-control text-center qty-field"
                                 required
                                 value="{{ old("recipes.$index.qty", $item->qty ?? 0) }}">
                        </td>
                        <td>
                          <input type="text"
                                 name="recipes[{{ $index }}][total_amount]"
                                 class="form-control total-field"
                                 readonly
                                 value="{{ old("recipes.$index.total_amount", $item->total_amount ?? '') }}">
                        </td>
                        <td class="text-center">
                          <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <div class="p-3 border-top text-end">
                <button type="button" id="addRowBtn" class="btn btn-sm"
                        style="border:1px solid #e2ae76;color:#041930;background-color:transparent;"
                        onmouseover="this.style.backgroundColor='#e2ae76';this.style.color='white';this.querySelector('i').style.color='white';"
                        onmouseout="this.style.backgroundColor='transparent';this.style.color='#041930';this.querySelector('i').style.color='#041930';">
                  <i class="bi bi-plus-circle me-1" style="color:#041930;"></i>
                  Aggiungi Ricetta
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Amount -->
        <div class="col-md-6">
          <label class="form-label fw-semibold">Incasso Totale (€)</label>
          <input type="text" id="totalAmount" name="total_amount" class="form-control" readonly
                 value="{{ old('total_amount', $externalSupply->total_amount ?? '') }}">
        </div>

        <!-- Submit Button -->
        <div class="col-12 text-end mt-4">
          <button type="submit" class="btn btn-lg" style="background-color:#e2ae76;color:#041930;">
            <i class="bi bi-save2 me-2" style="color:#041930;"></i>
            {{ isset($externalSupply) ? 'Aggiorna Fornitura Esterna' : 'Salva Fornitura Esterna' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const actionSelect      = document.getElementById('template_action');
  const nameInput         = document.getElementById('supply_name');
  const nameLabel         = document.getElementById('supplyNameLabel');
  const supplyBody        = document.getElementById('supplyTableBody');
  const addBtn            = document.getElementById('addRowBtn');
  const templateSelect    = document.getElementById('template_select');
  const totalAmountInput  = document.getElementById('totalAmount');
  const dateInput         = document.getElementById('supply_date');
  const form              = document.querySelector('form.needs-validation');

  function toggleNameRequirement() {
    const isTemplate = ['template','both'].includes(actionSelect?.value || 'none');
    nameLabel.textContent = isTemplate ? 'Nome Modello' : 'Nome Fornitura';
    if (nameInput) nameInput.required = isTemplate;
  }
  toggleNameRequirement();
  actionSelect?.addEventListener('change', toggleNameRequirement);

  let rowIndex = supplyBody.querySelectorAll('.supply-row').length;
  const baseRow = supplyBody.querySelector('.supply-row');

  function blankRow() {
    const clone = baseRow.cloneNode(true);
    clone.querySelectorAll('input,select').forEach(el => {
      if (el.tagName === 'SELECT') el.selectedIndex = 0;
      else if (el.type === 'number') el.value = 0;
      else el.value = '';
    });
    return clone;
  }

  function wireRowEvents(row) {
    const sel = row.querySelector('.recipe-select');
    const qty = row.querySelector('.qty-field');
    const rm  = row.querySelector('.remove-row');

    sel.addEventListener('change', () => recalcRow(row));
    qty.addEventListener('input', () => recalcRow(row));
    rm.addEventListener('click', () => {
      if (supplyBody.querySelectorAll('.supply-row').length > 1) {
        row.remove();
        calcSummary();
      }
    });

    recalcRow(row);
  }

  function recalcRow(row) {
    const opt      = row.querySelector('.recipe-select')?.selectedOptions[0];
    const priceIn  = row.querySelector('input[name*="[price]"]');
    const unitSpan = row.querySelector('.unit-field');
    const qtyIn    = row.querySelector('input[name*="[qty]"]');
    const totIn    = row.querySelector('input[name*="[total_amount]"]');

    const priceNum = parseFloat(opt?.dataset.price || 0);
    priceIn.value  = priceNum.toFixed(2);
    unitSpan.textContent = (opt?.dataset.sellMode || 'piece') === 'kg' ? '/kg' : '/pz';

    const qty = parseFloat(qtyIn.value || 0);
    totIn.value = (priceNum * qty).toFixed(2);

    calcSummary();
  }

  function calcSummary() {
    let sum = 0;
    document.querySelectorAll('.total-field').forEach(inp => {
      const v = parseFloat(inp.value);
      if (!isNaN(v)) sum += v;
    });
    totalAmountInput.value = sum.toFixed(2);
  }

  function addRow() {
    const newRow = blankRow();
    newRow.querySelectorAll('input[name],select[name]').forEach(el => {
      el.name = el.name.replace(/\[\d+\]/, `[${rowIndex}]`);
    });
    supplyBody.appendChild(newRow);
    wireRowEvents(newRow);
    rowIndex++;
    return newRow;
  }

  addBtn.addEventListener('click', () => {
    const r = addRow();
    r.querySelector('.recipe-select')?.focus();
  });

  // Optional: pressing Enter inside the table adds a row instead of submitting
  form?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && supplyBody.contains(e.target)) {
      if (e.shiftKey) return;
      e.preventDefault();
      const r = addRow();
      r.scrollIntoView({ block: 'nearest' });
      r.querySelector('.recipe-select')?.focus();
    }
  });

  templateSelect?.addEventListener('change', function () {
    const id = this.value;
    if (!id) return;

    fetch(`/external-supplies/template/${id}`)
      .then(r => r.json())
      .then(data => {
        nameInput.value    = data.supply_name || '';
        dateInput.value    = data.supply_date || '';
        if (actionSelect) { actionSelect.value = 'template'; toggleNameRequirement(); }

        supplyBody.innerHTML = '';
        rowIndex = 0;

        (data.rows || []).forEach(rowData => {
          const r = addRow();
          r.querySelector('.recipe-select').value = rowData.recipe_id ?? '';
          r.querySelector('input[name*="[qty]"]').value          = rowData.qty ?? 0;
          r.querySelector('input[name*="[price]"]').value        = parseFloat(rowData.price ?? 0).toFixed(2);
          r.querySelector('input[name*="[total_amount]"]').value = parseFloat(rowData.total_amount ?? 0).toFixed(2);
          recalcRow(r);
        });

        calcSummary();
      })
      .catch(console.error);
  });

  // Bind existing rows
  supplyBody.querySelectorAll('.supply-row').forEach(wireRowEvents);
});
</script>
@endsection
