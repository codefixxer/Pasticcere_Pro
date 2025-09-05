@extends('frontend.layouts.app')

@section('title', 'Crea Ricetta')

@php
    $sectionHeaderStyle = 'style="background-color: #041930; color: #e2ae76;"'; // not used anymore but kept if layout expects it
    $isEdit = isset($recipe);
    $formAction = $isEdit ? route('recipes.update', $recipe->id) : route('recipes.store');
@endphp

{{-- ===== Styles: Modern theme, animations, glass cards ===== --}}
@section('styles')
    <style>
        :root {
            --primary: #041930;
            --accent: #e2ae76;
            --primary-900: #02101f;
            --primary-800: #07223d;
            --text: #0f172a;
            --muted: #6b7280;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --ring: 0 0 0 0.25rem rgba(226, 174, 118, .25);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--accent) rgba(255, 255, 255, .25);
        }

        ::-webkit-scrollbar {
            height: 8px;
            width: 8px
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 8px
        }

        ::-webkit-scrollbar-track {
            background: transparent
        }

        body {
            background: linear-gradient(180deg, #f6f7fb 0%, #ffffff 100%);
        }

        .container {
            max-width: 1100px
        }

        .brand-header {
            background: linear-gradient(135deg, var(--primary) 0%, #07223d 100%);
            color: var(--accent);
            border: 0;
            border-bottom: 1px solid rgba(226, 174, 118, .25);
        }

        .brand-header h5 {
            color: var(--accent);
            letter-spacing: .3px
        }

        .card {
            border: 1px solid rgba(4, 25, 48, .06);
            border-radius: 18px !important;
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(4, 25, 48, .10)
        }

        .table {
            --bs-table-striped-bg: rgba(4, 25, 48, .02);
            --bs-table-hover-bg: rgba(226, 174, 118, .08);
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8fafc;
            border-bottom: 1px solid rgba(4, 25, 48, .08);
        }

        .table td,
        .table th {
            vertical-align: middle
        }

        .input-group-text {
            background: #f7f7fb;
            border-color: rgba(4, 25, 48, .15)
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid rgba(4, 25, 48, .18);
            background: #fff;
            transition: box-shadow .15s ease, border-color .15s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: var(--ring);
        }

        .form-label {
            font-weight: 600;
            color: var(--text)
        }

        .btn-accent {
            --bs-btn-bg: var(--accent);
            --bs-btn-border-color: var(--accent);
            --bs-btn-hover-bg: #f0c08f;
            --bs-btn-hover-border-color: #f0c08f;
            --bs-btn-color: var(--primary);
            border-width: 2px;
            border-radius: 12px;
            font-weight: 700;
            letter-spacing: .2px;
        }

        .btn-outline-light {
            border-color: rgba(255, 255, 255, .4)
        }

        .badge-soft {
            background: rgba(226, 174, 118, .15);
            color: var(--accent);
            border: 1px solid rgba(226, 174, 118, .35);
        }

        /* Mini (lower) banner for tutorials/promos */
        .mini-banner {
            margin-top: 1.75rem;
            background: linear-gradient(90deg, var(--primary) 0%, #0b2b53 100%);
            border: 1px solid rgba(226, 174, 118, .35);
            border-radius: 16px;
            padding: 10px 14px;
            color: #fff;
            box-shadow: 0 8px 24px rgba(4, 25, 48, .25);
        }

        .scroller {
            display: flex;
            gap: .5rem;
            overflow: auto;
            scroll-snap-type: x mandatory;
            padding-bottom: 6px;
        }

        .chip {
            scroll-snap-align: center;
            white-space: nowrap;
            padding: 8px 12px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(226, 174, 118, .4);
            color: #fff;
            border-radius: 999px;
            transition: transform .15s ease, background .15s ease;
        }

        .chip:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, .14)
        }

        .chip i {
            margin-right: 6px;
            color: var(--accent)
        }

        /* Reveal on scroll */
        .reveal {
            opacity: 0;
            transform: translateY(14px);
            transition: .5s ease
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0)
        }

        .submit-bar {
            position: sticky;
            bottom: 12px;
            z-index: 50;
            display: flex;
            justify-content: end;
            margin-top: -6px;
        }

        .submit_bar_inner {
            background: #ffffffd9;
            backdrop-filter: blur(6px);
            border: 1px solid rgba(4, 25, 48, .08);
            border-radius: 14px;
            padding: 10px;
            box-shadow: 0 10px 24px rgba(4, 25, 48, .12);
        }

        /* Icon circle in headers (SVG/BI) */
        .header-icon {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            margin-right: .5rem;
            border-radius: 10px;
            background: rgba(226, 174, 118, .14);
            border: 1px solid rgba(226, 174, 118, .35)
        }

        .header-icon svg {
            width: 18px;
            height: 18px;
            fill: var(--accent)
        }
    
    
    
    /* ===== Mini-Banner (elegant glass + glow) ===== */
:root{
  --primary:#041930; --primary-700:#0b2b53; --accent:#e2ae76;
  --chip-bg: rgba(255,255,255,.10); --chip-br: rgba(226,174,118,.42);
  --chip-bg-hover: rgba(255,255,255,.18);
}

.mini-banner{
  position:relative; margin-top:1.75rem;
  padding:14px 14px 16px;
  color:#fff;
  background:
    radial-gradient(120% 140% at 0% 0%, rgba(226,174,118,.25), transparent 60%),
    linear-gradient(135deg, var(--primary) 0%, var(--primary-700) 100%);
  border-radius:18px;
  border:1px solid rgba(226,174,118,.28);
  box-shadow:0 16px 40px rgba(4,25,48,.28);
  backdrop-filter: blur(6px);
}

/* header row */
.mini-head{
  display:flex; align-items:center; justify-content:space-between;
  gap:12px; margin-bottom:10px;
}
.mini-title{ position:relative; display:flex; align-items:center; gap:10px; }
.mini-icon{
  width:34px; height:34px; display:grid; place-items:center;
  border-radius:10px;
  background: rgba(226,174,118,.16);
  border:1px solid rgba(226,174,118,.36);
  color:var(--accent);
}
.mini-underline{
  position:absolute; left:44px; right:-10px; bottom:-6px; height:3px;
  border-radius:3px;
  background: linear-gradient(90deg, rgba(226,174,118,.85), rgba(226,174,118,.35));
  filter: drop-shadow(0 2px 4px rgba(226,174,118,.35));
  animation: sweep 3.4s infinite;
}
@keyframes sweep{
  0%{transform: scaleX(0); transform-origin: left;}
  50%{transform: scaleX(1);}
  100%{transform: scaleX(0); transform-origin: right;}
}

