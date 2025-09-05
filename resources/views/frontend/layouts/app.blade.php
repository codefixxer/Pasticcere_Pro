<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap"
        rel="stylesheet">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoWSClFY5OSZrN+8POMcF2Q3oV3gy1p25jmXoDkFdEY5b3+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" /> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <style>
        * {
            font-family: "Libre Baskerville", "montserrat" !important;
        }

        .dt-column-order {
            visibility: hidden;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>@yield('title', 'Admin Dashboard')</title>


    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.atom-one-dark.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">


    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />

    <style>
        .sidebar-menu li.dropdown.open>a,
        .sidebar-menu li.dropdown.dropdown-open>a {
            background-color: #041930 !important;
            color: #fff;
        }


        /* 1) mak
        e the UL stretch and push last item down */
        .sidebar-menu-area {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .sidebar-menu {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        /* normal li’s keep their default spacing */
        .sidebar-menu li {
            margin: 0;
        }

        /* push .sidebar-academy all the way to the bottom */
        .sidebar-menu li.sidebar-academy {
            margin-top: auto;
            padding: 5vw 1vw;
            text-align: center;
        }

        /* style the link like a big button */
        .sidebar-menu li.sidebar-academy a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            background-color: #f06292;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            border-radius: .5rem;
            padding: .75rem 1.25rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .2);
            transition: background-color .2s, transform .1s;
        }

        .sidebar-menu li.sidebar-academy a:hover {
            background-color: #ec407a;
            transform: translateY(-2px);
        }

        /* optional: make the icon a bit larger */
        .sidebar-menu li.sidebar-academy .academy-icon {
            font-size: 1.25rem;
        }

        .blink {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                background-color: transparent;
            }

            50% {
                background-color: yellow;
            }

            100% {
                background-color: transparent;
            }
        }




        /* Remove number‐input spinners globally */

        /* Chrome, Safari, Edge, Opera */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Newer browsers */
        input[type=number] {
            appearance: none;
        }
    </style>


    <style>
        .dropdown a::after {
            color: #e2ae76
        }

        .sidebar-menu-area {
            overflow: auto;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE 10+ */
            border: none !important;
        }

        .sidebar-menu-area::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .sidebar {
            /* position: relative; */
            background: url('{{ asset('assets/images/asset/sidebar.jpg') }}') center/cover no-repeat;
            color: #fff;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .sidebar::before {
            content: "";
            position: absolute;
            inset: 0;
            background-color: rgba(0, 24, 72, 0.763);
            pointer-events: none;
            z-index: 0;
        }

        .sidebar>* {
            position: relative;
            z-index: 1;
            /* lift logo, menu, button above overlay */
        }

        /* ─── Logout Button ──────────────────────────────────────────────────── */
        .sidebar-logout {
            margin-top: auto;
            /* push to bottom of flex container */

        }

        .sidebar-logo {
            border-right: 0 solid white;
            border-bottom: none;

        }

        .logout-btn {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin: 1rem;
            background-color: #ff4d4d;
            color: #041930;
            background-color: #e2ae76;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .logout-btn:hover {
            background-color: #041930;
            color: #e2ae76 !important;

        }

        .logout-icon {
            margin-right: 0.5rem;
            font-size: 1.25rem;
        }
    </style>
    {{-- GLOBAL DATATABLES DEFAULTS (put after jQuery + DataTables, before @yield('scripts')) --}}
    <script>
        (function() {
            function ready(fn) {
                if (document.readyState !== 'loading') fn();
                else document.addEventListener('DOMContentLoaded', fn);
            }

            ready(function() {
                if (!(window.jQuery && jQuery.fn && jQuery.fn.DataTable)) return;

                // A) Site-wide defaults
                jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                    pageLength: 25,
                    language: {
                        lengthMenu: "Mostra _MENU_ elementi per pagina",
                        search: "Cerca:",
                        info: "Mostra _START_ a _END_ di _TOTAL_ elementi",
                        zeroRecords: "Nessun record trovato",
                        paginate: {
                            first: "<<",
                            previous: "<",
                            next: ">",
                            last: ">>"
                        }
                    }
                });

                // B) Always enforce icon labels (even if a page sets its own language)
                jQuery(document).on('preInit.dt', function(e, settings) {
                    settings.oLanguage = settings.oLanguage || {};
                    settings.oLanguage.oPaginate = settings.oLanguage.oPaginate || {};
                    settings.oLanguage.oPaginate.sFirst = "<<";
                    settings.oLanguage.oPaginate.sPrevious = "<";
                    settings.oLanguage.oPaginate.sNext = ">";
                    settings.oLanguage.oPaginate.sLast = ">>";
                });

                // C) Normalize page length to 25 unless the page explicitly set pageLength in init options
                //    (ignores data-attributes and old saved values)
                jQuery(document).on('preInit.dt', function(e, settings) {
                    var hasExplicit = settings.oInit && typeof settings.oInit.pageLength !==
                    'undefined';
                    if (!hasExplicit) {
                        // If DataTables or saved state set 10 (default), bump to 25 before first draw
                        if (!settings._iDisplayLength || settings._iDisplayLength === 10) {
                            settings._iDisplayLength = 25;
                        }
                    }
                });

                // D) If stateSave is used anywhere, force saved length to 25
                jQuery(document).on('stateLoadParams.dt', function(e, settings, data) {
                    if (data && typeof data.length !== 'undefined') {
                        data.length = 25;
                    }
                });

                // E) Final safety: after init, if length still 10 and not explicitly overridden, set to 25
                jQuery(document).on('init.dt', function(e, settings) {
                    var hasExplicit = settings.oInit && typeof settings.oInit.pageLength !==
                    'undefined';
                    if (!hasExplicit) {
                        var api = new jQuery.fn.dataTable.Api(settings);
                        if (api.page.len() === 10) {
                            api.page.len(25).draw(false);
                        }
                    }
                });
            });
        })();
    </script>
