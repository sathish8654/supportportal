<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketAssignController;
use App\Http\Controllers\TicketCloseController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketProcessController;
use App\Http\Controllers\TicketQcController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadWorkflowController;
use App\Http\Controllers\QuotationWebController;
use App\Http\Controllers\OrderWebController;
use App\Http\Controllers\InstallationWebController;
use App\Http\Controllers\ImeiController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PartsController;
use App\Http\Controllers\PartsUsageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FirmwareController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::view('/login-with-otp', 'auth.loginwithotp')->name('login.with.otp');
Route::post('/loginwithotppost', [OTPController::class, 'loginwithotppost'])->name('login.with.otp.post');

Route::view('/confirm-login-with-otp', 'auth.confirmloginwithotp')->name('confirm.login.with.otp');
Route::post('/confirmloginwithotppost', [OTPController::class, 'confirmloginwithotppost'])->name('confirm.login.with.otp.post');


Route::controller(OTPController::class)->group(function(){
    Route::get('otp/login', 'login')->name('otp.login');
    Route::post('otp/generate', 'generate')->name('otp.generate');
    Route::get('otp/verification/{user_id}', 'verification')->name('otp.verification');
    Route::post('otp/login', 'loginWithOtp')->name('otp.getlogin');
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

 
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'New verification link sent again!, please check your email inbox and spam folder.');
})->middleware(['auth', 'throttle:6,1', 'web'])->name('verification.send');

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');


// Route::group(['middleware' => ['auth', 'web']], function() {

