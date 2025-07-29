<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientEmailController;
use App\Http\Controllers\ClientPhoneController;
use App\Http\Controllers\ClientCompanyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\ZapsignController;
use App\Http\Controllers\EntryChannelController;
use App\Http\Controllers\AchievementTypeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappController;

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
    if (auth()->check()) {
        return redirect()->route("home");
    }
    return view("welcome");
});

// Authentication Routes (sem registro público)
Auth::routes(["register" => false]);

Route::middleware("auth")->group(function () {
    Route::get("/home", [App\Http\Controllers\HomeController::class, "index"])->name("home");
    
    // Rotas de Clientes
    Route::resource("clients", ClientController::class);
    
    // Rotas para E-mails dos Clientes
    Route::post("/client-emails", [ClientEmailController::class, "store"])->name("client-emails.store");
    Route::get("/client-emails/{clientEmail}", [ClientEmailController::class, "show"])->name("client-emails.show");
    Route::put("/client-emails/{clientEmail}", [ClientEmailController::class, "update"])->name("client-emails.update");
    Route::delete("/client-emails/{clientEmail}", [ClientEmailController::class, "destroy"])->name("client-emails.destroy");
    Route::post("/client-emails/{clientEmail}/set-primary", [ClientEmailController::class, "setPrimary"])->name("client-emails.set-primary");
    Route::post("/client-emails/{clientEmail}/verify", [ClientEmailController::class, "verify"])->name("client-emails.verify");
    
    // Rotas para Telefones dos Clientes
    Route::post("/client-phones", [ClientPhoneController::class, "store"])->name("client-phones.store");
    Route::get("/client-phones/{clientPhone}", [ClientPhoneController::class, "show"])->name("client-phones.show");
    Route::put("/client-phones/{clientPhone}", [ClientPhoneController::class, "update"])->name("client-phones.update");
    Route::delete("/client-phones/{clientPhone}", [ClientPhoneController::class, "destroy"])->name("client-phones.destroy");
    Route::post("/client-phones/{clientPhone}/set-primary", [ClientPhoneController::class, "setPrimary"])->name("client-phones.set-primary");
    
    // Rotas para Empresas dos Clientes
    Route::post("/client-companies", [ClientCompanyController::class, "store"])->name("client-companies.store");
    Route::get("/client-companies/{clientCompany}", [ClientCompanyController::class, "show"])->name("client-companies.show");
    Route::put("/client-companies/{clientCompany}", [ClientCompanyController::class, "update"])->name("client-companies.update");
    Route::delete("/client-companies/{clientCompany}", [ClientCompanyController::class, "destroy"])->name("client-companies.destroy");
    Route::post("/client-companies/{clientCompany}/set-main", [ClientCompanyController::class, "setMain"])->name("client-companies.set-main");
    
    // Rotas de Produtos
    Route::resource("products", ProductController::class);
    
    // Rotas de Inscrições
    Route::resource("inscriptions", InscriptionController::class);
    Route::get("/api/inscriptions/kanban", [InscriptionController::class, "kanbanData"])->name("inscriptions.kanban-data");
    
    // Rotas de Importação
    Route::get("/import", [ImportController::class, "index"])->name("import.index");
    Route::post("/import", [ImportController::class, "import"])->name("import.process");
    Route::get("/import/template", [ImportController::class, "downloadTemplate"])->name("import.template");
    
    // Rotas de Documentos
    Route::prefix("inscriptions/{inscription}/documents")->name("documents.")->group(function () {
        Route::get("/", [DocumentController::class, "index"])->name("index");
        Route::get("/create", [DocumentController::class, "create"])->name("create");
        Route::post("/upload", [DocumentController::class, "upload"])->name("upload");
        Route::get("/{mediaId}/download", [DocumentController::class, "download"])->name("download");
        Route::delete("/{mediaId}", [DocumentController::class, "destroy"])->name("destroy");
    });
    
    // Rotas para registros relacionados às inscrições
    Route::post("/preceptor-records", [App\Http\Controllers\PreceptorRecordController::class, "store"])->name("preceptor-records.store");
    Route::delete("/preceptor-records/{preceptorRecord}", [App\Http\Controllers\PreceptorRecordController::class, "destroy"])->name("preceptor-records.destroy");
    
    Route::post("/payments", [App\Http\Controllers\PaymentController::class, "store"])->name("payments.store");
    Route::delete("/payments/{payment}", [App\Http\Controllers\PaymentController::class, "destroy"])->name("payments.destroy");
    
    Route::post("/sessions", [App\Http\Controllers\SessionController::class, "store"])->name("sessions.store");
    Route::delete("/sessions/{session}", [App\Http\Controllers\SessionController::class, "destroy"])->name("sessions.destroy");
    
    Route::post("/diagnostics", [App\Http\Controllers\DiagnosticController::class, "store"])->name("diagnostics.store");
    Route::delete("/diagnostics/{diagnostic}", [App\Http\Controllers\DiagnosticController::class, "destroy"])->name("diagnostics.destroy");
    
    Route::post("/achievements", [App\Http\Controllers\AchievementController::class, "store"])->name("achievements.store");
    Route::delete("/achievements/{achievement}", [App\Http\Controllers\AchievementController::class, "destroy"])->name("achievements.destroy");
    
    Route::post("/follow-ups", [App\Http\Controllers\FollowUpController::class, "store"])->name("follow-ups.store");
    Route::delete("/follow-ups/{followUp}", [App\Http\Controllers\FollowUpController::class, "destroy"])->name("follow-ups.destroy");
    
    // Rotas para documentos das inscrições
    Route::prefix("inscriptions/{inscription}/documents")->name("documents.")->group(function () {
        Route::get("/", [App\Http\Controllers\InscriptionDocumentController::class, "index"])->name("index");
        Route::post("/", [App\Http\Controllers\InscriptionDocumentController::class, "store"])->name("documents.store");
        Route::put("/{document}", [App\Http\Controllers\InscriptionDocumentController::class, "update"])->name("documents.update");
        Route::delete("/{document}", [App\Http\Controllers\InscriptionDocumentController::class, "destroy"])->name("documents.destroy");
        Route::get("/{document}/download", [App\Http\Controllers\InscriptionDocumentController::class, "download"])->name("documents.download");
        Route::post("/{document}/toggle-verification", [App\Http\Controllers\InscriptionDocumentController::class, "toggleVerification"])->name("documents.toggle-verification");
    });
    
    // Rotas para faturamentos das inscrições
    Route::prefix("inscriptions/{inscription}/faturamentos")->name("faturamentos.")->group(function () {
        Route::get("/", [App\Http\Controllers\FaturamentoController::class, "index"])->name("index");
        Route::post("/", [App\Http\Controllers\FaturamentoController::class, "store"])->name("store");
        Route::get("/{faturamento}", [App\Http\Controllers\FaturamentoController::class, "show"])->name("show");
        Route::put("/{faturamento}", [App\Http\Controllers\FaturamentoController::class, "update"])->name("update");
        Route::delete("/{faturamento}", [App\Http\Controllers\FaturamentoController::class, "destroy"])->name("destroy");
    });
    
    // Rotas para renovações das inscrições
    Route::prefix("inscriptions/{inscription}/renovacoes")->name("renovacoes.")->group(function () {
        Route::get("/", [App\Http\Controllers\RenovacaoController::class, "index"])->name("index");
        Route::post("/", [App\Http\Controllers\RenovacaoController::class, "store"])->name("store");
        Route::get("/{renovacao}", [App\Http\Controllers\RenovacaoController::class, "show"])->name("show");
        Route::put("/{renovacao}", [App\Http\Controllers\RenovacaoController::class, "update"])->name("update");
        Route::delete("/{renovacao}", [App\Http\Controllers\RenovacaoController::class, "destroy"])->name("destroy");
    });
    
    // Rotas de Gestão de Acessos (protegidas por permissão)
    Route::middleware("permission:manage-permissions")->group(function () {
        Route::resource("permissions", PermissionController::class);
    });
    
    Route::middleware("permission:manage-roles")->group(function () {
        Route::resource("roles", RoleController::class);
        Route::post("/roles/{role}/toggle-status", [RoleController::class, "toggleStatus"])->name("roles.toggle-status");
    });
    
    Route::middleware("permission:manage-users")->group(function () {
        Route::resource("users", UserManagementController::class);
        Route::post("/users/{user}/assign-role", [UserManagementController::class, "assignRole"])->name("users.assign-role");
        Route::post("/users/{user}/remove-role", [UserManagementController::class, "removeRole"])->name("users.remove-role");
        
        // Rota de registro apenas para administradores
        Route::get("/register", [App\Http\Controllers\Auth\RegisterController::class, "showRegistrationForm"])->name("register");
        Route::post("/register", [App\Http\Controllers\Auth\RegisterController::class, "register"]);
    });
    
    // Rotas de Integrações
    Route::middleware("permission:manage-integrations")->group(function () {
        Route::get("/integrations", [IntegrationController::class, "index"])->name("integrations.index");
        
        // ZapSign
        Route::get("/integrations/zapsign", [IntegrationController::class, "zapsign"])->name("integrations.zapsign");
        Route::post("/integrations/zapsign/settings", [IntegrationController::class, "updateZapsignSettings"])->name("integrations.zapsign.settings");
        Route::post("/integrations/zapsign/test", [IntegrationController::class, "testZapsignConnection"])->name("integrations.zapsign.test");
        Route::get("/integrations/zapsign/templates", [IntegrationController::class, "getZapsignTemplates"])->name("integrations.zapsign.templates");
        
        // Template Mappings
        Route::get("/integrations/zapsign/template-mappings", [ZapsignController::class, "templateMappings"])->name("integrations.zapsign.template-mappings");
        Route::get("/integrations/zapsign/template-mappings/create", [ZapsignController::class, "createTemplateMapping"])->name("integrations.zapsign.template-mappings.create");
        Route::post("/integrations/zapsign/template-mappings", [ZapsignController::class, "storeTemplateMapping"])->name("integrations.zapsign.template-mappings.store");
        Route::get("/integrations/zapsign/template-mappings/{mapping}/edit", [ZapsignController::class, "editTemplateMapping"])->name("integrations.zapsign.template-mappings.edit");
        Route::put("/integrations/zapsign/template-mappings/{mapping}", [ZapsignController::class, "updateTemplateMapping"])->name("integrations.zapsign.template-mappings.update");
        Route::delete("/integrations/zapsign/template-mappings/{mapping}", [ZapsignController::class, "destroyTemplateMapping"])->name("integrations.zapsign.template-mappings.destroy");
        
        // Document Creation
        Route::post("/integrations/zapsign/create-document/{inscription}", [ZapsignController::class, "createDocumentFromInscription"])->name("integrations.zapsign.create-document");
    });
    
    // Rota para dashboard principal
    Route::get("/dashboard", function () {
        return redirect()->route("clients.index");
    })->name("dashboard");
    
    // Rotas de Tipos de Conquista
    Route::resource("achievement_types", AchievementTypeController::class);
    
    // Rotas de Faixa de Faturamento
    Route::resource("faixa-faturamentos", App\Http\Controllers\FaixaFaturamentoController::class);
    Route::get("/api/faixa-faturamentos", [App\Http\Controllers\FaixaFaturamentoController::class, "api"])->name("faixa-faturamentos.api");
});

