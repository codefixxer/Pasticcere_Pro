resources/views/frontend/records/index.blade.php
@extends('frontend.layouts.app') @section('title', 'Filter Records')
@section('content')
<div class="container py-5">
    <h2 class="mb-5 text-center">Showcase &amp; External Supply Records</h2>

    {{-- Filters --}}
    <div class="row justify-content-center g-4 mb-5">
        <div class="col-sm-6 col-md-4">
            <label class="form-label text-center d-block">From</label>
            <input
                type="date"
                id="filter_from"
                class="form-control mx-auto"
                value="{{ $from }}"
            />
        </div>
        <div class="col-sm-6 col-md-4">
            <label class="form-label text-center d-block">To</label>
            <input
                type="date"
                id="filter_to"
                class="form-control mx-auto"
                value="{{ $to }}"
            />
        </div>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <label class="form-label text-center d-block">Recipe Name</label>
            <input
                type="text"
                id="filter_recipe"
                class="form-control mx-auto"
                placeholder="Enter recipe..."
            />
        </div>
    </div>

    <div
        id="noRecords"
        class="alert alert-info text-center"
        style="display: none"
    >
        No records found for the selected filters.
    </div>

    {{-- Summary Cards --}}
    <div
        id="summary"
        class="row justify-content-center mb-5 g-4"
        style="display: none"
    >
        <div class="col-sm-8 col-md-5 col-lg-4">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up display-4 text-primary mb-3"></i>
                    <h5 class="card-title">Total Showcase Revenue</h5>
                    <p class="display-5 fw-bold mb-1" id="totalShowRevenue">
                        0.00
                    </p>
                    <small class="text-muted" id="pctShow">0%</small>
                </div>
            </div>
        </div>
        <div class="col-sm-8 col-md-5 col-lg-4">
            <div class="card border-danger h-100">
                <div class="card-body text-center">
                    <i
                        class="bi bi-currency-dollar display-4 text-danger mb-3"
                    ></i>
                    <h5 class="card-title">Total External Cost</h5>
                    <p class="display-5 fw-bold mb-1" id="totalExternalCost">
                        0.00
                    </p>
                    <small class="text-muted" id="pctExt">0%</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tables --}}
    <div class="row gx-4 gy-5">
        {{-- Showcase --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-list-ul me-2"></i> Showcase Records
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-hover mb-0 align-middle"
                        >
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Date</th>
                                    <th class="text-start">Recipe</th>
                                    <th>Qty</th>
                                    <th>Sold</th>
                                    <th>Reuse</th>
                                    <th>Waste</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody
                                id="showcaseBody"
                                class="text-center"
                            ></tbody>
                            <tfoot class="table-light text-center">
                                <tr>
                                    <th colspan="2" class="text-end">
                                        Grand Total:
                                    </th>
                                    <th id="showcaseQtyFooter">0</th>
                                    <th id="showcaseSoldFooter">0</th>
                                    <th id="showcaseReuseFooter">0</th>
                                    <th id="showcaseWasteFooter">0</th>
                                    <th id="showcaseFooter">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- External Supply --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-box-seam me-2"></i> External Supply Records
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-hover mb-0 align-middle"
                        >
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Date</th>
                                    <th class="text-start">Client</th>
                                    <th class="text-start">Recipe</th>
                                    <th>Returns</th>
                                    <th>Qty</th>
                                    <th>Total ($)</th>
                                </tr>
                            </thead>
                            <tbody
                                id="externalBody"
                                class="text-center"
                            ></tbody>
                            <tfoot class="table-light text-center">
                                <tr>
                                    <th colspan="3" class="text-end">
                                        Grand Total:
                                    </th>
                                    <th id="externalReturnsFooter">0</th>
                                    <th id="externalQtyFooter">0</th>
                                    <th id="externalFooter">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection @section('scripts')
<script>
    @php
      // Prepare JS data arrays, including recipe‐level and return qty.
      $showData = $showcaseRecords->flatMap(fn($sc) =>
        $sc->recipes->map(fn($line) => [
          'date'        => $sc->showcase_date->format('Y-m-d'),
          'recipe_name' => $line->recipe->recipe_name,
          'quantity'    => $line->quantity,
          'sold'        => $line->sold,
          'reuse'       => $line->reuse,
          'waste'       => $line->waste,
          'revenue'     => $line->actual_revenue,
        ])
      )->all();

      $extData = $externalRecords->flatMap(fn($s) =>
        $s->recipes->map(fn($line) => [
          'date'        => $s->supply_date->format('Y-m-d'),
          'client'      => $s->client->name,
          'recipe_name' => $line->recipe->recipe_name,
          'returns'     => $s->returnedGoods
                              ->where('external_supply_id',$s->id)
                              ->flatMap(fn($rg) => $rg->recipes)
                              ->where('recipe_id',$line->recipe_id)
                              ->sum('qty'),
          'qty'         => $line->qty,
          'total'       => $line->total_amount,
        ])
      )->all();
    @endphp

    const showcaseData = {!! json_encode($showData) !!};
    const externalData = {!! json_encode($extData) !!};

    function render() {
      const from   = document.getElementById('filter_from').value;
      const to     = document.getElementById('filter_to').value;
      const recipe = document.getElementById('filter_recipe').value.trim().toLowerCase();

      // filter helpers
      const inRange = (d,valA,valB) => (!valA||d>=valA) && (!valB||d<=valB);
      const matchRec = r => !recipe || r.recipe_name.toLowerCase().includes(recipe);

      // filtered lists
      const fShow = showcaseData.filter(r => inRange(r.date,from,to) && matchRec(r));
      const fExt  = externalData.filter(r => inRange(r.date,from,to) && matchRec(r));

      const has = fShow.length||fExt.length;
      document.getElementById('noRecords').style.display = has?'none':'';
      document.getElementById('summary').style.display   = has?'flex':'none';

      // group by date
      function groupByDate(arr){
        return arr.reduce((acc,r)=>{
          (acc[r.date]||(acc[r.date]={ date:r.date, items:[], sums:{}})).items.push(r);
          return acc;
        },{});
      }

      // compute per‐day sums
      function summarizeShow(group){
        group.sums = group.items.reduce((S,r)=>{
          S.quantity = (S.quantity||0)+ +r.quantity;
          S.sold     = (S.sold||0)    + +r.sold;
          S.reuse    = (S.reuse||0)   + +r.reuse;
          S.waste    = (S.waste||0)   + +r.waste;
          S.revenue  = (S.revenue||0) + +r.revenue;
          return S;
        },{});
      }
      function summarizeExt(group){
        group.sums = group.items.reduce((S,r)=>{
          S.returns = (S.returns||0)+ +r.returns;
          S.qty     = (S.qty||0)    + +r.qty;
          S.total   = (S.total||0)  + +r.total;
          return S;
        },{});
      }

      const sGroups = Object.values(groupByDate(fShow));
      sGroups.forEach(summarizeShow);

      const eGroups = Object.values(groupByDate(fExt));
      eGroups.forEach(summarizeExt);

      // grand totals
      const grandShow = sGroups.reduce((s,g)=>s+g.sums.revenue,0);
      const grandExt  = eGroups.reduce((s,g)=>s+g.sums.total,0);
      const gross    = grandShow+grandExt;
      const pctShow  = gross?((grandShow/gross)*100).toFixed(0):0;
      const pctExt   = gross?((grandExt /gross)*100).toFixed(0):0;

      // populate summary cards
      document.getElementById('totalShowRevenue').textContent   = grandShow.toFixed(2);
      document.getElementById('pctShow').textContent           = pctShow+'%';
      document.getElementById('totalExternalCost').textContent = grandExt.toFixed(2);
      document.getElementById('pctExt').textContent            = pctExt+'%';

      // populate show table
      let outShow = '';
      sGroups.forEach(g=>{
        // header row
        outShow += `
        <tr class="group-header" data-date="${g.date}">
          <td colspan="2" class="text-start">
            <i class="bi bi-caret-right-fill toggle-icon"></i>
            ${g.date} (${g.items.length} lines)
          </td>
          <td>${g.sums.quantity}</td>
          <td>${g.sums.sold}</td>
          <td>${g.sums.reuse}</td>
          <td>${g.sums.waste}</td>
          <td>${g.sums.revenue.toFixed(2)}</td>
        </tr>`;
        // detail rows
        g.items.forEach(r=>{
          outShow += `
          <tr class="group-child group-${g.date}" style="display:none">
            <td>${r.date}</td>
            <td class="text-start">${r.recipe_name}</td>
            <td>${r.quantity}</td>
            <td>${r.sold}</td>
            <td>${r.reuse}</td>
            <td>${r.waste}</td>
            <td>${(+r.revenue).toFixed(2)}</td>
          </tr>`;
        });
      });
      // grand footer
      document.getElementById('showcaseBody').innerHTML = outShow;
      document.getElementById('showcaseQtyFooter').textContent   = sGroups.reduce((s,g)=>s+g.sums.quantity,0);
      document.getElementById('showcaseSoldFooter').textContent  = sGroups.reduce((s,g)=>s+g.sums.sold,0);
      document.getElementById('showcaseReuseFooter').textContent = sGroups.reduce((s,g)=>s+g.sums.reuse,0);
      document.getElementById('showcaseWasteFooter').textContent = sGroups.reduce((s,g)=>s+g.sums.waste,0);
      document.getElementById('showcaseFooter').textContent      = grandShow.toFixed(2);

      // populate external table
      let outExt = '';
      eGroups.forEach(g=>{
        outExt += `
        <tr class="group-header" data-date="${g.date}">
          <td colspan="3" class="text-start">
            <i class="bi bi-caret-right-fill toggle-icon"></i>
            ${g.date} (${g.items.length} lines)
          </td>
          <td>${g.sums.returns}</td>
          <td>${g.sums.qty}</td>
          <td>${g.sums.total.toFixed(2)}</td>
        </tr>`;
        g.items.forEach(r=>{
          outExt += `
          <tr class="group-child group-${g.date}" style="display:none">
            <td>${r.date}</td>
            <td class="text-start">${r.client}</td>
            <td class="text-start">${r.recipe_name}</td>
            <td>${r.returns}</td>
            <td>${r.qty}</td>
            <td>${(+r.total).toFixed(2)}</td>
          </tr>`;
        });
      });
      document.getElementById('externalBody').innerHTML = outExt;
      document.getElementById('externalReturnsFooter').textContent = eGroups.reduce((s,g)=>s+g.sums.returns,0);
      document.getElementById('externalQtyFooter').textContent     = eGroups.reduce((s,g)=>s+g.sums.qty,0);
      document.getElementById('externalFooter').textContent        = grandExt.toFixed(2);

      // wire up toggles
      document.querySelectorAll('.group-header').forEach(row=>{
        row.querySelector('.toggle-icon').onclick = () => {
          const date = row.dataset.date;
          const children = document.querySelectorAll(`.group-${date}`);
          const isHidden = children[0].style.display==='none';
          children.forEach(tr=>tr.style.display = isHidden?'':'none');
          row.querySelector('.toggle-icon')
             .classList.toggle('bi-caret-down-fill', isHidden);
          row.querySelector('.toggle-icon')
             .classList.toggle('bi-caret-right-fill', !isHidden);
        };
      });
    }

    function debounce(fn,ms=200){
      let t;
      return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms) };
    }

    document.getElementById('filter_from'  ).addEventListener('change', render);
    document.getElementById('filter_to'    ).addEventListener('change', render);
    document.getElementById('filter_recipe').addEventListener('input', debounce(render));
    // initial
    render();








      document.getElementById('addToIncomeBtn').addEventListener('click', () => {
      // build hidden form
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route("income.addFiltered") }}';
      form.style.display = 'none';

      // CSRF token
      const token = document.createElement('input');
      token.name  = '_token';
      token.value = document.querySelector('meta[name="csrf-token"]').content;
      form.appendChild(token);

      // add showcase inputs
      lastShowGroups.forEach((g,i) => {
        const d = document.createElement('input');
        d.name  = `showcase[${i}][date]`;
        d.value = g.date;
        form.appendChild(d);

        const a = document.createElement('input');
        a.name  = `showcase[${i}][amount]`;
        a.value = g.amount;
        form.appendChild(a);
      });

      // add external inputs
      lastExtGroups.forEach((g,i) => {
        const d = document.createElement('input');
        d.name  = `external[${i}][date]`;
        d.value = g.date;
        form.appendChild(d);

        const a = document.createElement('input');
        a.name  = `external[${i}][amount]`;
        a.value = g.amount;
        form.appendChild(a);
      });

      document.body.appendChild(form);
      form.submit();
    });


      // handle Add to Income click
      document.getElementById('addToIncomeBtn').addEventListener('click', () => {
      const payload = {
        showcase: lastShowGroups.map(g => ({ date: g.date, amount: g.sums.revenue })),
        external: lastExtGroups.map(g  => ({ date: g.date, amount: g.sums.total   })),
      };

      axios.post('{{ route("income.addFiltered") }}', payload)
        .then(res => alert(`Inserted ${res.data.count} income records.`))
        .catch(() => alert('Failed to add to income.'));
    });
</script>
@endsection
