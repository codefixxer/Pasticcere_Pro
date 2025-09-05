<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sessione Scaduta</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
      color: #333;
    }
    .error-card {
      max-width: 480px;
      margin: 80px auto;
    }
  </style>
</head>
<body>
  <div class="card error-card shadow-sm">
    <div class="card-body text-center">
      <h1 class="card-title display-5 mb-3">Sessione Scaduta</h1>
      <p class="card-text lead mb-4">
        La tua sessione è scaduta o non valida.<br>
        Per favore <a href="{{ route('login') }}" class="fw-semibold">effettua di nuovo l’accesso</a>.
      </p>
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Torna Indietro</a>
    </div>
  </div>
  <!-- Bootstrap JS Bundle (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
