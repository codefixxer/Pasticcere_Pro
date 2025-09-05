<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pasticcere Pro | Login</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}" sizes="16x16">
  <!-- remix icon font css  -->
  <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
  <!-- Bootstrap css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
  <!-- Apex Chart css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/apexcharts.css') }}">
  <!-- Data Table css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}">
  <!-- Text Editor css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor-katex.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.atom-one-dark.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.quill.snow.css') }}">
  <!-- Date picker css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}">
  <!-- Calendar css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/full-calendar.css') }}">
  <!-- Vector Map css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/jquery-jvectormap-2.0.5.css') }}">
  <!-- Popup css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/magnific-popup.css') }}">
  <!-- Slick Slider css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/slick.css') }}">
  <!-- Prism css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/prism.css') }}">
  <!-- File upload css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/file-upload.css') }}">
  <!-- Audio player css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/audioplayer.css') }}">
  <!-- Main css -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
  <section class="auth bg-base d-flex flex-wrap">
    <div class="auth-left d-none d-lg-flex align-items-center justify-content-center">
      <img src="{{ asset('assets/images/asset/login.jpg') }}" alt="">
    </div>

    <div class="auth-right py-32 px-24 d-flex align-items-center">
      <div class="mx-auto" style="max-width: 464px; width: 100%;">
        <div class="text-center mb-40">
          <a href="{{ url('/') }}">
            <img src="{{ asset('assets/images/asset/logo.jpg') }}" alt="">
          </a>
        </div>

        <h4 class="mb-2">Accedi a Pasticcere Pro</h4>
        <p class="mb-32 text-secondary-light text-lg">Bentornato! Inserisci i tuoi dati</p>

        @if(session('error'))
          <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif
        @if(session('status'))
          <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
          @csrf

          <div class="icon-field mb-16">
            <span class="icon top-50 translate-middle-y">
              <iconify-icon icon="mage:email"></iconify-icon>
            </span>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              required
              autofocus
              class="form-control h-56-px bg-neutral-50 radius-12 @error('email') is-invalid @enderror"
              placeholder="Email"
            >
            @error('email')
              <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="position-relative mb-16">
            <div class="icon-field">
              <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
              </span>
              <input
                type="password"
                name="password"
                id="your-password"
                required
                class="form-control h-56-px bg-neutral-50 radius-12 @error('password') is-invalid @enderror"
                placeholder="Password"
              >
            </div>
            <span
              class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
              data-toggle="#your-password"
            ></span>
            @error('password')
              <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-between align-items-center mb-24">
            <div class="form-check mb-0">
              <input
                class="form-check-input"
                type="checkbox"
                name="remember"
                id="remember"
                {{ old('remember') ? 'checked' : '' }}
              >
              <label class="form-check-label ms-2" for="remember">
                Ricordati di me
              </label>
            </div>
            <a href="{{ route('password.request') }}" class="text-sm">
              Password dimenticata?
            </a>
          </div>

          <div class="d-flex flex-wrap justify-content-center gap-2 mb-24">
            <button type="button"
                    class="btn btn-outline-primary quick-login-btn"
                    data-email="super@example.com"
                    data-password="password123">
              Super
            </button>
            <button type="button"
                    class="btn btn-outline-primary quick-login-btn"
                    data-email="admin@example.com"
                    data-password="password123">
              Admin
            </button>
            <button type="button"
                    class="btn btn-outline-primary quick-login-btn"
                    data-email="shop@example.com"
                    data-password="password123">
              Shop
            </button>
            <button type="button"
                    class="btn btn-outline-primary quick-login-btn"
                    data-email="lab@example.com"
                    data-password="password123">
              Lab
            </button>
            <button type="button"
                    class="btn btn-outline-primary quick-login-btn"
                    data-email="master@example.com"
                    data-password="password123">
              Master
            </button>
          </div>

          <button
            type="submit"
            class="btn text-sm btn-sm px-12 py-16 w-100 radius-12"
            style="background-color: #e2ae76; color: #041930; border: 2px solid #e2ae76;"
          >
            Sign In
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- jQuery library -->
  <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
  <!-- Bootstrap js -->
  <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
  <!-- Apex Chart js -->
  <script src="{{ asset('assets/js/lib/apexcharts.min.js') }}"></script>
  <!-- Data Table js -->
  <script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
  <!-- Iconify Font js -->
  <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
  <!-- jQuery UI js -->
  <script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
  <!-- Vector Map js -->
  <script src="{{ asset('assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
  <script src="{{ asset('assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
  <!-- Popup js -->
  <script src="{{ asset('assets/js/lib/magnific-popup.min.js') }}"></script>
  <!-- Slick Slider js -->
  <script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
  <!-- Prism js -->
  <script src="{{ asset('assets/js/lib/prism.js') }}"></script>
  <!-- File upload js -->
  <script src="{{ asset('assets/js/lib/file-upload.js') }}"></script>
  <!-- Audio player js -->
  <script src="{{ asset('assets/js/lib/audioplayer.js') }}"></script>
  <!-- Main js -->
  <script src="{{ asset('assets/js/app.js') }}"></script>

  <script>
    // Password toggle
    function initializePasswordToggle(sel) {
      $(sel).on('click', function() {
        $(this).toggleClass("ri-eye-off-line");
        let inp = $($(this).attr("data-toggle"));
        inp.attr('type', inp.attr('type') === 'password' ? 'text' : 'password');
      });
    }
    initializePasswordToggle('.toggle-password');

    // Quick-login buttons
    $(document).on('click', '.quick-login-btn', function() {
      $('input[name="email"]').val($(this).data('email'));
      $('input[name="password"]').val($(this).data('password'));
      $(this).closest('form').submit();
    });
  </script>
</body>

</html>
