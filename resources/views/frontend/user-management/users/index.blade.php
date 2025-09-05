  {{-- resources/views/frontend/user-management/users/index.blade.php --}}
  @extends('frontend.layouts.app')

  @section('title','Utenti')

  @section('content')
  @php
      use Carbon\Carbon;
  @endphp

  <div class="container py-5 px-md-4">

    {{-- Scheda Profilo Utente Loggato --}}
    <div class="row mb-5 justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg rounded-3 border-0 overflow-hidden">
          <div class="card-body text-center pt-5">
            <h4 class="fw-bold mb-1">{{ auth()->user()->name }}</h4>
            <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
            <div class="mb-3">
              @forelse(auth()->user()->roles as $role)
                <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
              @empty
                <span class="text-secondary">Nessun ruolo assegnato</span>
              @endforelse
            </div>
            <a href="{{ route('users.show', auth()->user()) }}"
              class="btn btn-deepblue btn-sm me-2" title="Visualizza Profilo">
              <i class="bi bi-eye me-1"></i>Visualizza Profilo
            </a>
            <a href="{{ route('users.edit', auth()->user()) }}"
              class="btn btn-gold btn-sm me-2" title="Modifica Profilo">
              <i class="bi bi-pencil me-1"></i>Modifica Profilo
            </a>
            <a href="{{ route('logout') }}" 
              onclick="event.preventDefault();document.getElementById('logout-form').submit();"
              class="btn btn-red btn-sm" title="Esci">
              <i class="bi bi-box-arrow-right me-1"></i>Esci
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </div>
        </div>
      </div>
    </div>

    {{-- Intestazione Pagina & Bottone Aggiungi Utente --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="page-header d-flex align-items-center mb-0">
        <i class="bi bi-people-fill me-2 fs-3" style="color: #e2ae76;"></i>
        <h2 class="mb-0 fw-bold" style="color: #041930;">Utenti</h2>
      </div>
      <a href="{{ route('users.create') }}" class="btn btn-gold-blue btn-lg">
        <i class="bi bi-plus-lg me-1"></i> Aggiungi Utente
      </a>
    </div>

    {{-- Messaggio di Successo --}}
    @if(session('success'))
      <div class="alert alert-success mb-4 p-3 rounded-3 shadow-sm">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Successo!</strong> {{ session('success') }}
      </div>
    @endif

    {{-- Tabella Utenti --}}
    <div class="card border-primary shadow-sm">
      <div class="card-header" style="background-color: #041930;">
        <h5 class="mb-0" style="color: #e2ae76;">Elenco Utenti</h5>
      </div>
      <div class="card-body px-4" style="overflow-x: hidden;">
        <div class="table-responsive p-3">
          <table
            id="usersTable"
            class="table table-bordered table-striped table-hover align-middle mb-0 text-center"
            data-page-length="25"
          >
            <thead>
              <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Ruoli</th>
                <th>Scadenza</th>
                <th>Stato</th>
                <th class="text-end">Azioni</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $u)
                @php
                  $today  = Carbon::today();
                  $expiry = $u->expiry_date ? Carbon::parse($u->expiry_date) : null;

                  if (!$expiry) {
                      $label = '—';
                      $badge = 'bg-secondary';
                  } else {
                      if ($expiry->isPast()) {
                          $diff = $expiry->diffInDays($today);
                          $label = "Scaduto {$diff} gg fa";
                          $badge = 'bg-danger blink';
                      } else {
                          $diff = $today->diffInDays($expiry);
                          $label = "Tra {$diff} gg";
                          $badge = $diff <= 7 ? 'bg-warning' : 'bg-success';
                      }
                  }
                @endphp
                <tr>
                  <td>{{ $u->name }}</td>
                  <td>{{ $u->email }}</td>
                  <td class="text-center">
                    @forelse($u->roles as $r)
                      <span class="badge bg-secondary">{{ ucfirst($r->name) }}</span>
                    @empty
                      <em class="text-muted">—</em>
                    @endforelse
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $badge }}">{{ $label }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $u->status ? 'bg-success' : 'bg-danger' }}">
                      {{ $u->status ? 'Attivo' : 'Inattivo' }}
                    </span>
                  </td>
                  <td class="text-end">
                    <a href="{{ route('users.show', $u) }}" class="btn btn-deepblue btn-sm me-1" title="Visualizza Utente">
                      <i class="bi bi-eye"></i> Visualizza
                    </a>
                    <a href="{{ route('users.edit', $u) }}" class="btn btn-gold btn-sm me-1" title="Modifica Utente">
                      <i class="bi bi-pencil"></i> Modifica
                    </a>
                    @if(auth()->user()->hasRole('super') && auth()->id() !== $u->id)
                      <form action="{{ route('users.toggleStatus', $u->id) }}" method="POST" class="d-inline me-1">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $u->status ? 'btn-red' : 'btn-deepblue' }}">
                          {{ $u->status ? 'Disattiva' : 'Attiva' }}
                        </button>
                      </form>
                      <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Eliminare questo utente?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-red btn-sm">
                          <i class="bi bi-trash"></i> Elimina
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <style>
    /* Table header */
    table.dataTable thead th {
      background-color: #e2ae76 !important;
      color: #041930 !important;
      text-align: center;
      vertical-align: middle;
    }

    /* Table cells */
    table.dataTable tbody td {
      text-align: center;
      vertical-align: middle;
    }

    /* Prevent bottom scrollbar */
    .card-body[style*="overflow-x"] .table-responsive {
      overflow-x: visible !important;
    }

    /* Buttons */
    .btn-gold {
      border: 1px solid #e2ae76 !important;
      color: #e2ae76 !important;
      background-color: transparent !important;
    }
    .btn-gold:hover {
      background-color: #e2ae76 !important;
      color: #fff !important;
    }
    .btn-gold-blue {
      background-color: #e2ae76 !important;
      color: #041930 !important;
      border: 1px solid #e2ae76;
    }
    .btn-gold-blue:hover {
      background-color: #d89d5c !important;
      color: #fff !important;
    }
    .btn-deepblue {
      border: 1px solid #041930 !important;
      color: #041930 !important;
      background-color: transparent !important;
    }
    .btn-deepblue:hover {
      background-color: #041930 !important;
      color: #fff !important;
    }
    .btn-red {
      border: 1px solid red !important;
      color: red !important;
      background-color: transparent !important;
    }
    .btn-red:hover {
      background-color: red !important;
      color: #fff !important;
    }

    /* Blinking animation for expired badges */
    @keyframes blink {
      0%, 49%   { opacity: 1; }
      50%, 100% { opacity: 0; }
    }
    .blink {
      animation: blink 1s steps(1, end) infinite;
    }

    /* Prevent Primo/Ultimo from wrapping */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      white-space: nowrap;
    }
  </style>

  @endsection

  @section('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    if (window.$ && $.fn.DataTable) {
      $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10, // ensure it shows only 10 per page
        lengthChange: false, // hides the "Show X entries" dropdown (optional)
        ordering: true,
        columnDefs: [
          { orderable: false, targets: -1 } // Disable ordering on last column (Azioni)
        ],
        language: {
          emptyTable: "Nessun utente trovato.",
          search: "_INPUT_",
          searchPlaceholder: "Cerca utenti...",
          lengthMenu: "Mostra _MENU_ elementi",
          zeroRecords: "Nessun utente corrispondente",
          info: "Mostra _START_ a _END_ di _TOTAL_ elementi",
          infoEmpty: "Nessun utente disponibile",
          paginate: {
            first: "Primo",
            last:  "Ultimo",
            next:  "→",
            previous: "←"
          }
        }
      });
    }

    // Bootstrap 5 tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  });
  </script>

  @endsection
