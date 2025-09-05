{{-- resources/views/frontend/clients/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Clienti')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Aggiungi / Modifica Cliente -->
  <div class="card border-primary shadow-sm mb-5">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-person-lines-fill fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($client) ? 'Modifica Cliente' : 'Aggiungi Cliente' }}
      </h5>
    </div>
    <div class="card-body">
      <form
        action="{{ isset($client) ? route('clients.update', $client->id) : route('clients.store') }}"
        method="POST"
        class="row g-3 needs-validation"
        novalidate>
        @csrf
        @if(isset($client)) @method('PUT') @endif

        <div class="col-md-6">
          <label for="name" class="form-label fw-semibold">Nome Cliente</label>
          <input
            type="text"
            name="name"
            id="name"
            class="form-control form-control-lg"
            value="{{ old('name', $client->name ?? '') }}"
            required>
          <div class="invalid-feedback">Inserisci il nome del cliente.</div>
        </div>

        <div class="col-md-6">
          <label for="location" class="form-label fw-semibold">Sede</label>
          <input
            type="text"
            name="location"
            id="location"
            class="form-control form-control-lg"
            value="{{ old('location', $client->location ?? '') }}">
        </div>

        <div class="col-md-4">
          <label for="phone" class="form-label fw-semibold">Telefono</label>
          <input
            type="text"
            name="phone"
            id="phone"
            class="form-control form-control-lg"
            value="{{ old('phone', $client->phone ?? '') }}">
        </div>

        <div class="col-md-4">
          <label for="email" class="form-label fw-semibold">Email</label>
          <input
            type="email"
            name="email"
            id="email"
            class="form-control form-control-lg"
            value="{{ old('email', $client->email ?? '') }}">
        </div>

        <div class="col-md-4">
          <label for="notes" class="form-label fw-semibold">Note</label>
          <input
            type="text"
            name="notes"
            id="notes"
            class="form-control form-control-lg"
            value="{{ old('notes', $client->notes ?? '') }}">
        </div>

        <div class="col-12 text-end">
          <button
            type="submit"
            class="btn btn-lg fw-semibold"
            style="background-color: #e2ae76; color: #041930;">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($client) ? 'Aggiorna Cliente' : 'Salva Cliente' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabella Clienti -->
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #041930;">
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        <i class="bi bi-people me-2"></i> Clienti
      </h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table
          id="clientsTable"
          class="table table-bordered table-striped table-hover align-middle mb-0 text-center"
          data-page-length="25">
          <thead style="background-color: #e2ae76; color: #041930;">
            <tr>
              <th>Nome</th>
              <th>Sede</th>
              <th>Telefono</th>
              <th>Email</th>
              <th>Note</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @forelse($clients as $client)
              <tr>
                <td>{{ $client->name }}</td>
                <td>{{ $client->location }}</td>
                <td>{{ $client->phone }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ \Illuminate\Support\Str::limit($client->notes, 50) }}</td>
                <td>
                  <a
                    href="{{ route('clients.show', $client) }}"
                    class="btn btn-sm btn-deepblue me-1"
                    title="Visualizza">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a
                    href="{{ route('clients.edit', $client) }}"
                    class="btn btn-sm btn-gold me-1"
                    title="Modifica">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form
                    action="{{ route('clients.destroy', $client) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Eliminare questo cliente?');">
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="btn btn-sm btn-red"
                      title="Elimina">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-muted">Nessun cliente trovato.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-3">
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (window.$ && $.fn.DataTable) {
      $.fn.dataTable.ext.errMode = 'none';
      $('#clientsTable').DataTable({
        paging:      true,
        ordering:    true,
        responsive:  true,
        pageLength:  $('#clientsTable').data('page-length') || 10,
        columnDefs: [
          { orderable: false, targets: -1 }
        ],
        language: {
          lengthMenu:    "Mostra _MENU_ elementi per pagina",
          search:        "Cerca:",
          info:          "Mostra _START_ a _END_ di _TOTAL_ elementi",
          zeroRecords:   "Nessun record trovato",
          paginate: {
            first:    "Primo",
            previous: "←",
            next:     "→",
            last:     "Ultimo"
          }
        }
      });
    }

    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  });
</script>
@endsection


<style>
  .btn-gold {
    border: 1px solid #e2ae76 !important;
    color: #e2ae76 !important;
    background-color: transparent !important;
  }
  .btn-gold:hover {
    background-color: #e2ae76 !important;
    color: white !important;
  }

  .btn-deepblue {
    border: 1px solid #041930 !important;
    color: #041930 !important;
    background-color: transparent !important;
  }
  .btn-deepblue:hover {
    background-color: #041930 !important;
    color: white !important;
  }

  .btn-red {
    border: 1px solid #ff0000 !important;
    color: red !important;
    background-color: transparent !important;
  }
  .btn-red:hover {
    background-color: #ff0000 !important;
    color: white !important;
  }

  table thead th {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    text-align: center !important;
    vertical-align: middle !important;
  }
  table tbody td {
    text-align: center !important;
    vertical-align: middle !important;
  }
  table thead .sorting:after,
  table thead .sorting_asc:after,
  table thead .sorting_desc:after {
    color: #041930 !important;
  }
</style>
