@extends('frontend.layouts.app')

@section('title', 'Tutte le Notizie')

@section('content')
<div class="container py-5 px-md-5">
    <div class="d-flex justify-content-between align-items-center page-header mb-4">
        <div class="d-flex align-items-center">
          <i class="bi bi-megaphone fs-3 me-2"></i>
          <h4 class="mb-0 fw-bold">Tutte le Notizie</h4>
        </div>
        <a href="{{ route('news.create') }}" class="btn btn-gold-filled btn-lg">
          <i class="bi bi-plus-circle me-1"></i> Aggiungi Notizia
        </a>
    </div>

    <div class="card border-primary shadow-sm mt-50">
        <div class="card-header d-flex align-items-center" style="background-color: #041930;">
            <i class="bi bi-newspaper fs-4 me-2" style="color: #e2ae76;"></i>
            <h5 class="mb-0 fw-bold" style="color: #e2ae76;">Elenco Notizie</h5>
        </div>
        <div class="card-body table-responsive">
            <table
                id="newsTable"
                class="table table-bordered table-hover align-middle text-center mb-0">
                <thead style="background-color: #e2ae76; color: #041930;">
                    <tr>
                        <th class="no-sort">Immagine</th>
                        <th class="sortable">Titolo <span class="sort-indicator"></span></th>
                        <th class="sortable">Data Evento <span class="sort-indicator"></span></th>
                        <th class="sortable">Stato <span class="sort-indicator"></span></th>
                        <th class="no-sort">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($news as $item)
                    <tr>
                        <td>
                            @if($item->image)
                              <img src="{{ asset('storage/'.$item->image) }}" alt="" style="height:40px;">
                            @else
                              &mdash;
                            @endif
                        </td>
                        <td>{{ $item->title }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($item->event_date)->format('Y-m-d') }}">
                            {{ \Carbon\Carbon::parse($item->event_date)->format('Y-m-d') }}
                        </td>
                        <td data-order="{{ $item->is_active ? 1 : 0 }}">
                            <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $item->is_active ? 'Attivo' : 'Inattivo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('news.edit', $item) }}" class="btn btn-sm btn-gold me-1" title="Modifica">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('news.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa notizia?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-red" title="Elimina">
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
@endsection

<style>
    .btn-gold-filled {
        background-color: #e2ae76 !important;
        color: #041930 !important;
        border: none !important;
        font-weight: 500;
        padding: 8px 20px;
        border-radius: 10px;
        transition: background-color 0.2s ease;
    }
    .btn-gold-filled:hover { background-color: #d89d5c !important; color: white !important; }
    .btn-gold {
        border: 1px solid #e2ae76 !important;
        color: #e2ae76 !important;
        background-color: transparent !important;
    }
    .btn-gold:hover { background-color: #e2ae76 !important; color: white !important; }
    .btn-red {
        border: 1px solid #dc2626 !important;
        color: #dc2626 !important;
        background-color: transparent !important;
    }
    .btn-red:hover { background-color: #dc2626 !important; color: white !important; }
    table th, table td { vertical-align: middle !important; }
    .page-header {
        background-color: #041930;
        color: #e2ae76;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
    }
    .page-header h4, .page-header i { color: #e2ae76; }

    #newsTable thead th.sortable {
        cursor: pointer;
        user-select: none;
        position: relative;
        white-space: nowrap;
    }
    #newsTable thead th .sort-indicator {
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
    #newsTable thead th[data-sort-dir] .sort-indicator { opacity: 1; }

    /* Remove DataTables default arrows */
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

        const HEADER_KEY = 'news_sort_state';

        const table = $('#newsTable').DataTable({
            paging:      false,     // no pagination
            ordering:    true,
            searching:   false,
            info:        false,     // hide info line
            lengthChange:false,
            responsive:  true,
            dom:         't',       // only the table
            order:       [[1,'asc']],
            orderMulti:  false,
            columnDefs: [
                { orderable: false, targets: [0,4] }
            ]
        });

        function updateIndicators() {
            $('#newsTable thead th.sortable').removeAttr('data-sort-dir')
                .find('.sort-indicator').text('');
            const ord = table.order();
            if (!ord.length) return;
            const col = ord[0][0];
            const dir = ord[0][1];
            const th  = $('#newsTable thead th').eq(col);
            if (!th.hasClass('sortable')) return;
            th.attr('data-sort-dir', dir);
            th.find('.sort-indicator').text(dir === 'asc' ? '▲' : '▼');
        }

        // Restore saved sort
        try {
            const saved = sessionStorage.getItem(HEADER_KEY);
            if (saved) {
                const { col, dir } = JSON.parse(saved);
                if (typeof col === 'number' && (dir === 'asc' || dir === 'desc')) {
                    table.order([col, dir]).draw();
                }
            }
        } catch(e){}

        updateIndicators();

        // 2‑state toggle
        $('#newsTable thead').on('click', 'th.sortable', function() {
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
                sessionStorage.setItem(HEADER_KEY, JSON.stringify({ col: ord[0][0], dir: ord[0][1] }));
            } catch(e){}
        });

        $('#newsTable thead').on('mousedown', 'th', function(e){
            if (e.shiftKey) e.preventDefault();
        });
    }
});
</script>
@endsection
