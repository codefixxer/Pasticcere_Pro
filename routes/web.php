<?php

     use Illuminate\Support\Facades\Route;
     use App\Http\Controllers\AuthController;
     use App\Http\Controllers\DashboardController;
     use App\Http\Controllers\UserController;
     use App\Http\Controllers\RoleController;
     use App\Http\Controllers\PermissionController;
     use App\Http\Controllers\IngredientController;
     use App\Http\Controllers\RecordFilterController;
     use App\Http\Controllers\RecipeController;
     use App\Http\Controllers\RecipeCategoryController;
     use App\Http\Controllers\ExternalSuppliesController;
     use App\Http\Controllers\ReturnedGoodController;
     use App\Http\Controllers\ShowcaseController;
     use App\Http\Controllers\LaborCostController;
     use App\Http\Controllers\ClientController;
     use App\Http\Controllers\CostCategoryController;
     use App\Http\Controllers\CostController;
     use App\Http\Controllers\DepartmentController;
     use App\Http\Controllers\NewsController;
     use App\Http\Controllers\NotificationController;
     use App\Http\Controllers\IncomeController;
     use App\Http\Controllers\PastryChefController;
     use App\Http\Controllers\EquipmentController;
     use App\Http\Controllers\ProductionController;
     use App\Http\Controllers\RolesController;
     use App\Http\Controllers\IncomeCategoryController;





     // Test custom 403 page
     Route::get('/__error-403', function () {
     abort(403);
     });

     // Test custom 419 page
     Route::get('/__error-419', function () {
     abort(419);
     });



     // Password Reset
     Route::get('forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
     Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
     Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
     Route::post('reset-password', [AuthController::class, 'reset'])->name('password.update');

     // Public landing & Authentication
     Route::get('/',    [AuthController::class, 'showLoginForm'])->name('login');
     Route::get('login',    [AuthController::class, 'showLoginForm'])->name('login');
     Route::post('login',   [AuthController::class, 'login'])->name('login.submit');
     Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
     Route::post('register',[AuthController::class, 'register'])->name('register.submit');
     Route::post('logout',  [AuthController::class, 'logout'])->name('logout');

     // All routes below require authentication
     Route::middleware('auth')->group(function() {


          Route::get('/api/labor-costs/rates', [App\Http\Controllers\LaborCostController::class, 'rates'])->name('labor-cost.rates');



          
     Route::middleware('can:income categories')->group(function() {

     Route::resource('income-categories', IncomeCategoryController::class)
          ->only(['index','store','update','destroy','edit','create','show']) // keep full set if you prefer
          ->middleware('can:income categories');

     });





     Route::get('profile', [UserController::class, 'profile'])->name('profile');
     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

     // User Management
     Route::middleware('can:manage-users')->group(function() {
          Route::resource('users', UserController::class);
          Route::resource('permissions', PermissionController::class);
     });

     // Ingredients
     Route::resource('ingredients', IngredientController::class)
          ->middleware('can:ingredients');

     // Sale Comparison
     Route::get('comparison', [RecordFilterController::class, 'index'])
          ->name('comparison.index')
          ->middleware('can:sale comparison');
     Route::post('records/add-income', [RecordFilterController::class, 'addFiltered'])
          ->name('income.addFiltered')
          ->middleware('can:sale comparison');

     // Recipes
     Route::resource('recipes', RecipeController::class)
          ->middleware('can:recipe');
     // Duplicate Recipe (Point 9)
     Route::post('recipes/{recipe}/duplicate', [RecipeController::class, 'duplicate'])
          ->name('recipes.duplicate')
          ->middleware('can:recipe');
          Route::get('/departments/{department}/labor-rates', [RecipeController::class, 'departmentRates'])
     ->name('departments.rates')
     ->middleware('auth');

     // Recipe Categories
     Route::resource('recipe-categories', RecipeCategoryController::class)
          ->middleware('can:recipe categories');

     // External Supplies & Templates
     Route::resource('external-supplies', ExternalSuppliesController::class)
          ->middleware('can:external supplies');
     Route::get('external-supplies/template/{id}', [ExternalSuppliesController::class, 'getTemplate'])
          ->name('external-supplies.template')
          ->middleware('can:external supplies');

     // Returned Goods
     Route::resource('returned-goods', ReturnedGoodController::class)
          ->middleware('can:returned goods');

     // Daily Showcase & Templates
     Route::resource('showcase', ShowcaseController::class)
          ->middleware('can:showcase');
     Route::get('showcase/recipe-sales', [ShowcaseController::class, 'recipeSales'])
          ->name('showcase.recipeSales')
          ->middleware('can:showcase');
     Route::get('showcase/{showcase}/manage', [ShowcaseController::class, 'manage'])
          ->name('showcase.manage')
          ->middleware('can:showcase');
     Route::get('showcase/getTemplate/{id}', [ShowcaseController::class, 'getTemplate'])
          ->name('showcase.template');

     // Labor Cost
     Route::resource('labor-cost', LaborCostController::class)
          ->middleware('can:labor cost');
     Route::get('/labor-cost/fetch', [LaborCostController::class, 'ajaxFetch'])
     ->name('labor-cost.fetch')
     ->middleware('can:labor cost');
     // Clients
     Route::resource('clients', ClientController::class)
          ->middleware('can:clients');

     // Cost Categories
     Route::resource('cost_categories', CostCategoryController::class)
          ->middleware('can:cost categories');

     // Costs & Comparison Dashboard
     Route::resource('costs', CostController::class)
          ->middleware('can:costs');
     Route::get('costs-comparison', [CostController::class, 'dashboard'])
          ->name('costs.dashboard')
          ->middleware('can:cost comparison');
          Route::post('/costs/opening-days/save', [CostController::class, 'saveOpeningDays'])
    ->middleware(['auth'])
    ->name('costs.opening-days.save');

     // Departments
     Route::resource('departments', DepartmentController::class)
          ->middleware('can:departments');

     // Notifications
     Route::get('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
          ->name('notifications.markAsRead');
     Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])
          ->name('notifications.markAllAsRead');
     Route::get('/notifications/unread', [NotificationController::class, 'unread']);
     Route::resource('notifications', NotificationController::class)
          ->middleware('can:news');

     // News & Blogs
     Route::resource('news', NewsController::class);
     Route::get('blogs', [NewsController::class, 'blogs'])->name('blogs');

     // Income
     Route::resource('incomes', IncomeController::class)
          ->middleware('can:income');

     // Pastry Chefs
     Route::resource('pastry-chefs', PastryChefController::class)
          ->middleware('can:pastry chefs');

     // Equipment
     Route::resource('equipment', EquipmentController::class)
          ->middleware('can:equipment');

     // Production Entries & Templates
     Route::resource('production', ProductionController::class)
          ->middleware('can:production');
     Route::get('production/template/{id}', [ProductionController::class, 'getTemplate'])
          ->name('production.template')
          ->middleware('can:production');
     });

     // Roles (outside auth middleware)
     Route::resource('roles', RolesController::class);

     // User status toggles
     Route::put('/users/{id}/status', [UserController::class, 'updateStatus'])
          ->name('users.updateStatus');
     Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
          ->name('users.toggleStatus');

          // routes/web.php

     Route::get('profile', [UserController::class, 'profile'])->name('profile');
     Route::put('profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
          
