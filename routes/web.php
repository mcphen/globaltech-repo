<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfilFrontController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ProductOrderController;
use App\Http\Controllers\LeadFormationController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\VisitorTrackerController;


Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/about', [HomeController::class,'about'])->name('about');
Route::get('/services', [HomeController::class,'services'])->name('services');
Route::get('/products', [HomeController::class,'products'])->name('products');
Route::get('/products/{id}', [HomeController::class,'productShow'])->name('product.show');
Route::get('/portfolio', [HomeController::class,'portfolio'])->name('portfolio');
Route::get('/team-members/listes', [TeamMemberController::class,'getListeDatas'])->name('api.team-members.listes');
Route::get('/partners/listes', [PartnerController::class,'getListeDatas'])->name('api.team-members.listes');
Route::get('/testimonials/listes', [TestimonialController::class,'getListeDatas'])->name('api.team-members.listes');
Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
Route::get('/{id}/blog', [HomeController::class, 'blogShow'])->name('blog.show');

// Public formations routes
Route::get('/api/formations/featured', [FormationController::class, 'apiFeatured'])->name('api.formations.featured');
Route::get('/api/formation-categories', [FormationController::class, 'apiCategories'])->name('api.formation-categories');
Route::get('/formations', [FormationController::class, 'frontIndex'])->name('formations');
Route::get('/formations/{slug}', [FormationController::class, 'show'])->name('formations.show');
Route::get('/formations/{formation}/participation-status', [FormationController::class, 'participationStatus'])->name('formations.participation-status');
Route::post('/formations/{formation}/participate', [FormationController::class, 'participate'])->name('formations.participate');

// Contact routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Public route subscribe
Route::post('/subscribe', [SubscribeController::class, 'store'])->name('subscribe.store');

// Appointment booking routes (public: guests can book and will be connected at the end)
Route::get('/appointment', [AppointmentController::class, 'create'])->name('appointment.create');
Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
Route::get('/appointment/{appointment}/confirmation', [AppointmentController::class, 'confirmation'])->name('appointment.confirmation');

// Cart routes (session-based)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
// Checkout flow: GET page requires auth; POST submit also requires auth
Route::middleware(['auth'])->group(function () {
    Route::get('/cart/checkout', [CartController::class, 'checkoutPage'])->name('cart.checkout');
    Route::post('/cart/checkout/submit', [CartController::class, 'checkout'])->name('cart.checkout.submit');
    Route::get('/cart/confirmation', [CartController::class, 'confirmation'])->name('cart.confirmation');
});

// Front Profile pages (requires authentication)
Route::middleware(['auth'])->group(function () {
    // Route::get('/profile', function () {
    //     return Inertia::render('Front/Profile');
    // })->name('front.profile');

    // Route::get('/prospect/profile/edit',[HomeController::class,'prospectProfileEdit'])->name('front.profile.edit');


    // Routes pour le profil


    Route::get('/profile', [ProfilFrontController::class, 'index'])->name('front.profile');
    Route::get('/prospect/profile/edit', [ProfilFrontController::class, 'edit'])->name('front.profile.edit');

    // Routes PATCH pour les mises à jour (front)
    Route::patch('/profile/update', [ProfilFrontController::class, 'updateProfile'])->name('front.profile.update');
    Route::patch('/profile/password', [ProfilFrontController::class, 'updatePassword'])->name('front.profile.password.update');
});

//Prospect dashboard (lead role) - no email verification required
Route::get('/prospect/dashboard', [HomeController::class,'homePropect']
)->middleware(['auth'])->name('prospect.dashboard');

// Prospect orders APIs (requires auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/prospect/training-orders', [LeadFormationController::class, 'index'])
        ->name('prospect.training-orders');
    Route::get('/prospect/product-orders', [ProductOrderController::class, 'index'])
        ->name('prospect.product-orders');

});

// Prospect profile page
Route::middleware(['auth'])->group(function () {
    Route::get('/prospect/profile', [ProfilFrontController::class,'index'])->name('prospect.profile');

});




