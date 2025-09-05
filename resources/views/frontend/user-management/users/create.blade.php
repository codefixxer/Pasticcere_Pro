@extends('frontend.layouts.app')

@section('title', $isEdit ? 'Modifica Utente' : 'Aggiungi Utente')

@section('content')
    @php
        // For expiry‐date toggle & value
        $expiryEnabled = old('expiry_enabled', $user->expiry_date ? 'on' : '');
        $expiryValue = old(
            'expiry_date',
            $user->expiry_date ? \Carbon\Carbon::parse($user->expiry_date)->format('Y-m-d') : '',
        );
        $minDate = date('Y-m-d');
        $currentRole = $isEdit ? optional($user->roles->first())->name : null;
    @endphp

    <div class="container py-5 px-md-4">
        <div class="page-header d-flex align-items-center mb-4"
            style="background-color: #041930; border-radius: .75rem; padding: 1rem 2rem;">
            <i class="bi bi-person-fill-gear me-2 fs-3" style="color: #e2ae76;"></i>
            <h4 class="mb-0 fw-bold" style="color: #e2ae76;">
                {{ $isEdit ? 'Modifica Utente' : 'Aggiungi Utente' }}
            </h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    {{-- Nome --}}
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                            required>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}"
                            required>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Password
                            @if ($isEdit)
                                <small class="text-muted">(lascia vuoto per mantenere quella attuale)</small>
                            @endif
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="passwordInput" class="form-control"
                                {{ $isEdit ? '' : 'required' }}>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Ruolo --}}
                    <div class="mb-4">
                        <label for="role" class="form-label">Ruolo</label>
                        @if ($isEdit && auth()->id() === $user->id)
                            <div>
                                <span class="badge bg-primary">{{ ucfirst($currentRole) }}</span>
                            </div>
                            <input type="hidden" name="role" value="{{ optional($user->roles->first())->id }}">
                        @else
                            <select id="role" name="role" class="form-select" required>
                                @foreach ($roles as $role)
                                    @if ($role->name === 'super')
                                        @continue
                                    @endif
                                    <option value="{{ $role->id }}"
                                        {{ (string) old('role', optional($user->roles->first())->id) === (string) $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- VAT --}}
                    {{-- Partita IVA (was VAT) --}}
                    <div class="mb-3">
                        <label for="vat" class="form-label">Partita IVA</label>
                        <input type="text" id="vat" name="vat" class="form-control"
                            value="{{ old('vat', $user->vat) }}">
                    </div>

                    {{-- Indirizzo (was Address) --}}
                    <div class="mb-3">
                        <label for="address" class="form-label">Indirizzo</label>
                        <textarea id="address" name="address" class="form-control">{{ old('address', $user->address) }}</textarea>
                    </div>

                    {{-- Profile Photo --}}
{{-- Foto profilo --}}
<div class="mb-3">
  <label for="photo" class="form-label">Foto profilo</label>
  <input type="file" name="photo" id="photo" class="form-control" accept="image/*">

  <div class="mt-2">
    <small class="text-muted d-block mb-1">Foto attuale:</small>
    
    @if ($user->photo && Storage::disk('public')->exists('photos/'.$user->photo))
      {{-- User photo --}}
      <img src="{{ asset('storage/photos/'.$user->photo) }}" 
           alt="Profile Photo" 
           width="110" 
           height="110" 
           style="object-fit:cover;border-radius:8px;">
    @else
      {{-- Default placeholder --}}
      <img src="{{ asset('assets/images/asset/user-placeholder.jpg') }}" 
           alt="No Photo" 
           width="110" 
           height="110" 
           style="object-fit:cover;border-radius:8px;">
    @endif
  </div>
</div>


                    {{-- Expiry date toggle & picker only for super --}}
                    @role('super')
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="expiryToggle" name="expiry_enabled"
                                {{ $expiryEnabled === 'on' ? 'checked' : '' }}>
                            <label class="form-check-label" for="expiryToggle">
                                Aggiungi data di scadenza / Next renew
                            </label>
                        </div>

                        <div class="mb-3" id="expiryDateWrapper"
                            style="{{ $expiryEnabled === 'on' ? '' : 'display:none' }};">
                            <label class="form-label">Data di scadenza</label>
                            <input type="date" name="expiry_date" id="expiryDate" class="form-control"
                                min="{{ $minDate }}" value="{{ $expiryValue }}">
                        </div>
                    @endrole

                    <button type="submit" class="btn btn-gold-blue px-4 py-2 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isEdit ? 'Aggiorna Utente' : 'Aggiungi Utente' }}
                    </button>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toggleBtn = document.getElementById('togglePassword');
                        const passwordInput = document.getElementById('passwordInput');
                        const icon = document.getElementById('togglePasswordIcon');

                        toggleBtn.addEventListener('click', function() {
                            const isPassword = passwordInput.getAttribute('type') === 'password';
                            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                            icon.classList.toggle('bi-eye');
                            icon.classList.toggle('bi-eye-slash');
                        });

                        @role('super')
                            // expiry date show/hide
                            const expiryToggle = document.getElementById('expiryToggle');
                            const expiryWrapper = document.getElementById('expiryDateWrapper');
                            expiryToggle.addEventListener('change', function() {
                                expiryWrapper.style.display = this.checked ? '' : 'none';
                            });
                        @endrole
                    });
                </script>

            </div>
        </div>
    </div>

    <style>
        .btn-gold-blue {
            background-color: #e2ae76 !important;
            color: #041930 !important;
            border: 1px solid #e2ae76;
        }

        .btn-gold-blue:hover {
            background-color: #d89d5c !important;
            color: #fff !important;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('expiryToggle');
            const wrapper = document.getElementById('expiryDateWrapper');
            const dateInput = document.getElementById('expiryDate');

            function refresh() {
                if (toggle.checked) {
                    wrapper.style.display = '';
                    dateInput.required = true;
                } else {
                    wrapper.style.display = 'none';
                    dateInput.required = false;
                    dateInput.value = '';
                }
            }

            if (toggle) {
                toggle.addEventListener('change', refresh);
                refresh();
            }
        });
    </script>
@endsection