// Rota de teste para template fields (temporária)
Route::get("/integrations/zapsign/templates/{templateId}/fields", [ZapsignController::class, "getTemplateFields"])->name("integrations.zapsign.template-fields");

// Webhook público (sem autenticação)

// Rotas do WhatsApp
Route::middleware("auth")->group(function () {
    Route::get("/whatsapp", [WhatsappController::class, "index"])->name("whatsapp.index");
    Route::get("/whatsapp/config", [WhatsappController::class, "config"])->name("whatsapp.config");
    Route::post("/whatsapp/connect", [WhatsappController::class, "connect"])->name("whatsapp.connect");
    Route::post("/whatsapp/disconnect", [WhatsappController::class, "disconnect"])->name("whatsapp.disconnect");
    Route::get("/whatsapp/qr-code", [WhatsappController::class, "getQrCode"])->name("whatsapp.qr-code");
    
    // API Routes
    Route::prefix("api/whatsapp")->group(function () {
        Route::get("/conversations", [WhatsappController::class, "conversations"]);
        Route::get("/conversations/{conversation}/messages", [WhatsappController::class, "messages"]);
        Route::post("/messages", [WhatsappController::class, "sendMessage"]);
        Route::post("/conversations/{conversation}/read", [WhatsappController::class, "markAsRead"]);
    });
});


    // Rotas para associação de conversas
    Route::prefix("api/whatsapp")->group(function () {
        Route::get("/conversations/{conversation}/matches", [WhatsappController::class, "possibleMatches"]);
        Route::post("/conversations/{conversation}/associate", [WhatsappController::class, "associate"]);
        Route::post("/conversations/{conversation}/unlink", [WhatsappController::class, "unlink"]);
    });
    
    // Rota para criar cliente a partir do WhatsApp
    Route::get("/whatsapp/create-client", [WhatsappController::class, "createClient"])->name("whatsapp.create-client");


