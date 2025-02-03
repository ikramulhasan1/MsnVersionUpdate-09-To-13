<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['XSS'])->namespace('Web')->group(function () {

    // Home Route
    Route::get('/', 'HomeController@index')->name('home');

    // Pages Route
    Route::get('/page/{slug}', 'HomeController@page')->name('page.single');

    // About Route
    Route::get('/about', 'AboutController@index')->name('about');

    // Article Routes
    Route::get('/blogs', 'ArticleController@index')->name('blogs');
    Route::get('/blogs/{slug}', 'ArticleController@category')->name('blog.category');
    Route::get('/blog-search', 'ArticleController@search')->name('blog.search');
    Route::get('/blog/{slug}', 'ArticleController@show')->name('blog.single');

    // Service Routes
    Route::get('/services', 'ServiceController@index')->name('services');
    Route::get('/service/{slug}', 'ServiceController@show')->name('service.single');

    // Portfolio Routes
    Route::get('/portfolios', 'PortfolioController@index')->name('portfolios');
    Route::get('/portfolio/{slug}', 'PortfolioController@show')->name('portfolio.single');

    // Pricing Route
    Route::get('/pricing', 'PricingController@index')->name('pricing');

    // Faq Routes
    Route::get('/faqs', 'FaqsController@index')->name('faqs');
    Route::get('/faqs/{slug}', 'FaqsController@category')->name('faqs.category');

    // Contact Routes
    Route::get('/contact', 'ContactController@index')->name('contact');
    Route::post('/contact', 'ContactController@sendMail')->name('contact.send');

    // Get Quote
    Route::get('/get-quote', 'GetQuoteController@index')->name('get-quote');
    Route::post('/get-quote', 'GetQuoteController@store')->name('get-quote.store');

    // Subscribe Route
    Route::post('/subscribe', 'HomeController@subscribe')->name('subscribe');

    // Payment Routes
    Route::get('/handle-payment/{id}', 'PayPalPaymentController@handlePayment')->name('make.payment');
    Route::get('/cancel-payment/{id}', 'PayPalPaymentController@paymentCancel')->name('cancel.payment');
    Route::get('/payment-success/{id}', 'PayPalPaymentController@paymentSuccess')->name('success.payment');
    Route::get('/payment-feedback', 'PayPalPaymentController@paymentFeedback')->name('payment.feedback');
});


// Auth Routes
//Auth::routes();
Auth::routes(['register' => false]);

// Admin Routes
Route::middleware(['auth:web', 'XSS'])->name('admin.')->namespace('Admin')->prefix('admin')->group(function () {

    // Dashboard Route
    Route::get('/', 'DashboardController@index')->name('dashboard.index');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard.index');

    // Get Quote Routes
    Route::resource('get-quote', 'GetQuoteController');
    Route::post('quote-action/{id}/{action}', 'GetQuoteController@action')->name('get-quote.action');
    Route::get('quote-invoice/{id}/{action}', 'GetQuoteController@invoice')->name('get-quote.invoice');
    Route::post('quote-invoice', 'GetQuoteController@invoiceStore')->name('get-quote.invoice.store');

    // Invoice Routes
    Route::resource('invoice', 'InvoiceController');

    // Article Routes
    Route::resource('article-category', 'ArticleCategoryController');
    Route::resource('article', 'ArticleController');

    // Portfolio Routes
    Route::resource('portfolio-category', 'PortfolioCategoryController');
    Route::resource('portfolio', 'PortfolioController');

    // Service Routes
    Route::resource('service', 'ServiceController');

    // Pricing Routes
    Route::resource('pricing', 'PricingController');

    // Member Routes
    Route::resource('designation', 'DesignationController');
    Route::resource('member', 'MemberController');

    // FAQ Routes
	Route::resource('faq-category', 'FaqCategoryController');
	Route::resource('faq', 'FaqController');
    
    // Slider Routes
    Route::resource('slider', 'SliderController');
    
    // Client Routes
    Route::resource('client', 'ClientController');

    // Testimonial Routes
    Route::resource('testimonial', 'TestimonialController');
    
    // Work Process Routes
    Route::resource('work-process', 'WorkProcessController');

    // Why Us Routes
    Route::resource('why-choose-us', 'WhyChooseUsController');

    // Counter Routes
    Route::resource('counter', 'CounterController');

    // Contact Routes
    Route::resource('contact', 'ContactController');

    // Subscriber Routes
    Route::resource('subscriber', 'SubscriberController');

    // About Routes
    Route::resource('about', 'AboutController');

    // Page Routes
    Route::resource('page', 'PageController');

    // Page Setup Routes
    Route::resource('page-setup', 'PageSetupController');
    
    // Section Routes
    Route::resource('section', 'SectionController');

    // Email Template Routes
    Route::resource('template', 'EmailTemplateController');

    // LiveChat Routes
    Route::resource('livechat', 'LiveChatController');

    // Language Routes
    Route::resource('language', 'LanguageController');
    Route::get('language-default/{id}', 'LanguageController@default')->name('language.default');

    // Setting Routes
    Route::get('setting', 'SettingController@index')->name('setting.index');
    Route::post('siteinfo', 'SettingController@siteInfo')->name('setting.siteinfo');
    Route::post('contactinfo', 'SettingController@contactInfo')->name('setting.contactinfo');
    Route::post('changemail', 'SettingController@changeMail')->name('setting.changemail');
    Route::post('changepass', 'SettingController@changePass')->name('setting.changepass');
    Route::post('socialinfo', 'SettingController@socialInfo')->name('setting.socialinfo');
    Route::post('customcode', 'SettingController@customCode')->name('setting.customcode');
});