<script>
(function ($) {
  if (!window.jQuery) return;

  // 1) Global defaults (covers DT 1.x and 2.x)
  if (window.DataTable || DataTable.defaults) {
    DataTable.defaults.pageLength = 25;
    DataTable.defaults.iDisplayLength = 25; // safety for internal alias
  }
  if ($.fn && $.fn.dataTable && $.fn.dataTable.defaults) {
    $.extend(true, $.fn.dataTable.defaults, { pageLength: 25 });
  }

  // 2) Hard override per-table in case markup or code tries to force 10
  $(document)
    .on('preInit.dt', function (e, settings) {
      settings.oInit.pageLength = 25;
      settings._iDisplayLength = 25;
    })
    .on('stateLoadParams.dt', function (e, settings, data) {
      // if stateSave is ever enabled, force 25 on load
      if (data && typeof data.length !== 'undefined') data.length = 25;
    });
})(jQuery);
</script>
        @yield('styles')

</head>

<body>

    <main class="dashboard-main">
        @include('frontend.layouts.sidebar')
        @include('frontend.layouts.navbar')

        <!-- Main Content Wrapper -->

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
      
        <!-- End wrapper -->
        @yield('scripts')
@stack('scripts')

        <script>
            (function($) {
                // 1) Hide header arrows globally (DT 1.10 + 2.x)
                const css = `
    .dt-column-order { display: none !important; } /* DT v2 icon */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after,
    table.dataTable thead th.dt-orderable-asc:before,
    table.dataTable thead th.dt-orderable-desc:after,
    table.dataTable thead th.dt-ordering-asc:before,
    table.dataTable thead th.dt-ordering-desc:after {
      display: none !important;
      content: none !important;
    }
  `;
                const style = document.createElement('style');
                style.type = 'text/css';
                style.appendChild(document.createTextNode(css));
                document.head.appendChild(style);

                // 2) Global DataTables defaults
                $.extend(true, $.fn.dataTable.defaults, {
                    // Two-state toggle only (ASC ⇄ DESC) on every sortable column
                    columnDefs: [{
                        targets: '_all',
                        orderSequence: ['asc', 'desc']
                    }],

                    // No multi-column sorting via Shift-click
                    orderMulti: false,

                    // Keep the chosen order for the whole browser session
                    stateSave: true,
                    stateDuration: 0, // 0 = sessionStorage

                    // Sensible default (can be overridden per table)
                    order: [
                        [0, 'asc']
                    ]
                });

                // 3) Belt & braces: block Shift multi-sort on all tables
                $(document).on('mousedown', 'table.dataTable thead th', function(e) {
                    if (e.shiftKey) e.preventDefault();
                });

            })(jQuery);
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Disable up and down arrow key changes on all number inputs
                document.querySelectorAll('input[type="number"]').forEach(input => {
                    input.addEventListener('keydown', function(e) {
                        if (e.key === "ArrowUp" || e.key === "ArrowDown") {
                            e.preventDefault(); // Prevents the default behavior
                        }
                    });
                });
            });
        </script>




        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>



        <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
            <script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
        <script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>

        <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
