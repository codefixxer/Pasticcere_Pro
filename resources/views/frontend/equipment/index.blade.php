{{-- resources/views/frontend/equipment/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Vetrina delle Attrezzature')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Aggiungi Attrezzatura -->
  <div class="card mb-5 border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-tools fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">Aggiungi Nuova Attrezzatura</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('equipment.store') }}" method="POST" class="row g-3 needs-validation" novalidate>
        @csrf
        <div class="col-md-8">
          <label for="Name" class="form-label fw-semibold">Nome Attrezzatura</label>
          <input
            type="text"
            id="Name"
            name="name"
            class="form-control form-control-lg"
            placeholder="es. Planetaria, Forno"
            required
            value="{{ old('name') }}">
          <div class="invalid-feedback">Inserisci un nome per l'attrezzatura.</div>
        </div>
        <div class="col-md-4 text-end align-self-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-1"></i> Salva Attrezzatura
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Elenco Attrezzature -->
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #041930;">
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        <i class="bi bi-list-ul me-2" style="color: #e2ae76;"></i> Elenco Attrezzature
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table
        id="equipmentTable"
        class="table table-bordered table-striped table-hover align-middle text-center mb-0"
        data-page-length="25">
        <thead>
          <tr>
            <th class="text-center sortable">Nome <span class="sort-indicator"></span></th>
            <th class="text-center">Azioni</th>
          </tr>
        </thead>
        <tbody>
          @forelse($equipments as $equ)
            <tr>
              <td>{{ $equ->name }}</td>
              <td>
                <a href="{{ route('equipment.show', $equ) }}" class="btn btn-sm btn-deepblue me-1" title="Visualizza">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('equipment.edit', $equ) }}" class="btn btn-sm btn-gold me-1" title="Modifica">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form
                  action="{{ route('equipment.destroy', $equ) }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('Eliminare questa attrezzatura?');">
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
              <td colspan="2" class="text-muted">Nessuna attrezzatura trovata.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection


<style>
  .btn-gold-filled {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: none !important;
    font-weight: 500;
    padding: 10px 24px;
    border-radius: 12px;
    transition: background-color 0.2s ease;
  }
  .btn-gold-filled:hover {
    background-color: #d89d5c !important;
    color: white !important;
  }
  .btn-gold-filled i { color: inherit !important; }

  .btn-gold {
    border: 1px solid #e2ae76 !important;
    color: #e2ae76 !important;
    background-color: transparent !important;
    transition: all 0.2s ease-in-out;
  }
  .btn-gold:hover { background-color: #e2ae76 !important; color: white !important; }

  .btn-deepblue {
    border: 1px solid #041930 !important;
    color: #041930 !important;
    background-color: transparent !important;
    transition: all 0.2s ease-in-out;
  }
  .btn-deepblue:hover { background-color: #041930 !important; color: white !important; }

  .btn-red {
    border: 1px solid #ff0000 !important;
    color: red !important;
    background-color: transparent !important;
    transition: all 0.2s ease-in-out;
  }
  .btn-red:hover { background-color: #ff0000 !important; color: white !important; }

  table thead th {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    text-align: center;
    vertical-align: middle;
  }
  table tbody td {
    text-align: center;
    vertical-align: middle;
  }

  /* Custom 2‑state sorting indicators */
  #equipmentTable thead th.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
    white-space: nowrap;
  }
  #equipmentTable thead th .sort-indicator {
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
  #equipmentTable thead th[data-sort-dir] .sort-indicator { opacity: 1; }

  /* Remove DataTables default arrow icons */
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
  document.addEventListener('DOMContentLoaded', function () {
    if (window.$ && $.fn.DataTable) {
      $.fn.dataTable.ext.errMode = 'none';

      const STORAGE_COL = 'equipment_sort_col';
      const STORAGE_DIR = 'equipment_sort_dir';

      const table = $('#equipmentTable').DataTable({
        paging:      true,
        ordering:    true,
        orderMulti:  false,          // single column only
        responsive:  true,
        pageLength:  $('#equipmentTable').data('page-length') || 10,
        order:       [[0,'asc']],
        columnDefs: [
          { orderable: false, targets: -1 } // Azioni non ordinabile
        ],
        language: {
          search:        "Cerca:",
          lengthMenu:    "Mostra _MENU_ voci",
          info:          "Mostra _START_ di _END_ di _TOTAL_ elementi",
          infoEmpty:     "Nessun elemento disponibile",
          zeroRecords:   "Nessuna corrispondenza trovata",
          paginate: {
            first:    "Primo",
            last:     "Ultimo",
            next:     "Successivo",
            previous: "Precedente"
          }
        }
      });

      // Restore saved sort
      try {
        const sc = sessionStorage.getItem(STORAGE_COL);
        const sd = sessionStorage.getItem(STORAGE_DIR);
        if (sc !== null && sd) {
          table.order([parseInt(sc,10), sd]).draw();
        }
      } catch(e){}

      function updateIndicators() {
        $('#equipmentTable thead th.sortable').removeAttr('data-sort-dir').find('.sort-indicator').text('');
        const ord = table.order();
        if (!ord.length) return;
        const col = ord[0][0];
        const dir = ord[0][1];
        const th  = $('#equipmentTable thead th').eq(col);
        if (!th.hasClass('sortable')) return;
        th.attr('data-sort-dir', dir);
        th.find('.sort-indicator').text(dir === 'asc' ? '▲' : '▼');
      }

      updateIndicators();

      // 2‑state toggle (asc <-> desc only, no neutral third state)
      $('#equipmentTable thead').on('click', 'th.sortable', function() {
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
          sessionStorage.setItem(STORAGE_COL, ord[0][0]);
          sessionStorage.setItem(STORAGE_DIR, ord[0][1]);
        } catch(e){}
      });

      // Prevent multi-column shift ordering
      $('#equipmentTable thead').on('mousedown', 'th', function(e){
        if (e.shiftKey) e.preventDefault();
      });
    }

    // Validazione Bootstrap
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