/* scroll controls */
.mini-ctrl{ display:flex; gap:6px; }
.mini-btn{
  width:34px; height:34px; display:grid; place-items:center;
  background:transparent; color:#fff; border:1px solid rgba(226,174,118,.35);
  border-radius:10px; transition:.15s ease; backdrop-filter: blur(4px);
}
.mini-btn:hover{ background: rgba(255,255,255,.08); transform: translateY(-1px); }
.mini-btn:disabled{ opacity:.4; cursor:not-allowed; transform:none; }

/* chips scroller */
.scroller{
  display:flex; gap:.65rem; overflow:auto; padding:6px 2px 4px;
  scroll-snap-type: x mandatory; scrollbar-width: thin;
}
.scroller::-webkit-scrollbar{ height:8px; }
.scroller::-webkit-scrollbar-thumb{ background:var(--accent); border-radius:8px; }
.scroller::-webkit-scrollbar-track{ background: transparent; }

/* edge fade */
.with-fade{ mask-image: linear-gradient( to right, transparent 0, #000 28px, #000 calc(100% - 28px), transparent 100% ); }

/* chip */
.chip{
  display:inline-flex; align-items:center; gap:8px;
  padding:10px 14px; border-radius:999px;
  background: var(--chip-bg);
  border:1px solid var(--chip-br);
  color:#fff; text-decoration:none; white-space:nowrap; scroll-snap-align:center;
  transition: transform .18s ease, background .18s ease, border-color .18s ease, box-shadow .18s ease;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.06), 0 6px 14px rgba(4,25,48,.18);
}
.chip:hover{
  transform: translateY(-2px);
  background: var(--chip-bg-hover);
  border-color: rgba(226,174,118,.6);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.10), 0 10px 22px rgba(4,25,48,.28);
}
.chip:focus-visible{ outline: none; box-shadow: 0 0 0 0.25rem rgba(226,174,118,.33); }

.chip-icon{
  width:26px; height:26px; display:grid; place-items:center;
  border-radius:50%;
  background: rgba(226,174,118,.18);
  border:1px solid rgba(226,174,118,.35);
  color:var(--accent);
}

/* reveal */
.reveal{ opacity:0; transform: translateY(14px); transition:.5s ease; }
.reveal.visible{ opacity:1; transform: translateY(0); }

    </style>
@endsection

