<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DecesController;
use App\Http\Controllers\DecesHopController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\Doctors\DoctorDashboard;
use App\Http\Controllers\Doctors\SousDoctorsDashboard;
use App\Http\Controllers\Vendor\VendorDashboard;
use App\Http\Controllers\MariageController;
use App\Http\Controllers\NaissanceController;
use App\Http\Controllers\NaissanceDeclaController;
use App\Http\Controllers\NaissHopController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SousAdminController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use App\Models\Naissance;
use App\Models\Vendor;
use App\Models\Alert;

Route::get('/login', function () {
    return view('auth.login');
});
Route::get('/doctor/dashboard', function () {
    return view('doctor.dashboard');
});
Route::get('/admin/login', function () {
    return view('admin.auth.login');
});



// Route pour le tableau de bord (affichage général)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::get('/dashboard/{id}', [DashboardController::class, 'show'])->name('user.dashboard');


// Routes liées au profil de l'utilisateur
Route::middleware('auth:web')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes pour la gestion des mariages et décès


// Routes administratives
    Route::prefix('admin')->middleware('auth:admin')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
    });

    //Les routes du Docteur

    Route::middleware('auth:doctor')->prefix('doctors/dashboard')->group(function(){
        Route::get('/',[DoctorDashboard::class, 'index'])->name('doctor.dashboard'); 
        Route::get('/logout',[DoctorDashboard::class, 'logout'])->name('doctor.logout'); 
    });
    //Authenfication de doctor

    Route::prefix('doctors/')->group(function () {
    Route::get('/register', [DoctorController::class, 'register'])->name('doctor.register');
    Route::post('/register', [DoctorController::class, 'handleRegister'])->name('handleRegister');
    Route::get('/login', [DoctorController::class, 'login'])->name('doctor.login');
    Route::post('/login', [DoctorController::class, 'handleLogin'])->name('handleLogin');
    
    });

    //Les routes de l'administrator (Mairie)
    Route::middleware('auth:vendor')->prefix('vendors/dashboard')->group(function(){
        Route::get('/', [VendorDashboard::class, 'index'])->name('vendor.dashboard');
        Route::get('/logout', [VendorDashboard::class, 'logout'])->name('vendor.logout');
    });
    
    // Routes pour l'authentification
    Route::prefix('vendors/')->group(function () {
        Route::get('/register', [VendorController::class, 'register'])->name('vendor.register');
        Route::post('/register', [VendorController::class, 'handleRegister'])->name('vendor.handleRegister');
        Route::get('/login', [VendorController::class, 'login'])->name('vendor.login');
        Route::post('/login', [VendorController::class, 'handleLogin'])->name('vendor.handleLogin');

        //edit de l'etat de naissance
        Route::get('/naissance/{id}/edit', [VendorController::class, 'edit'])->name('naissances.edit');
        Route::post('/naissance/{id}/update-etat', [VendorController::class, 'updateEtat'])->name('naissances.updateEtat');

        //edit de l'etat de naissance grand
        Route::get('/naissanced/{id}/edit', [VendorController::class, 'edit1'])->name('naissanced.edit');
        Route::post('/naissanced/{id}/update-etat', [VendorController::class, 'updateEtat1'])->name('naissanced.updateEtat');

        //edit de l'etat de décès 
        Route::get('/deces/{id}/edit', [VendorController::class, 'edit2'])->name('deces.edit');
        Route::post('/deces/{id}/update-etat', [VendorController::class, 'updateEtat2'])->name('deces.updateEtat');

        //edit de l'etat de mariage 
        Route::get('/mariage/{id}/edit', [VendorController::class, 'edit3'])->name('mariage.edit');
        Route::post('/mariage/{id}/update-etat', [VendorController::class, 'updateEtat3'])->name('mariage.updateEtat');

    });

    // Routes pour le sous-admin (Sous docteurs)
    Route::prefix('sous-admin')->name('sous_admin.')->group(function () {
        Route::get('/login', [SousAdminController::class, 'souslogin'])->name('login');
        Route::post('/login', [SousAdminController::class, 'soushandleLogin'])->name('handlelogin');
    });
    Route::middleware('auth:sous_admin')->prefix('sous-admin')->name('sous_admin.')->group(function(){
    Route::get('/dashboard', [SousAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout',[SousAdminController::class, 'souslogout'])->name('logout');
    });
    

    Route::middleware('sous_admin')->prefix('SousDoctor')->group(function(){
    
    });
    Route::get('/validate-account/{email}', [SousAdminController::class, 'defineAccess']);
    Route::post('/validate-account/{email}', [SousAdminController::class, 'submitDefineAccess'])->name('doctor.validate');


    //creer un docteurs

    Route::middleware('auth:doctor')->prefix('SousDoctor')->group(function () {
    Route::get('/index',[DoctorController::class, 'index'])->name('doctor.index');
    Route::get('/create',[DoctorController::class, 'create'])->name('doctor.create');
    Route::post('/create',[DoctorController::class, 'store'])->name('doctor.store');
    Route::get('/edit/{sousadmin}',[DoctorController::class, 'edit'])->name('doctor.edit');
    Route::put('/edit/{sousadmin}',[DoctorController::class, 'update'])->name('doctor.update');
    Route::get('/delete/{sousadmin}',[DoctorController::class, 'delete'])->name('doctor.delete');

    });

//les routes de extraits naissances
    Route::prefix('naissances')->group(function() {
        Route::get('/', [NaissanceController::class, 'index'])->name('naissance.index');        
        Route::post('/create', [NaissanceController::class, 'store'])->name('naissance.store');
        Route::get('/create', [NaissanceController::class, 'create'])->name('naissance.create');
        Route::get('/edit/{naissance}', [NaissanceController::class, 'edit'])->name('naissance.edit');
        Route::get('/naissance/{id}', [NaissanceController::class, 'show'])->name('naissance.show');
        
    });

    Route::prefix('naissHop')->group(function () {
        // Routes pour les naissances à l'hôpital
        Route::get('/hopital', [NaissHopController::class, 'index'])->name('naissHop.index');
        Route::get('/hopital/create', [NaissHopController::class, 'create'])->name('naissHop.create');
        Route::post('/hopital/create', [NaissHopController::class, 'store'])->name('naissHop.store');
        Route::get('/hopital/edit/{naisshop}', [NaissHopController::class, 'edit'])->name('naissHop.edit');
        Route::put('/hopital/edit/{naisshop}', [NaissHopController::class, 'update'])->name('naissHop.update');
        Route::get('/hopital/delete/{naisshop}', [NaissHopController::class, 'delete'])->name('naissHop.delete');
        Route::get('/hopital/download/{id}', [NaissHopController::class, 'download'])->name('naissHop.download');
        Route::get('/hopital/{id}', [NaissHopController::class, 'show'])->name('naissHop.show');
        Route::get('/mairie/{id}', [NaissHopController::class, 'mairieshow'])->name('naissHopmairie.show');
    
        // Routes pour la mairie
        Route::get('/mairie', [NaissHopController::class, 'mairieindex'])->name('naissHop.mairieindex');
        Route::get('/mairie-deces', [NaissHopController::class, 'mairieDecesindex'])->name('deces.mairieDecesindex');
    
        // Route spécifique pour vérifier le code DM
        Route::post('/verifier-code-dm', [NaissHopController::class, 'verifierCodeDM'])->name('verifierCodeDM');
    });
    
    Route::prefix('decesHop')->group(function(){
        //Les routes les routes cotés Naissances hopital
        Route::get('/', [DecesHopController::class, 'index'])->name('decesHop.index');        
        Route::post('/create', [DecesHopController::class, 'store'])->name('decesHop.store');
        Route::get('/create', [DecesHopController::class, 'create'])->name('decesHop.create');
        Route::get('/edit/{deceshop}', [DecesHopController::class, 'edit'])->name('decesHop.edit');
        Route::put('/edit/{deceshop}', [DecesHopController::class, 'update'])->name('decesHop.update');
        Route::get('/delete/{deceshop}', [DecesHopController::class, 'delete'])->name('decesHop.delete');
        Route::get('/{id}', [DecesHopController::class, 'show'])->name('decesHop.show');
        Route::get('/mairie/{id}', [DecesHopController::class, 'mairieshow'])->name('mairiedecesHop.show');
        Route::post('/deces/verifierCodeCMD', [DecesHopController::class, 'verifierCodeCMD'])->name('deces.verifierCodeCMD');
        Route::get('/download/{id}', [DecesHopController::class, 'download'])->name('decesHop.download');
    });

    // Les routes pour les statistiques
    Route::prefix('stats')->group(function () {
        Route::get('/', [StatController::class, 'index'])->name('stats.index');
        Route::get('/download', [StatController::class, 'download'])->name('stats.download');
        
    });

    //les routes de declarations deces
    

    //les routes de declarations naissances
    Route::prefix('naissances/declarations')->group(function() {
        Route::get('/', [NaissanceDeclaController::class, 'index'])->name('naissanced.index');        
        Route::post('/create', [NaissanceDeclaController::class, 'store'])->name('naissanced.store');
        Route::get('/create', [NaissanceDeclaController::class, 'create'])->name('naissanced.create');
        Route::get('/naissanced/{id}', [NaissanceDeclaController::class, 'show'])->name('naissanced.show');

    });
    //les routes de deces
    Route::prefix('deces')->group(function() {
        Route::get('/', [DecesController::class, 'index'])->name('deces.index');        
        Route::post('/create', [DecesController::class, 'store'])->name('deces.store');
        Route::get('/create', [DecesController::class, 'create'])->name('deces.create');
        Route::get('/deces/{id}', [DecesController::class, 'show'])->name('deces.show');
    });

    //les routes de mariages
    Route::prefix('mariages')->group(function() {
        Route::get('/', [MariageController::class, 'index'])->name('mariage.index');        
        Route::post('/create', [MariageController::class, 'store'])->name('mariage.store');
        Route::get('/create', [MariageController::class, 'create'])->name('mariage.create');
        Route::get('/mariage/{id}', [MariageController::class, 'show'])->name('mariage.show');
    });

    Route::post('/alerts/{id}/mark-as-read', [VendorDashboard::class, 'markAlertAsRead']);


    require __DIR__.'/auth.php';
    require __DIR__.'/admin-auth.php';
