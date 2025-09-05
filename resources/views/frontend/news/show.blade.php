{{-- resources/views/frontend/news/show.blade.php --}}

@extends('frontend.layouts.app')

@section('title', $news->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg rounded-3 overflow-hidden">

                {{-- Anteprima Immagine --}}
                @if ($news->image)
                    <img src="{{ asset('storage/' . $news->image) }}"
                         alt="{{ $news->title }}"
                         class="card-img-top"
                         style="object-fit: cover; height: 300px;">
                @endif

                {{-- Titolo & Timestamp --}}
                <div class="card-header" style="background-color: #041930; color: #e2ae76;">
                    <h2 class="mb-0"  style="background-color: #041930; color: #e2ae76;">{{ $news->title }}</h2>
                    <span class="badge bg-warning text-dark">{{ $news->created_at->diffForHumans() }}</span>
                </div>

                {{-- Contenuto con link rilevati automaticamente --}}
                <div class="card-body">
                    <div class="text-muted" style="white-space: pre-wrap;">
                        {!! preg_replace(
                            '~(https?://[^\s<]+)~',
                            '<a href="$1" class="text-primary" target="_blank" rel="noopener noreferrer">$1</a>',
                            e($news->content)
                        ) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
