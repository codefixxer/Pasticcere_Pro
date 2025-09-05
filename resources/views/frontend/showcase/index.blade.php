{{-- resources/views/frontend/showcase/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Tutte le Vetrine')

@section('content')
<div class="container py-5 px-md-5">
  <div class="card mb-4 border-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#041930;">
      <h5 class="mb-0 fw-bold d-flex align-items-center" style="color:#e2ae76;">
        <svg class="me-2" viewBox="0 0 512.005 512.005" xmlns="http://www.w3.org/2000/svg" style="width:1.5em;height:1em;color:#e2ae76;fill:currentColor;"></svg>
        Vetrine Giornaliere
      </h5>
      <a href="{{ route('showcase.create') }}" class="btn btn-gold d-flex align-items-center">
        <i class="bi bi-plus-circle me-1"></i> Nuova Vetrina
      </a>
    </div>
    <div class="card-body">
      <p class="mb-0 text-muted">Sfoglia e gestisci tutte le tue vetrine salvate qui sotto.</p>
    </div>
  </div>

  <div class="card border-primary shadow-sm">
    <div class="card-body table-responsive">
      <div class="row mb-3">
        <div class="col-md-3">
          <input type="text" id="filter-date" class="form-control" placeholder="Filtra per Data">
        </div>
        <div class="col-md-3">
          <input type="text" id="filter-name" class="form-control" placeholder="Filtra per Nome">
        </div>
      </div>

      <table  data-page-length="25"id="showcasesTable" class="table table-bordered table-striped table-hover align-middle text-center mb-0" style="width:100%;">
        <thead>
          <tr>
            <th>Data</th>
            <th>Nome</th>
            <th>Punto di Pareggio (€)</th>
            <th>Ricavo Totale (€)</th>
            <th>Extra (€)</th>
            <th>Margine Reale (€)</th>
            <th>Azioni</th>
          </tr>
        </thead>
        <tbody>
          @forelse($showcases as $s)
            <tr>
              <td>{{ \Carbon\Carbon::parse($s->showcase_date)->format('Y-m-d') }}</td>
              <td>{{ $s->showcase_name }}</td>
              <td>€{{ number_format($s->break_even, 2) }}</td>
              <td>€{{ number_format($s->total_revenue, 2) }}</td>
              <td>€{{ number_format($s->plus, 2) }}</td>
              <td>
                @if($s->real_margin >= 0)
                  <span class="text-success">€{{ $s->real_margin }}</span>
                @else
                  <span class="text-danger">€{{ $s->real_margin }}</span>
                @endif
              </td>
              <td>
                <div class="btn-group" role="group">
                  <a href="{{ route('showcase.show', $s->id) }}" class="btn btn-sm btn-deepblue" title="Visualizza">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('showcase.edit', $s->id) }}" class="btn btn-sm btn-gold" title="Modifica">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <form action="{{ route('showcase.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questa vetrina?');" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-red" title="Elimina">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-muted">Nessuna vetrina trovata.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

<style>
  table th { background-color:#e2ae76!important;color:#041930!important;text-align:center;vertical-align:middle;cursor:pointer; }
  table td { text-align:center;vertical-align:middle; }
  .btn-gold { border:1px solid #e2ae76!important;color:#e2ae76!important;background-color:transparent!important; }
  .btn-gold:hover { background-color:#e2ae76!important;color:#fff!important; }
  .btn-deepblue { border:1px solid #041930!important;color:#041930!important;background-color:transparent!important; }
  .btn-deepblue:hover { background-color:#041930!important;color:#fff!important; }
  .btn-red { border:1px solid #ff0000!important;color:red!important;background-color:transparent!important; }
  .btn-red:hover { background-color:#ff0000!important;color:#fff!important; }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (!window.$ || !$.fn.DataTable) return;
  $.fn.dataTable.ext.errMode = 'none';

  var table = $('#showcasesTable').DataTable({
    paging: true,
    ordering: true,
    responsive: true,
    pageLength: 10,
    order: [[0, 'desc']],
    orderMulti: false,
    stateSave: false,
    columns: [null, null, null, null, null, null, { orderable:false }],
    language: {
      search: "Cerca:",
      lengthMenu: "Mostra _MENU_ voci per pagina",
      info: "Mostrando da _START_ a _END_ di _TOTAL_ vetrine",
      paginate: { previous: "&laquo;", next: "&raquo;" },
      zeroRecords: "Nessuna vetrina corrispondente trovata"
    }
  });

  $('#showcasesTable thead').on('click', 'th', function() {
    var colIdx = $(this).index();
    var colSettings = table.settings()[0].aoColumns[colIdx];
    if (colSettings.bSortable === false) return;

    var current = table.order();
    var currentCol = current.length ? current[0][0] : null;
    var currentDir = current.length ? current[0][1] : 'asc';

    if (currentCol === colIdx) {
      table.order([colIdx, currentDir === 'asc' ? 'desc' : 'asc']).draw();
    } else {
      table.order([colIdx, 'asc']).draw();
    }
  });

  $('#showcasesTable thead').on('mousedown', 'th', function(e){ if (e.shiftKey) e.preventDefault(); });

  $('#filter-date').on('keyup change', function(){ table.column(0).search(this.value).draw(); });
  $('#filter-name').on('keyup change', function(){ table.column(1).search(this.value).draw(); });
});
</script>
@endsection
