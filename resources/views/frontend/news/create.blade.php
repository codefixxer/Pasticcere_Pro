@extends('frontend.layouts.app')

@section('title', isset($news) ? 'Modifica Notizia' : 'Crea Notizia')

@section('content')
    <div class="container py-5 px-md-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center" style="background-color: #041930;">
                <i class="bi bi-megaphone fs-4 me-2" style="color: #e2ae76;"></i>
                <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
                  {{ isset($news) ? 'Modifica Notizia' : 'Crea Notizia' }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ isset($news) ? route('news.update', $news->id) : route('news.store') }}"
                      class="needs-validation" novalidate enctype="multipart/form-data">
                    @csrf

                    @if (isset($news))
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label for="image" class="form-label fw-semibold">Immagine</label>
                        <input type="file" id="image" name="image" class="form-control form-control-lg"
                               accept="image/*">
                        @if (isset($news) && $news->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $news->image) }}"
                                     alt="Anteprima immagine"
                                     style="max-height:100px;">
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">Titolo</label>
                        <input type="text" id="title" name="title" class="form-control form-control-lg"
                               value="{{ old('title', $news->title ?? '') }}" required>
                        <div class="invalid-feedback">Per favore inserisci un titolo.</div>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-semibold">Contenuto</label>
                        <textarea id="content" name="content" class="form-control form-control-lg" rows="4" required>{{ old('content', $news->content ?? '') }}</textarea>
                        <div class="invalid-feedback">Per favore inserisci il contenuto.</div>
                    </div>

                    <div class="mb-4">
                        <label for="event_date" class="form-label fw-semibold">Data Evento</label>
                        <input type="date" id="event_date" name="event_date" class="form-control form-control-lg"
                               value="{{ old('event_date', isset($news) ? $news->event_date->format('Y-m-d') : '') }}"
                               required>
                        <div class="invalid-feedback">Per favore seleziona una data.</div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-gold-filled btn-lg">
                            <i class="bi bi-send me-2"></i>
                            {{ isset($news) ? 'Aggiorna Notizia' : 'Crea Notizia' }}
                        </button>
                    </div>
                </form>
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

    .btn-gold-filled i {
        color: inherit !important;
    }
</style>

@section('scripts')
    <script>
        // Validazione Bootstrap
        (() => {
            'use strict';
            document.querySelectorAll('.needs-validation').forEach(form => {
                form.addEventListener('submit', e => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        })();
    </script>
@endsection
