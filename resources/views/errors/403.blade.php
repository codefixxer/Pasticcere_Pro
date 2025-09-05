@extends('frontend.layouts.app')

@section('title', 'Azione non consentita')

@section('content')
<style>
  body {
    background-color: #f8fafc;
  }
  .error-container {
    min-height: calc(100vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }
  .error-card {
    max-width: 40vw;
    background: #ffffff;
    border-radius: 16px;
    padding: 40px 32px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    text-align: center;
  }
  .error-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    background: #fee2e2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .error-icon svg {
    width: 28px;
    height: 28px;
    stroke: #dc2626;
  }
  h1 {
    font-weight: 600;
    font-size: 1.5rem;
    margin-bottom: 8px;
    color: #1f2937;
  }
  p {
    color: #6b7280;
    font-size: 0.95rem;
    margin-bottom: 24px;
  }
</style>

<div class="error-container">
  <div class="error-card">
    <div class="error-icon">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" 
              d="M12 9v3.75m0 3.75h.007M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
      </svg>
    </div>
    <h1>Azione non consentita</h1>
    <p>Torna indietro per continuare</p>
    <a href="{{ url()->previous() }}" class="btn btn-primary px-4">Torna Indietro</a>
  </div>
</div>
@endsection