Route::get('dashboard', [VisitorTrackerController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {


    Route::prefix('admin')->group(function () {


        Route::resource('services', ServiceController::class)
            ->names([
                'index' => 'admin.services.index',
                'create' => 'admin.services.create',
                'store' => 'admin.services.store',
                'show' => 'admin.services.show',
                'edit' => 'admin.services.edit',
                'update' => 'admin.services.update',
                'destroy' => 'admin.services.destroy',
            ]);
        Route::resource('products', ProductController::class)
            ->names([
                'index' => 'admin.products.index',
                'create' => 'admin.products.create',
                'store' => 'admin.products.store',
                'show' => 'admin.products.show',
                'edit' => 'admin.products.edit',
                'update' => 'admin.products.update',
                'destroy' => 'admin.products.destroy',
            ]);
        Route::resource('team-members', TeamMemberController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.team-members.index',
                'create' => 'admin.team-members.create',
                'store' => 'admin.team-members.store',
                'edit' => 'admin.team-members.edit',
                'update' => 'admin.team-members.update',
                'destroy' => 'admin.team-members.destroy',
            ]);

        Route::resource('partners', PartnerController::class)
            ->only(['index','store','update','destroy'])->names([
                'index' => 'admin.partners.index',

                'store' => 'admin.partners.store',

                'update' => 'admin.partners.update',
                'destroy' => 'admin.partners.destroy',
            ]);

        Route::resource('testimonials', TestimonialController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.testimonials.index',
                'create' => 'admin.testimonials.create',
                'store' => 'admin.testimonials.store',
                'edit' => 'admin.testimonials.edit',
                'update' => 'admin.testimonials.update',
                'destroy' => 'admin.testimonials.destroy',
            ]);

        Route::resource('actualites', ActualiteController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.actualites.index',
                'create' => 'admin.actualites.create',
                'store' => 'admin.actualites.store',
                'edit' => 'admin.actualites.edit',
                'update' => 'admin.actualites.update',
                'destroy' => 'admin.actualites.destroy',
            ]);

        // Formation categories management
        Route::resource('formation-categories', \App\Http\Controllers\Admin\FormationCategoryController::class)
            ->names([
                'index'   => 'admin.formation-categories.index',
                'create'  => 'admin.formation-categories.create',
                'store'   => 'admin.formation-categories.store',
                'edit'    => 'admin.formation-categories.edit',
                'update'  => 'admin.formation-categories.update',
                'destroy' => 'admin.formation-categories.destroy',
            ]);

        // Formations management routes
        Route::resource('formations', FormationController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.formations.index',
                'create' => 'admin.formations.create',
                'store' => 'admin.formations.store',
                'edit' => 'admin.formations.edit',
                'update' => 'admin.formations.update',
                'destroy' => 'admin.formations.destroy',
            ]);
        // Admin formation detail (show)
        Route::get('formations/{formation}', [FormationController::class, 'adminShow'])->name('admin.formations.show');
        Route::put('formations/{formation}/leads/{lead}/update-status', [FormationController::class, 'updateLeadStatus'])->name('admin.formations.leads.update-status');

        Route::prefix('albums')->group(function () {
            Route::get('/', [AlbumController::class, 'index'])->name('admin.albums.index');
            Route::get('/create', [AlbumController::class, 'create'])->name('admin.albums.create');
            Route::post('/', [AlbumController::class, 'store'])->name('admin.albums.store');
            Route::get('/{album}/edit', [AlbumController::class, 'edit'])->name('admin.albums.edit');
            Route::post('/{album}', [AlbumController::class, 'update'])->name('admin.albums.update'); // En POST au lieu de PUT/PATCH
            Route::delete('/{album}', [AlbumController::class, 'destroy'])->name('admin.albums.destroy');
        });

        Route::get('albums/{album}',[AlbumController::class,'show'])->name('admin.albums.show');

        // Banner management routes
        Route::get('banner', [BannerController::class, 'index'])->name('admin.banner.index');
        Route::post('banner/update', [BannerController::class, 'update'])->name('admin.banner.update');

        // Orders management routes
        Route::get('orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('orders/list', [OrderController::class, 'list'])->name('admin.orders.list');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');

        // Nouvelles routes pour les factures
        Route::post('/orders/{order}/generate-invoice', [OrderController::class, 'generateInvoice'])
            ->name('admin.orders.generate-invoice');
        Route::get('/orders/{order}/download-invoice', [OrderController::class, 'downloadInvoice'])
            ->name('admin.orders.download-invoice');

        // Nouvelles routes pour les appels
        Route::post('/orders/{order}/appels', [OrderController::class, 'storeAppel'])
            ->name('admin.orders.appels.store');
        Route::get('/orders/{order}/appels', [OrderController::class, 'listAppels'])
            ->name('admin.orders.appels.index');
        Route::delete('/orders/{order}/appels/{appel}', [OrderController::class, 'deleteAppel'])
            ->name('admin.orders.appels.destroy');

        // Settings management routes
        Route::resource('settings', SettingController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.settings.index',
                'create' => 'admin.settings.create',
                'store' => 'admin.settings.store',
                'edit' => 'admin.settings.edit',
                'update' => 'admin.settings.update',
                'destroy' => 'admin.settings.destroy',
            ]);

        // Contact settings
        Route::get('contact-settings', [SettingController::class, 'contactSettings'])->name('admin.contact-settings');
        Route::post('contact-settings', [SettingController::class, 'updateContactSettings'])->name('admin.contact-settings.update');

        // Mail (SMTP) settings
        Route::get('mail-settings', [SettingController::class, 'mailSettings'])->name('admin.mail-settings');
        Route::post('mail-settings', [SettingController::class, 'updateMailSettings'])->name('admin.mail-settings.update');

        // Schedules management routes
        Route::resource('schedules', ScheduleController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names([
                'index' => 'admin.schedules.index',
                'store' => 'admin.schedules.store',
                'update' => 'admin.schedules.update',
                'destroy' => 'admin.schedules.destroy',
            ]);
        Route::post('/schedules/{schedule}/duplicate', [ScheduleController::class, 'duplicate'])->name('admin.schedules.duplicate');
        Route::get('/schedules/listes', [ScheduleController::class, 'getListeDatas'])->name('api.schedules.listes');

        // Appointments management routes
        Route::resource('appointments', AppointmentController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->names([
                'index' => 'admin.appointments.index',
                'show' => 'admin.appointments.show',
                'update' => 'admin.appointments.update',
                'updateStatus' => 'admin.appointments.updateStatus',
                'destroy' => 'admin.appointments.destroy',
            ]);
        Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('admin.appointments.confirm');
        // Routes pour les appels
        Route::post('/appointments/{appointment}/appels', [AppointmentController::class, 'storeAppel'])
            ->name('admin.appointments.appels.store');
        Route::get('/appointments/{appointment}/appels', [AppointmentController::class, 'listAppels'])
            ->name('admin.appointments.appels.index');
        Route::delete('/appointments/{appointment}/appels/{appel}', [AppointmentController::class, 'deleteAppel'])
            ->name('admin.appointments.appels.destroy');


        // Contacts management routes
        Route::get('contacts', [ContactController::class, 'adminIndex'])->name('admin.contacts.index');
        Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('admin.contacts.show');
         Route::patch('/contacts/{contact}/status', [ContactController::class, 'updateStatus'])->name('admin.contacts.update-status');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('admin.contacts.destroy');

        // Admin routes for subscribes
        Route::get('/admin/subscribes', [SubscribeController::class, 'index'])->name('admin.subscribes.index');
        Route::delete('/admin/subscribes/{subscribe}', [SubscribeController::class, 'destroy'])->name('admin.subscribes.destroy');


        // Leads management routes
        Route::resource('leads', LeadController::class)
            ->except(['create', 'store'])
            ->names([
                'index' => 'admin.leads.index',
                'show' => 'admin.leads.show',
                'edit' => 'admin.leads.edit',
                'update' => 'admin.leads.update',
                'destroy' => 'admin.leads.destroy',
            ]);

        // Lead formations management
        Route::put('leads/{lead}/formations/{formation}', [LeadController::class, 'updateFormationStatus'])->name('admin.leads.formations.update');

        // Lead orders management
        Route::put('leads/{lead}/orders/{order}', [LeadController::class, 'updateOrderStatus'])->name('admin.leads.orders.update');

        // Lead appointments management
        Route::put('leads/{lead}/appointments/{appointment}', [LeadController::class, 'updateAppointmentStatus'])->name('admin.leads.appointments.update');

        Route::post('/leads/{lead}/appels', [LeadController::class, 'storeAppel'])
            ->name('admin.leads.appels.store');
        Route::get('/leads/{lead}/appels', [LeadController::class, 'listAppels'])
            ->name('admin.leads.appels.index');
        Route::delete('/leads/{lead}/appels/{appel}', [LeadController::class, 'deleteAppel'])
            ->name('admin.leads.appels.destroy');


            //Route About
        Route::get('/admin/about', [AboutController::class, 'edit'])->name('admin.about.edit');
        Route::get('/admin/about/edit', [AboutController::class, 'edit'])->name('admin.about.edit');
        Route::post('/admin/about', [AboutController::class, 'update'])->name('admin.about.update');
        Route::get('/admin/about/index', [AboutController::class, 'indexAdminAbout'])->name('admin.about.index');




        // Users management routes
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.users.index',
                'create' => 'admin.users.create',
                'store' => 'admin.users.store',
                'edit' => 'admin.users.edit',
                'update' => 'admin.users.update',
                'destroy' => 'admin.users.destroy',
            ]);
    });

    // API routes for authenticated users
    Route::get('/api/banner-photos', [BannerController::class, 'getBannerPhotos'])->name('api.banner-photos');
});

// Public API routes
Route::get('/api/albums/latest', [AlbumController::class, 'latest'])->name('api.albums.latest');
Route::get('/api/services', [ServiceController::class, 'getServices'])->name('api.services');
Route::get('/api/services/all-with-items', [ServiceController::class, 'getAllServicesWithItems'])->name('api.services.all-with-items');
Route::get('/api/products', [ProductController::class, 'getProducts'])->name('api.products');
Route::get('/api/schedules/available', [App\Http\Controllers\Admin\ScheduleController::class, 'getAvailableSchedules'])->name('api.schedules.available');
Route::get('/api/actualites/latest', [ActualiteController::class, 'latest'])->name('api.actualites.latest');

// Visitor tracking route
Route::post('/api/track-action', [App\Http\Controllers\VisitorTrackerController::class, 'trackAction'])->name('api.track-action');
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
