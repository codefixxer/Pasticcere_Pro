{{-- resources/views/frontend/recipe/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Tutte le ricette')

@section('content')
    <style>
        /* nuke all header arrows with max specificity */
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th,
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th.sorting,
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th.sorting_asc,
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th.sorting_desc,
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th.dt-orderable-asc,
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th.dt-orderable-desc,
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th.dt-ordering-asc,
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th.dt-ordering-desc {
            background-image: none !important
        }

        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th:before,
        .dataTables_wrapper table#recipesTable thead.custom-recipe-head th:after {
            content: none !important;
            display: none !important
        }

        .dt-column-order {
            /* visibility:hidden; */
        }

        /* compact stat card */
        .stat-card {
            border: 1px dashed #e2ae76;
            border-radius: .75rem;
            padding: 12px
        }

        .stat-title {
            font-weight: 700;
            color: #041930
        }

        .stat-value {
            font-size: 1.15rem;
            font-weight: 800
        }
    </style>

    <div class="container py-5">

        <!-- Header -->
        <div class="page-header d-flex align-items-center mb-4 p-4 rounded" style="background-color:#041930;">
            <i class="bi bi-bookmark-star-fill me-3 fs-3" style="color:#e2ae76;"></i>
            <div>
                <h4 class="mb-0 fw-bold" style="color:#e2ae76;">Tutte le ricette</h4>
                <small class="d-block text-light">Cerca, ordina e filtra rapidamente tutte le tue ricette qui sotto.</small>
            </div>
        </div>

        <!-- Card w/ Filter + Table -->
        <div class="card shadow-sm">
            <div class="card-body">

                <!-- Filters + Sort + Compact Avg -->
                <div class="row g-3 mb-3 align-items-end">
                    <div class="col-md-3">
                        <label for="sellModeFilter" class="form-label fw-semibold">Filtra per modalità di vendita</label>
                        <select id="sellModeFilter" class="form-select">
                            <option value="">Tutte</option>
                            <option value="piece">Pezzo</option>
                            <option value="kg">kg</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="categoryFilter" class="form-label fw-semibold">Filtra per categoria</label>
                        <select id="categoryFilter" class="form-select">
                            <option value="">Tutte</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="sortBy" class="form-label fw-semibold">Ordina per</label>
                        <select id="sortBy" class="form-select">
                            <option value="">Predefinito</option>
                            <option value="name_asc">Nome ↑</option>
                            <option value="name_desc">Nome ↓</option>
                            <option value="salesmode_asc">Modalità vendita ↑</option>
                            <option value="salesmode_desc">Modalità vendita ↓</option>
                            <option value="price_asc">Prezzo ↑</option>
                            <option value="price_desc">Prezzo ↓</option>
                            <option value="entrycost_asc">Costo ingr. ↑</option>
                            <option value="entrycost_desc">Costo ingr. ↓</option>
                            <option value="labourcost_asc">Costo lavoro ↑</option>
                            <option value="labourcost_desc">Costo lavoro ↓</option>
                            <option value="totalcost_asc">Costo totale ↑</option>
                            <option value="totalcost_desc">Costo totale ↓</option>
                            <option value="margin_asc">Margine ↑</option>
                            <option value="margin_desc">Margine ↓</option>
                        </select>
                    </div>

                    <!-- LIVE average (excludes negatives) with its own selector -->
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="stat-title">Media margine</div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark d-none" id="avgMarginCategoryBadge">Tutte</span>
                                    <select id="avgCatSelect" class="form-select form-select-sm" style="min-width:140px">
                                        <option value="">Tutte</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="stat-value mt-1">
                                <span id="avgMarginValue">—</span><span>%</span>
                            </div>
                            <small class="text-muted" id="avgMarginHelp">Negativi esclusi.</small>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table   id="recipesTable" class="table table-striped table-hover table-bordered mb-0"
                        style="width:100%;" data-page-length="25">
                        <thead class="custom-recipe-head">
                            <tr class="text-center">
                                <th class="sortable">Nome</th>
                                <th class="sortable">Modalità vendita</th>
                                <th class="text-end sortable">Prezzo</th>
                                <th class="text-end sortable">Costo ingr.</th>
                                <th class="text-end sortable">Costo lavoro</th>
                                <th class="text-end sortable">Costo totale</th>
                                <th class="text-end sortable">Margine</th>
                                <th class="text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recipes as $r)
                                @php
                                    /*
                                     * IMPORTANT: all per‑unit values (ingredient, labor, packaging, total)
                                     * are recomputed live here from current data to avoid stale totals.
                                     */

                                    // 1) Selling price (gross + net)
                                    $unitSell =
                                        $r->sell_mode === 'piece'
                                            ? $r->selling_price_per_piece ?? 0
                                            : $r->selling_price_per_kg ?? 0;
                                    $vatRate = (float) ($r->vat_rate ?? 0);
                                    $netSell = $vatRate ? $unitSell / (1 + $vatRate / 100) : $unitSell;

                                    // 2) Batch labor cost (already computed in controller with current effective rates):
                                    //    $r->batch_labor_cost = (minutes * €/min)
                                    $batchLabCost = (float) ($r->batch_labor_cost ?? 0);

                                    // Convert batch labor -> unit labor
                                    if ($r->sell_mode === 'piece') {
                                        $pcs = ($r->total_pieces ?? 0) > 0 ? (int) $r->total_pieces : 1;
                                        $unitLabCost = $batchLabCost / $pcs;
                                    } else {
                                        $wLoss = (float) ($r->recipe_weight ?? 0);
                                        if ($wLoss <= 0) {
                                            // fallback to sum of ingredient grams if recipe_weight is empty
                                            $wLoss = $r->ingredients->sum(function ($i) {
                                                return (float) $i->quantity_g;
                                            });
                                        }
                                        $kg = $wLoss > 0 ? $wLoss / 1000 : 1;
                                        $unitLabCost = $batchLabCost / $kg;
                                    }

                                    // 3) LIVE ingredient costs from current ingredient prices
                                    $ingredientsData = $r->ingredients
                                        ->map(function ($ri) {
                                            $priceKg = (float) (optional($ri->ingredient)->price_per_kg ?? 0);
                                            $cost = round(($ri->quantity_g / 1000) * $priceKg, 2);
                                            return [
                                                'name' => optional($ri->ingredient)->ingredient_name ?? '—',
                                                'qty_g' => (float) $ri->quantity_g,
                                                'cost' => $cost,
                                            ];
                                        })
                                        ->values();

                                    $batchIngCost = collect($ingredientsData)->sum('cost'); // € for whole batch

                                    // Convert batch ingredient -> unit ingredient
                                    if ($r->sell_mode === 'piece') {
                                        $pcs = ($r->total_pieces ?? 0) > 0 ? (int) $r->total_pieces : 1;
                                        $unitIngCost = $pcs > 0 ? $batchIngCost / $pcs : 0;
                                    } else {
                                        $wLoss = (float) ($r->recipe_weight ?? 0);
                                        if ($wLoss <= 0) {
                                            $wLoss = $r->ingredients->sum(function ($i) {
                                                return (float) $i->quantity_g;
                                            });
                                        }
                                        $kg = $wLoss > 0 ? $wLoss / 1000 : 1;
                                        $unitIngCost = $batchIngCost / $kg;
                                    }

                                    // 4) Packaging per unit
                                    $pack = (float) ($r->packing_cost ?? 0);
                                    if ($r->sell_mode === 'piece') {
                                        $pcs = ($r->total_pieces ?? 0) > 0 ? (int) $r->total_pieces : 1;
                                        $unitPackCost = $pcs > 0 ? $pack / $pcs : 0;
                                    } else {
                                        // packaging is already per kg in KG mode
                                        $unitPackCost = $pack;
                                    }

                                    // 5) FINAL per‑unit total cost (LIVE)
                                    $unitTotalCost = round($unitIngCost + $unitLabCost + $unitPackCost, 2);

                                    // 6) Margin (per unit) & percentages against net selling price
                                    $unitMargin = round($netSell - $unitTotalCost, 2);
                                    $ingPct = $netSell > 0 ? round(($unitIngCost * 100) / $netSell, 2) : 0;
                                    $labPct = $netSell > 0 ? round(($unitLabCost * 100) / $netSell, 2) : 0;
                                    $totalPct = $netSell > 0 ? round(($unitTotalCost * 100) / $netSell, 2) : 0;
                                    $marPct = $netSell > 0 ? round(($unitMargin * 100) / $netSell, 2) : 0;
                                @endphp

                                <tr class="dt-control text-center" data-sell-mode="{{ $r->sell_mode }}"
                                    data-category="{{ optional($r->category)->id ?? '' }}"
                                    data-margin-pct="{{ $marPct }}" data-ingredients='@json($ingredientsData)'>
                                    <td class="text-start">{{ $r->recipe_name }}</td>
                                    <td><span class="badge bg-secondary text-uppercase">{{ $r->sell_mode }}</span></td>

                                    <td class="text-end" data-order="{{ $unitSell }}">
                                        <div class="d-flex flex-column align-items-end">
                                            <span>€{{ number_format($unitSell, 2) }}</span>
                                            <small class="text-muted">({{ number_format($netSell, 2) }})</small>
                                        </div>
                                    </td>

                                    <td class="text-end" data-order="{{ $unitIngCost }}">
                                        <div class="d-flex flex-column align-items-end">
                                            <span>€{{ number_format($unitIngCost, 2) }}</span>
                                            <small class="text-muted">({{ $ingPct }}%)</small>
                                        </div>
                                    </td>

                                    <td class="text-end" data-order="{{ $unitLabCost }}">
                                        <div class="d-flex flex-column align-items-end">
                                            <span>€{{ number_format($unitLabCost, 2) }}</span>
                                            <small class="text-muted">({{ $labPct }}%)</small>
                                        </div>
                                    </td>

                                    <td class="text-end" data-order="{{ $unitTotalCost }}">
                                        <div class="d-flex flex-column align-items-end">
                                            <span>€{{ number_format($unitTotalCost, 2) }}</span>
                                            <small class="text-muted">({{ $totalPct }}%)</small>
                                        </div>
                                    </td>

                                    <td class="text-end" data-order="{{ $unitMargin }}">
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="{{ $unitMargin >= 0 ? 'text-success' : 'text-danger' }}">
                                                €{{ number_format($unitMargin, 2) }}
                                            </span>
                                            <small class="text-muted">({{ $marPct }}%)</small>
                                        </div>
                                    </td>

                                    <td>
                                        <!-- Edit -->
                                        <a href="{{ route('recipes.edit', $r) }}" class="btn btn-sm me-1"
                                            style="border:1px solid #e2ae76;color:#e2ae76;"
                                            onmouseover="this.style.backgroundColor='#e2ae76';this.style.color='#fff';"
                                            onmouseout="this.style.backgroundColor='transparent';this.style.color='#e2ae76';">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <!-- View -->
                                        <a href="{{ route('recipes.show', $r) }}" class="btn btn-sm me-1"
                                            style="border:1px solid #041930;color:#041930;"
                                            onmouseover="this.style.backgroundColor='#041930';this.style.color='#fff';"
                                            onmouseout="this.style.backgroundColor='transparent';this.style.color='#041930';">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- Duplicate -->
                                        <form action="{{ route('recipes.duplicate', $r) }}" method="POST"
                                            class="d-inline me-1" onsubmit="return confirm('Duplicare questa ricetta?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm"
                                                style="border:1px solid #6c757d;color:#6c757d;"
                                                onmouseover="this.style.backgroundColor='#6c757d';this.style.color='#fff';"
                                                onmouseout="this.style.backgroundColor='transparent';this.style.color='#6c757d';">
                                                <i class="bi bi-files"></i>
                                            </button>
                                        </form>

                                        <!-- Delete -->
                                        <form action="{{ route('recipes.destroy', $r) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Eliminare questa ricetta?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm"
                                                style="border:1px solid #ff0000;color:#ff0000;"
                                                onmouseover="this.style.backgroundColor='#ff0000';this.style.color='#fff';"
                                                onmouseout="this.style.backgroundColor='transparent';this.style.color='#ff0000';">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

<style>
    table#recipesTable thead.custom-recipe-head th {
        background-color: #e2ae76 !important;
        color: #041930 !important;
        text-align: center;
        cursor: pointer
    }

    #recipesTable thead th:last-child {
        cursor: default
    }

    /* Hide DataTables sort arrows (classic + new skins) */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after,
    table.dataTable thead th.dt-orderable-asc:before,
    table.dataTable thead th.dt-orderable-desc:after,
    table.dataTable thead th.dt-ordering-asc:before,
    table.dataTable thead th.dt-ordering-desc:after {
        display: none !important
    }
