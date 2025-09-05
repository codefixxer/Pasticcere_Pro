{{-- resources/views/frontend/departments/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Tutti i Reparti')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Aggiungi / Modifica Reparto -->
  <div class="card mb-5 border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-building fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($department) ? 'Modifica Reparto' : 'Aggiungi Reparto' }}
      </h5>
    </div>
    <div class="card-body">
      <form
        action="{{ isset($department) ? route('departments.update', $department) : route('departments.store') }}"
        method="POST"
        class="needs-validation row g-3"
        novalidate>
        @csrf
        @if(isset($department)) @method('PUT') @endif

        <div class="col-md-8">
          <label for="name" class="form-label fw-semibold">Nome Reparto</label>
          <input
            type="text"
            name="name"
            id="name"
            class="form-control form-control-lg"
            placeholder="es. Pasticceria, Pizzeria, Cioccolateria"
            value="{{ old('name', $department->name ?? '') }}"
            required>
          <div class="invalid-feedback">Per favore inserisci il nome del reparto.</div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($department) ? 'Aggiorna Reparto' : 'Salva Reparto' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabella Reparti -->
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #041930;">
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        <i class="bi bi-building me-2"></i> Tutti i Reparti
      </h5>
      <a href="{{ route('departments.create') }}" class="btn btn-gold btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Nuovo Reparto
      </a>
    </div>
    <div class="card-body table-responsive">
      <table
        id="departmentsTable"
        class="table table-bordered table-striped table-hover align-middle text-center mb-0"
        data-page-length="25">
        <thead>
          <tr>
            <th class="sortable">Nome Reparto <span class="sort-indicator"></span></th>
            <th>Azioni</th>
          </tr>
        </thead>
        <tbody>
          @forelse($departments as $department)
            <tr>
              <td>{{ $department->name ?? '—' }}</td>
              <td>
                <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-deepblue me-1" title="Visualizza">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-gold me-1" title="Modifica">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form
                  action="{{ route('departments.destroy', $department) }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('Eliminare questo reparto?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-red" title="Elimina">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="text-center text-muted">Nessun reparto trovato.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

<style>
  .btn-gold {
    border: 1px solid #e2ae76 !important;
    color: #e2ae76 !important;
    background: transparent !important;
    transition: all 0.2s;
  }
  .btn-gold:hover { background: #e2ae76 !important; color: #fff !important; }

  .btn-deepblue {
    border: 1px solid #041930 !important;
    color: #041930 !important;
    background: transparent !important;
    transition: all 0.2s;
  }
  .btn-deepblue:hover { background: #041930 !important; color: #fff !important; }

  .btn-red {
    border: 1px solid #ff0000 !important;
    color: red !important;
    background: transparent !important;
    transition: all 0.2s;
  }
  .btn-red:hover { background: #ff0000 !important; color: #fff !important; }

  .btn-gold-filled {
    background: #e2ae76 !important;
    color: #041930 !important;
    border: none !important;
    font-weight: 500;
    padding: 10px 24px;
    border-radius: 12px;
    transition: background 0.2s;
  }
  .btn-gold-filled:hover { background: #d89d5c !important; color: #fff !important; }

  table thead th {
    background: #e2ae76 !important;
    color: #041930 !important;
    text-align: center;
    vertical-align: middle;
  }
  table td { vertical-align: middle !important; }

  /* Custom 2‑state sort indicators */
  #departmentsTable thead th.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
    white-space: nowrap;
  }
  #departmentsTable thead th .sort-indicator {
    display: inline-block;
    width: 14px;
    text-align: center;
    font-size: .7rem;
    line-height: 1;
    margin-left: 4px;
    color: #041930;
    opacity: 0;
    transition: opacity .15s;
  }
  #departmentsTable thead th[data-sort-dir] .sort-indicator { opacity: 1; }

  /* Remove default DataTables arrows */
  table.dataTable thead .sorting:after,
  table.dataTable thead .sorting_asc:after,
  table.dataTable thead .sorting_desc:after,
  table.dataTable thead .sorting:before,
  table.dataTable thead .sorting_asc:before,
  table.dataTable thead .sorting_desc:before {
    content: '' !important;
  }
</style>

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (window.$ && $.fn.DataTable) {
      $.fn.dataTable.ext.errMode = 'none';

      const STORE_KEY = 'departments_sort_state';

      const table = $('#departmentsTable').DataTable({
        paging:     true,
        ordering:   true,
        orderMulti: false,
        responsive: true,
        pageLength: $('#departmentsTable').data('page-length') || 10,
        order: [[0,'asc']],
        columnDefs: [
          { orderable: false, targets: -1 } // Azioni
        ],
        language: {
          lengthMenu:    "Mostra _MENU_ elementi per pagina",
            zeroRecords:   "Nessun record trovato",
            info:          "Mostra _START_ a _END_ di _TOTAL_ elementi",
            infoEmpty:     "Mostra 0 a 0 di 0 elementi",
            infoFiltered:  "(filtrati da _MAX_ totali)",
            search:        "Cerca:",
            paginate: {
              first:    "Primo",
              previous: "←",
              next:     "→",
              last:     "Ultimo"
            }
        }
      });

      // Restore previous sort
      try {
        const saved = sessionStorage.getItem(STORE_KEY);
        if (saved) {
          const { col, dir } = JSON.parse(saved);
            if (typeof col === 'number' && (dir === 'asc' || dir === 'desc')) {
              table.order([col, dir]).draw();
            }
        }
      } catch(e){}

      function updateIndicators() {
        $('#departmentsTable thead th.sortable')
          .removeAttr('data-sort-dir')
          .find('.sort-indicator').text('');
        const ord = table.order();
        if (!ord.length) return;
        const col = ord[0][0];
        const dir = ord[0][1];
        const th = $('#departmentsTable thead th').eq(col);
        if (!th.hasClass('sortable')) return;
        th.attr('data-sort-dir', dir);
        th.find('.sort-indicator').text(dir === 'asc' ? '▲' : '▼');
      }
      updateIndicators();

      // 2‑state toggle
      $('#departmentsTable thead').on('click', 'th.sortable', function() {
        const idx = $(this).index();
        const colSettings = table.settings()[0].aoColumns[idx];
        if (colSettings.bSortable === false) return;

        const current = table.order();
        const currentCol = current.length ? current[0][0] : null;
        const currentDir = current.length ? current[0][1] : 'asc';
        const newDir = (currentCol === idx && currentDir === 'asc') ? 'desc' : 'asc';

        table.order([idx, newDir]).draw();
        updateIndicators();

        try {
          const ord = table.order();
          sessionStorage.setItem(STORE_KEY, JSON.stringify({ col: ord[0][0], dir: ord[0][1] }));
        } catch(e){}
      });

      // Prevent shift multi-order
      $('#departmentsTable thead').on('mousedown', 'th', function(e){
        if (e.shiftKey) e.preventDefault();
      });
    }

    // Validazione client-side Bootstrap
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        this.classList.add('was-validated');
      }, false);
    });
  });
</script>
@endsection