@section('content')
    <div class="container py-4">

        {{-- Validation errors will appear inline via @error(kept) --}}

        <form method="POST" action="{{ $formAction }}" class="reveal" id="recipeForm">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            {{-- ===== Dettagli Ricetta ===== --}}
            <div class="card mb-4 shadow-sm reveal">
                <div class="card-header brand-header d-flex align-items-center">
                    <div class="header-icon" aria-hidden="true">
                        {{-- whisk icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path
                                d="M356.334,494.134c43.124-12.321,153.745-52.878,155.636-110.035c1.88-57.184-85.049-58.301-139.549-49.294L356.334,494.134z" />
                            <path
                                d="M17.864,155.664l159.328-16.088c9.01-54.497,7.893-141.426-49.291-139.546C70.742,1.918,30.184,112.54,17.864,155.664z" />
                            <path
                                d="M182.525,479.291c17.563,9.501,39.263,18.014,58.835,23.244c44.236,11.799,107.683,14.791,113.83-4.066c6.165-18.838,22.757-161.567,15.537-175.497c-7.204-13.913-32.605-22.372-47.628-26.378c-5.971-1.59-13.743-3.393-21.822-4.467L182.525,479.291z" />
                            <path
                                d="M9.466,270.641c5.227,19.569,13.741,41.27,23.244,58.835l187.165-118.752c-1.076-8.081-2.879-15.851-4.47-21.824c-4.015-15.03-12.462-40.422-26.375-47.626c-13.93-7.219-156.661,9.37-175.497,15.537C-5.325,162.957-2.332,226.404,9.466,270.641z" />
                            <path
                                d="M277.509,234.492c-10.833-10.833-30.659-28.329-46.765-27.786C214.616,207.227,48.711,314.21,34.496,328.424c-14.223,14.223,18.794,66.572,50.651,98.429c31.855,31.855,84.205,64.874,98.429,50.651c14.215-14.215,121.194-180.123,121.717-196.251C305.836,265.147,288.341,245.322,277.509,234.492z" />
                        </svg>
                    </div>
                    <h5 class="mb-0">Dettagli Ricetta</h5>
                    <span class="badge badge-soft ms-3">Nuova</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label for="recipeName" class="form-label">Nome</label>
                            <input type="text" id="recipeName" name="recipe_name"
                                class="form-control @error('recipe_name') is-invalid @enderror"
                                placeholder="Torta al cioccolato"
                                value="{{ old('recipe_name', $isEdit ? $recipe->recipe_name : '') }}">
                            @error('recipe_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="recipeCategory" class="form-label">Categoria</label>
                            <select id="recipeCategory" name="recipe_category_id"
                                class="form-select @error('recipe_category_id') is-invalid @enderror">
                                <option value="">Scegli…</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('recipe_category_id', $isEdit ? $recipe->recipe_category_id : '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('recipe_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="recipeDept" class="form-label">Reparto</label>
                            <select id="recipeDept" name="department_id"
                                class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">Scegli…</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ old('department_id', $isEdit ? $recipe->department_id : '') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- ===== Ingredienti ===== --}}
            <div class="card mb-4 shadow-sm reveal">
                <div class="card-header brand-header d-flex align-items-center">
                    <div class="header-icon" aria-hidden="true">
                        {{-- sparkles icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path
                                d="M479.605,91.769c-23.376,23.376-66.058,33.092-79.268,19.882c-13.21-13.21-3.494-55.892,19.883-79.268s85.999-26.614,85.999-26.614S502.982,68.393,479.605,91.769z" />
                            <path
                                d="M506.218,5.785L400.345,111.658c13.218,13.2,55.888,3.483,79.26-19.889C502.864,68.511,506.186,6.411,506.218,5.785z" />
                            <path
                                d="M432.367,200.156c-33.059,0-70.11-23.311-70.11-41.992s37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S465.426,200.156,432.367,200.156z" />
                            <path
                                d="M311.84,79.629c0,33.059,23.311,70.11,41.992,70.11s41.992-37.052,41.992-70.11S353.832,0,353.832,0S311.84,46.571,311.84,79.629z" />
                            <path
                                d="M367.516,265.006c-33.059,0-70.11-23.311-70.11-41.992s37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S400.575,265.006,367.516,265.006z" />
                            <path
                                d="M246.99,144.48c0,33.059,23.311,70.11,41.992,70.11c18.681,0,41.992-37.052,41.992-70.11S288.982,64.85,288.982,64.85S246.99,111.421,246.99,144.48z" />
                            <path
                                d="M302.666,329.857c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S335.726,329.857,302.666,329.857z" />
                            <path
                                d="M182.14,209.33c0,33.059,23.311,70.11,41.992,70.11s41.992-37.052,41.992-70.11s-41.992-79.629-41.992-79.629S182.14,176.27,182.14,209.33z" />
                            <path
                                d="M237.025,395.498c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S270.085,395.498,237.025,395.498z" />
                            <path
                                d="M116.498,274.97c0,33.059,23.31,70.11,41.992,70.11s41.992-37.052,41.992-70.11s-41.992-79.629-41.992-79.629S116.498,241.912,116.498,274.97z" />
                            <path
                                d="M170.438,462.084c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S203.497,462.084,170.438,462.084z" />
                            <path
                                d="M49.912,341.558c0,33.059,23.31,70.11,41.992,70.11s41.992-37.052,41.992-70.11s-41.992-79.629-41.992-79.629S49.912,308.499,49.912,341.558z" />
                            <path
                                d="M4.917,507.087c-6.552-6.552-6.552-17.174,0-23.725L404.75,83.527c6.552-6.552,17.174-6.552,23.725,0c6.552,6.552,6.552,17.174,0,23.725L28.643,507.087C22.091,513.637,11.468,513.637,4.917,507.087z" />
                        </svg>
                    </div>
                    <h5 class="mb-0">Ingredienti</h5>
                    <button type="button" class="btn btn-outline-light ms-auto" data-bs-toggle="modal"
                        data-bs-target="#addIngredientModal"><i class="bi bi-plus-lg"></i> Nuovo Ingrediente</button>
                </div>

                <style>
                    .incidence-col {
                        display: none !important
                    }
                </style>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table  data-page-length="25"class="table table-hover table-striped mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Ingrediente</th>
                                    <th class="text-center">Qtà (g)</th>
                                    <th class="text-center">Costo (€)</th>
                                    <th class="text-center incidence-col">Incidenza (%)</th>
                                    <th class="text-center">Azione</th>
                                </tr>
                            </thead>
                            <tbody id="ingredientsTable">
                                @php $oldRows = old('ingredients', []); @endphp

                                @if (count($oldRows) > 0)
                                    @foreach ($oldRows as $i => $old)
                                        <tr class="ingredient-row">
                                            <td>
                                                <select name="ingredients[{{ $i }}][id]"
                                                    class="form-select ingredient-select @error('ingredients.' . $i . '.id') is-invalid @enderror">
                                                    <option value="">Seleziona ingrediente…</option>
                                                    @foreach ($ingredients as $ing)
                                                        <option value="{{ $ing->id }}"
                                                            data-price="{{ $ing->price_per_kg }}"
                                                            {{ (int) ($old['id'] ?? '') === $ing->id ? 'selected' : '' }}>
                                                            {{ $ing->ingredient_name }} (€{{ $ing->price_per_kg }}/kg)
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('ingredients.' . $i . '.id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                    name="ingredients[{{ $i }}][quantity]"
                                                    class="form-control text-center ingredient-quantity @error('ingredients.' . $i . '.quantity') is-invalid @enderror"
                                                    value="{{ $old['quantity'] ?? '' }}">
                                                @error('ingredients.' . $i . '.quantity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="ingredients[{{ $i }}][cost]"
                                                    class="form-control text-center ingredient-cost" readonly
                                                    value="{{ $old['cost'] ?? '' }}">
                                            </td>
                                            <td class="incidence-col">
                                                <input type="text" class="form-control text-center ingredient-incidence"
                                                    readonly>
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm remove-ingredient">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif($isEdit && $recipe->ingredients->isNotEmpty())
                                    @foreach ($recipe->ingredients as $i => $line)
                                        <tr class="ingredient-row">
                                            <td>
                                                <select name="ingredients[{{ $i }}][id]"
                                                    class="form-select ingredient-select @error('ingredients.' . $i . '.id') is-invalid @enderror">
                                                    <option value="">Seleziona ingrediente…</option>
                                                    @foreach ($ingredients as $ing)
                                                        <option value="{{ $ing->id }}"
                                                            data-price="{{ $ing->price_per_kg }}"
                                                            {{ $ing->id === $line->ingredient_id ? 'selected' : '' }}>
                                                            {{ $ing->ingredient_name }} (€{{ $ing->price_per_kg }}/kg)
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('ingredients.' . $i . '.id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                    name="ingredients[{{ $i }}][quantity]"
                                                    class="form-control text-center ingredient-quantity @error('ingredients.' . $i . '.quantity') is-invalid @enderror"
                                                    value="{{ old('ingredients.' . $i . '.quantity', $line->quantity_g) }}">
                                                @error('ingredients.' . $i . '.quantity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="ingredients[{{ $i }}][cost]"
                                                    class="form-control text-center ingredient-cost" readonly
                                                    value="{{ old('ingredients.' . $i . '.cost', $line->cost) }}">
                                            </td>
                                            <td class="incidence-col">
                                                <input type="text"
                                                    class="form-control text-center ingredient-incidence" readonly>
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm remove-ingredient">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="ingredient-row">
                                        <td>
                                            <select name="ingredients[0][id]"
                                                class="form-select ingredient-select @error('ingredients.0.id') is-invalid @enderror">
                                                <option value="">Seleziona ingrediente…</option>
                                                @foreach ($ingredients as $ing)
                                                    <option value="{{ $ing->id }}"
                                                        data-price="{{ $ing->price_per_kg }}">
                                                        {{ $ing->ingredient_name }} (€{{ $ing->price_per_kg }}/kg)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ingredients.0.id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="ingredients[0][quantity]"
                                                class="form-control text-center ingredient-quantity @error('ingredients.0.quantity') is-invalid @enderror">
                                            @error('ingredients.0.quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" name="ingredients[0][cost]"
                                                class="form-control text-center ingredient-cost" readonly>
                                        </td>
                                        <td class="incidence-col">
                                            <input type="text" class="form-control text-center ingredient-incidence"
                                                readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm remove-ingredient">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>

                            <tfoot class="table-light">
                                <tr>
                                    <td class="fw-semibold">Peso Totale (g)</td>
                                    <td>
                                        <input type="number" id="totalWeightFooter" class="form-control text-center"
                                            readonly>
                                        <input type="hidden" name="ingredients_total_weight"
                                            id="ingredientsTotalWeightHidden">
                                    </td>
                                    <td>
                                        <input type="text" id="totalCostFooter" name="ingredients_total_cost"
                                            class="form-control text-center" readonly>
                                    </td>
                                    <td class="incidence-col">
                                        <input type="text" id="totalIncidenceFooter"
                                            class="form-control text-center fw-bold" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button id="addIngredientBtn" class="btn btn-outline-success btn-sm"
                                            title="Aggiungi riga">
                                            <i class="bi bi-plus"></i> Aggiungi
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Peso dopo il calo (g)</td>
                                    <td>
                                        <input type="number" id="weightWithLoss" name="recipe_weight"
                                            class="form-control text-center @error('recipe_weight') is-invalid @enderror"
                                            value="{{ old('recipe_weight', $isEdit ? $recipe->recipe_weight : 0) }}">
                                        @error('recipe_weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===== Manodopera ===== --}}
            <div class="card mb-4 shadow-sm reveal">
                {{-- pass the selected labor cost record ID --}}
                <input type="hidden" name="labor_cost_id" value="{{ optional($laborCost)->id }}">
                {{-- preserve your existing shop/external rates for JS (fallback) --}}
                <input type="hidden" id="shopRate" value="{{ optional($laborCost)->shop_cost_per_min ?? 0 }}">
                <input type="hidden" id="externalRate" value="{{ optional($laborCost)->external_cost_per_min ?? 0 }}">
                {{-- NEW: department-aware rates map from controller --}}
                <input type="hidden" id="deptRatesJson" value='@json($ratesByDept ?? [])'>

                <div class="card-header brand-header d-flex align-items-center">
                    <div class="header-icon"><i class="bi bi-clock-history" style="color:var(--accent)"></i></div>
                    <h5 class="mb-0">Manodopera</h5>
                </div>

                <div class="card-body">
                    <div class="form-check form-check-inline @error('labor_cost_mode') is-invalid @enderror">
                        <input class="form-check-input" type="radio" name="labor_cost_mode" id="costModeShop"
                            value="shop"
                            {{ old('labor_cost_mode', $isEdit ? $recipe->labor_cost_mode : 'shop') == 'shop' ? 'checked' : '' }}>
                        <label class="form-check-label" for="costModeShop">Usa costo interno (€/min)</label>
                    </div>
                    <div class="form-check form-check-inline @error('labor_cost_mode') is-invalid @enderror">
                        <input class="form-check-input" type="radio" name="labor_cost_mode" id="costModeExternal"
                            value="external"
                            {{ old('labor_cost_mode', $isEdit ? $recipe->labor_cost_mode : 'shop') == 'external' ? 'checked' : '' }}>
                        <label class="form-check-label" for="costModeExternal">Usa costo esterno (€/min)</label>
                    </div>
                    @error('labor_cost_mode')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="row g-3 mt-3">
                        <div class="col-md-3">
                            <label for="laborTimeInput" class="form-label">Tempo lavoro (min)</label>
                            <input type="number" id="laborTimeInput" name="labor_time_input"
                                class="form-control @error('labor_time_input') is-invalid @enderror" min="0"
                                value="{{ old('labor_time_input', $isEdit ? $recipe->labour_time_min : 0) }}">
                            @error('labor_time_input')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="costPerMin" class="form-label">Costo al minuto (€)</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="text" id="costPerMin" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="laborCost" class="form-label">Costo lavoro (€)</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="text" id="laborCost" name="labor_cost"
                                    class="form-control @error('labor_cost') is-invalid @enderror" readonly
                                    value="{{ old('labor_cost', $isEdit ? $recipe->labor_cost : '') }}">
                            </div>
                            @error('labor_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- incidence stays hidden --}}
                        <div class="col-md-3" style="display:none">
                            <label for="laborIncidence" class="form-label">Incidenza (%)</label>
                            <input type="text" id="laborIncidence" class="form-control text-center" readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Spesa & Vendita ===== --}}
            <div class="row gx-4 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 reveal">
                        <div class="card-header brand-header d-flex align-items-center">
                            <div class="header-icon"><i class="bi bi-calculator" style="color:var(--accent)"></i></div>
                            <h5 class="mb-0">Spesa Totale</h5>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="input-group w-100 mb-3">
                                <span class="input-group-text">Costo €/kg prima Imballaggio</span>
                                <span class="input-group-text">€</span>
                                <input type="text" id="prodCostKg" name="production_cost_per_kg"
                                    class="form-control text-end @error('production_cost_per_kg') is-invalid @enderror"
                                    readonly
                                    value="{{ old('production_cost_per_kg', $isEdit ? $recipe->production_cost_per_kg : '') }}">
                                @error('production_cost_per_kg')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="input-group w-100 mb-3">
                                <span class="input-group-text">Imballaggio</span>
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" id="packingCost" name="packing_cost"
                                    class="form-control text-end @error('packing_cost') is-invalid @enderror"
                                    value="{{ old('packing_cost', $isEdit ? $recipe->packing_cost : 0) }}">
                                @error('packing_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="input-group input-group-lg w-100 mb-2">
                                <span class="input-group-text">Costo €/kg dopo Imballaggio</span>
                                <span class="input-group-text">€</span>
                                <input type="text" id="totalExpense" name="total_expense"
                                    class="form-control fw-bold text-center @error('total_expense') is-invalid @enderror"
                                    readonly value="{{ old('total_expense', $isEdit ? $recipe->total_expense : '') }}">
                                @error('total_expense')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="w-100 text-center mt-1">
                                <span class="fw-semibold">Margine Potenziale:</span>
                                <span id="potentialMargin" class="fw-bold ms-2">
                                    {{ old('potential_margin', $isEdit ? $recipe->potential_margin : '') }}
                                </span>
                                <input type="hidden" name="potential_margin" id="potentialMarginInput"
                                    value="{{ old('potential_margin', $isEdit ? $recipe->potential_margin : '') }}">
                                <input type="hidden" name="potential_margin_pct" id="potentialMarginPctInput"
                                    value="{{ old('potential_margin_pct', $isEdit ? $recipe->potential_margin_pct : '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm h-100 reveal">
                        <div class="card-header brand-header d-flex align-items-center">
                            <div class="header-icon"><i class="bi bi-shop" style="color:var(--accent)"></i></div>
                            <h5 class="mb-0">Modalità di Vendita</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 @error('sell_mode') is-invalid @enderror">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sell_mode" id="modePiece"
                                        value="piece"
                                        {{ old('sell_mode', $isEdit ? $recipe->sell_mode : 'piece') == 'piece' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="modePiece">Vendita a Pezzo</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sell_mode" id="modeKg"
                                        value="kg"
                                        {{ old('sell_mode', $isEdit ? $recipe->sell_mode : '') == 'kg' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="modeKg">Vendita al Kg</label>
                                </div>
                                @error('sell_mode')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="pieceInputs">
                                <div class="mb-3">
                                    <label for="totalPieces" class="form-label">pezzi/dose</label>
                                    <input type="number" id="totalPieces" name="total_pieces"
                                        class="form-control @error('total_pieces') is-invalid @enderror"
                                        value="{{ old('total_pieces', $isEdit ? $recipe->total_pieces : '') }}">
                                    @error('total_pieces')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="weightPerPiece" class="form-label">Peso per Pezzo (g)</label>
                                    <input type="text" id="weightPerPiece" class="form-control" readonly
                                        value="{{ old('weight_per_piece', $isEdit && $recipe->total_pieces > 0 ? number_format(1000 / $recipe->total_pieces, 2) : '') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="pricePerPiece" class="form-label">Prezzo di Vendita per Pezzo (€)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" step="0.01" id="pricePerPiece"
                                            name="selling_price_per_piece"
                                            class="form-control @error('selling_price_per_piece') is-invalid @enderror"
                                            value="{{ old('selling_price_per_piece', $isEdit ? $recipe->selling_price_per_piece : '') }}">
                                    </div>
                                    @error('selling_price_per_piece')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="kgInputs" class="d-none">
                                <div class="mb-3">
                                    <label for="pricePerKg" class="form-label">Prezzo di Vendita per Kg (€)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" step="0.01" id="pricePerKg" name="selling_price_per_kg"
                                            class="form-control @error('selling_price_per_kg') is-invalid @enderror"
                                            value="{{ old('selling_price_per_kg', $isEdit ? $recipe->selling_price_per_kg : '') }}">
                                    </div>
                                    @error('selling_price_per_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="vatRate" class="form-label">Aliquota IVA</label>
                                    @php $currentVat = old('vat_rate', $isEdit ? $recipe->vat_rate : 0); @endphp
                                    <select id="vatRate" name="vat_rate"
                                        class="form-select @error('vat_rate') is-invalid @enderror">
                                        <option value="0" @selected($currentVat == 0)>Esente IVA</option>
                                        <option value="4" @selected($currentVat == 4)>4%</option>
                                        <option value="10" @selected($currentVat == 10)>10%</option>
                                        <option value="22" @selected($currentVat == 22)>22%</option>
                                    </select>
                                    @error('vat_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Aggiunte ===== --}}
            @if (!$isEdit || ($isEdit && !$alreadyAsIngredient))
                <div class="card mb-4 shadow-sm reveal">
                    <div class="card-header brand-header d-flex align-items-center">
                        <div class="header-icon"><i class="bi bi-plus-circle" style="color:var(--accent)"></i></div>
                        <h5 class="mb-0">Aggiunte</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input @error('add_as_ingredient') is-invalid @enderror"
                                type="checkbox" id="addAsIngredient" name="add_as_ingredient" value="1"
                                {{ old('add_as_ingredient', 0) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="addAsIngredient">
                                Aggiungi questa ricetta come <em>ingrediente</em>
                            </label>
                            @error('add_as_ingredient')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <p class="small text-muted mb-0">
                                Se selezionato, il nome della ricetta verrà salvato nella tabella degli ingredienti
                                con un costo €/kg pari al "Costo €/kg prima Imballaggio".
                            </p>
                            <p class="small text-info mb-0">Nota: gli ingredienti verranno aggiunti come costo al kg</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===== Submit Bar (sticky) ===== --}}
            <div class="submit-bar">
                <div class="submit_bar_inner">
                    <button type="submit" class="btn btn-lg btn-accent">
                        <i class="bi bi-save2 me-2"></i> {{ $isEdit ? 'Aggiorna Ricetta' : 'Salva Ricetta' }}
                    </button>
                </div>
            </div>

        </form>

 <!-- Quick Tips & Tutorials -->
<section class="mini-banner reveal" aria-label="Tutorials e risorse">
  <div class="mini-head">
    <div class="mini-title">
      <span class="mini-icon"><i class="bi bi-magic"></i></span>
      <strong>Consigli rapidi &amp; Tutorial</strong>
      <span class="mini-underline" aria-hidden="true"></span>
    </div>

    <!-- scroll controls -->
    <div class="mini-ctrl">
      <button class="mini-btn prev" type="button" aria-label="Scorri a sinistra">
        <i class="bi bi-chevron-left"></i>
      </button>
      <button class="mini-btn next" type="button" aria-label="Scorri a destra">
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </div>

  <div class="scroller with-fade" data-scroller>
    <a href="#" class="chip">
      <span class="chip-icon"><i class="bi bi-play-circle"></i></span>
      <span>Video: creare una ricetta perfetta</span>
    </a>
    <a href="#" class="chip">
      <span class="chip-icon"><i class="bi bi-currency-euro"></i></span>
      <span>Come calcolare i costi al kg</span>
    </a>
    <a href="#" class="chip">
      <span class="chip-icon"><i class="bi bi-bag-check"></i></span>
      <span>Prezzi di vendita: best practice</span>
    </a>
    <a href="#" class="chip">
      <span class="chip-icon"><i class="bi bi-graph-up-arrow"></i></span>
      <span>Margini &amp; IVA spiegati</span>
    </a>
    <a href="#" class="chip">
      <span class="chip-icon"><i class="bi bi-speedometer2"></i></span>
      <span>Ottimizza tempi di manodopera</span>
    </a>
    <a href="#" class="chip">
      <span class="chip-icon"><i class="bi bi-journal-text"></i></span>
      <span>Template ingredienti scaricabile</span>
    </a>
  </div>
</section>


    </div>

    {{-- ===== Modal: Add Ingredient ===== --}}
    <div class="modal fade" id="addIngredientModal" tabindex="-1" aria-labelledby="addIngredientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addIngredientModalLabel">Aggiungi Nuovo Ingrediente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    <form id="addIngredientForm" action="{{ route('ingredients.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="ingredientNameModal" class="form-label">Nome Ingrediente</label>
                            <input type="text" id="ingredientNameModal" name="ingredient_name"
                                class="form-control @error('ingredient_name') is-invalid @enderror"
                                value="{{ old('ingredient_name') }}">
                            @error('ingredient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="pricePerKgModal" class="form-label">Prezzo per kg (€)</label>
                            <input type="number" id="pricePerKgModal" name="price_per_kg"
                                class="form-control @error('price_per_kg') is-invalid @enderror" step="0.01"
                                value="{{ old('price_per_kg') }}">
                            @error('price_per_kg')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-accent">Salva Ingrediente</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- ===== Scripts: your original logic + reveal animations ===== --}}
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ===== Scroll reveal (subtle) =====
            const revealEls = document.querySelectorAll('.reveal');
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        io.unobserve(e.target);
                    }
                });
            }, {
                threshold: .12
            });
            revealEls.forEach(el => io.observe(el));

            // ====== Your original logic (kept, small polish only) ======
            const addForm = document.getElementById('addIngredientForm');
            const modalEl = document.getElementById('addIngredientModal');
            const vatRateEl = document.getElementById('vatRate');
            const shopRateEl = document.getElementById('shopRate');
            const externalRateEl = document.getElementById('externalRate');
            const costModeShop = document.getElementById('costModeShop');
            const costModeExternal = document.getElementById('costModeExternal');
            const laborTimeInput = document.getElementById('laborTimeInput');
            const costPerMinIn = document.getElementById('costPerMin');
            const laborCostIn = document.getElementById('laborCost');
            const laborIncidenceIn = document.getElementById('laborIncidence');
            const pricePerPiece = document.getElementById('pricePerPiece');
            const pricePerKg = document.getElementById('pricePerKg');
            const modePiece = document.getElementById('modePiece');
            const modeKg = document.getElementById('modeKg');
            const totalPiecesIn = document.getElementById('totalPieces');
            const weightPerPieceIn = document.getElementById('weightPerPiece');
            const weightWithLossIn = document.getElementById('weightWithLoss');
            const tableBody = document.getElementById('ingredientsTable');
            const totalWeightFt = document.getElementById('totalWeightFooter');
            const hiddenTotalWt = document.getElementById('ingredientsTotalWeightHidden');
            const totalCostIn = document.getElementById('totalCostFooter');
            const totalIncidenceIn = document.getElementById('totalIncidenceFooter');
            const packingCostIn = document.getElementById('packingCost');
            const prodCostKgIn = document.getElementById('prodCostKg');
            const totalExpenseIn = document.getElementById('totalExpense');
            const potentialMargin = document.getElementById('potentialMargin');
            const potentialInput = document.getElementById('potentialMarginInput');
            const potentialPctInput = document.getElementById('potentialMarginPctInput');

            const deptSelect = document.getElementById('recipeDept');
            let ratesByDept = {};
            try {
                ratesByDept = JSON.parse(document.getElementById('deptRatesJson')?.value || '{}');
            } catch (e) {
                ratesByDept = {};
            }

            const isEdit = {{ $isEdit ? 'true' : 'false' }};
            const addAsIngredient = document.getElementById('addAsIngredient');
            const pieceWrap = document.getElementById('pieceInputs');
            const kgWrap = document.getElementById('kgInputs');
            const pieceFields = pieceWrap ? pieceWrap.querySelectorAll('input,select,textarea,button') : [];

            let weightEdited = {{ $isEdit ? 'true' : 'false' }};
            let idx = {{ isset($recipe) ? $recipe->ingredients->count() : 1 }};

            // AJAX “Add Ingredient”
            addForm?.addEventListener('submit', async e => {
                e.preventDefault();
                const fd = new FormData(addForm);
                try {
                    const res = await fetch(addForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: fd
                    });
                    const json = await res.json();
                    if (!res.ok) {
                        alert(json.errors ? Object.values(json.errors).flat().join('\n') :
                            'Impossibile salvare l\'ingrediente.');
                        return;
                    }
                    const opt = document.createElement('option');
                    opt.value = json.id;
                    opt.textContent = `${json.ingredient_name} (€${json.price_per_kg}/kg)`;
                    opt.dataset.price = json.price_per_kg;

                    document.querySelectorAll('.ingredient-select').forEach(sel => {
                        const prev = sel.value;
                        const clone = opt.cloneNode(true);
                        let inserted = false;
                        const newName = json.ingredient_name.toLowerCase().trim();
                        for (let i = 1; i < sel.options.length; i++) {
                            const existingName = sel.options[i].textContent.split('(')[0]
                                .toLowerCase().trim();
                            if (newName.localeCompare(existingName) < 0) {
                                sel.insertBefore(clone, sel.options[i]);
                                inserted = true;
                                break;
                            }
                        }
                        if (!inserted) sel.appendChild(clone);
                        const placeholder = sel.querySelector('option[value=""]');
                        if (placeholder) {
                            sel.removeChild(placeholder);
                            sel.insertBefore(placeholder, sel.firstChild);
                        }
                        sel.value = prev || '';
                        if (prev && sel.value !== prev) sel.selectedIndex = 0;
                    });

                    const bsModal = bootstrap.Modal.getInstance(modalEl);
                    bsModal && bsModal.hide();
                    addForm.reset();
                    weightEdited = false;
                } catch (err) {
                    console.error(err);
                    alert('Errore imprevisto durante il salvataggio dell\'ingrediente.');
                }
            });

            function netPrice(gross) {
                const vat = parseFloat(vatRateEl?.value) || 0;
                return gross / (1 + vat / 100);
            }

            function calcWeightPerPiece() {
                const pcs = parseFloat(totalPiecesIn?.value) || 0;
                const totalG = parseFloat(weightWithLossIn?.value) || 0;
                if (weightPerPieceIn) weightPerPieceIn.value = pcs > 0 ? (totalG / pcs).toFixed(2) : '';
            }

            function currentDeptRates() {
                const id = (deptSelect && deptSelect.value) ? deptSelect.value : 'default';
                if (ratesByDept && ratesByDept[id]) return ratesByDept[id];
                if (ratesByDept && ratesByDept['default']) return ratesByDept['default'];
                return {
                    shop: parseFloat(shopRateEl?.value) || 0,
                    external: parseFloat(externalRateEl?.value) || 0
                };
            }

            function updateCostPerMin() {
                const r = currentDeptRates();
                const rate = costModeShop?.checked ? (parseFloat(r.shop) || 0) : (parseFloat(r.external) || 0);
                if (costPerMinIn) costPerMinIn.value = (rate || 0).toFixed(4);
                updateLaborCost();
            }

            function updateLaborCost() {
                const mins = parseFloat(laborTimeInput?.value) || 0;
                const rate = parseFloat(costPerMinIn?.value) || 0;
                if (laborCostIn) laborCostIn.value = (mins * rate).toFixed(2);
                calculateLaborIncidence();
                recalcTotals();
            }

            function calculateLaborIncidence() {
                const lc = parseFloat(laborCostIn?.value) || 0;
                const g = parseFloat(weightWithLossIn?.value) || 0;
                const kg = g / 1000;
                const sell = modePiece?.checked ?
                    (parseFloat(totalPiecesIn?.value) || 0) * (parseFloat(pricePerPiece?.value) || 0) :
                    (parseFloat(pricePerKg?.value) || 0);
                if (laborIncidenceIn) laborIncidenceIn.value = (kg > 0 && sell > 0 ? ((lc / kg) / netPrice(sell)) *
                    100 : 0).toFixed(2);
            }

            function recalcRow(row) {
                const price = parseFloat(row.querySelector('.ingredient-select')?.selectedOptions[0]?.dataset
                    .price) || 0;
                const qty = parseFloat(row.querySelector('.ingredient-quantity')?.value) || 0;
                const cost = price / 1000 * qty;
                row.querySelector('.ingredient-cost').value = cost.toFixed(2);

                const g = parseFloat(weightWithLossIn?.value) || 0;
                const kg = g / 1000;
                const sell = modePiece?.checked ?
                    (parseFloat(totalPiecesIn?.value) || 0) * (parseFloat(pricePerPiece?.value) || 0) :
                    (parseFloat(pricePerKg?.value) || 0);
                const inc = (kg > 0 && sell > 0 ? ((cost / kg) / netPrice(sell)) * 100 : 0).toFixed(2);
                const incEl = row.querySelector('.ingredient-incidence');
                if (incEl) incEl.value = inc;
            }

            function recalcTotals() {
                calculateLaborIncidence();
                let sW = 0,
                    sC = 0,
                    sI = 0;
                document.querySelectorAll('.ingredient-row').forEach(r => {
                    sW += parseFloat(r.querySelector('.ingredient-quantity')?.value) || 0;
                    sC += parseFloat(r.querySelector('.ingredient-cost')?.value) || 0;
                    sI += parseFloat(r.querySelector('.ingredient-incidence')?.value) || 0;
                });
                if (totalWeightFt) totalWeightFt.value = sW;
                if (totalCostIn) totalCostIn.value = sC.toFixed(2);
                if (totalIncidenceIn) totalIncidenceIn.value = sI.toFixed(2);
                if (hiddenTotalWt) hiddenTotalWt.value = sW;

                if (!weightEdited && weightWithLossIn) weightWithLossIn.value = sW;

                recalcExpense();
                recalcMargin();
            }

            function recalcExpense() {
                const ing = parseFloat(totalCostIn?.value) || 0;
                const lab = parseFloat(laborCostIn?.value) || 0;
                const raw = ing + lab;
                const pack = parseFloat(packingCostIn?.value) || 0;

                if (modePiece?.checked) {
                    const pcs = parseFloat(totalPiecesIn?.value) || 0;
                    const before = pcs > 0 ? raw / pcs : 0;
                    const packPerPiece = pcs > 0 ? pack / pcs : 0;
                    if (prodCostKgIn) prodCostKgIn.value = before.toFixed(2);
                    if (totalExpenseIn) totalExpenseIn.value = (before + packPerPiece).toFixed(2);
                } else {
                    const g = parseFloat(weightWithLossIn?.value) || 0;
                    const kg = g / 1000;
                    const before = kg > 0 ? raw / kg : 0;
                    if (prodCostKgIn) prodCostKgIn.value = before.toFixed(2);
                    if (totalExpenseIn) totalExpenseIn.value = (before + pack).toFixed(2);
                }
                recalcMargin();
            }

            function recalcMargin() {
                const sellP = parseFloat(pricePerPiece?.value) || 0;
                const sellK = parseFloat(pricePerKg?.value) || 0;
                const netSP = netPrice(sellP);
                const netSK = netPrice(sellK);
                const costU = parseFloat(totalExpenseIn?.value) || 0;

                if (modePiece?.checked) {
                    const m = netSP - costU;
                    const pct = netSP > 0 ? (m * 100 / netSP) : 0;
                    if (potentialMargin) potentialMargin.innerText =
                    `€${m.toFixed(2)} (${pct.toFixed(2)}%) / piece`;
                    if (potentialInput) potentialInput.value = m.toFixed(2);
                    if (potentialPctInput) potentialPctInput.value = pct.toFixed(2);
                } else {
                    const m = netSK - costU;
                    const pct = netSK > 0 ? (m * 100 / netSK) : 0;
                    if (potentialMargin) potentialMargin.innerText = `€${m.toFixed(2)} (${pct.toFixed(2)}%) / kg`;
                    if (potentialInput) potentialInput.value = m.toFixed(2);
                    if (potentialPctInput) potentialPctInput.value = pct.toFixed(2);
                }
            }

            function updateMode() {
                const beforeLabel = prodCostKgIn.closest('.input-group').querySelector('.input-group-text');
                const afterLabel = totalExpenseIn.closest('.input-group').querySelector('.input-group-text');
                if (modePiece?.checked) {
                    beforeLabel.textContent = 'Costo per pz prima dell’imballaggio';
                    afterLabel.textContent = '€';
                } else {
                    beforeLabel.textContent = 'Costo €/kg prima dell’imballaggio';
                    afterLabel.textContent = '€';
                }
                document.getElementById('pieceInputs')?.classList.toggle('d-none', !modePiece?.checked);
                document.getElementById('kgInputs')?.classList.toggle('d-none', !!modePiece?.checked);
                recalcTotals();
                if (modePiece?.checked) calcWeightPerPiece();
            }

            function setSellMode(mode) {
                if (mode === 'kg') {
                    if (modeKg) modeKg.checked = true;
                    pieceWrap?.classList.add('d-none');
                    kgWrap?.classList.remove('d-none');
                } else {
                    if (modePiece) modePiece.checked = true;
                    pieceWrap?.classList.remove('d-none');
                    kgWrap?.classList.add('d-none');
                }
                updateMode();
            }

            function lockKgIfIngredient() {
                const lock = addAsIngredient && addAsIngredient.checked;
                if (modePiece) modePiece.disabled = lock;
                pieceFields?.forEach(el => el.disabled = lock);
                if (lock) setSellMode('kg');
            }

            costModeShop?.addEventListener('change', updateCostPerMin);
            costModeExternal?.addEventListener('change', updateCostPerMin);
            laborTimeInput?.addEventListener('input', updateLaborCost);
            vatRateEl?.addEventListener('change', recalcTotals);
            packingCostIn?.addEventListener('input', recalcExpense);
            deptSelect?.addEventListener('change', updateCostPerMin);

            weightWithLossIn?.addEventListener('input', () => {
                weightEdited = true;
                calcWeightPerPiece();
                recalcTotals();
            });

            pricePerPiece?.addEventListener('input', recalcMargin);
            pricePerKg?.addEventListener('input', recalcMargin);

            totalPiecesIn?.addEventListener('input', () => {
                calcWeightPerPiece();
                updateMode();
            });
            modePiece?.addEventListener('change', () => {
                calcWeightPerPiece();
                updateMode();
            });
            modeKg?.addEventListener('change', updateMode);

            tableBody?.addEventListener('input', e => {
                if (e.target.matches('.ingredient-select, .ingredient-quantity')) {
                    weightEdited = false;
                    recalcRow(e.target.closest('.ingredient-row'));
                    recalcTotals();
                }
            });

            document.getElementById('addIngredientBtn')?.addEventListener('click', e => {
                e.preventDefault();
                const first = tableBody.querySelector('.ingredient-row');
                if (!first) return;
                const clone = first.cloneNode(true);
                const newIdx = idx++;
                clone.querySelectorAll('select[name], input[name]').forEach(el => {
                    el.name = el.name.replace(/\[\d+\]/, `[${newIdx}]`);
                    if (el.tagName === 'SELECT') el.selectedIndex = 0;
                    else el.value = el.classList.contains('ingredient-quantity') ? '0' : '';
                });
                tableBody.appendChild(clone);
                weightEdited = false;
                recalcRow(clone);
                recalcTotals();
            });

            tableBody?.addEventListener('click', e => {
                if (e.target.closest('.remove-ingredient') && tableBody.children.length > 1) {
                    e.target.closest('.ingredient-row').remove();
                    weightEdited = false;
                    recalcTotals();
                }
            });

            // Initial setup
            if (!isEdit && modeKg) modeKg.checked = true;
            updateMode();
            updateCostPerMin();
            document.querySelectorAll('.ingredient-row').forEach(r => recalcRow(r));
            calcWeightPerPiece();
            recalcTotals();

            if (addAsIngredient) {
                lockKgIfIngredient();
                addAsIngredient.addEventListener('change', lockKgIfIngredient);
            }
        });

        // Tiny helper: reveal on scroll + prev/next buttons + disable when at ends
document.addEventListener('DOMContentLoaded', () => {
  // reveal
  const io = new IntersectionObserver((entries)=>{
    entries.forEach(e => e.isIntersecting && e.target.classList.add('visible'));
  }, {threshold: 0.2});
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));

  // scroller controls
  const scroller = document.querySelector('[data-scroller]');
  if (!scroller) return;
  const prev = document.querySelector('.mini-btn.prev');
  const next = document.querySelector('.mini-btn.next');
  const update = () => {
    const max = scroller.scrollWidth - scroller.clientWidth - 2;
    prev.disabled = scroller.scrollLeft <= 4;
    next.disabled = scroller.scrollLeft >= max;
  };
  const step = () => Math.min(280, Math.max(180, scroller.clientWidth * 0.5));
  prev.addEventListener('click', ()=> scroller.scrollBy({left: -step(), behavior:'smooth'}));
  next.addEventListener('click', ()=> scroller.scrollBy({left:  step(), behavior:'smooth'}));
  scroller.addEventListener('scroll', update, {passive:true});
  update();
});

    </script>
@endsection
