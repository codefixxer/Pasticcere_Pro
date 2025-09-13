@extends('frontend.layouts.app')

@section('title', 'Pasticcere Pro | Cruscotto')

@section('content')
    <style>
        /* Force donut labels, datalabels and legends to black (fallback) */
        .apexcharts-legend-text,
        .apexcharts-datalabel text,
        .apexcharts-datalabel-value,
        .apexcharts-text.apexcharts-datalabel-value,
        .apexcharts-text.apexcharts-datalabel-label,
        .apexcharts-legend .apexcharts-legend-text {
            fill: #000 !important;
            color: #000 !important;
        }
    </style>
    {{-- Beautiful Welcome Banner --}}
    <div class="col-12 mb-4">
        <div class="alert text-center fw-bold fs-4 rounded-pill" style="background-color: #041930; color: #e2ae76;">
            Benvenuto, {{ auth()->user()->name }}!
        </div>
    </div>

    <div class="dashboard-main-body">

        <!-- ====================== TOOLBAR ====================== -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <!-- Left: title + breadcrumb -->
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-semibold mb-0">Cruscotto</h6>

                <ul class="d-flex align-items-center gap-2 mb-0 small text-secondary breadcrumb-lite">
                    <li class="fw-medium">
                        <a href="index.html"
                            class="d-flex align-items-center gap-1 link-underline-opacity-0 link-underline-opacity-75-hover">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon"></iconify-icon>
                            Home
                        </a>
                    </li>
                    <li class="opacity-50">-</li>
                    <li class="fw-medium">CRM</li>
                </ul>
            </div>

            {{-- Global date filter – right aligned on lg+, full width on mobile --}}
            <form class="ms-lg-auto w-100 w-lg-auto">
                <div class="row g-2 align-items-center justify-content-end">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-auto">
                        <select id="globalRange" class="form-select form-select-sm">
                            <option value="custom" selected>Personalizzato</option>
                            <option value="this_month">Questo mese</option>
                            <option value="last_month">Mese scorso</option>
                            <option value="this_quarter">Questo trimestre</option>
                            <option value="this_year">Questo anno</option>
                            <option value="last_12m">Ultimi 12 mesi</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-auto">
                        <input type="date" id="globalStart" class="form-control form-control-sm" />
                    </div>
                    <div class="col-6 col-md-4 col-lg-auto">
                        <input type="date" id="globalEnd" class="form-control form-control-sm" />
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-auto">
                        <button id="applyGlobalFilters" class="btn btn-sm btn-primary w-100 w-lg-auto btn-lift">
                            Applica
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <style>
            .ms-lg-auto {
                margin-bottom: 2vw;
            }

            /* visual polish only */
            .btn-lift {
                transition: transform .12s ease, box-shadow .12s ease;
            }

            .btn-lift:hover {
                transform: translateY(-1px);
                box-shadow: 0 .5rem 1rem rgba(13, 110, 253, .15)
            }
        </style>
        <div class="row g-4">

            {{-- resources/views/dashboard.blade.php --}}
            <div class="col-12">
                <!-- Use row-cols to make cards flow perfectly on all screens -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">

                    @if ($isSuper)
                        {{-- Total Admins (system-wide) --}}
                        <div class="col">
                            <div class="card card-kpi h-100 bg-gradient-end-1">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="icon-badge bg-dark text-white">
                                                <iconify-icon icon="mdi:account-cog"></iconify-icon>
                                            </span>
                                            <div>
                                                <small class="text-secondary d-block">Total Admins</small>
                                                <h6 class="fw-semibold mb-0">{{ number_format($adminsCount) }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-secondary small mb-0">Tutti gli admin del sistema</p>
                                </div>
                            </div>
                        </div>

                        {{-- Total Users (all roles, system-wide) --}}
                        <div class="col my-20" style="margin-bottom: 3vw">
                            <div class="card card-kpi h-100 bg-gradient-end-2">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="icon-badge bg-dark text-white">
                                                <iconify-icon icon="mdi:account-group"></iconify-icon>
                                            </span>
                                            <div>
                                                <small class="text-secondary d-block">Total Users (All Roles)</small>
                                                <h6 class="fw-semibold mb-0">{{ number_format($allUsersCount) }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-secondary small mb-0">Utenti di tutti i ruoli</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Utenti Totali --}}
                    <div class="col">
                        <div class="card card-kpi h-100 bg-gradient-end-1">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon-badge bg-primary text-white">
                                            <iconify-icon icon="mingcute:user-follow-fill"></iconify-icon>
                                        </span>
                                        <div>
                                            <small class="text-secondary d-block">Personale personale totale</small>
                                            <h6 class="fw-semibold mb-0">{{ number_format($totalUsers) }}</h6>
                                        </div>
                                    </div>
                                    <div id="total-users-chart" class="w-100 w-sm-auto"></div>
                                </div>
                                <p class="text-secondary small mb-0">Da creazione del gruppo</p>
                            </div>
                        </div>
                    </div>

                    {{-- Ricette Totali --}}
                    <div class="col">
                        <div class="card card-kpi h-100 bg-gradient-end-2">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon-badge bg-success text-white">
                                            <iconify-icon icon="uis:box"></iconify-icon>
                                        </span>
                                        <div>
                                            <small class="text-secondary d-block">Ricette Totali</small>
                                            <h6 class="fw-semibold mb-0">{{ number_format($totalRecipes) }}</h6>
                                        </div>
                                    </div>
                                    <div id="total-recipes-chart" class="w-100 w-sm-auto"></div>
                                </div>
                                <p class="text-secondary small mb-0">Tra tutti gli utenti del gruppo</p>
                            </div>
                        </div>
                    </div>

                    {{-- Vetrine Totali --}}
                    <div class="col">
                        <div class="card card-kpi h-100 bg-gradient-end-3">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon-badge bg-warning text-white">
                                            <iconify-icon icon="mdi:television-ambient-light"></iconify-icon>
                                        </span>
                                        <div>
                                            <small class="text-secondary d-block">Vetrine Totali</small>
                                            <h6 class="fw-semibold mb-0">{{ number_format($totalShowcases) }}</h6>
                                        </div>
                                    </div>
                                    <div id="total-showcase-chart" class="w-100 w-sm-auto"></div>
                                </div>
                                <p class="text-secondary small mb-0">Conteggio totale</p>
                            </div>
                        </div>
                    </div>

                    @can('Dashboard(Sales, Costs)')
                        {{-- Vendite (Anno) --}}
                        <div class="col">
                            <div class="card card-kpi h-100 bg-gradient-end-4">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="icon-badge bg-purple text-white">
                                                <iconify-icon icon="iconamoon:discount-fill"></iconify-icon>
                                            </span>
                                            <div>
                                                <small class="text-secondary d-block">Vendite ({{ $year }})</small>
                                                <h6 class="fw-semibold mb-0">€{{ number_format($totalSaleThisYear, 2) }}</h6>
                                            </div>
                                        </div>
                                        <div id="total-sales-chart" class="w-100 w-sm-auto"></div>
                                    </div>
                                    <p class="text-secondary small mb-0">Anno in corso</p>
                                </div>
                            </div>
                        </div>
                    @endcan

                    {{-- Sprechi (Anno) --}}
                    <div class="col">
                        <div class="card card-kpi h-100 bg-gradient-end-5">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon-badge bg-pink text-white">
                                            <iconify-icon icon="fluent:trash-24-regular"></iconify-icon>
                                        </span>
                                        <div>
                                            <small class="text-secondary d-block">Sprechi ({{ $year }})</small>
                                            <h6 class="fw-semibold mb-0">{{ number_format($totalWasteThisYear) }}</h6>
                                        </div>
                                    </div>
                                    <div id="total-waste-chart" class="w-100 w-sm-auto"></div>
                                </div>
                                <p class="text-secondary small mb-0">Quantità anno in corso</p>
                            </div>
                        </div>
                    </div>

                    @can('Dashboard(Sales, Costs)')
                        {{-- Profitto (Anno) --}}
                        <div class="col">
                            <div class="card card-kpi h-100 bg-gradient-end-6">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="icon-badge bg-info text-white">
                                                <iconify-icon icon="streamline:bag-dollar-solid"></iconify-icon>
                                            </span>
                                            <div>
                                                <small class="text-secondary d-block">Profitto ({{ $year }})</small>
                                                <h6 class="fw-semibold mb-0">€{{ number_format($totalProfitThisYear, 2) }}
                                                </h6>
                                            </div>
                                        </div>
                                        <div id="total-profit-chart" class="w-100 w-sm-auto"></div>
                                    </div>
                                    <p class="text-secondary small mb-0">Margine anno in corso</p>
                                </div>
                            </div>
                        </div>
                    @endcan

                </div>
            </div>
        </div>
        <!-- ====================== ORDERED (1 → 12) ====================== -->
        <div class="row g-4">

            {{-- 1) Averages by Category (all) --}}
            <div class="col-12 col-lg-6 col-xxl-4">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h6 class="mb-0">Medie per categoria <span class="text-secondary">(tutte)</span></h6>
                            <span class="badge bg-light text-dark">Media globale:
                                {{ number_format($globalAvgMarginPos, 2) }}%</span>
                        </div>

                        <div class="table-responsive mt-3">
                            <table data-page-length="25"class="table table-sm align-middle mb-0">
                                <thead class="text-secondary">
                                    <tr>
                                        <th>Categoria</th>
                                        <th class="text-end">Media margine %</th>
                                        <th class="text-end"># Prodotti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categoryAvgTop as $row)
                                        <tr>
                                            <td class="py-2 text-break">{{ $row->name }}</td>
                                            <td class="text-end py-2">{{ number_format($row->avg_margin_pos ?? 0, 2) }}
                                            </td>
                                            <td class="text-end py-2">{{ $row->pos_cnt }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-3 text-secondary">Nessun dato disponibile.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <small class="text-secondary d-block mt-3">
                            Mostrando max 8 categorie. I valori medi escludono margini negativi.
                        </small>
                    </div>
                </div>
            </div>

            {{-- 2) Earnings by Category --}}
            <div class="col-12 col-sm-6 col-xxl-4">
                <div class="card card-elevated h-100">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h6 class="fw-semibold mb-0">💰 Incassi per Categoria</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-6 col-md-auto">
                                <input type="date" id="revStart" class="form-control form-control-sm" />
                            </div>
                            <div class="col-6 col-md-auto">
                                <input type="date" id="revEnd" class="form-control form-control-sm" />
                            </div>
                            <div class="col-12 col-md-auto">
                                <button id="revFilter" class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">
                                    <i class="bi bi-funnel"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="revCategoryChart" class="chart-min-260"></div>
                    </div>
                </div>
            </div>

            {{-- 3) Costs by Category --}}
            <div class="col col-lg-5 col-xxl-4">
                <div class="card card-elevated h-100">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="fw-semibold mb-0">Costi per Categoria</h6>

                        <div class="row g-2 align-items-center">
                            <div class="col-6 col-md-auto">
                                <input type="date" id="costStart" class="form-control form-control-sm" />
                            </div>
                            <div class="col-6 col-md-auto">
                                <input type="date" id="costEnd" class="form-control form-control-sm" />
                            </div>
                            <div class="col-12 col-md-auto">
                                <button id="costFilter" class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">
                                    <i class="bi bi-funnel"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="costByCategoryDonut" class="chart-min-260"></div>
                    </div>
                </div>
            </div>

            {{-- 4) Cost vs Revenue Ratio --}}
            @can('Dashboard(Sales, Costs)')
                <div class="col-12">
                    <div class="card card-elevated h-100">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h6 class="fw-semibold mb-0">Incidenza Costi vs Ricavi</h6>
                            <div class="row g-2 align-items-center">
                                <div class="col-6 col-md-auto">
                                    <input type="date" id="incStart" class="form-control form-control-sm" />
                                </div>
                                <div class="col-6 col-md-auto">
                                    <input type="date" id="incEnd" class="form-control form-control-sm" />
                                </div>
                                <div class="col-12 col-md-auto">
                                    <button id="incFilter"
                                        class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">Applica</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="incomeCostDonut" class="chart-min-260"></div>
                        </div>
                    </div>
                </div>
            @endcan

            {{-- 5–7) Costs vs. income (current month) + Annual costs + Annual incomes --}}
            @can('Dashboard(Sales, Costs)')
                <!-- 3 charts row -->
                <div class="col-12 col-lg-6 col-xxl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 p-md-4">
                            {!! $comparisonChart->container() !!}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 col-xxl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 p-md-4">
                            {!! $yearlyCostChart->container() !!}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 col-xxl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 p-md-4">
                            {!! $yearlyIncomeChart->container() !!}
                        </div>
                    </div>
                </div>
            @endcan

            {{-- 8) Earning statistics --}}
            @can('Dashboard(Sales, Costs)')
                <div class="col-12">
                    <div class="card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 p-md-4">
                            <div
                                class="d-flex align-items-start align-items-md-center flex-column flex-md-row gap-2 justify-content-between">
                                <div>
                                    <h6 class="mb-1 fw-bold">Statistiche Guadagni</h6>
                                    <span class="text-secondary small">Panoramica vendite mensili</span>
                                </div>

                                <div class="ms-md-auto">
                                    <div class="row g-2">
                                        <div class="col-6 col-md-auto">
                                            <input type="date" id="startDate" class="form-control form-control-sm" />
                                        </div>
                                        <div class="col-6 col-md-auto">
                                            <input type="date" id="endDate" class="form-control form-control-sm" />
                                        </div>
                                        <div class="col-12 col-md-auto">
                                            <button id="applyDateFilter"
                                                class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">Applica</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-center flex-wrap gap-3">
                                <div
                                    class="d-inline-flex align-items-center gap-2 p-2 rounded-3 border bg-light-subtle kpi-chip">
                                    <span class="icon-tile">
                                        <iconify-icon icon="fluent:cart-16-filled"></iconify-icon>
                                    </span>
                                    <div>
                                        <small class="text-secondary d-block">Vendite</small>
                                        <h6 class="mb-0">€{{ number_format($sales, 2) }}</h6>
                                    </div>
                                </div>

                                <div
                                    class="d-inline-flex align-items-center gap-2 p-2 rounded-3 border bg-light-subtle kpi-chip">
                                    <span class="icon-tile">
                                        <iconify-icon icon="uis:chart"></iconify-icon>
                                    </span>
                                    <div>
                                        <small class="text-secondary d-block">Margine Lordo</small>
                                        <h6 class="mb-0">€{{ number_format($plus, 2) }}</h6>
                                    </div>
                                </div>

                                <div
                                    class="d-inline-flex align-items-center gap-2 p-2 rounded-3 border bg-light-subtle kpi-chip">
                                    <span class="icon-tile">
                                        <iconify-icon icon="ph:arrow-fat-up-fill"></iconify-icon>
                                    </span>
                                    <div>
                                        <small class="text-secondary d-block">Profitto Netto</small>
                                        <h6 class="mb-0">€{{ number_format($realMargin, 2) }}</h6>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 overflow-hidden">
                                <div id="barChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            {{-- 9) Top 5 sold products --}}
            <div class="col-12 col-lg-6 col-xxl-8">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div
                        class="card-header bg-body-tertiary py-2 px-3 px-md-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="mb-0">Top 5 Prodotti Venduti</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-6 col-md-auto">
                                <input type="date" id="soldStart" class="form-control form-control-sm" />
                            </div>
                            <div class="col-6 col-md-auto">
                                <input type="date" id="soldEnd" class="form-control form-control-sm" />
                            </div>
                            <div class="col-12 col-md-auto">
                                <button id="soldFilter"
                                    class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">Applica</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="table-responsive mb-3">
                            <table data-page-length="25"class="table table-hover mb-0" id="soldTable">
                                <thead>
                                    <tr>
                                        <th>Prodotto</th>
                                        <th class="text-end">Quantità Venduta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topSold as $item)
                                        <tr>
                                            <td class="text-break">{{ $item->recipe->recipe_name }}</td>
                                            <td class="text-end">{{ $item->sold }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="soldPie"></div>
                    </div>
                </div>
            </div>

            {{-- 10) Top 5 waste products --}}
            <div class="col-12 col-lg-12 col-xxl-12 offset-xxl-12 my-14">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div
                        class="card-header bg-body-tertiary py-2 px-3 px-md-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="mb-0">Top 5 Prodotti Sprecati</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-6 col-md-auto">
                                <input type="date" id="wastedStart" class="form-control form-control-sm" />
                            </div>
                            <div class="col-6 col-md-auto">
                                <input type="date" id="wastedEnd" class="form-control form-control-sm" />
                            </div>
                            <div class="col-12 col-md-auto">
                                <button id="wastedFilter"
                                    class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">Applica</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="table-responsive mb-3">
                            <table data-page-length="25"class="table table-hover mb-0" id="wastedTable">
                                <thead>
                                    <tr>
                                        <th>Prodotto</th>
                                        <th class="text-end">Quantità Sprecata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topWasted as $item)
                                        <tr>
                                            <td class="text-break">{{ $item->recipe->recipe_name }}</td>
                                            <td class="text-end">{{ $item->waste }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="wastedPie"></div>
                    </div>
                </div>
            </div>

            {{-- 11) Production vs waste --}}
            <div class="col-12">
                <div class="card card-elevated h-100">
                    <div class="card-header">
                        <h6 class="fw-semibold mb-0">Produzione vs Spreco</h6>
                    </div>
                    <div class="card-body">
                        {!! $prodWasteChart->container() !!}
                    </div>
                </div>
            </div>

            <div class="my-20 col-12 col-sm-6 col-xxl-4">
                <div class="card card-elevated h-100">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h6 class="fw-semibold mb-0">Produzione per Pasticcere</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-6 col-md-auto">
                                <input type="date" id="chefStart" class="form-control form-control-sm" />
                            </div>
                            <div class="col-6 col-md-auto">
                                <input type="date" id="chefEnd" class="form-control form-control-sm" />
                            </div>
                            <div class="col-12 col-md-auto">
                                <button id="chefFilter" class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">
                                    <i class="bi bi-funnel"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chefProdChart" class="chart-min-260"></div>

                        {{-- keep the original Larapex container so no code is “missed”, but hide it to prevent double charts --}}
                        <div class="visually-hidden">
                            {!! $chefChart->container() !!}
                        </div>
                    </div>
                </div>
            </div>


            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const rows = @json($fullChefData ?? []);
                    const data = rows
                        .map(r => ({
                            date: r.date,
                            chef: r.chef_name || 'Sconosciuto',
                            qty: Number(r.qty) || 0
                        }))
                        .sort((a, b) => a.date.localeCompare(b.date));

                    function groupByChef(start, end) {
                        const s = new Date(start),
                            e = new Date(end);
                        const agg = {};
                        data.forEach(x => {
                            const d = new Date(x.date);
                            if (!isNaN(d) && d >= s && d <= e) {
                                agg[x.chef] = (agg[x.chef] || 0) + x.qty;
                            }
                        });
                        const ordered = Object.entries(agg).sort((a, b) => b[1] - a[1]); // highest first
                        return {
                            labels: ordered.map(([name]) => name),
                            series: ordered.map(([, val]) => val)
                        };
                    }

                    function renderChefChart(start, end) {
                        const {
                            labels,
                            series
                        } = groupByChef(start, end);

                        if (window.chefChartJS) {
                            window.chefChartJS.destroy();
                            window.chefChartJS = null;
                        }

                        if (!series.length) {
                            document.querySelector('#chefProdChart').innerHTML =
                                '<div class="text-muted py-3">Nessun dato per l\'intervallo selezionato.</div>';
                            return;
                        }

                        window.chefChartJS = new ApexCharts(document.querySelector('#chefProdChart'), {
                            chart: {
                                type: 'bar',
                                height: 320
                            },
                            series: [{
                                name: 'Unità prodotte',
                                data: series
                            }],
                            xaxis: {
                                categories: labels,
                                labels: {
                                    rotate: -45,
                                    trim: true
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 4,
                                    columnWidth: '60%'
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: v => v.toLocaleString()
                                }
                            },
                            legend: {
                                show: false
                            }
                        });
                        window.chefChartJS.render();
                    }

                    const defMin = data.length ? data[0].date : new Date().toISOString().slice(0, 10);
                    const defMax = data.length ? data[data.length - 1].date : defMin;

                    const $s = document.getElementById('chefStart');
                    const $e = document.getElementById('chefEnd');
                    const $b = document.getElementById('chefFilter');

                    if ($s && $e) {
                        $s.value = defMin;
                        $e.value = defMax;
                        renderChefChart(defMin, defMax);
                        if ($b) {
                            $b.addEventListener('click', () => {
                                const s = $s.value,
                                    e = $e.value;
                                if (s && e) renderChefChart(s, e);
                            });
                        }
                    }
                });
            </script>




















            {{-- Returns vs. Restocks (Larapex donut) --}}
            <div class="col-12 col-sm-6 col-xxl-4">
                <div class="card card-elevated h-100">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h6 class="fw-semibold mb-0">Resi vs Rifornimenti</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-6 col-md-auto">
                                <input type="date" id="retStart" class="form-control form-control-sm" />
                            </div>
                            <div class="col-6 col-md-auto">
                                <input type="date" id="retEnd" class="form-control form-control-sm" />
                            </div>
                            <div class="col-12 col-md-auto">
                                <button type="button" id="retFilter"
                                    class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">
                                    <i class="bi bi-funnel"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="mb-1">Totale Fornito: <strong>{{ number_format($totalSupplied) }}</strong></p>
                        <p class="mb-3">Totale Resi: <strong>{{ number_format($totalReturned) }}</strong></p>

                        {{-- Larapex container (same pattern as the other charts) --}}
                        <div style="min-height:260px">
                            {!! $returnRateChart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    try {
                        const id = @json($returnRateChart->id); // Larapex container id
                        const supplied = Number({{ (int) $totalSupplied }});
                        const returned = Number({{ (int) $totalReturned }});
                        const restocked = Math.max(0, supplied - returned);
                        const el = document.getElementById(id);

                        // if Larapex didn't render anything, draw it manually
                        if (el && el.innerHTML.trim() === '') {
                            const chart = new ApexCharts(el, {
                                chart: {
                                    type: 'donut',
                                    height: 300
                                },
                                series: [returned, restocked],
                                labels: ['Resi', 'Riforniti'],
                                legend: {
                                    position: 'bottom'
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '70%',
                                            labels: {
                                                show: true,
                                                total: {
                                                    show: true,
                                                    label: 'Fornito',
                                                    formatter: () => supplied.toLocaleString()
                                                }
                                            }
                                        }
                                    }
                                },
                                title: {
                                    text: 'Resi vs Rifornimenti'
                                },
                                tooltip: {
                                    y: {
                                        formatter: v => v.toLocaleString()
                                    }
                                }
                            });
                            chart.render();
                        }
                    } catch (e) {
                        console.error('Return/Restock chart fallback error:', e);
                    }

                    // prevent accidental form submit on the small filter UI
                    document.getElementById('retFilter')?.addEventListener('click', e => e.preventDefault());
                });
            </script>


















































        </div>

        <!-- ====================== (KEEPING EVERY OTHER ORIGINAL BLOCK) ====================== -->

        <!-- ====================== KPI CARDS (kept intact) ====================== -->


        <!-- ====================== POLISH CSS (visual only; responsiveness handled by Bootstrap) ====================== -->

        {{-- resources/views/dashboard.blade.php --}}
        {{-- resources/views/dashboard.blade.php --}}
        <div class="row g-4 mt-14" style="margin-top: 1vw">




            {{-- Sprechi + Costi side-by-side --}}
            <div class="col-12">
                <div class="row row-cols-1 row-cols-lg-2 g-4 align-items-stretch">

                    {{-- Top 5 Sprechi (donut) --}}
                    <div class="col col-lg-7 col-xxl-8">
                        <div class="card card-elevated h-100">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-semibold mb-0">Prodotti con Sprechi</h6>
                                    <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis px-3">Top
                                        5</span>
                                </div>

                                <div class="row g-2 align-items-center">
                                    <div class="col-6 col-md-auto">
                                        <input type="date" id="wastedRowStart" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-6 col-md-auto">
                                        <input type="date" id="wastedRowEnd" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-12 col-md-auto">
                                        <button id="wastedRowFilter"
                                            class="btn btn-sm btn-primary w-100 w-md-auto btn-lift">
                                            <i class="bi bi-funnel"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="wastedRowPie" class="chart-min-260"></div>
                                <small class="text-secondary d-block mt-2">
                                    * Mostriamo i 5 prodotti con maggiore spreco nel periodo selezionato.
                                </small>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            @can('Dashboard(Sales, Costs)')
                {{-- Incidenza Costi vs Ricavi (already moved to #4) --}}
            @endcan

            {{-- Production vs Waste Trend (already shown in #11) --}}
            <div class="col-12">
                <div class="card card-elevated h-100">
                    <div class="card-header">
                        <h6 class="fw-semibold mb-0">Produzione vs Spreco</h6>
                    </div>
                    <div class="card-body">
                        {!! $prodWasteChart->container() !!}
                    </div>
                </div>
            </div>

        </div>

        <!-- ===================== JS (unchanged IDs; your existing logic remains) ===================== -->

        {{-- === Averages by Category (compact, positives only) === --}}

    </div>

    </div>

@endsection


@section('styles')
    <style>
        /* KPI cards look */
        .card-kpi {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 .25rem .75rem rgba(16, 24, 40, .06);
            overflow: hidden
        }

        .card-kpi .card-body {
            padding: 1rem 1rem
        }

        @media (min-width:768px) {
            .card-kpi .card-body {
                padding: 1.25rem 1.25rem
            }
        }

        .icon-badge {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 14px rgba(0, 0, 0, .08)
        }

        .icon-tile {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            background: #f1f3f5
        }

        .btn-lift {
            transition: transform .12s ease, box-shadow .12s ease
        }

        .btn-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 .5rem 1rem rgba(13, 110, 253, .15)
        }

        .table td,
        .table th {
            vertical-align: middle
        }

        /* soft gradients to match your classes */
        .bg-gradient-end-1 {
            background: linear-gradient(180deg, #fff, #f7fbff)
        }

        .bg-gradient-end-2 {
            background: linear-gradient(180deg, #fff, #f5fff7)
        }

        .bg-gradient-end-3 {
            background: linear-gradient(180deg, #fff, #fff8ed)
        }

        .bg-gradient-end-4 {
            background: linear-gradient(180deg, #fff, #f7f5ff)
        }

        .bg-gradient-end-5 {
            background: linear-gradient(180deg, #fff, #fff5f8)
        }

        .bg-gradient-end-6 {
            background: linear-gradient(180deg, #fff, #f2fbff)
        }

        .card-elevated {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 .25rem 1rem rgba(16, 24, 40, .08);
            overflow: hidden
        }

        .card-elevated .card-header {
            background: var(--bs-body-bg);
            border-bottom: 1px solid rgba(0, 0, 0, .06)
        }

        .btn-lift {
            transition: transform .12s ease, box-shadow .12s ease
        }

        .btn-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 .5rem 1rem rgba(13, 110, 253, .15)
        }

        .chart-min-260 {
            min-height: 260px
        }
    </style>
@endsection

@section('scripts')
    {{-- === SCRIPTS START === --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Larapex (server-rendered) chart scripts --}}
    {!! $chart->script() !!}
    {!! $comparisonChart->script() !!}
    {!! $yearlyCostChart->script() !!}
    {!! $yearlyIncomeChart->script() !!}
    {!! $soldPieChart->script() !!}
    {!! $wastedPieChart->script() !!}
    {{-- {!! $returnRateChart->script() !!} --}}
    {!! $chefChart->script() !!}
    {!! $prodWasteChart->script() !!}
    {{-- {!! $costCategoryChart->script() !!} --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const supplied = Number({{ (int) $totalSupplied }});
            const returned = Number({{ (int) $totalReturned }});
            const used = Math.max(0, supplied - returned);

            const el = document.querySelector('#retRateChart');
            if (!el) return;

            if (window.retRateChart) {
                window.retRateChart.destroy();
            }

            window.retRateChart = new ApexCharts(el, {
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: ['Resi', 'Utilizzati'],
                series: [returned, used],
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: v => v.toLocaleString()
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Fornito',
                                    formatter: () => supplied.toLocaleString()
                                }
                            }
                        }
                    }
                },
                title: {
                    text: 'Resi vs. Utilizzo'
                }
            });
            window.retRateChart.render();

            // (Optional) prevent page reload if user clicks the filter button now
            const btn = document.getElementById('retFilter');
            if (btn) btn.addEventListener('click', e => e.preventDefault());
        });
    </script>
    {{-- 💰 Incassi per Categoria (donut) – with date filters --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const incomesRaw = @json($fullIncomeData ?? []);
            const incomes = incomesRaw
                .map(i => ({
                    date: i.date,
                    amount: Number(i.amount) || 0,
                    // Blade JS (Incassi per Categoria) — change ONLY the category mapping line

                    category: (i.category && i.category !== '—' && i.category.toLowerCase() !==
                        'uncategorized') ? i.category : 'Senza categoria'
                }))
                .sort((a, b) => a.date.localeCompare(b.date));

            function incFilterAndGroup(start, end) {
                const s = new Date(start),
                    e = new Date(end);
                const grouped = {};
                let total = 0;

                incomes.forEach(i => {
                    const d = new Date(i.date);
                    if (!isNaN(d) && d >= s && d <= e) {
                        total += i.amount;
                        grouped[i.category] = (grouped[i.category] || 0) + i.amount;
                    }
                });

                return {
                    grouped,
                    total
                };
            }

            function renderRevChart(start, end) {
                const {
                    grouped,
                    total
                } = incFilterAndGroup(start, end);

                if (window.revChart) {
                    window.revChart.destroy();
                    window.revChart = null;
                }

                const labels = Object.keys(grouped);
                const series = Object.values(grouped);

                if (!series.length || total === 0) {
                    document.querySelector('#revCategoryChart').innerHTML =
                        '<div class="text-muted py-3">Nessun dato per l\'intervallo selezionato.</div>';
                    return;
                }
                window.revChart = new ApexCharts(document.querySelector("#revCategoryChart"), {
                    chart: {
                        type: 'donut',
                        height: 280
                    },
                    series,
                    labels,
                    legend: {
                        position: 'bottom',
                        labels: {
                            colors: '#000'
                        } // legend text black
                    },
                    tooltip: {
                        y: {
                            formatter: v => `€${v.toLocaleString()}`
                        }
                    },

                    // data labels inside slices (value + percent)
                    dataLabels: {
                        formatter: (percent, opts) => {
                            const val = opts.w.config.series[opts.seriesIndex];
                            return `${val.toLocaleString()}€ (${percent.toFixed(1)}%)`;
                        },
                        style: {
                            colors: ['#000'] // force black
                        }
                    },

                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        style: {
                                            color: '#000',
                                            fontSize: '13px'
                                        }
                                    },
                                    value: {
                                        show: true,
                                        style: {
                                            color: '#000',
                                            fontSize: '14px',
                                            fontWeight: '600'
                                        },
                                        formatter: val => `€${Number(val).toLocaleString()}`
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: () =>
                                            `€${total.toLocaleString(undefined,{minimumFractionDigits:2})}`,
                                        style: {
                                            color: '#000',
                                            fontSize: '16px',
                                            fontWeight: '700'
                                        }
                                    }
                                }
                            }
                        }
                    }
                });

                window.revChart.render();
            }

            const revMin = incomes.length ? incomes[0].date : new Date().toISOString().slice(0, 10);
            const revMax = incomes.length ? incomes[incomes.length - 1].date : revMin;

            const $revStart = document.getElementById('revStart');
            const $revEnd = document.getElementById('revEnd');
            if ($revStart && $revEnd) {
                $revStart.value = revMin;
                $revEnd.value = revMax;
                renderRevChart(revMin, revMax);

                const $revBtn = document.getElementById('revFilter');
                if ($revBtn) {
                    $revBtn.addEventListener('click', () => {
                        const s = $revStart.value;
                        const e = $revEnd.value;
                        if (s && e) renderRevChart(s, e);
                    });
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // --- Costi per Categoria ---
            const costsRaw = @json($fullCostData ?? []); // [{date, amount, category}]
            const costs = costsRaw
                .map(c => ({
                    date: c.date,
                    amount: Number(c.amount) || 0,
                    category: c.category || 'Senza categoria'
                }))
                .sort((a, b) => a.date.localeCompare(b.date));

            function groupCosts(start, end) {
                const s = new Date(start),
                    e = new Date(end);
                const grouped = {};
                let total = 0;

                costs.forEach(c => {
                    const d = new Date(c.date);
                    if (!isNaN(d) && d >= s && d <= e) {
                        total += c.amount;
                        grouped[c.category] = (grouped[c.category] || 0) + c.amount;
                    }
                });

                return {
                    grouped,
                    total
                };
            }

            function renderCostDonut(start, end) {
                const {
                    grouped,
                    total
                } = groupCosts(start, end);

                if (window.costCatChart) {
                    window.costCatChart.destroy();
                    window.costCatChart = null;
                }

                const labels = Object.keys(grouped);
                const series = Object.values(grouped);

                if (!series.length || total === 0) {
                    document.querySelector('#costByCategoryDonut').innerHTML =
                        '<div class="text-muted py-3">Nessun dato per l\'intervallo selezionato.</div>';
                    return;
                }

                window.costCatChart = new ApexCharts(document.querySelector('#costByCategoryDonut'), {
                    chart: {
                        type: 'donut',
                        height: 320,
                        id: 'costByCategoryDonutChart'
                    },
                    series,
                    labels,
                    legend: {
                        position: 'right',
                        labels: {
                            colors: '#000'
                        } // legend text black
                    },
                    tooltip: {
                        y: {
                            formatter: val => '€ ' + Number(val).toFixed(2)
                        }
                    },

                    dataLabels: {
                        formatter: (percent, opts) => {
                            const val = opts.w.config.series[opts.seriesIndex];
                            return `€ ${Number(val).toFixed(2)} (${percent.toFixed(1)}%)`;
                        },
                        style: {
                            colors: ['#000'] // <-- black
                        }
                    },

                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Totale',
                                        formatter: () => '€ ' + total.toFixed(2),
                                        style: {
                                            color: '#000'
                                        } // total color
                                    },
                                    value: {
                                        formatter: val => '€ ' + Number(val).toFixed(2),
                                        style: {
                                            color: '#000'
                                        }
                                    },
                                    name: {
                                        style: {
                                            color: '#000'
                                        }
                                    }
                                }
                            }
                        }
                    }
                });

                window.costCatChart.render();
            }

            const costMin = costs.length ? costs[0].date : new Date().toISOString().slice(0, 10);
            const costMax = costs.length ? costs[costs.length - 1].date : costMin;

            const $costStart = document.getElementById('costStart');
            const $costEnd = document.getElementById('costEnd');
            if ($costStart && $costEnd) {
                $costStart.value = costMin;
                $costEnd.value = costMax;
                renderCostDonut(costMin, costMax);

                const $costBtn = document.getElementById('costFilter');
                if ($costBtn) {
                    $costBtn.addEventListener('click', () => {
                        const s = $costStart.value;
                        const e = $costEnd.value;
                        if (s && e) renderCostDonut(s, e);
                    });
                }
            }

            // --- Top 5 Sprechi (card in the row with its own donut) ---
            const wastedRaw = @json($fullWastedData ?? []); // [{recipe_name, waste, date}]
            const wasted = wastedRaw
                .map(w => ({
                    name: w.recipe_name || 'Sconosciuto',
                    waste: Number(w.waste) || 0,
                    date: w.date
                }))
                .sort((a, b) => a.date.localeCompare(b.date));

            function top5Wasted(start, end) {
                const s = new Date(start),
                    e = new Date(end);
                const agg = {};
                wasted.forEach(i => {
                    const d = new Date(i.date);
                    if (!isNaN(d) && d >= s && d <= e) {
                        agg[i.name] = (agg[i.name] || 0) + i.waste;
                    }
                });
                return Object.entries(agg)
                    .map(([name, val]) => ({
                        name,
                        val
                    }))
                    .sort((a, b) => b.val - a.val)
                    .slice(0, 5);
            }

            function renderWastedRow(start, end) {
                const top = top5Wasted(start, end);

                if (window.wastedRowChart) {
                    window.wastedRowChart.destroy();
                    window.wastedRowChart = null;
                }

                if (!top.length) {
                    document.querySelector('#wastedRowPie').innerHTML =
                        '<div class="text-muted py-3">Nessun dato per l\'intervallo selezionato.</div>';
                    return;
                }

                window.wastedRowChart = new ApexCharts(document.querySelector('#wastedRowPie'), {
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    series: top.map(i => i.val),
                    labels: top.map(i => i.name),
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        y: {
                            formatter: v => v.toLocaleString()
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Totale',
                                        formatter: w => {
                                            const t = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return t.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
                window.wastedRowChart.render();
            }

            const wrMin = wasted.length ? wasted[0].date : new Date().toISOString().slice(0, 10);
            const wrMax = wasted.length ? wasted[wasted.length - 1].date : wrMin;

            const $wrStart = document.getElementById('wastedRowStart');
            const $wrEnd = document.getElementById('wastedRowEnd');
            if ($wrStart && $wrEnd) {
                $wrStart.value = wrMin;
                $wrEnd.value = wrMax;
                renderWastedRow(wrMin, wrMax);

                const $wrBtn = document.getElementById('wastedRowFilter');
                if ($wrBtn) {
                    $wrBtn.addEventListener('click', () => {
                        const s = $wrStart.value;
                        const e = $wrEnd.value;
                        if (s && e) renderWastedRow(s, e);
                    });
                }
            }

            // Stub to avoid errors for server-rendered "Resi vs Riforniti"
            const $retBtn = document.getElementById('retFilter');
            if ($retBtn) {
                $retBtn.addEventListener('click', () => {
                    console.log('Apply filter Resi vs Riforniti', {
                        from: document.getElementById('retStart')?.value,
                        to: document.getElementById('retEnd')?.value
                    });
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const fullData = @json($fullMonthlyData ?? []);
            const $barEl = document.querySelector('#barChart');
            if (!$barEl) return;

            window.barChart = new ApexCharts($barEl, {
                series: [{
                    name: 'Guadagni',
                    data: fullData.map(i => i.total)
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                xaxis: {
                    categories: fullData.map(i =>
                        new Date(i.date).toLocaleString('default', {
                            month: 'short'
                        })
                    )
                }
            });
            window.barChart.render();

            const $btn = document.getElementById('applyDateFilter');
            if ($btn) {
                $btn.addEventListener('click', () => {
                    const s = document.getElementById('startDate').value;
                    const e = document.getElementById('endDate').value;
                    if (!s || !e) return;
                    const fd = fullData.filter(i => i.date >= s && i.date <= e);
                    window.barChart.updateOptions({
                        xaxis: {
                            categories: fd.map(i =>
                                new Date(i.date).toLocaleString('default', {
                                    month: 'short'
                                })
                            )
                        }
                    });
                    window.barChart.updateSeries([{
                        name: 'Guadagni',
                        data: fd.map(i => i.total)
                    }]);
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const fullSold = @json($fullSoldData ?? []); // [{recipe_name, sold, date}]
            const fullWasted = @json($fullWastedData ?? []); // [{recipe_name, waste, date}]

            function aggregateTop(data, key) {
                const agg = {};
                data.forEach(i => agg[i.recipe_name] = (agg[i.recipe_name] || 0) + (Number(i[key]) || 0));
                return Object.entries(agg)
                    .map(([name, val]) => ({
                        name,
                        val
                    }))
                    .sort((a, b) => b.val - a.val)
                    .slice(0, 5);
            }

            // init Sold donut
            const soldTop = aggregateTop(fullSold, 'sold');
            window.soldChart = new ApexCharts(document.querySelector('#soldPie'), {
                chart: {
                    type: 'donut',
                    height: 250
                },
                series: soldTop.map(i => i.val),
                labels: soldTop.map(i => i.name)
            });
            window.soldChart.render();

            // init Wasted donut
            const wastedTop = aggregateTop(fullWasted, 'waste');
            window.wastedChart = new ApexCharts(document.querySelector('#wastedPie'), {
                chart: {
                    type: 'donut',
                    height: 250
                },
                series: wastedTop.map(i => i.val),
                labels: wastedTop.map(i => i.name)
            });
            window.wastedChart.render();

            // bind Sold filter
            const $soldBtn = document.getElementById('soldFilter');
            if ($soldBtn) {
                $soldBtn.addEventListener('click', () => {
                    const s = document.getElementById('soldStart').value;
                    const e = document.getElementById('soldEnd').value;
                    if (!s || !e) return;
                    const fd = fullSold.filter(i => i.date >= s && i.date <= e);
                    const top = aggregateTop(fd, 'sold');
                    const $tbody = document.querySelector('#soldTable tbody');
                    if ($tbody) {
                        $tbody.innerHTML = top.map(i =>
                            `<tr><td>${i.name}</td><td class="text-end">${i.val}</td></tr>`).join('');
                    }
                    window.soldChart.updateOptions({
                        labels: top.map(i => i.name)
                    });
                    window.soldChart.updateSeries(top.map(i => i.val));
                });
            }

            // bind Wasted filter
            const $wastedBtn = document.getElementById('wastedFilter');
            if ($wastedBtn) {
                $wastedBtn.addEventListener('click', () => {
                    const s = document.getElementById('wastedStart').value;
                    const e = document.getElementById('wastedEnd').value;
                    if (!s || !e) return;
                    const fd = fullWasted.filter(i => i.date >= s && i.date <= e);
                    const top = aggregateTop(fd, 'waste');
                    const $tbody = document.querySelector('#wastedTable tbody');
                    if ($tbody) {
                        $tbody.innerHTML = top.map(i =>
                            `<tr><td>${i.name}</td><td class="text-end">${i.val}</td></tr>`).join('');
                    }
                    window.wastedChart.updateOptions({
                        labels: top.map(i => i.name)
                    });
                    window.wastedChart.updateSeries(top.map(i => i.val));
                });
            }
        });

     // === Incidenza Costi vs Ricavi — single source of truth ===
document.addEventListener('DOMContentLoaded', function () {
  const costs   = @json($fullCostData ?? []);   // [{date, amount, category}]
  const incomes = @json($fullIncomeData ?? []); // [{date, amount, category}]

  const CATS       = ['Materie prime', 'Stipendi + TFR', 'Affitto', 'Energia elettrica', 'Altri costi'];
  const baseColors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'];
  const zeroColor  = '#e0e0e0';

  const $incStart = document.getElementById('incStart');
  const $incEnd   = document.getElementById('incEnd');
  const $incBtn   = document.getElementById('incFilter');
  const $el       = document.querySelector('#incomeCostDonut');

  // income category dropdown (optional, keeps existing behavior)
  let $incomeSel = document.getElementById('incIncomeCategory');
  if (!$incomeSel) {
    $incomeSel = document.createElement('select');
    $incomeSel.id = 'incIncomeCategory';
    $incomeSel.className = 'form-select form-select-sm';
    const wrap = document.createElement('div');
    wrap.className = 'col-12 col-md-auto';
    wrap.appendChild($incomeSel);
    const row = $incBtn && $incBtn.closest('.row');
    if (row) row.insertBefore(wrap, $incBtn.closest('.col-12') || row.lastChild);
  }

  const catsIncome = Array.from(new Set(incomes.map(i => (i.category || 'Senza categoria').trim()))).sort();
  $incomeSel.innerHTML = ['<option value="__all__">Tutte le entrate</option>']
    .concat(catsIncome.map(c => `<option value="${String(c).replace(/"/g,'&quot;')}">${String(c).replace(/</g,'&lt;')}</option>`))
    .join('');

  const toMs = d => new Date(d).getTime();

  function sumIncome(start, end, cat) {
    const s = toMs(start), e = toMs(end);
    let tot = 0;
    for (const i of incomes) {
      const m = toMs(i.date); if (isNaN(m) || m < s || m > e) continue;
      const label = (i.category || 'Senza categoria').trim();
      if (cat && cat !== '__all__' && label !== cat) continue;
      tot += Number(i.amount) || 0;
    }
    return tot;
  }

  function groupCosts(start, end) {
    const s = toMs(start), e = toMs(end);
    const out = Object.fromEntries(CATS.map(c => [c, 0]));
    for (const c of costs) {
      const m = toMs(c.date); if (isNaN(m) || m < s || m > e) continue;
      const amt = Number(c.amount) || 0;
      const cat = (c.category || '').toLowerCase();
      if (cat.includes('materie prime') || cat.includes('raw materials')) out['Materie prime'] += amt;
      else if (cat.includes('stipendi') || cat.includes('tfr') || cat.includes('salary')) out['Stipendi + TFR'] += amt;
      else if (cat.includes('affitto')) out['Affitto'] += amt;
      else if (cat.includes('energia elettrica') || cat.includes('electricity')) out['Energia elettrica'] += amt;
      else out['Altri costi'] += amt;
    }
    return out;
  }

  function render(start, end, incomeCat) {
    const sums        = groupCosts(start, end);
    const incomeTotal = sumIncome(start, end, incomeCat);
    const rawSeries   = CATS.map(k => sums[k]);
    const costTotal   = rawSeries.reduce((a,b) => a + b, 0);

    if (window.incChart) { try { window.incChart.destroy(); } catch(_){} window.incChart = null; }

    if (!incomeTotal) {
      $el.innerHTML = '<div class="text-muted py-3">Nessun ricavo nel periodo selezionato.</div>';
      return;
    }

    // slices = cost amounts; percentages = cost / total income
    const series = rawSeries.map(v => v > 0 ? v : 0.0001);
    const colors = rawSeries.map((v,i) => v === 0 ? zeroColor : baseColors[i]);

    window.incChart = new ApexCharts($el, {
      chart: { type: 'donut', height: 300 },
      series,
      labels: CATS,
      colors,
      legend: { position: 'right', labels: { colors: '#000' } },

      tooltip: {
        y: {
          formatter: (val) => {
            const pct = (val / incomeTotal) * 100;
            return (val > incomeTotal)
              ? `€ ${val.toLocaleString(undefined,{minimumFractionDigits:2})}`
              : `€ ${val.toLocaleString(undefined,{minimumFractionDigits:2})} · ${pct.toFixed(1)}% dei ricavi`;
          }
        }
      },

      plotOptions: {
        pie: {
          donut: {
            size: '70%',
            labels: {
              show: true,
              name: { show: false },
              value: {
                show: true,
                formatter: () => `€ ${incomeTotal.toLocaleString(undefined,{minimumFractionDigits:2})}`, // center = income
                style: { fontSize: '18px', fontWeight: 700, color: '#000' }
              },
              total: {
                show: true,
                label: 'Netto',
                formatter: () => `€ ${(incomeTotal - costTotal).toLocaleString(undefined,{minimumFractionDigits:2})}`,
                style: { fontSize: '13px', fontWeight: 600, color: '#000' }
              }
            }
          }
        }
      },

      dataLabels: {
        enabled: true,
        formatter: (_ignoredPercent, opts) => {
          const val = rawSeries[opts.seriesIndex] || 0;
          if (val > incomeTotal) return `€ ${Number(val).toLocaleString()}`; // hide % when > income
          const pct = (val / incomeTotal) * 100;
          return `€ ${Number(val).toLocaleString()} (${pct.toFixed(1)}%)`;
        },
        style: { colors: ['#000'] }
      },

      responsive: [{ breakpoint: 768, options: { legend: { position: 'bottom' } } }]
    });

    window.incChart.render();
  }

  // initial range from income data
  const dates = incomes.map(i => i.date).filter(Boolean).sort();
  const d0 = dates[0] || new Date().toISOString().slice(0,10);
  const d1 = dates[dates.length-1] || d0;
  if ($incStart) $incStart.value ||= d0;
  if ($incEnd)   $incEnd.value   ||= d1;

  render($incStart?.value, $incEnd?.value, $incomeSel.value);

  $incBtn?.addEventListener('click', e => {
    e.preventDefault();
    render($incStart.value, $incEnd.value, $incomeSel.value);
  });
  $incomeSel.addEventListener('change', () => render($incStart.value, $incEnd.value, $incomeSel.value));
});


        document.addEventListener('DOMContentLoaded', function() {
            const $range = document.getElementById('globalRange');
            const $start = document.getElementById('globalStart');
            const $end = document.getElementById('globalEnd');
            const $apply = document.getElementById('applyGlobalFilters');

            function pad(n) {
                return (n < 10 ? '0' : '') + n;
            }

            function iso(d) {
                return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
            }

            function quarterBounds(date) {
                const m = date.getMonth();
                const q = Math.floor(m / 3); // 0..3
                const qs = new Date(date.getFullYear(), q * 3, 1);
                const qe = new Date(date.getFullYear(), q * 3 + 3, 0);
                return [qs, qe];
            }

            function setPresetRange(key) {
                const now = new Date();
                let s = new Date(),
                    e = new Date();

                switch (key) {
                    case 'this_month':
                        s = new Date(now.getFullYear(), now.getMonth(), 1);
                        e = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        break;
                    case 'last_month':
                        s = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                        e = new Date(now.getFullYear(), now.getMonth(), 0);
                        break;
                    case 'this_quarter': {
                        const [qs, qe] = quarterBounds(now);
                        s = qs;
                        e = qe;
                        break;
                    }
                    case 'this_year':
                        s = new Date(now.getFullYear(), 0, 1);
                        e = new Date(now.getFullYear(), 11, 31);
                        break;
                    case 'last_12m':
                        e = now;
                        s = new Date(now);
                        s.setFullYear(s.getFullYear() - 1);
                        s.setDate(s.getDate() + 1);
                        break;
                    default:
                        return; // custom
                }
                $start.value = iso(s);
                $end.value = iso(e);
            }

            if ($range) {
                $range.addEventListener('change', () => setPresetRange($range.value));
            }

            if ($apply) {
                $apply.addEventListener('click', () => {
                    const s = $start?.value;
                    const e = $end?.value;
                    if (!s || !e) return;

                    // Push range into each card's local filter and click its "Applica" button
                    const setVal = (id, val) => {
                        const el = document.getElementById(id);
                        if (el) el.value = val;
                    };
                    const click = id => {
                        const btn = document.getElementById(id);
                        if (btn) btn.click();
                    };

                    // Monthly bar chart
                    setVal('startDate', s);
                    setVal('endDate', e);
                    click('applyDateFilter');

                    // Sold + Wasted (cards)
                    setVal('soldStart', s);
                    setVal('soldEnd', e);
                    click('soldFilter');
                    setVal('wastedStart', s);
                    setVal('wastedEnd', e);
                    click('wastedFilter');

                    // Incassi per categoria
                    setVal('revStart', s);
                    setVal('revEnd', e);
                    click('revFilter');

                    // Costi per categoria (right donut) + Top 5 Sprechi in row
                    setVal('costStart', s);
                    setVal('costEnd', e);
                    click('costFilter');
                    setVal('wastedRowStart', s);
                    setVal('wastedRowEnd', e);
                    click('wastedRowFilter');

                    // Incidenza costi vs ricavi
                    setVal('incStart', s);
                    setVal('incEnd', e);
                    click('incFilter');

                    // Resi vs Riforniti (server-rendered; stub)
                    setVal('retStart', s);
                    setVal('retEnd', e);
                    click('retFilter');
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // --- Incassi per Categoria ---
            const incomesRaw = @json($fullIncomeData ?? []);
            const incomes = incomesRaw.map(i => ({
                date: i.date,
                amount: Number(i.amount) || 0,
                // Blade JS (Incassi per Categoria) — change ONLY the category mapping line
                category: (i.category && i.category !== '—' && i.category.toLowerCase() !==
                    'uncategorized') ? i.category : 'Senza categoria'
            })).sort((a, b) => a.date.localeCompare(b.date));

            function incFilterAndGroup(start, end) {
                const s = new Date(start),
                    e = new Date(end);
                const grouped = {};
                let total = 0;
                incomes.forEach(i => {
                    const d = new Date(i.date);
                    if (!isNaN(d) && d >= s && d <= e) {
                        total += i.amount;
                        grouped[i.category] = (grouped[i.category] || 0) + i.amount;
                    }
                });
                return {
                    grouped,
                    total
                };
            }

            function renderRevChart(start, end) {
                const {
                    grouped,
                    total
                } = incFilterAndGroup(start, end);
                if (window.revChart) {
                    window.revChart.destroy();
                    window.revChart = null;
                }
                const labels = Object.keys(grouped),
                    series = Object.values(grouped);
                if (!series.length || total === 0) {
                    document.querySelector('#revCategoryChart').innerHTML =
                        '<div class="text-muted py-3">Nessun dato per l\'intervallo selezionato.</div>';
                    return;
                }
                window.revChart = new ApexCharts(document.querySelector("#revCategoryChart"), {
                    chart: {
                        type: 'donut',
                        height: 280
                    },
                    series,
                    labels,
                    legend: {
                        position: 'bottom',
                        labels: {
                            colors: '#000'
                        } // legend text black
                    },
                    tooltip: {
                        y: {
                            formatter: v => `€${v.toLocaleString()}`
                        }
                    },

                    // data labels inside slices (value + percent)
                    dataLabels: {
                        formatter: (percent, opts) => {
                            const val = opts.w.config.series[opts.seriesIndex];
                            return `${val.toLocaleString()}€ (${percent.toFixed(1)}%)`;
                        },
                        style: {
                            colors: ['#000'] // force black
                        }
                    },

                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        style: {
                                            color: '#000',
                                            fontSize: '13px'
                                        }
                                    },
                                    value: {
                                        show: true,
                                        style: {
                                            color: '#000',
                                            fontSize: '14px',
                                            fontWeight: '600'
                                        },
                                        formatter: val => `€${Number(val).toLocaleString()}`
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: () =>
                                            `€${total.toLocaleString(undefined,{minimumFractionDigits:2})}`,
                                        style: {
                                            color: '#000',
                                            fontSize: '16px',
                                            fontWeight: '700'
                                        }
                                    }
                                }
                            }
                        }
                    }
                });

                window.revChart.render();
            }

            const revMin = incomes.length ? incomes[0].date : new Date().toISOString().slice(0, 10);
            const revMax = incomes.length ? incomes[incomes.length - 1].date : revMin;
            const $revStart = document.getElementById('revStart');
            const $revEnd = document.getElementById('revEnd');
            const $revBtn = document.getElementById('revFilter');
            if ($revStart && $revEnd) {
                $revStart.value = revMin;
                $revEnd.value = revMax;
                renderRevChart(revMin, revMax);
                if ($revBtn) {
                    $revBtn.addEventListener('click', () => {
                        const s = $revStart.value;
                        const e = $revEnd.value;
                        if (s && e) renderRevChart(s, e);
                    });
                }
            }

            // --- Costi per Categoria ---
            const costsRaw = @json($fullCostData ?? []);
            const costs = costsRaw.map(c => ({
                date: c.date,
                amount: Number(c.amount) || 0,
                category: c.category || 'Senza categoria'
            })).sort((a, b) => a.date.localeCompare(b.date));

            function groupCosts(start, end) {
                const s = new Date(start),
                    e = new Date(end);
                const g = {};
                let t = 0;
                costs.forEach(c => {
                    const d = new Date(c.date);
                    if (!isNaN(d) && d >= s && d <= e) {
                        t += c.amount;
                        g[c.category] = (g[c.category] || 0) + c.amount;
                    }
                });
                return {
                    grouped: g,
                    total: t
                };
            }

            function renderCostDonut(start, end) {
                const {
                    grouped,
                    total
                } = groupCosts(start, end);
                if (window.costCatChart) {
                    window.costCatChart.destroy();
                    window.costCatChart = null;
                }
                const labels = Object.keys(grouped),
                    series = Object.values(grouped);
                if (!series.length || total === 0) {
                    document.querySelector('#costByCategoryDonut').innerHTML =
                        '<div class="text-muted py-3">Nessun dato per l\'intervallo selezionato.</div>';
                    return;
                }
                window.costCatChart = new ApexCharts(document.querySelector('#costByCategoryDonut'), {
                    chart: {
                        type: 'donut',
                        height: 320,
                        id: 'costByCategoryDonutChart'
                    },
                    series,
                    labels,
                    legend: {
                        position: 'right',
                        labels: {
                            colors: '#000'
                        } // legend text black
                    },
                    tooltip: {
                        y: {
                            formatter: val => '€ ' + Number(val).toFixed(2)
                        }
                    },

                    dataLabels: {
                        formatter: (percent, opts) => {
                            const val = opts.w.config.series[opts.seriesIndex];
                            return `€ ${Number(val).toFixed(2)} (${percent.toFixed(1)}%)`;
                        },
                        style: {
                            colors: ['#000'] // <-- black
                        }
                    },

                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Totale',
                                        formatter: () => '€ ' + total.toFixed(2),
                                        style: {
                                            color: '#000'
                                        } // total color
                                    },
                                    value: {
                                        formatter: val => '€ ' + Number(val).toFixed(2),
                                        style: {
                                            color: '#000'
                                        }
                                    },
                                    name: {
                                        style: {
                                            color: '#000'
                                        }
                                    }
                                }
                            }
                        }
                    }
                });

                window.costCatChart.render();
            }

            const costMin = costs.length ? costs[0].date : new Date().toISOString().slice(0, 10);
            const costMax = costs.length ? costs[costs.length - 1].date : costMin;
            const $costStart = document.getElementById('costStart');
            const $costEnd = document.getElementById('costEnd');
            const $costBtn = document.getElementById('costFilter');
            if ($costStart && $costEnd) {
                $costStart.value = costMin;
                $costEnd.value = costMax;
                renderCostDonut(costMin, costMax);
                if ($costBtn) {
                    $costBtn.addEventListener('click', () => {
                        const s = $costStart.value;
                        const e = $costEnd.value;
                        if (s && e) renderCostDonut(s, e);
                    });
                }
            }

            // --- Top 5 Sprechi ---
            const wastedRaw = @json($fullWastedData ?? []);
            const wasted = wastedRaw.map(w => ({
                name: w.recipe_name || 'Sconosciuto',
                waste: Number(w.waste) || 0,
                date: w.date
            })).sort((a, b) => a.date.localeCompare(b.date));

            function top5Wasted(start, end) {
                const s = new Date(start),
                    e = new Date(end);
                const agg = {};
                wasted.forEach(i => {
                    const d = new Date(i.date);
                    if (!isNaN(d) && d >= s && d <= e) {
                        agg[i.name] = (agg[i.name] || 0) + i.waste;
                    }
                });
                return Object.entries(agg)
                    .map(([name, val]) => ({
                        name,
                        val
                    }))
                    .sort((a, b) => b.val - a.val)
                    .slice(0, 5);
            }

            function renderWastedRow(start, end) {
                const top = top5Wasted(start, end);
                if (window.wastedRowChart) {
                    window.wastedRowChart.destroy();
                    window.wastedRowChart = null;
                }
                if (!top.length) {
                    document.querySelector('#wastedRowPie').innerHTML =
                        '<div class="text-muted py-3">Nessun dato per l\'intervallo selezionato.</div>';
                    return;
                }
                window.wastedRowChart = new ApexCharts(document.querySelector('#wastedRowPie'), {
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    series: top.map(i => i.val),
                    labels: top.map(i => i.name),
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        y: {
                            formatter: v => v.toLocaleString()
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Totale',
                                        formatter: w => {
                                            const t = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return t.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
                window.wastedRowChart.render();
            }

            const wrMin = wasted.length ? wasted[0].date : new Date().toISOString().slice(0, 10);
            const wrMax = wasted.length ? wasted[wasted.length - 1].date : wrMin;
            const $wrStart = document.getElementById('wastedRowStart');
            const $wrEnd = document.getElementById('wastedRowEnd');
            const $wrBtn = document.getElementById('wastedRowFilter');
            if ($wrStart && $wrEnd) {
                $wrStart.value = wrMin;
                $wrEnd.value = wrMax;
                renderWastedRow(wrMin, wrMax);
                if ($wrBtn) {
                    $wrBtn.addEventListener('click', () => {
                        const s = $wrStart.value;
                        const e = $wrEnd.value;
                        if (s && e) renderWastedRow(s, e);
                    });
                }
            }

            // (Optional) Stub for Ret filter to avoid errors; server-side chart stays as-is.
            const $retBtn = document.getElementById('retFilter');
            if ($retBtn) {
                $retBtn.addEventListener('click', () => {
                    const $retStart = document.getElementById('retStart');
                    const $retEnd = document.getElementById('retEnd');
                    console.log('Apply filter Resi vs Riforniti', {
                        from: $retStart ? $retStart.value : null,
                        to: $retEnd ? $retEnd.value : null
                    });
                });
            }
        });
    </script>

@endsection
