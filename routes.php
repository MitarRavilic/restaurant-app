<?php
    return [
    
        //Item Routes
        \App\Core\Route::get('|^items/?$|', 'Item', 'home'),
        \App\Core\Route::get('|^items/([0-9]+)/?$|', 'Item', 'itemDetails'),

        // Category Routes
        \App\Core\Route::get('|^categories/?$|', 'Category', 'listAllCategories'),


        // User Routes
        \App\Core\Route::get('|^user/register/?$|', 'User', 'userGetRegister'),
        \App\Core\Route::post('|^user/register/?$|', 'User', 'userPostRegister'),
        \App\Core\Route::get('|^user/login/?$|', 'User', 'userGetLogin'),
        \App\Core\Route::post('|^user/login/?$|', 'User', 'userPostLogin'),
       
        // API CART Routes
        App\Core\Route::get('|^api/cart/?$|', 'ApiCart', 'getCartItems'),
        App\Core\Route::get('|^api/cart/add/([0-9]+)/([A-Z a-z]{3,12})/([0-9]+)/?$|', 'ApiCart', 'addItemToCart'),
        App\Core\Route::get('|^api/cart/clear/?$|', 'ApiCart', 'clearCart'),

        //CART Routes
        App\Core\Route::get('|^cart/?$|', 'Cart', 'showCart'),
        App\Core\Route::post('|^cart/checkout/?$|', 'Cart', 'postCart'),
        App\Core\Route::get('|^cart/checkout/order/?$|', 'Cart', 'redirectCart'),

        // Dispatcher Routes
        App\Core\Route::get('|^dispatcher/dashboard/?$|', 'DispatcherDashboard', 'home'),
        App\Core\Route::post('|^dispatcher/dashboard/processOrder/?$|', 'DispatcherDashboard', 'processOrder'),
       
        // ADMIN Routes
        App\Core\Route::get('|^admin/dashboard/?$|', 'AdministratorDashboard', 'home'),
        App\Core\Route::get('|^admin/dashboard/user/register/?$|', 'AdministratorDashboard', 'administratorGetUserRegistration'),
        App\Core\Route::get('|^admin/dashboard/item/register/?$|', 'AdministratorDashboard', 'administratorGetItemRegistration'),
        App\Core\Route::get('|^admin/dashboard/category/register/?$|', 'AdministratorDashboard', 'administratorGetCategoryRegistration'),
        App\Core\Route::get('|^admin/dashboard/ingredient/register/?$|', 'AdministratorDashboard', 'administratorGetIngredientRegistration'),
        //App\Core\Route::post('|^admin/dashboard/register/?$|', 'AdministratorDashboard', 'administratorPostRegistration'),

        // API Admin ROUTES
        App\Core\Route::get('|^api/users/?$|', 'ApiAdministratorDashboard', 'getAllUsers'),
        App\Core\Route::get('|^api/items/?$|', 'ApiAdministratorDashboard', 'getAllItems'),
        App\Core\Route::get('|^api/ingredients/?$|', 'ApiAdministratorDashboard', 'getAllIngredients'),
        App\Core\Route::get('|^api/categories/?$|', 'ApiAdministratorDashboard', 'getAllCategories'),

        // API Dispatcher ROUTES
        App\Core\Route::get('|^api/orders/?$|', 'ApiDispatcherDashboard', 'displayOrders'),

        // Updates Routes
        // User ROUTES
        App\Core\Route::any('|^admin/dashboard/user/register/update/?$|', 'AdministratorDashboard', 'administratorUpdateUser'),
        App\Core\Route::post('|^admin/dashboard/user/register/create/?$|', 'AdministratorDashboard', 'administratorCreateUser'),
        App\Core\Route::get('|^admin/dashboard/user/register/delete/([0-9]+)/?$|', 'AdministratorDashboard', 'administratorDeleteUser'),
        // Item ROUTES
        App\Core\Route::post('|^admin/dashboard/item/register/update/?$|', 'AdministratorDashboard', 'administratorUpdateItem'),
        App\Core\Route::post('|^admin/dashboard/item/register/create/?$|', 'AdministratorDashboard', 'administratorCreateItem'),
        App\Core\Route::get('|^admin/dashboard/item/register/delete/([0-9]+)/?$|', 'AdministratorDashboard', 'administratorDeleteItem'),
        // Category Routes
        App\Core\Route::post('|^admin/dashboard/category/register/update/?$|', 'AdministratorDashboard', 'administratorUpdateCategory'),
        App\Core\Route::post('|^admin/dashboard/category/register/create/?$|', 'AdministratorDashboard', 'administratorCreateCategory'),
        App\Core\Route::get('|^admin/dashboard/category/register/delete/([0-9]+)/?$|', 'AdministratorDashboard', 'administratorDeleteCategory'),
        // Ingredient Routes
        App\Core\Route::post('|^admin/dashboard/ingredient/register/update/?$|', 'AdministratorDashboard', 'administratorUpdateIngredient'),
        App\Core\Route::post('|^admin/dashboard/ingredient/register/create/?$|', 'AdministratorDashboard', 'administratorCreateIngredient'),
        App\Core\Route::get('|^admin/dashboard/ingredient/register/delete/([0-9]+)/?$|', 'AdministratorDashboard', 'administratorDeleteIngredient'),
        // Order Routes
        App\Core\Route::post('|^order/processOrder?$|', 'Order', 'createOrder'),

        \App\Core\Route::get('|^.*$|',      'Item', 'home'),
    ];
