    @extends('frontend.layouts.app')

    @section('title','Create External Supplier')

    @section('content')
<div class="container py-5">
    <form method="POST" action="#">
        @csrf
        {{-- 1) Supplier & Product Details --}}
        <div class="card mb-4 border-primary shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-truck fs-4 me-2"></i>
                <h5 class="mb-0">Supplier & Product Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="supplierName" class="form-label fw-semibold">Supplier Name</label>
                        <input type="text" id="supplierName" name="supplier_name" class="form-control" placeholder="ABC Suppliers" required>
                    </div>
                    <div class="col-md-6">
                        <label for="productName" class="form-label fw-semibold">Product Name</label>
                        <input type="text" id="productName" name="product_name" class="form-control" placeholder="Imported Chocolate Cake" required>
                    </div>
                    <div class="col-md-6">
                        <label for="productCategory" class="form-label fw-semibold">Category</label>
                        <input type="text" id="productCategory" name="category" class="form-control" placeholder="Dessert" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2) Purchase Details (replacing Ingredients) --}}
        <div class="card mb-4 border-info shadow-sm">
            <div class="card-header bg-info text-white d-flex align-items-center">
                <i class="bi bi-cash-stack fs-4 me-2"></i>
                <h5 class="mb-0">Purchase Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="purchaseCostPerKg" class="form-label fw-semibold">Cost per Kg ($)</label>
                        <input type="number" step="0.01" id="purchaseCostPerKg" name="purchase_cost_per_kg" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6">
                        <label for="totalWeightPurchased" class="form-label fw-semibold">Total Weight Purchased (Kg)</label>
                        <input type="number" step="0.01" id="totalWeightPurchased" name="total_weight_purchased" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6">
                        <label for="totalPurchaseCost" class="form-label fw-semibold">Total Purchase Cost ($)</label>
                        <input type="text" id="totalPurchaseCost" name="total_purchase_cost" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3) Labor --}}
        <div class="card mb-4 border-warning shadow-sm">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="bi bi-clock-history fs-4 me-2"></i>
                <h5 class="mb-0">Labor</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="laborTimeInput" class="form-label fw-semibold">Active Preparation Time (min)</label>
                        <input type="number" id="laborTimeInput" name="labor_time_input" class="form-control" placeholder="0" required>
                    </div>
                    <div class="col-md-4">
                        <label for="costPerMin" class="form-label fw-semibold">Cost per Minute</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="text" id="costPerMin" class="form-control" value="{{ optional($laborCost)->cost_per_minute ?? '0.00' }}" readonly required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="laborCost" class="form-label fw-semibold">Labor Cost ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="text" id="laborCost" name="labor_cost" class="form-control" readonly required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4) Totals, Selling Mode & Cost Adjustments --}}
        <div class="row gx-4 mb-4">
            {{-- Total Expense & Packing --}}
            <div class="col-md-6">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-header bg-success text-white d-flex align-items-center">
                        <i class="bi bi-calculator fs-4 me-2"></i>
                        <h5 class="mb-0">Total Expense</h5>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center">
                        {{-- Packing Cost --}}
                        <div class="input-group w-75 mb-3">
                            <span class="input-group-text">Packing</span>
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" id="packingCost" name="packing_cost" class="form-control text-end" value="0.00">
                        </div>

                        {{-- Sum Expense --}}
                        <div class="input-group input-group-lg w-75 mb-3">
                            <span class="input-group-text">€</span>
                            <input type="text" id="totalExpense" name="total_expense" class="form-control fw-bold text-center" readonly required>
                        </div>

                        {{-- Adjusted Break-Even Calculation --}}
                        <div class="w-75 text-center">
                            <span class="fw-semibold">Adjusted Break-Even:</span>
                            <span id="adjustedBreakEven" class="fw-bold ms-2"></span>
                            <input type="hidden" name="adjusted_break_even" id="adjustedBreakEvenInput">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Selling Mode & Additional Inputs --}}
            <div class="col-md-6">
                <div class="card border-secondary shadow-sm h-100">
                    <div class="card-header bg-secondary text-white d-flex align-items-center">
                        <i class="bi bi-shop fs-4 me-2"></i>
                        <h5 class="mb-0">Selling Mode</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="sell_mode" id="modePiece" value="piece" checked>
                                <label class="form-check-label" for="modePiece">Sell by Piece</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="sell_mode" id="modeKg" value="kg">
                                <label class="form-check-label" for="modeKg">Sell by Kg</label>
                            </div>
                        </div>

                        {{-- Piece Inputs --}}
                        <div id="pieceInputs">
                            <div class="mb-3">
                                <label for="totalPieces" class="form-label fw-semibold">Total Pieces</label>
                                <input type="number" id="totalPieces" name="total_pieces" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="pricePerPiece" class="form-label fw-semibold">Selling Price per Piece ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" step="0.01" id="pricePerPiece" name="selling_price_per_piece" class="form-control">
                                </div>
                            </div>
                        </div>

                        {{-- Kg Inputs --}}
                        <div id="kgInputs" class="d-none">
                            <div class="mb-3">
                                <label for="totalWeightKg" class="form-label fw-semibold">Total Weight (Kg)</label>
                                <input type="number" step="0.01" id="totalWeightKg" name="product_weight" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="pricePerKg" class="form-label fw-semibold">Selling Price per Kg ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" step="0.01" id="pricePerKg" name="selling_price_per_kg" class="form-control">
                                </div>
                            </div>
                        </div>

                        {{-- Additional Input: Driver Salary --}}
                        <div class="mb-3">
                            <label for="driverSalary" class="form-label fw-semibold">Driver Salary per Minute ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" id="driverSalary" name="driver_salary" class="form-control">
                            </div>
                        </div>

                        {{-- Potential Margin Display --}}
                        <div class="mb-3">
                            <label for="potentialMargin" class="form-label fw-semibold">Potential Margin</label>
                            <p id="potentialMargin" class="form-control-plaintext"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="text-end">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="bi bi-save2 me-2"></i> Save External Supplier Details
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Purchase Details Elements
    const purchaseCostPerKg = document.getElementById('purchaseCostPerKg');
    const totalWeightPurchased = document.getElementById('totalWeightPurchased');
    const totalPurchaseCost = document.getElementById('totalPurchaseCost');

    // Labor and Totals Elements
    const laborTimeInput = document.getElementById('laborTimeInput');
    const costPerMin = parseFloat(document.getElementById('costPerMin').value) || 0;
    const laborCostIn = document.getElementById('laborCost');
    const packingCostIn = document.getElementById('packingCost');
    const totalExpenseIn = document.getElementById('totalExpense');
    const adjustedBreakEven = document.getElementById('adjustedBreakEven');
    const adjustedBreakEvenInput = document.getElementById('adjustedBreakEvenInput');
    const driverSalaryInp = document.getElementById('driverSalary');

    // Selling Mode Elements
    const modePiece = document.getElementById('modePiece');
    const modeKg = document.getElementById('modeKg');
    const totalPiecesIn = document.getElementById('totalPieces');
    const pricePerPiece = document.getElementById('pricePerPiece');
    const pricePerKg = document.getElementById('pricePerKg');
    const totalWeightKg = document.getElementById('totalWeightKg');

    // Calculate Purchase Cost from cost per Kg and total weight purchased
    function recalcPurchaseCost() {
        const costKg = parseFloat(purchaseCostPerKg.value) || 0;
        const weight = parseFloat(totalWeightPurchased.value) || 0;
        const purchaseCost = costKg * weight;
        totalPurchaseCost.value = purchaseCost.toFixed(2);
        totalWeightKg.value = weight.toFixed(2);
        recalcTotals();
    }

    // Calculate Labor Cost using active preparation time and cost per minute
    function recalcLabor() {
        const mins = parseFloat(laborTimeInput.value || 0);
        const laborC = mins * costPerMin;
        laborCostIn.value = laborC.toFixed(2);
        recalcTotals();
    }

    // Recalculate total expense: Purchase Cost + Labor Cost + Packing Cost
    function recalcTotals() {
        const purchaseCost = parseFloat(totalPurchaseCost.value) || 0;
        const laborCost = parseFloat(laborCostIn.value) || 0;
        const packCost = parseFloat(packingCostIn.value) || 0;
        const totalExp = purchaseCost + laborCost + packCost;
        totalExpenseIn.value = totalExp.toFixed(2);
        recalcAdjustedBreakEven();
        recalcMargin();
    }

    // Compute Adjusted Break-Even: sum of labor and packing costs plus driver salary multiplied by labor time.
    function recalcAdjustedBreakEven() {
        const laborCost = parseFloat(laborCostIn.value) || 0;
        const packCost = parseFloat(packingCostIn.value) || 0;
        const driverSalary = parseFloat(driverSalaryInp.value) || 0;
        const laborTime = parseFloat(laborTimeInput.value) || 0;
        const adjustedValue = (laborCost + packCost) + (driverSalary * laborTime);
        adjustedBreakEven.innerText = '$' + adjustedValue.toFixed(2);
        adjustedBreakEvenInput.value = adjustedValue.toFixed(2);
    }

    // Recalculate potential margin based on selling mode
    function recalcMargin() {
        const expTotal = parseFloat(totalExpenseIn.value) || 0;
        let margin, label;
        if (modePiece.checked) {
            const pcs = parseFloat(totalPiecesIn.value) || 1;
            const sellP = parseFloat(pricePerPiece.value) || 0;
            margin = sellP - (expTotal / pcs);
            label = ' / piece';
        } else {
            const wt = parseFloat(totalWeightKg.value) || 1;
            const sellK = parseFloat(pricePerKg.value) || 0;
            margin = sellK - (expTotal * 1000 / wt);
            label = ' / kg';
        }
        document.getElementById('potentialMargin').innerText = '$' + margin.toFixed(2) + label;
    }

    // Attach events for Purchase Details
    purchaseCostPerKg.addEventListener('input', recalcPurchaseCost);
    totalWeightPurchased.addEventListener('input', recalcPurchaseCost);

    // Attach events for Labor input
    laborTimeInput.addEventListener('input', recalcLabor);

    // Attach event for Packing cost change
    packingCostIn.addEventListener('input', recalcTotals);

    // Attach event for Driver Salary change
    driverSalaryInp.addEventListener('input', recalcAdjustedBreakEven);

    // Mode toggle events for Selling Mode
    function updateMode() {
        const pieceInputs = document.getElementById('pieceInputs');
        const kgInputs = document.getElementById('kgInputs');
        if (modePiece.checked) {
            pieceInputs.classList.remove('d-none');
            kgInputs.classList.add('d-none');
        } else {
            pieceInputs.classList.add('d-none');
            kgInputs.classList.remove('d-none');
        }
        recalcMargin();
    }
    modePiece.addEventListener('change', updateMode);
    modeKg.addEventListener('change', updateMode);

    // Attach events for pricing inputs
    pricePerPiece.addEventListener('input', recalcMargin);
    pricePerKg.addEventListener('input', recalcMargin);

    // Initialize calculations
    updateMode();
    recalcPurchaseCost();
    recalcLabor();
});
</script>
@endsection
