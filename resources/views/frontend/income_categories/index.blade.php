@extends('frontend.layouts.app')

@section('title','Categorie Entrate')

@section('content')
<div class="container py-5 px-md-5">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card shadow-sm border-success">
        <div class="card-header" style="background:#041930;color:#e2ae76;">
          <strong class="mb-0">
            @isset($editingCategory) Modifica Categoria @else Nuova Categoria @endisset
          </strong>
        </div>
        <div class="card-body">
          <form method="POST"
                action="@isset($editingCategory) {{ route('income-categories.update', $editingCategory) }} @else {{ route('income-categories.store') }} @endisset"
                class="row g-3 needs-validation" novalidate>
            @csrf
            @isset($editingCategory) @method('PUT') @endisset

            <div class="col-12">
              <label class="form-label fw-semibold">Nome</label>
              <input type="text" name="name" required
                     value="{{ old('name', $editingCategory->name ?? '') }}"
                     class="form-control form-control-lg @error('name') is-invalid @enderror">
              <div class="invalid-feedback">{{ $errors->first('name', 'Inserisci il nome.') }}</div>
            </div>

            <div class="col-12 text-end">
              <button type="submit" class="btn btn-gold-save btn-lg">
                @isset($editingCategory) Aggiorna @else Salva @endisset
              </button>
              @isset($editingCategory)
                <a href="{{ route('income-categories.index') }}" class="btn btn-deepblue btn-lg ms-2">Annulla</a>
              @endisset
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-header" style="background:#041930;color:#e2ae76;">
          <h5 class="mb-0 fw-bold">Categorie Entrate</h5>
        </div>
        <div class="card-body table-responsive">
          <table  data-page-length="25"class="table table-hover align-middle text-center mb-0">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Azioni</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categories as $cat)
                <tr>
                  <td class="fw-semibold">{{ $cat->name }}</td>
                  {{-- <td>
                    @if(is_null($cat->user_id))
                      <span class="badge bg-secondary">Globale</span>
                    @else
                      <span class="badge bg-primary">Personale</span>
                    @endif
                  </td> --}}
                  <td>
                    @if(!is_null($cat->user_id))
                      <a href="{{ route('income-categories.edit', $cat) }}" class="btn btn-sm btn-gold me-1">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <form class="d-inline" method="POST"
                            action="{{ route('income-categories.destroy', $cat) }}"
                            onsubmit="return confirm('Eliminare la categoria?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-red">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted">Nessuna categoria.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .btn-gold-save{ border:1px solid #e2ae76!important;color:#041930!important;background-color:#e2ae76!important; }
  .btn-gold-save:hover{ background-color:#d89d5c!important;color:#fff!important; }
  .btn-deepblue{ border:1px solid #041930!important;color:#041930!important;background:transparent!important; }
  .btn-deepblue:hover{ background:#041930!important;color:#fff!important; }
  .btn-red{ border:1px solid red!important;color:red!important;background:transparent!important; }
  .btn-red:hover{ background:red!important;color:#fff!important; }
</style>
<style>
  /* Left align all table body cells by default */
  table tbody td {
    text-align: left !important;
    vertical-align: middle !important;
  }

  /* Keep table header left aligned (except actions) */
  table thead th {
    text-align: left !important;
    vertical-align: middle !important;
  }

  /* Actions column should stay centered */
  table thead th:last-child,
  table tbody td:last-child {
    text-align: center !important;
    width: 120px; /* optional, keeps buttons compact */
  }
</style>

@endsection