Route::middleware(['auth', 'verified', 'permission'])->group(function () {
   
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
Route::post('/profile', [ProfileController::class, 'store'])->name('user.profile.store');



Route::any('createTicket.getVendorInfo', [TicketController::class, 'getVendorInfo'])->name('createTicket.getVendorInfo');

Route::any('createTicket', [TicketController::class, 'index'])->name('createTicket');
Route::any('createTicket.autocompleteaddress', [TicketController::class, 'autocompleteaddress'])->name('autocompleteaddress');
Route::post('createTicket.subcat', [CategoryController::class, 'subcat'])->name('subcat');
Route::any('vendor.submitCreateTickets', [TicketController::class, 'submitCreateTickets'])->name('submitCreateTickets');
Route::post('/validate-serial', [TicketController::class, 'validateSerial'])->name('validateSerial');

// check and need to delete
// Route::any('logsTickets', [TicketController::class, 'logsTickets'])->name('logsTickets');
// Route::any('logsTickets.getTicketsLogList', [TicketController::class, 'getTicketsLogList'])->name('getTicketsLogList');


// Route::any('logTickets', [TicketController::class, 'logTickets'])->name('logTickets');
// Route::get('logTickets.logTicketsDetails', [TicketController::class, 'logTicketsDetails'])->name('logTicketsDetails');
///////////////////////
Route::any('allLogTickets', [TicketController::class, 'allLogTickets'])->name('allLogTickets');
Route::get('logTicketsDetails', [TicketController::class, 'logTicketsDetails'])->name('logTicketsDetails');

/////////////////////////
Route::any('viewTickets', [TicketAssignController::class, 'viewTickets'])->name('viewTickets');
Route::any('viewTickets.getTicketsListData', [TicketAssignController::class, 'getTicketsListData'])->name('getTicketsListData');
Route::any('viewTickets.assignTicket', [TicketAssignController::class, 'assignTicket'])->name('assignTicket');
Route::any('viewTickets.assignTicketSubmit', [TicketAssignController::class, 'assignTicketSubmit'])->name('assignTicketSubmit');
Route::any('viewTickets.assignTicketDelete', [TicketAssignController::class, 'assignTicketDelete'])->name('assignTicketDelete');

Route::any('processTickets.getProcessTicketsListData', [TicketProcessController::class, 'getProcessTicketsListData'])->name('getProcessTicketsListData');
Route::any('processTickets', [TicketProcessController::class, 'processTickets'])->name('processTickets');
Route::any('processTickets.processAssignTicket', [TicketProcessController::class, 'processAssignTicket'])->name('processAssignTicket');
Route::any('processTickets.processTicketSubmit', [TicketProcessController::class, 'processTicketSubmit'])->name('processTicketSubmit');
Route::any('processTickets.deleteTicketSubmit', [TicketProcessController::class, 'deleteTicketSubmit'])->name('processTickets.deleteTicketSubmit');
Route::any('processTickets.partsSub', [TicketProcessController::class, 'partsSub'])->name('partsSub');


Route::any('qcTickets.getqcTicketsListData', [TicketQcController::class, 'getqcTicketsListData'])->name('getqcTicketsListData');
Route::any('qcTickets', [TicketQcController::class, 'qcTickets'])->name('qcTickets');
Route::any('qcTickets.qcAssignTicket', [TicketQcController::class, 'qcAssignTicket'])->name('qcAssignTicket');
Route::any('qcTickets.qcTicketSubmit', [TicketQcController::class, 'qcTicketSubmit'])->name('qcTicketSubmit');
Route::any('qcTickets.deleteTicketSubmit', [TicketQcController::class, 'deleteTicketSubmit'])->name('qcTickets.deleteTicketSubmit');


Route::any('closedTickets', [TicketCloseController::class, 'closedTickets'])->name('closedTickets');

Route::any('viewTickets.getClosedTicketsListData', [TicketCloseController::class, 'getClosedTicketsListData'])->name('getClosedTicketsListData');

Route::any('deliveredTickets', [TicketCloseController::class, 'deliveredTickets'])->name('deliveredTickets');
// Route::any('viewTickets.getDeliveredTicketsListData', [TicketCloseController::class, 'getDeliveredTicketsListData'])->name('getDeliveredTicketsListData');
Route::any('deliveredTickets.getDeliveredTicketsListData', [TicketCloseController::class, 'getDeliveredTicketsListData'])->name('getDeliveredTicketsListData');
Route::any('deliveredTickets.resetDeliverySubmit', [TicketCloseController::class, 'resetDeliverySubmit'])->name('resetDeliverySubmit');


// https://www.tutsmake.com/laravel-dynamic-dependent-dropdown-using-ajax/
Route::get('category', [CategoryController::class, 'index'])->name('category');
Route::any('category.submitAddCase', [CategoryController::class, 'submitAddCase'])->name('submitAddCase');
Route::any('category.getCategoryData', [CategoryController::class, 'getCategoryData'])->name('getCategoryData');
Route::any('category.getSubCatData', [CategoryController::class, 'getSubCatData'])->name('getSubCatData');
Route::any('category.disableSubCategory', [CategoryController::class, 'disableSubCategory'])->name('disableSubCategory');// disable sub category
Route::any('category.submitAddCat', [CategoryController::class, 'submitAddCat'])->name('submitAddCat');
Route::any('category.getSubCat1', [CategoryController::class, 'getSubCat1'])->name('getSubCat1');
Route::any('category.getSubCat2', [CategoryController::class, 'getSubCat2'])->name('getSubCat2');
Route::any('category.submitAddSubCat1', [CategoryController::class, 'submitAddSubCat1'])->name('submitAddSubCat1');
Route::any('category.submitUpdate', [CategoryController::class, 'submitUpdate'])->name('submitUpdate');
Route::post('/category-status/{id}', [CategoryController::class, 'categoryStatus'])->name('categoryStatus');

//// specification
Route::any('productSpecification', [CategoryController::class, 'productSpecification'])->name('productSpecification');
Route::any('category.getSpecificationData', [CategoryController::class, 'getSpecificationData'])->name('getSpecificationData');
Route::any('category.getSubSpecData', [CategoryController::class, 'getSubSpecData'])->name('getSubSpecData');
Route::any('category.addNewSpec', [CategoryController::class, 'addNewSpec'])->name('addNewSpec');
Route::any('category.addNewSubSpec', [CategoryController::class, 'addNewSubSpec'])->name('addNewSubSpec');
Route::any('category.updateProdSpec', [CategoryController::class, 'updateProdSpec'])->name('updateProdSpec');
Route::any('category.disableProdSpec', [CategoryController::class, 'disableProdSpec'])->name('disableProdSpec');
Route::any('specSub', [ProductController::class, 'specSub'])->name('specSub');

Route::any('vendor', [VendorController::class, 'index'])->name('vendor');
Route::any('vendor.getVendorsListData', [VendorController::class, 'getVendorsListData'])->name('getVendorsListData');
Route::any('vendor.submitAddImeiMany', [VendorController::class, 'submitAddImeiMany'])->name('vendor.submitAddImeiMany');
Route::any('vendor.addNewVendor', [VendorController::class, 'addNewVendor'])->name('addNewVendor');
Route::any('vendor.getVendorAddressInfo', [VendorController::class, 'getVendorAddressInfo'])->name('getVendorAddressInfo');
Route::any('vendor.updateVendorAddress', [VendorController::class, 'updateVendorAddress'])->name('updateVendorAddress');
Route::any('vendor.deleteVendorAddress', [VendorController::class, 'deleteVendorAddress'])->name('deleteVendorAddress');
Route::any('vendor.autocompleteCity', [VendorController::class, 'autocompleteCity'])->name('autocompleteCity');
Route::any('vendor.autocompleteDistrict', [VendorController::class, 'autocompleteDistrict'])->name('autocompleteDistrict');
Route::any('vendor.autocompleteState', [VendorController::class, 'autocompleteState'])->name('autocompleteState');


// Manage parts
Route::any('parts', [PartsController::class, 'index'])->name('parts');
Route::any('parts.getpartsListData', [PartsController::class, 'getpartsListData'])->name('getpartsListData');
Route::any('parts.getpartsSubListData', [PartsController::class, 'getpartsSubListData'])->name('getpartsSubListData');
// Route::any('parts.submitAddImeiMany', [PartsController::class, 'submitAddImeiMany'])->name('submitAddImeiMany');
Route::any('parts.addNewPart', [PartsController::class, 'addNewPart'])->name('addNewPart');
Route::any('parts.addNewSubPart', [PartsController::class, 'addNewSubPart'])->name('addNewSubPart');
Route::any('parts.getPartInfo', [PartsController::class, 'getPartInfo'])->name('getPartInfo');
Route::any('parts.updatePartInfo', [PartsController::class, 'updatePartInfo'])->name('updatePartInfo');
Route::any('parts.updatePartInfoSub', [PartsController::class, 'updatePartInfoSub'])->name('updatePartInfoSub');
Route::any('parts.disablePart', [PartsController::class, 'disablePart'])->name('disablePart');
// Route::any('parts.autocompleteCity', [PartsController::class, 'autocompleteCity'])->name('autocompleteCity');
// Route::any('parts.autocompleteDistrict', [PartsController::class, 'autocompleteDistrict'])->name('autocompleteDistrict');
// Route::any('parts.autocompleteState', [PartsController::class, 'autocompleteState'])->name('autocompleteState');



Route::any('partsUsage', [PartsUsageController::class, 'index'])->name('partsUsage');





#Print Controller
Route::any('printAddress', [PrintController::class, 'printAddress'])->name('printAddress');
Route::any('printAddress.getVendorAddressInfo', [VendorController::class, 'getVendorAddressInfo'])->name('printAddress.getVendorAddressInfo');

Route::any('printAddress.getVendorInfo', [PrintController::class, 'getVendorInfo'])->name('printAddress.getVendorInfo');

Route::any('printTickets', [PrintController::class, 'printTickets'])->name('printTickets');
// Route::any('printTickets.getVendorInfo', [PrintController::class, 'getVendorInfo'])->name('getVendorInfo');
Route::any('printTickets.getTicketsListDataByStatus', [PrintController::class, 'getTicketsListDataByStatus'])->name('getTicketsListDataByStatus');
Route::any('printTickets.getVendorListDataByStatus', [PrintController::class, 'getVendorListDataByStatus'])->name('getVendorListDataByStatus');
Route::any('printTickets.testTickets', [PrintController::class, 'testTickets'])->name('testTickets');
Route::any('printTickets.manageDelivery', [PrintController::class, 'manageDelivery'])->name('manageDelivery');
Route::get('printTickets.printForm', [PrintController::class, 'printForm'])->name('printForm');


Route::any('printAck', [PrintController::class, 'printForm'])->name('printAck');

# manage admin menus
Route::get('/userAdmins', [AdminController::class, 'Adminslist'])->name('userAdmins');
Route::match(['get', 'post'], '/manage-permissions', [AdminController::class, 'manageUserPermissions']);

Route::get('/userlist.getAdminListData', [AdminController::class, 'getAdminListData'])->name('getAdminListData');
Route::get('/userlist.searchAdminUsers', [AdminController::class, 'searchAdminUsers'])->name('searchAdminUsers');
Route::get('/userlist.getAppAdminListData', [AdminController::class, 'getAppAdminListData'])->name('getAppAdminListData');
Route::post('/userlist.getAdminList', [AdminController::class, 'getAdminList'])->name('getAdminList');
Route::post('/userlist.updateAdminList', [AdminController::class, 'updateAdminList'])->name('updateAdminList');
Route::post('/userlist.deleteAdminList', [AdminController::class, 'deleteAdminList'])->name('deleteAdminList');
Route::post('/userlist.removeAppAdminAccess', [AdminController::class, 'removeAppAdminAccess'])->name('removeAppAdminAccess');


Route::any('/userAddImei', [ImeiController::class, 'userAddImei'])->name('userAddImei');
Route::any('/userAddImei.submitAddImei', [ImeiController::class, 'submitAddImei'])->name('submitAddImei');
Route::any('/userAddImei.submitAddImeiMany', [ImeiController::class, 'submitAddImeiMany'])->name('userAddImei.submitAddImeiMany');
Route::post('/userAddImei/upload', [ImeiController::class, 'upload'])->name('userAddImei.upload');
Route::get('/userAddImei/downloadSample', [ImeiController::class, 'downloadSample'])->name('userAddImei.downloadSample');

Route::any('/userAddImei.getImeiList', [ImeiController::class, 'getImeiList'])->name('getImeiList');
Route::any('/userAddImei.getImeiListData', [ImeiController::class, 'getImeiListData'])->name('getImeiListData');
Route::post('/userAddImei.updateImeiList', [ImeiController::class, 'updateImeiList'])->name('updateImeiList');
Route::post('/userAddImei.deleteImeiList', [ImeiController::class, 'deleteImeiList'])->name('deleteImeiList');
///////////////////// device user list
Route::any('/userDevice.addUpdateUserDevice', [ImeiController::class, 'addUpdateUserDevice'])->name('addUpdateUserDevice');

/////////////////
Route::any('appendImeiPrint', [ImeiController::class, 'appendImeiPrint'])->name('appendImeiPrint');/////////////////////

Route::get('/userimei', [HomeController::class, 'userimei'])->name('userimei');
Route::get('/userimei.getUserImeiData', [HomeController::class, 'getUserImeiData'])->name('getUserImeiData');
Route::post('/userimei.getUserImei', [HomeController::class, 'getUserImei'])->name('getUserImei');
Route::post('/userimei.updateUserImei', [HomeController::class, 'updateUserImei'])->name('updateUserImei');
Route::post('/userimei.deleteUserImei', [HomeController::class, 'deleteUserImei'])->name('deleteUserImei');
Route::post('/userDevice.deleteDeviceUser', [HomeController::class, 'deleteDeviceUser'])->name('deleteDeviceUser');


Route::get('/userlist', [HomeController::class, 'userlist'])->name('userlist');
Route::get('/userlist.getUserListData', [HomeController::class, 'getUserListData'])->name('getUserListData');
Route::post('/userlist.getUserList', [HomeController::class, 'getUserList'])->name('getUserList');
Route::post('/userlist.updateUserList', [HomeController::class, 'updateUserList'])->name('updateUserList');
Route::post('/userlist.deleteUserList', [HomeController::class, 'deleteUserList'])->name('deleteUserList');

Route::get('/userDevice', [HomeController::class, 'userDevice'])->name('userDevice');
Route::get('/userDevice.getUserListDevice', [HomeController::class, 'getUserListDevice'])->name('getUserListDevice');
// Manage User Sim card
Route::get('/usersim', [SimController::class, 'usersim'])->name('usersim');
Route::any('/usersim.getUserSimData', [SimController::class, 'getUserSimData'])->name('getUserSimData');
Route::post('/usersim.getUserSimList', [SimController::class, 'getUserSimList'])->name('getUserSimList');
Route::any('/usersim.addNewSim', [SimController::class, 'addNewSim'])->name('addNewSim');
Route::post('/usersim.updateSimInfo', [SimController::class, 'updateSimInfo'])->name('updateSimInfo');
Route::any('/usersim.deleteUserSimList', [SimController::class, 'deleteUserSimList'])->name('deleteUserSimList');
Route::any('/usersim.upload', [SimController::class, 'upload'])->name('upload');


// Manage User Sim card renewals history
Route::any('/usersimPayment', [SimController::class, 'usersimPayment'])->name('usersimPayment');
Route::any('/usersimPayment.getusersimPaymentData', [SimController::class, 'getusersimPaymentData'])->name('getusersimPaymentData');
Route::post('/usersimPayment.getusersimPaymentList', [SimController::class, 'getusersimPaymentList'])->name('getusersimPaymentList');
Route::any('/usersimPayment.addNewPayment', [SimController::class, 'addNewPayment'])->name('addNewPayment');
Route::post('/usersimPayment.updatePaymentInfo', [SimController::class, 'updatePaymentInfo'])->name('updatePaymentInfo');
Route::any('/usersimPayment.deletesimPaymentList', [SimController::class, 'deletesimPaymentList'])->name('deletesimPaymentList');
Route::any('/usersimPayment.getSiminfo', [SimController::class, 'getSiminfo'])->name('getSiminfo');

// table reload
Route::get('/usersimPayment/partial', [SimController::class, 'getPaymentsPartial'])->name('usersimPayment.partial');


// manifactire prodcuts
Route::any('productCreate', [ProductController::class, 'index'])->name('productCreate');
Route::any('productCreate.submitCreateProducts', [ProductController::class, 'createProducts'])->name('submitCreateProducts');
Route::any('productBatch', [ProductController::class, 'batch'])->name('productBatch');



Route::any('productProcess', [ProductController::class, 'process'])->name('productProcess');
Route::any('productProcess.processAssignProduct', [ProductController::class, 'getAssignProduct'])->name('processAssignProduct');
Route::any('productProcess.processProductSubmit', [ProductController::class, 'submitAssignProduct'])->name('processProductSubmit');


Route::any('productQc', [ProductController::class, 'qc'])->name('productQc');
Route::any('productReady', [ProductController::class, 'ready'])->name('productReady');
Route::any('productGenSerial', [ProductController::class, 'productGenSerial'])->name('productGenSerial');
Route::any('updateSerial', [ProductController::class, 'updateSerial'])->name('updateSerial');
Route::any('appendProductModel', [ProductController::class, 'appendProductModel'])->name('appendProductModel');/////////////////////


Route::any('productSold', [ProductController::class, 'sold'])->name('productSold');
Route::get('productSold.logProductHandlerDetails', [ProductController::class, 'logProductHandlerDetails'])->name('logProductHandlerDetails');

Route::any('productDefective', [ProductController::class, 'defective'])->name('productDefective');

Route::any('getProductInfo', [ProductController::class, 'productInfo'])->name('getProductInfo');
Route::any('updateLocation', [ProductController::class, 'updateLocation'])->name('updateLocation');
Route::post('upload-image', [ProductController::class, 'storeImages'])->name('upload-image');

Route::any('search', [SearchController::class, 'index'])->name('search');

Route::prefix('manage-firmware')->group(function () {
    Route::get('/', [FirmwareController::class, 'index'])->name('firmware.index');
    Route::post('/upload', [FirmwareController::class, 'store'])->name('firmware.store');
    Route::post('/deploy/{baseName}', [FirmwareController::class, 'deploy'])->name('firmware.deploy');
    Route::post('/clear-upload', [FirmwareController::class, 'clearUpload'])->name('firmware.clearUpload');
    Route::delete('/delete/{baseName}', [FirmwareController::class, 'destroy'])->name('firmware.destroy');
});

    // Sales Module Routes
    Route::resource('leads', LeadController::class);
    Route::post('/leads/{lead}/request-delete', [LeadWorkflowController::class, 'requestDelete'])->name('lead-workflow.request-delete');
    Route::put('/leads/{lead}/approve-to-accounts', [LeadWorkflowController::class, 'approveToAccounts'])->name('lead-workflow.approve-to-accounts');
    
    Route::get('/quotations', [QuotationWebController::class, 'index'])->name('quotations.index');
    Route::post('/quotations', [QuotationWebController::class, 'store'])->name('quotations.store');
    Route::put('/quotations/{quotation}', [QuotationWebController::class, 'update'])->name('quotations.update');
    Route::delete('/quotations/{quotation}', [QuotationWebController::class, 'destroy'])->name('quotations.destroy');
    Route::put('/quotations/{quotation}/approve', [LeadWorkflowController::class, 'approveQuotation'])->name('quotation-workflow.approve');
    Route::put('/quotations/{quotation}/customer-confirmed', [LeadWorkflowController::class, 'customerConfirmed'])->name('quotation-workflow.customer-confirmed');
    Route::get('/quotations/{quotation}/print', function ($quotation) {
        $quotationModel = \App\Models\Quotation::with(['lead.vendor', 'vendor', 'salesExecutive', 'items.product'])->find($quotation);
        if (!$quotationModel) {
            abort(404, 'Quotation not found');
        }
        return view('quotations.print', ['quotation' => $quotationModel]);
    })->name('quotations.print');
    
    Route::get('/orders', [OrderWebController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderWebController::class, 'store'])->name('orders.store');
    Route::put('/orders/{order}/submit-production', [OrderWebController::class, 'submitProduction'])->name('orders.submit-production');
    
    Route::get('/installations', [InstallationWebController::class, 'index'])->name('installations.index');
    Route::post('/installations', [InstallationWebController::class, 'store'])->name('installations.store');
    Route::put('/installations/{installation}/update-status', [InstallationWebController::class, 'updateStatus'])->name('installations.update-status');
    Route::put('/installations/{installation}/assign', [InstallationWebController::class, 'assign'])->name('installations.assign');

});


// all the unknown route redirect to home page
// Route::any('{query}',
//   function() { return redirect('/'); })
//   ->where('query', '.*');