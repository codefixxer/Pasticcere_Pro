{{-- resources/views/frontend/production/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Tutti i Record di Produzione')

<style>
    .filter-chip .remove-filter {
        font-weight: bold;
        margin-left: 6px;
        cursor: pointer;
    }
    .filter-chip .remove-filter:hover { color: red; }

    .btn-gold {
        border: 1px solid #e2ae76 !important;
        color: #e2ae76 !important;
        background-color: transparent !important;
    }
    .btn-gold:hover { background-color: #e2ae76 !important; color: white !important; }

    .btn-deepblue {
        border: 1px solid #041930 !important;
        color: #041930 !important;
        background-color: transparent !important;
    }
    .btn-deepblue:hover { background-color: #041930 !important; color: white !important; }

    .btn-red {
        border: 1px solid red !important;
        color: red !important;
        background-color: transparent !important;
    }
    .btn-red:hover { background-color: red !important; color: white !important; }

    .page-header {
        background-color: #041930;
        color: #e2ae76;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        font-size: 2rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .page-header i { font-size: 2rem; color: #e2ae76; }

    .filter-chip {
        display: inline-block;
        background: #e2ae76;
        color: #041930;
        padding: .25em .6em;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        margin-right: 0.25rem;
        margin-top: 0.25rem;
    }

    .revenue-card {
        background: linear-gradient(to right, #041930 0%, #e2ae76 100%);
        color: #fff;
        border-radius: 0.75rem;
    }
    .revenue-card .card-body i { color: #e2ae76; }
    .revenue-card .h5,
    .revenue-card .h3 { color: #fff; }

    .filter-card { background: #fff; border: 1px solid #e0e0e0; border-radius: .75rem; }
    .filter-card .dropdown-menu { max-height: 200px; overflow-y: auto; border-radius: .5rem; }

    .production-table { border-radius: .5rem; overflow: hidden; }
    .production-table thead th {
        background-color: #e2ae76 !important;
        color: #041930 !important;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        user-select: none;
        position: relative;
    }
    .production-table thead th.sort-disabled {
        cursor: default;
    }
    .production-table thead th[data-sort-dir="asc"]::after,
    .production-table thead th[data-sort-dir="desc"]::after {
        content: attr(data-sort-dir) == 'asc' ? '▲' : '';
    }
    .production-table thead th[data-sort-dir="asc"]::after,
    .production-table thead th[data-sort-dir="desc"]::after {
        font-size: .65rem;
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        color: #041930;
    }
    .production-table thead th[data-sort-dir="asc"]::after { content: '▲'; }
    .production-table thead th[data-sort-dir="desc"]::after { content: '▼'; }

    .production-table tbody td { text-align: center; vertical-align: middle; }
    .production-table tbody tr:hover { background: rgba(13, 110, 253, .05); }

    .detail-row td { background: #fafafa; }

    .toggle-btn i { transition: transform .2s; }
    .toggle-btn.open i { transform: rotate(90deg); }
</style>

@section('content')
    @php
        use Illuminate\Support\Str;
        $allRecipes = $productions->flatMap(fn($p) => $p->details->pluck('recipe.recipe_name'))->unique()->sort();
        $allChefs   = $productions->flatMap(fn($p) => $p->details->pluck('chef.name'))->unique()->sort();
    @endphp

    <div class="container py-5">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <i class="bi bi-gear-fill"></i>
            <span>Record di Produzione</span>
        </div>

        {{-- Filters Card --}}
        <div class="card filter-card mb-4 shadow-sm p-3">
            <div class="row g-3 align-items-end">
                {{-- Recipe Filter --}}
                <div class="col-md-3">
                    <label class="form-label small">Ricetta</label>
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button class="btn btn-outline-primary w-100 text-start dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-journal-bookmark me-1"></i> Ricette
                        </button>
                        <div class="dropdown-menu p-3">
                            @foreach ($allRecipes as $r)
                                @php $slug = Str::slug($r,'_') @endphp
                                <div class="form-check mb-1">
                                    <input class="form-check-input recipe-checkbox" type="checkbox"
                                        value="{{ strtolower($r) }}" id="recipe_{{ $slug }}">
                                    <label class="form-check-label"
                                        for="recipe_{{ $slug }}">{{ $r }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Chef Filter --}}
                <div class="col-md-3">
                    <label class="form-label small">Pasticcere</label>
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button class="btn btn-outline-success w-100 text-start dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-lines-fill me-1"></i> Pasticcere
                        </button>
                        <div class="dropdown-menu p-3">
                            @foreach ($allChefs as $c)
                                @php $slug = Str::slug($c,'_') @endphp
                                <div class="form-check mb-1">
                                    <input class="form-check-input chef-checkbox" type="checkbox"
                                        value="{{ strtolower($c) }}" id="chef_{{ $slug }}">
                                    <label class="form-check-label"
                                        for="chef_{{ $slug }}">{{ $c }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Date Range Filter --}}
                <div class="col-md-2">
                    <label class="form-label small">Da</label>
                    <input type="date" id="filterStartDate" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">A</label>
                    <input type="date" id="filterEndDate" class="form-control">
                </div>
            </div>
        </div>

        {{-- Active Filters Chips --}}
        <div id="activeFilters" class="mb-4"></div>

        {{-- Total Revenue Card --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card revenue-card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-cash-stack fs-2 me-3"></i>
                            <div>
                                <div class="small text-white">Potenziale Totale</div>
                                <div class="h5 fw-bold text-white mb-0">Ricavi</div>
                            </div>
                        </div>
                        <div id="totalRevenue" class="h3 fw-bold text-white">€0.00</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Production Table --}}
        <div class="card production-table shadow-sm">
            <div class="card-body p-0">
                <table  data-page-length="25"class="table mb-0" id="productionTable">
                    <thead>
                        <tr>
                            <th class="sort-disabled" style="width:48px"></th>
                            <th class="sortable">Data</th>
                            <th class="sortable">Voci</th>
                            <th class="sortable">Potenziale</th>
                            <th class="text-center sort-disabled">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productions as $p)
                            @php
                                $equipNames = collect(explode(',', $p->details->pluck('equipment_ids')->join(',')))
                                    ->filter()
                                    ->unique()
                                    ->map(fn($id) => $equipmentMap[$id] ?? '')
                                    ->filter()
                                    ->implode(', ');
                                $rowRecipes = strtolower($p->details->pluck('recipe.recipe_name')->join(' '));
                                $rowChefs   = strtolower($p->details->pluck('chef.name')->join(' '));
                            @endphp

                            {{-- Main Row --}}
                            <tr class="prod-row"
                                data-recipes="{{ $rowRecipes }}"
                                data-chefs="{{ $rowChefs }}"
                                data-equipment="{{ strtolower($equipNames) }}"
                                data-date="{{ $p->production_date }}">
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                                        data-bs-target="#detail-{{ $p->id }}" aria-expanded="false"
                                        aria-controls="detail-{{ $p->id }}">
                                        <i class="bi bi-caret-right-fill"></i>
                                    </button>
                                </td>
                                <td>{{ $p->production_date }}</td>
                                <td>{{ $p->details->count() }}</td>
                                <td class="row-potential" data-original="{{ $p->total_potential_revenue }}">
                                    €{{ number_format($p->total_potential_revenue, 2) }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('production.show', $p) }}" class="btn btn-sm btn-deepblue me-1">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('production.edit', $p) }}" class="btn btn-sm btn-gold me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('production.destroy', $p) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Eliminare?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-red"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Detail Row --}}
                            <tr id="detail-{{ $p->id }}" class="detail-row collapse">
                                <td colspan="5" class="p-3">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($p->details as $d)
                                            @php
                                                $ids   = array_filter(explode(',', $d->equipment_ids));
                                                $names = collect($ids)
                                                    ->map(fn($id) => $equipmentMap[$id] ?? '')
                                                    ->filter()
                                                    ->implode(', ');
                                            @endphp
                                            <li data-recipe="{{ strtolower($d->recipe->recipe_name) }}"
                                                data-chef="{{ strtolower($d->chef->name) }}"
                                                data-potential="{{ $d->potential_revenue }}">
                                                <strong>{{ $d->recipe->recipe_name }}</strong> × {{ $d->quantity }}
                                                — Chef: {{ $d->chef->name }}, <i class="bi bi-tools"></i>
                                                {{ $names ?: '—' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const recipeCBs    = Array.from(document.querySelectorAll('.recipe-checkbox'));
    const chefCBs      = Array.from(document.querySelectorAll('.chef-checkbox'));
    const rows         = Array.from(document.querySelectorAll('.prod-row'));
    const detailRows   = Array.from(document.querySelectorAll('.detail-row'));
    const totalRevElem = document.getElementById('totalRevenue');
    const activeTags   = document.getElementById('activeFilters');
    const startInput   = document.getElementById('filterStartDate');
    const endInput     = document.getElementById('filterEndDate');

    // Toggle caret icons
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const icon = btn.querySelector('i');
            icon.classList.toggle('bi-caret-right-fill');
            icon.classList.toggle('bi-caret-down-fill');
        });
    });

    function updateActiveFilters(recipes, chefs) {
        activeTags.innerHTML = '';
        recipes.forEach(r => {
            const span = document.createElement('span');
            span.className = 'filter-chip';
            span.innerHTML = `${r} <span class="remove-filter" data-type="recipe" data-value="${r}">&times;</span>`;
            activeTags.appendChild(span);
        });
        chefs.forEach(c => {
            const span = document.createElement('span');
            span.className = 'filter-chip';
            span.innerHTML = `${c} <span class="remove-filter" data-type="chef" data-value="${c}">&times;</span>`;
            activeTags.appendChild(span);
        });
        document.querySelectorAll('.remove-filter').forEach(el => {
            el.addEventListener('click', () => {
                const { type, value } = el.dataset;
                const group = type === 'recipe' ? recipeCBs : chefCBs;
                group.forEach(cb => { if (cb.value === value) cb.checked = false; });
                filterTable();
            });
        });
    }

    function filterTable() {
        const selRecipes = recipeCBs.filter(cb => cb.checked).map(cb => cb.value);
        const selChefs   = chefCBs.filter(cb => cb.checked).map(cb => cb.value);

        updateActiveFilters(selRecipes, selChefs);

        const startDate = startInput.value ? new Date(startInput.value) : null;
        const endDate   = endInput.value   ? new Date(endInput.value)   : null;

        let grandTotal = 0;

        rows.forEach((row, i) => {
            const recs    = row.dataset.recipes;
            const chefs   = row.dataset.chefs;
            const rowDate = new Date(row.dataset.date);

            const recipeMatch = !selRecipes.length || selRecipes.some(r => recs.includes(r));
            const chefMatch   = !selChefs.length   || selChefs.some(c => chefs.includes(c));
            let dateMatch    = true;
            if (startDate && rowDate < startDate) dateMatch = false;
            if (endDate   && rowDate > endDate)   dateMatch = false;

            const showRow = recipeMatch && chefMatch && dateMatch;

            row.style.display           = showRow ? '' : 'none';
            detailRows[i].style.display = showRow ? '' : 'none';

            if (!showRow) return;

            // Recalculate potentials
            let rowSum = 0;
            detailRows[i].querySelectorAll('li').forEach(li => {
                const recipe    = li.dataset.recipe;
                const chef      = li.dataset.chef;
                const potential = parseFloat(li.dataset.potential) || 0;

                const recOk  = !selRecipes.length || selRecipes.includes(recipe);
                const chefOk = !selChefs.length   || selChefs.includes(chef);

                if (recOk && chefOk) {
                    li.style.display = '';
                    rowSum += potential;
                } else {
                    li.style.display = 'none';
                }
            });

            row.querySelector('.row-potential').textContent = `€${rowSum.toFixed(2)}`;
            grandTotal += rowSum;
        });

        totalRevElem.textContent = `€${grandTotal.toFixed(2)}`;
    }

    // Wire up filter inputs
    [...recipeCBs, ...chefCBs].forEach(cb => {
        cb.addEventListener('change', filterTable);
    });
    startInput.addEventListener('change', filterTable);
    endInput.addEventListener('change', filterTable);

    filterTable();

    // ===== 2-STATE SORTING + SESSION PERSISTENCE =====
    const prodTable = document.getElementById('productionTable');
    const sortableHeaders = prodTable.querySelectorAll('thead th.sortable');
    const STORAGE_KEY = 'production_sort_state';

    function parseValue(colIndex, cellText) {
        let text = cellText.trim();
        if (colIndex === 1) { // Data
            // Expect YYYY-MM-DD
            return new Date(text).getTime() || 0;
        }
        if (colIndex === 2) { // Voci (count)
            return parseFloat(text) || 0;
        }
        if (colIndex === 3) { // Potenziale "€123.45"
            text = text.replace(/[€,\s]/g,'');
            return parseFloat(text) || 0;
        }
        return text.toLowerCase();
    }

    function applySort(colIndex, dir) {
        const tbody = prodTable.querySelector('tbody');
        const rowsArr = Array.from(tbody.querySelectorAll('tr'))
            .filter(tr => tr.classList.contains('prod-row')); // only main rows

        rowsArr.sort((a,b) => {
            const aCell = a.children[colIndex].textContent;
            const bCell = b.children[colIndex].textContent;
            const A = parseValue(colIndex, aCell);
            const B = parseValue(colIndex, bCell);

            if (typeof A === 'number' && typeof B === 'number') {
                return dir === 'asc' ? A - B : B - A;
            }
            if (A < B) return dir === 'asc' ? -1 : 1;
            if (A > B) return dir === 'asc' ? 1 : -1;
            return 0;
        });

        // Re-append rows and their detail rows right after each main row
        rowsArr.forEach(main => {
            const id = main.querySelector('[data-bs-target]')?.getAttribute('data-bs-target');
            const detail = id ? document.querySelector(id) : null;
            tbody.appendChild(main);
            if (detail) tbody.appendChild(detail);
        });
    }

    function clearHeaderDirs(except) {
        sortableHeaders.forEach(h => {
            if (h !== except) h.removeAttribute('data-sort-dir');
        });
    }

    // Restore previous sort
    try {
        const saved = sessionStorage.getItem(STORAGE_KEY);
        if (saved) {
            const { col, dir } = JSON.parse(saved);
            const header = sortableHeaders[col];
            if (header && (dir === 'asc' || dir === 'desc')) {
                header.setAttribute('data-sort-dir', dir);
                applySort(col + 1, dir); // +1 because first column (expand) is non-sortable
            }
        }
    } catch(e){}

    sortableHeaders.forEach((th, visibleIndex) => {
        th.addEventListener('click', () => {
            const current = th.getAttribute('data-sort-dir');
            const newDir = current === 'asc' ? 'desc' : 'asc';
            clearHeaderDirs(th);
            th.setAttribute('data-sort-dir', newDir);

            // visibleIndex maps: Data=0, Voci=1, Potenziale=2
            // actual table column index: skip expand (0) => add 1
            applySort(visibleIndex + 1, newDir);

            try {
                sessionStorage.setItem(STORAGE_KEY, JSON.stringify({ col: visibleIndex, dir: newDir }));
            } catch(e){}
        });
    });
});
</script>
@endsection
