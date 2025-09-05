@extends('frontend.layouts.app')

@section('title', 'Recipe Sales by Date')

@section('content')
<div class="container py-5">

  {{-- Filter Form Section --}}
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-center">
        {{-- Product Filter --}}
        <div class="col-md-3">
          <label class="form-label">Product</label>
          <select name="recipe_id" class="form-select">
            <option value="">All products</option>
            @foreach($recipes as $id => $name)
              <option value="{{ $id }}" @selected($id == $recipeId)>{{ $name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Category Filter --}}
        <div class="col-md-3">
          <label class="form-label">Category</label>
          <select name="category_id" class="form-select">
            <option value="">All categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected($cat->id == $categoryId)>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Department Filter --}}
        <div class="col-md-3">
          <label class="form-label">Department</label>
          <select name="department_id" class="form-select">
            <option value="">All departments</option>
            @foreach($departments as $dept)
              <option value="{{ $dept->id }}" @selected($dept->id == $departmentId)>{{ $dept->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Date Range --}}
        <div class="col-md-2">
          <label class="form-label">From</label>
          <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">To</label>
          <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>

        {{-- Submit Button --}}
        <div class="col-md-2 text-end">
          <button class="btn btn-primary w-100">Filter</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Results Table --}}
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table  data-page-length="25"class="table table-striped table-bordered mb-0">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Department</th>
            <th class="text-end">Pieces Sold</th>
            <th class="text-end">Waste</th>
            <th class="text-end">Total Revenue (€)</th>
          </tr>
        </thead>
        <tbody>
          @php
            $grandSold = $grandWaste = $grandRevenue = 0;
          @endphp

          @forelse($recordsByRecipe as $rId => $days)
            @php
              $rec     = $recipes->contains($rId) ? $recipes->get($rId) : '–';
              $model   = $days->first()->first()->recipe;
              $cat     = $model->category?->name ?? '–';
              $dept    = $model->department?->name ?? '–';
              $sold    = $days->flatten()->sum('sold');
              $waste   = $days->flatten()->sum('waste');
              $revenue = $days->flatten()->sum('actual_revenue');

              $grandSold    += $sold;
              $grandWaste   += $waste;
              $grandRevenue += $revenue;
            @endphp

            {{-- Parent Row --}}
            <tr class="accordion-toggle" 
                data-bs-toggle="collapse"
                data-bs-target="#details-{{ $rId }}"
                aria-expanded="false"
                style="cursor: pointer"
            >
              <td>
                <i class="bi bi-caret-down-fill me-1 toggle-icon" id="icon-{{ $rId }}"></i>
                {{ $model->recipe_name }}
              </td>
              <td>{{ $cat }}</td>
              <td>{{ $dept }}</td>
              <td class="text-end">{{ $sold }}</td>
              <td class="text-end">{{ $waste }}</td>
              <td class="text-end">{{ number_format($revenue, 2) }}</td>
            </tr>

            {{-- Detailed Row --}}
            <tr class="collapse" id="details-{{ $rId }}">
              <td colspan="6" class="p-0">
                <table  data-page-length="25"class="table table-sm mb-0">
                  <thead>
                    <tr class="table-light">
                      <th>Date</th>
                      <th class="text-end">Sold</th>
                      <th class="text-end">Waste</th>
                      <th class="text-end">Revenue (€)</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($days as $date => $rowsOnDate)
                      <tr>
                        <td>{{ $date }}</td>
                        <td class="text-end">{{ $rowsOnDate->sum('sold') }}</td>
                        <td class="text-end">{{ $rowsOnDate->sum('waste') }}</td>
                        <td class="text-end">{{ number_format($rowsOnDate->sum('actual_revenue'), 2) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </td>
            </tr>

          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-3">
                No data for that selection.
              </td>
            </tr>
          @endforelse
        </tbody>

        @if($recordsByRecipe->isNotEmpty())
          <tfoot class="table-light">
            <tr>
              <th colspan="3">Total</th>
              <th class="text-end">{{ $grandSold }}</th>
              <th class="text-end">{{ $grandWaste }}</th>
              <th class="text-end">{{ number_format($grandRevenue, 2) }}</th>
            </tr>
          </tfoot>
        @endif
      </table>
    </div>
  </div>

</div>
@endsection

{{-- Include Bootstrap's JS bundle --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Get all collapsible elements (the table rows)
    const collapsibleElements = document.querySelectorAll('.accordion-toggle');
    
    collapsibleElements.forEach(item => {
      item.addEventListener('click', function () {
        // Get target ID and icon
        const targetId = item.getAttribute('data-bs-target').substring(1); // Remove the '#' character
        const icon = document.getElementById('icon-' + targetId);

        // Toggle the collapse
        const collapseElement = document.getElementById(targetId);

        // Check if collapse is open or closed and toggle icon accordingly
        if (collapseElement.classList.contains('show')) {
          icon.classList.remove('bi-caret-up-fill');
          icon.classList.add('bi-caret-down-fill');
        } else {
          icon.classList.remove('bi-caret-down-fill');
          icon.classList.add('bi-caret-up-fill');
        }
      });
    });
  });
</script>
@endpush
