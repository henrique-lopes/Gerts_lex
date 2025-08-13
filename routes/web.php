<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantDashboardController;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\Auth\TenantAuthController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/", function () {
    return view("welcome");
});

// Authentication Routes (Custom Tenant Auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [TenantAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [TenantAuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [TenantAuthController::class, 'logout'])->name('logout');
    Route::get('/session-info', [TenantAuthController::class, 'sessionInfo'])->name('session.info');
});

// Payment Dashboard (for blocked tenants)
Route::middleware('guest')->group(function () {
    Route::get('/payment', [PaymentController::class, 'dashboard'])->name('payment.dashboard');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
});

// Admin routes (landlord) - Super Admin only
Route::prefix("admin")->name("admin.")->middleware(['auth', 'tenant.access'])->group(function () {
    Route::resource("tenants", TenantController::class);
    Route::post("tenants/{tenant}/suspend", [TenantController::class, "suspend"])->name("tenants.suspend");
    Route::post("tenants/{tenant}/activate", [TenantController::class, "activate"])->name("tenants.activate");
    Route::post("tenants/{tenant}/update-payment", [TenantAuthController::class, "updatePaymentStatus"])->name("tenants.update-payment");
});

// Tenant routes - Protected by tenant access middleware
Route::middleware(['auth', 'tenant.access'])->group(function () {
    // Main dashboard
    Route::get("/dashboard", [TenantDashboardController::class, "index"])->name("dashboard");
    Route::get("/tenant/dashboard", [TenantDashboardController::class, "index"])->name("tenant.dashboard");
    
    // Lawyer Dashboard
    Route::get("/lawyer-dashboard", [App\Http\Controllers\LawyerDashboardController::class, "index"])->name("lawyer.dashboard");

    // Tenant specific routes
    Route::prefix("tenant")->name("tenant.")->group(function () {
        Route::get("/settings", [TenantDashboardController::class, "settings"])->name("settings");
        Route::post("/settings", [TenantDashboardController::class, "updateSettings"])->name("settings.update");
        Route::get("/subscription", [TenantDashboardController::class, "subscription"])->name("subscription");
        
        // User management for tenant
        Route::resource("users", TenantUserController::class);

        // Lawyer management for tenant
        Route::resource("lawyers", LawyerController::class);

        // Client management for tenant
        Route::resource("clients", ClientController::class);

        // Case management for tenant
        Route::resource("cases", CaseController::class);

        // Fee management for tenant
        Route::resource("fees", FeeController::class);

        // Appointment management for tenant
        Route::resource("appointments", AppointmentController::class);

        // Deadline management for tenant
        Route::resource("deadlines", DeadlineController::class);

        // Case Document management for tenant
        Route::resource("case-documents", CaseDocumentController::class);

        // Invitation system
        Route::get("/invitations/create", [TenantInvitationController::class, "create"])->name("invitations.create");
        Route::post("/invitations", [TenantInvitationController::class, "store"])->name("invitations.store");
    });
});

// Premium features - Requires premium plan
Route::middleware(['auth', 'tenant.access', 'tenant.premium'])->group(function () {
    Route::prefix("premium")->name("premium.")->group(function () {
        Route::get("/analytics", [AnalyticsController::class, "index"])->name("analytics");
        Route::get("/reports", [ReportsController::class, "index"])->name("reports");
        Route::get("/integrations", [IntegrationsController::class, "index"])->name("integrations");
    });
});

// Public route for invited user registration
Route::get("/register/invited", [TenantInvitationController::class, "showRegistrationForm"])->name("tenant.invitations.register");

// Profile routes
Route::middleware(['auth', 'tenant.access'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Stripe Webhook
Route::post("stripe/webhook", "\Laravel\Cashier\Http\Controllers\WebhookController@handle");

// API Routes for AJAX calls
Route::middleware(['auth', 'tenant.access'])->prefix('api')->name('api.')->group(function () {
    Route::get('/tenant/info', [TenantAuthController::class, 'getCurrentTenant'])->name('tenant.info');
    Route::get('/session/info', [TenantAuthController::class, 'sessionInfo'])->name('session.info');
    Route::post('/payment/update-status', [TenantAuthController::class, 'updatePaymentStatus'])->name('payment.update-status');
});

// Remove default auth routes since we're using custom authentication
// require __DIR__.'/auth.php';


