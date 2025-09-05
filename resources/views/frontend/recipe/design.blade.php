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
:root{
  --primary:#041930;
  --accent:#e2ae76;
  --primary-900:#02101f;
  --primary-800:#07223d;
  --text:#0f172a;
  --muted:#6b7280;
  --glass-bg: rgba(255,255,255,0.75);
  --ring: 0 0 0 0.25rem rgba(226,174,118,.25);
}

*{scrollbar-width:thin; scrollbar-color: var(--accent) rgba(255,255,255,.25)}
::-webkit-scrollbar{height:8px;width:8px}
::-webkit-scrollbar-thumb{background:var(--accent);border-radius:8px}
::-webkit-scrollbar-track{background:transparent}

body{background:linear-gradient(180deg,#f6f7fb 0%, #ffffff 100%)}
.container{max-width:1100px}

/* ——— Cards / headers (keep your look) ——— */
.brand-header{
  background: linear-gradient(135deg, var(--primary) 0%, #07223d 100%);
  color:var(--accent); border:0; border-bottom:1px solid rgba(226,174,118,.25)
}
.brand-header h5{color:var(--accent); letter-spacing:.3px}
.card{
  border:1px solid rgba(4,25,48,.06); border-radius:18px!important;
  background:var(--glass-bg); backdrop-filter: blur(8px); overflow:hidden;
  transition: transform .2s ease, box-shadow .2s ease;
}
.card:hover{ transform: translateY(-2px); box-shadow: 0 12px 30px rgba(4,25,48,.10) }

/* ——— Table polish ——— */
.table{ --bs-table-striped-bg: rgba(4,25,48,.02); --bs-table-hover-bg: rgba(226,174,118,.08) }
.table thead th{ position:sticky; top:0; z-index:5; background:#f8fafc; border-bottom:1px solid rgba(4,25,48,.08) }
.table td,.table th{ vertical-align:middle }

/* ——— Inputs ——— */
.input-group-text{background:#f7f7fb;border-color:rgba(4,25,48,.15)}
.form-control,.form-select{
  border-radius:12px; border:1px solid rgba(4,25,48,.18); background:#fff;
  transition: box-shadow .15s ease, border-color .15s ease;
}
.form-control:focus,.form-select:focus{ border-color:var(--accent); box-shadow:var(--ring) }
.form-label{font-weight:600;color:var(--text)}

.btn-accent{
  --bs-btn-bg: var(--accent); --bs-btn-border-color: var(--accent);
  --bs-btn-hover-bg:#f0c08f; --bs-btn-hover-border-color:#f0c08f; --bs-btn-color: var(--primary);
  border-width:2px; border-radius:12px; font-weight:700; letter-spacing:.2px;
}
.btn-outline-light{border-color: rgba(255,255,255,.4)}
.badge-soft{ background: rgba(226,174,118,.15); color: var(--accent); border:1px solid rgba(226,174,118,.35) }

/* ——— Reveal on scroll ——— */
.reveal{opacity:0; transform: translateY(14px); transition:.5s ease}
.reveal.visible{opacity:1; transform: translateY(0)}

/* ——— Submit bar ——— */
.submit-bar{ position:sticky; bottom:12px; z-index:50; display:flex; justify-content:end; margin-top:-6px }
.submit_bar_inner{
  background:#ffffffd9; backdrop-filter: blur(6px); border:1px solid rgba(4,25,48,.08);
  border-radius:14px; padding:10px; box-shadow: 0 10px 24px rgba(4,25,48,.12);
}

/* ——— Icon bubble ——— */
.header-icon{
  width:36px;height:36px;display:grid;place-items:center;margin-right:.5rem;border-radius:10px;
  background: rgba(226,174,118,.14); border:1px solid rgba(226,174,118,.35)
}
.header-icon svg{width:18px;height:18px;fill:var(--accent)}

/* =========================================================
   SUPER-ATTRACTIVE MINI BANNER
   ========================================================= */
.mini-banner{
  --br: 18px;
  position:relative;
  margin-top:1.75rem;
  padding: 14px 16px 12px;
  color:#fff;
  border-radius: var(--br);
  background: radial-gradient(120% 140% at 0% 0%, #0b2b53 0%, var(--primary) 42%, #0a264a 100%);
  border: 1px solid rgba(226,174,118,.35);
  box-shadow: 0 12px 32px rgba(4,25,48,.30), inset 0 1px 0 rgba(255,255,255,.04);
  overflow:hidden;
}

/* Animated conic border ring */
.mini-banner::before{
  content:"";
  position:absolute; inset:-1px;
  padding:1.25px; border-radius: inherit;
  background: conic-gradient(from 0deg,
     rgba(226,174,118,.85) 0deg, transparent 90deg,
     rgba(226,174,118,.65) 180deg, transparent 270deg,
     rgba(226,174,118,.85) 360deg);
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor; mask-composite: exclude;
  animation: spin 10s linear infinite;
  pointer-events:none;
}

/* Soft sparkles */
.mini-banner::after{
  content:"";
  position:absolute; inset:0;
  background:
    radial-gradient(600px 200px at 90% -50%, rgba(226,174,118,.25), transparent 60%),
    radial-gradient(180px 180px at 10% 120%, rgba(255,255,255,.12), transparent 55%),
    radial-gradient(2px 2px at 40% 60%, rgba(255,255,255,.35), transparent 40%),
    radial-gradient(2px 2px at 70% 35%, rgba(255,255,255,.25), transparent 40%);
  pointer-events:none;
  mix-blend-mode: screen;
  animation: floatSparkles 8s ease-in-out infinite alternate;
}

@keyframes spin{ to{ transform: rotate(360deg) } }
@keyframes floatSparkles{ 0%{transform: translateY(0)} 100%{transform: translateY(-6px)} }
@keyframes shineSweep{ 0%{transform: translateX(-120%) skewX(-12deg)} 100%{transform: translateX(120%) skewX(-12deg)} }
@keyframes ping{ 0%{transform: scale(.9); opacity:.9} 70%{transform: scale(1.15); opacity:.15} 100%{transform: scale(1.35); opacity:0} }

/* Head */
.mini-banner-head{ position:relative; z-index:2 }
.banner-kicker{
  font-size:.70rem; letter-spacing:.14em; text-transform:uppercase;
  color: var(--accent);
  padding:2px 8px; border-radius:999px;
  border:1px solid rgba(226,174,118,.5);
  background: rgba(226,174,118,.08);
}
.banner-title{
  margin:0; font-weight:800; letter-spacing:.2px;
  background: linear-gradient(90deg, #ffe0bf, var(--accent));
  -webkit-background-clip:text; background-clip:text; color:transparent;
  text-shadow: 0 0 .0px transparent, 0 8px 28px rgba(226,174,118,.18);
}
.banner-cta{
  display:inline-flex; align-items:center; gap:2px;
  font-weight:600; color:#fff; text-decoration:none;
  padding:6px 10px; border-radius:10px;
  background: rgba(255,255,255,.08);
  border:1px solid rgba(226,174,118,.35);
  transition: transform .15s ease, background .15s ease, border-color .15s ease;
}
.banner-cta:hover{ transform: translateY(-1px); background: rgba(255,255,255,.14); border-color: rgba(226,174,118,.6) }

/* Scroller */
.scroller{
  position:relative; z-index:2;
  display:flex; gap:.6rem; overflow:auto;
  scroll-snap-type: x mandatory; padding: 6px 44px 8px 44px;
}
.scroller:focus{ outline: none; box-shadow: var(--ring) }

/* Scroll arrows */
.scroll-arrow{
  position:absolute; top:50%; transform: translateY(-50%);
  width:38px; height:38px; border-radius:12px;
  background: rgba(255,255,255,.12);
  color:#fff; border:1px solid rgba(226,174,118,.35);
  display:grid; place-items:center; z-index:3; cursor:pointer;
  backdrop-filter: blur(6px);
  transition: transform .15s ease, background .15s ease, border-color .15s ease;
}
.scroll-arrow:hover{ transform: translateY(-50%) scale(1.06); background: rgba(255,255,255,.18); border-color: rgba(226,174,118,.6) }
.scroll-arrow.left{ left:8px }
.scroll-arrow.right{ right:8px }

/* Chip */
.chip{
  position:relative; scroll-snap-align:center; white-space:nowrap;
  display:inline-flex; align-items:center; gap:.55rem;
  padding: 10px 14px;
  background: rgba(255,255,255,.10);
  border:1px solid rgba(226,174,118,.38);
  color:#fff; border-radius:999px;
  transition: transform .15s ease, background .15s ease, border-color .15s ease, box-shadow .15s ease;
  will-change: transform;
  box-shadow: 0 6px 16px rgba(4,25,48,.18);
  text-decoration:none;
}
.chip:hover{
  transform: translateY(-2px);
  background: rgba(255,255,255,.16);
  border-color: rgba(226,174,118,.7);
  box-shadow: 0 10px 22px rgba(4,25,48,.22);
}
.icon-badge{
  width:24px;height:24px; display:grid; place-items:center;
  border-radius:999px; background: rgba(226,174,118,.20);
  border:1px solid rgba(226,174,118,.45);
  color: var(--accent); flex:0 0 auto;
}

/* CTA / HOT variants */
.chip--cta{
  background: linear-gradient(90deg, rgba(226,174,118,.95), #ffd7ac);
  color: var(--primary);
  border-color: rgba(226,174,118,1);
  box-shadow: 0 10px 24px rgba(226,174,118,.45);
}
.chip--cta .icon-badge{ background: rgba(255,255,255,.7); border-color: rgba(255,255,255,.9); color: var(--primary) }

.chip--hot .ping{
  position:absolute; inset:0; border-radius:inherit; pointer-events:none;
  border:2px solid rgba(226,174,118,.7); filter: drop-shadow(0 0 6px rgba(226,174,118,.35));
  animation: ping 2.2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Shine sweep on chips */
.chip .shine{
  content:""; position:absolute; inset:auto 0 0 0; height:100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.45), transparent);
  mix-blend-mode: screen; opacity:.0; pointer-events:none;
  transform: translateX(-120%) skewX(-12deg);
}
.chip:hover .shine{ animation: shineSweep .9s ease forwards }

/* Reduced motion */
@media (prefers-reduced-motion: reduce){
  .mini-banner::before,.mini-banner::after,.chip .shine,.chip--hot .ping{ animation:none }
  .chip, .banner-cta, .scroll-arrow{ transition:none }
}
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

        <section class="mini-banner reveal" aria-label="Tutorials e risorse">
            <!-- animated gradient ring & soft sparkles are handled in CSS -->
            <div class="mini-banner-head d-flex align-items-center mb-2">
                <span class="banner-kicker">Nuovo</span>
                <h6 class="banner-title ms-2">Consigli rapidi &amp; Tutorial</h6>
                <a href="#" class="ms-auto banner-cta">
                    Vedi tutti <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>

            <!-- Scroll arrows -->
            <button class="scroll-arrow left" type="button" aria-label="Scorri a sinistra">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="scroll-arrow right" type="button" aria-label="Scorri a destra">
                <i class="bi bi-chevron-right"></i>
            </button>

            <div class="scroller" tabindex="0">
                <a href="#" class="chip chip--cta">
                    <span class="icon-badge"><i class="bi bi-play-circle"></i></span>
                    Video: ricetta perfetta
                    <span class="shine"></span>
                </a>

                <a href="#" class="chip">
                    <span class="icon-badge"><i class="bi bi-currency-euro"></i></span>
                    Costi al kg (guida)
                    <span class="shine"></span>
                </a>

                <a href="#" class="chip">
                    <span class="icon-badge"><i class="bi bi-bag-check"></i></span>
                    Prezzi di vendita: best practice
                    <span class="shine"></span>
                </a>

                <a href="#" class="chip chip--hot">
                    <span class="icon-badge"><i class="bi bi-graph-up-arrow"></i></span>
                    Margini &amp; IVA spiegati
                    <span class="ping"></span>
                    <span class="shine"></span>
                </a>

                <a href="#" class="chip">
                    <span class="icon-badge"><i class="bi bi-speedometer2"></i></span>
                    Ottimizza manodopera
                    <span class="shine"></span>
                </a>

                <a href="#" class="chip">
                    <span class="icon-badge"><i class="bi bi-journal-text"></i></span>
                    Template ingredienti
                    <span class="shine"></span>
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
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => e.isIntersecting && e.target.classList.add('visible'));
            }, {
                threshold: 0.2
            });
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
            prev.addEventListener('click', () => scroller.scrollBy({
                left: -step(),
                behavior: 'smooth'
            }));
            next.addEventListener('click', () => scroller.scrollBy({
                left: step(),
                behavior: 'smooth'
            }));
            scroller.addEventListener('scroll', update, {
                passive: true
            });
            update();
        });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', () => {
  // Smooth arrows + gentle auto-scroll (pauses on hover/focus)
  const banner = document.querySelector('.mini-banner');
  const scroller = banner?.querySelector('.scroller');
  const left = banner?.querySelector('.scroll-arrow.left');
  const right = banner?.querySelector('.scroll-arrow.right');

  const step = ()=> Math.max(240, scroller.clientWidth * 0.6);

  left?.addEventListener('click', () => scroller.scrollBy({left: -step(), behavior: 'smooth'}));
  right?.addEventListener('click', () => scroller.scrollBy({left:  step(), behavior: 'smooth'}));

  let raf;
  const speed = 0.35; // px per frame
  const loop = () => {
    if (!document.hidden && scroller && !scroller.matches(':hover,:focus-within')) {
      scroller.scrollLeft += speed;
      const max = scroller.scrollWidth - scroller.clientWidth;
      if (scroller.scrollLeft >= max - 1) scroller.scrollTo({left: 0});
    }
    raf = requestAnimationFrame(loop);
  };
  loop();
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) cancelAnimationFrame(raf); else loop();
  });
});
</script>

@endsection
