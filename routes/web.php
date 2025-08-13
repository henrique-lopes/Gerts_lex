<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\TenantDashboardController;

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

// Admin routes (landlord)
Route::prefix("admin")->name("admin.")->group(function () {
    Route::resource("tenants", TenantController::class);
    Route::post("tenants/{tenant}/suspend", [TenantController::class, "suspend"])->name("tenants.suspend");
    Route::post("tenants/{tenant}/activate", [TenantController::class, "activate"])->name("tenants.activate");
});

// Tenant routes
Route::middleware(["auth", "verified"])->group(function () {
    // Override default dashboard with tenant dashboard
    Route::get("/dashboard", [TenantDashboardController::class, "index"])->name("dashboard");

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

// Public route for invited user registration
Route::get("/register/invited", [TenantInvitationController::class, "showRegistrationForm"])->name("tenant.invitations.register");

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';



// Stripe Webhook
Route::post("stripe/webhook", "\Laravel\Cashier\Http\Controllers\WebhookController@handle");
