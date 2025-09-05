{{-- resources/views/frontend/returned-goods/form.blade.php --}}
@extends('frontend.layouts.app')

@section('title', $returnedGood->exists ? 'Edit Returned Goods' : 'Create Returned Goods')

@section('content')
<div class="container py-5">
  <h2 class="mb-4">
    {{ $returnedGood->exists ? 'Edit' : 'Create' }} Returned Goods
  </h2>

  <form
    method="POST"
    action="{{ $returnedGood->exists
                ? route('returned-goods.update', $returnedGood)
                : route('returned-goods.store') }}">
    @csrf
    @if($returnedGood->exists)
      @method('PUT')
    @endif

    {{-- 1) Client & Return Date --}}
    <div class="row mb-4 g-3">
      <div class="col-md-6">
        <label class="form-label">Client</label>
        <select name="client_id" class="form-select" required>
          <option value="">Select…</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}"
              {{ old('client_id', $returnedGood->client_id) == $c->id ? 'selected' : '' }}>
              {{ $c->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Return Date</label>
        <input
          type="date"
          name="return_date"
          class="form-control"
          value="{{ old('return_date',
                        $returnedGood->return_date
                          ? $returnedGood->return_date->format('Y-m-d')
                          : ''
                      ) }}"
          required
        >
      </div>
    </div>

    {{-- 2) Returned Items --}}
    <div class="card mb-4">
      <div class="card-header bg-secondary text-white"><strong>Returned Items</strong></div>
      <div class="card-body p-0">
        <table  data-page-length="25"class="table mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>Recipe</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="linesTable">
            @php
              // decide which set of lines to render:
              if(old('recipes')) {
                $lines = old('recipes');
              } elseif($returnedGood->exists) {
                $lines = $returnedGood->recipes->map(fn($r)=>[
                  'id'           => $r->recipe_id,
                  'price'        => $r->price,
                  'qty'          => $r->qty,
                  'total_amount' => $r->total_amount,
                ])->toArray();
              } else {
                $lines = [['id'=>'','price'=>'','qty'=>1,'total_amount'=>'']];
              }
            @endphp

            @foreach($lines as $i => $line)
              <tr class="line-row">
                <td>
                  <select name="recipes[{{ $i }}][id]" class="form-select recipe-select" required>
                    <option value="">Select…</option>
                    @foreach($recipes as $r)
                      <option
                        value="{{ $r->id }}"
                        data-price="{{ $r->sell_mode==='kg'
                                      ? $r->selling_price_per_kg
                                      : $r->selling_price_per_piece }}"
                        data-unit="{{ $r->sell_mode }}"
                        {{ (int)($line['id'] ?? '') === $r->id ? 'selected' : '' }}>
                        {{ $r->recipe_name }}
                      </option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-text">€</span>
                    <input
                      type="text"
                      name="recipes[{{ $i }}][price]"
                      class="form-control price-field"
                      readonly
                      value="{{ $line['price'] ?? '' }}"
                    >
                    <span class="input-group-text unit-span"></span>
                  </div>
                </td>
                <td>
                  <input
                    type="number"
                    name="recipes[{{ $i }}][qty]"
                    class="form-control qty-field"
                    min="1"
                    value="{{ $line['qty'] ?? 1 }}"
                    required
                  >
                </td>
                <td>
                  <input
                    type="text"
                    name="recipes[{{ $i }}][total_amount]"
                    class="form-control total-field"
                    readonly
                    value="{{ $line['total_amount'] ?? '' }}"
                  >
                </td>
                <td>
                  <button type="button" class="btn btn-sm btn-outline-danger remove-line">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="p-3 text-end">
          <button type="button" id="addLineBtn" class="btn btn-sm btn-outline-success">
            <i class="bi bi-plus-lg"></i> Add Item
          </button>
        </div>
      </div>
    </div>

    {{-- 3) Grand Total --}}
    <div class="mb-4">
      <label class="form-label">Total Returned (€)</label>
      <input
        type="text"
        name="total_amount"
        id="grandTotal"
        class="form-control"
        readonly
        value="{{ old('total_amount', $returnedGood->total_amount) }}"
      >
    </div>

    <button type="submit" class="btn btn-primary">
      <i class="bi bi-save2 me-1"></i>
      {{ $returnedGood->exists ? 'Update' : 'Save' }}
    </button>
  </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  let idx       = document.querySelectorAll('.line-row').length;
  const table   = document.getElementById('linesTable');
  const addBtn  = document.getElementById('addLineBtn');

  // 1) Add new blank row
  addBtn.addEventListener('click', () => {
    const first = table.querySelector('.line-row');
    const tr    = first.cloneNode(true);
    tr.querySelectorAll('input,select').forEach(el => {
      el.name = el.name.replace(/\[\d+\]/, `[${idx}]`);
      if (el.tagName === 'SELECT') {
        el.selectedIndex = 0;
      } else if (el.type === 'number') {
        el.value = '1';
      } else {
        el.value = '';
      }
    });
    table.appendChild(tr);
    idx++;
  });

  // 2) Recalc a single row
  function recalc(tr) {
    const opt   = tr.querySelector('.recipe-select').selectedOptions[0];
    const price = parseFloat(opt.dataset.price||0).toFixed(2);
    const unit  = opt.dataset.unit==='kg' ? '/kg' : '/pz';
    tr.querySelector('.price-field').value = price;
    tr.querySelector('.unit-span').textContent = unit;
    const qty   = parseFloat(tr.querySelector('.qty-field').value||0);
    tr.querySelector('.total-field').value = (price*qty).toFixed(2);
    recalcGrand();
  }

  // 3) Grand total
  function recalcGrand(){
    let sum = 0;
    document.querySelectorAll('.total-field')
            .forEach(i => sum += parseFloat(i.value)||0);
    document.getElementById('grandTotal').value = sum.toFixed(2);
  }

  // 4) Delegate
  table.addEventListener('change', e => {
    if (e.target.classList.contains('recipe-select'))
      recalc(e.target.closest('tr'));
  });
  table.addEventListener('input', e => {
    if (e.target.classList.contains('qty-field'))
      recalc(e.target.closest('tr'));
  });
  table.addEventListener('click', e => {
    if (e.target.closest('.remove-line')
        && table.querySelectorAll('.line-row').length > 1) {
      e.target.closest('tr').remove();
      recalcGrand();
    }
  });

  // 5) Initialize existing rows on load
  document.querySelectorAll('.line-row').forEach(r => recalc(r));
});
</script>
@endsection