</style>

@section('scripts')
    <script>
        $(function() {
            const storageKey = 'recipesTableState';

            const table = $('#recipesTable').DataTable({
                paging: true,
                ordering: true,
                responsive: true,
                pageLength: 10,
                orderMulti: false,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                        orderable: false,
                        targets: -1
                    },
                    {
                        targets: '_all',
                        orderSequence: ['asc', 'desc']
                    }
                ],
                stateSave: true,
                stateDuration: 0,
                stateSaveCallback: function(settings, data) {
                    try {
                        sessionStorage.setItem(storageKey, JSON.stringify(data));
                    } catch (e) {}
                },
                stateLoadCallback: function() {
                    try {
                        return JSON.parse(sessionStorage.getItem(storageKey));
                    } catch (e) {
                        return null;
                    }
                },
                language: {
                    lengthMenu: "Mostra _MENU_ elementi per pagina",
                    search: "Cerca:",
                    searchPlaceholder: "Cerca ricette..."
                }
            });

            // -------- helpers for the Media-margine card (independent selector)
            function getCardSelectedCategory() {
                const $sel = $('#avgCatSelect');
                return $sel.length ? $sel.val() : $('#categoryFilter').val();
            }

            function getCardSelectedCategoryText() {
                const $sel = $('#avgCatSelect');
                if ($sel.length) return $sel.find('option:selected').text() || 'Tutte';
                const $tbl = $('#categoryFilter');
                return $tbl.find('option:selected').text() || 'Tutte';
            }

            // --- compute average of margin % over visible rows; exclude negatives
            function updateAvgMarginCard() {
                const cardCatId = getCardSelectedCategory();
                const allVisibleNodes = table.rows({
                    filter: 'applied'
                }).nodes().toArray();

                // if card has category selected, limit to that category (string compare)
                const nodes = cardCatId ?
                    allVisibleNodes.filter(r => (($(r).data('category') ?? '') + '') === (cardCatId + '')) :
                    allVisibleNodes;

                let sum = 0,
                    count = 0;
                nodes.forEach(r => {
                    const v = parseFloat($(r).attr('data-margin-pct'));
                    if (!isNaN(v) && v >= 0) {
                        sum += v;
                        count++;
                    }
                });

                $('#avgMarginCategoryBadge').text(getCardSelectedCategoryText());

                if (count === 0) {
                    $('#avgMarginValue').text('—');
                    $('#avgMarginHelp').text('Nessun elemento con margine ≥ 0 nella vista corrente.');
                } else {
                    const avg = (sum / count).toFixed(2);
                    $('#avgMarginValue').text(avg);
                    $('#avgMarginHelp').text(`Calcolata su ${count} prodotto/i (negativi esclusi).`);
                }
            }

            // DataTables custom filters (sell mode + table category)
            $.fn.dataTable.ext.search.push((settings, data, rowIndex) => {
                if (settings.nTable.id !== 'recipesTable') return true;

                const sellMode = $('#sellModeFilter').val();
                const catFilter = $('#categoryFilter').val();

                const row = table.row(rowIndex).node();
                const modeOk = !sellMode || ($(row).data('sell-mode') === sellMode);

                const rowCat = ($(row).data('category') ?? '').toString();
                const catOk = !catFilter || rowCat === catFilter;

                return modeOk && catOk;
            });

            // Recompute & redraw on table filters
            $('#sellModeFilter, #categoryFilter').on('change', function() {
                table.draw();
                updateAvgMarginCard();
            });

            // Recompute when the card’s own selector changes (does not redraw table)
            $(document).on('change', '#avgCatSelect', updateAvgMarginCard);

            // --- sort dropdown mapping
            const mapToKey = (col, dir) => {
                const map = {
                    0: {
                        asc: 'name_asc',
                        desc: 'name_desc'
                    },
                    1: {
                        asc: 'salesmode_asc',
                        desc: 'salesmode_desc'
                    },
                    2: {
                        asc: 'price_asc',
                        desc: 'price_desc'
                    },
                    3: {
                        asc: 'entrycost_asc',
                        desc: 'entrycost_desc'
                    },
                    4: {
                        asc: 'labourcost_asc',
                        desc: 'labourcost_desc'
                    },
                    5: {
                        asc: 'totalcost_asc',
                        desc: 'totalcost_desc'
                    },
                    6: {
                        asc: 'margin_asc',
                        desc: 'margin_desc'
                    }
                };
                return (map[col] && map[col][dir]) ? map[col][dir] : '';
            };
            const mapFromKey = (key) => {
                const pairs = {
                    'name_asc': [0, 'asc'],
                    'name_desc': [0, 'desc'],
                    'salesmode_asc': [1, 'asc'],
                    'salesmode_desc': [1, 'desc'],
                    'price_asc': [2, 'asc'],
                    'price_desc': [2, 'desc'],
                    'entrycost_asc': [3, 'asc'],
                    'entrycost_desc': [3, 'desc'],
                    'labourcost_asc': [4, 'asc'],
                    'labourcost_desc': [4, 'desc'],
                    'totalcost_asc': [5, 'asc'],
                    'totalcost_desc': [5, 'desc'],
                    'margin_asc': [6, 'asc'],
                    'margin_desc': [6, 'desc']
                };
                return pairs[key] || null;
            };

            // Keep dropdown synced + keep avg up to date
            const syncDropdownWithOrder = () => {
                const ord = table.order();
                if (ord && ord.length) {
                    const key = mapToKey(ord[0][0], ord[0][1]);
                    $('#sortBy').val(key);
                } else {
                    $('#sortBy').val('');
                }
            };
            table.on('draw', function() {
                syncDropdownWithOrder();
                updateAvgMarginCard();
            });
            syncDropdownWithOrder();
            updateAvgMarginCard();

            // Dropdown -> apply order + persist
            $('#sortBy').on('change', function() {
                const key = $(this).val();
                const ord = mapFromKey(key) || [0, 'asc'];
                table.order([ord]).draw();
                try {
                    const s = JSON.parse(sessionStorage.getItem(storageKey)) || {};
                    s.order = table.order();
                    sessionStorage.setItem(storageKey, JSON.stringify(s));
                } catch (e) {}
            });

            // Prevent shift multi-sort
            $('#recipesTable thead').on('mousedown', 'th', function(e) {
                if (e.shiftKey) e.preventDefault();
            });

            // Child-row toggle
            $('#recipesTable tbody').on('click', 'tr.dt-control', function() {
                const tr = $(this),
                    row = table.row(tr),
                    ingredients = JSON.parse(tr.attr('data-ingredients') || '[]');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    let html = '<table  data-page-length="25" class="table table-borderless mb-0"><thead><tr>' +
                        '<th>Ingrediente</th><th class="text-end">Qtà (g)</th><th class="text-end">Costo</th>' +
                        '</tr></thead><tbody>';
                    ingredients.forEach(i => {
                        const name = i.name ?? '—';
                        const qty = Number(i.qty_g ?? 0);
                        const cost = Number(i.cost ?? 0);
                        html +=
                            `<tr><td>${name}</td><td class="text-end">${qty}</td><td class="text-end">€${cost.toFixed(2)}</td></tr>`;
                    });
                    html += '</tbody></table>';
                    row.child(html).show();
                    tr.addClass('shown');
                }
            });
        });
    </script>
@endsection
