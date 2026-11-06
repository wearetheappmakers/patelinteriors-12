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

Route::get('/', function () {
	return view('auth.login');
});

Auth::routes();

Route::group(['middleware' => 'auth'],function(){

	
	Route::get('/home/delete-multiple', 'HomeController@deleteMultiple')->name('home.delete-multiple');
	Route::get('/home/change-multiple-status', 'HomeController@changeMultipleStatus')->name('home.change-multiple-status');

	

	Route::group(['prefix' => 'product', 'as' => 'product.'],function(){
		Route::get('/index/',' @index')->name('index');
		Route::get('/create/','ProductController@create')->name('create');
		Route::post('/store/','ProductController@store')->name('store');
		Route::get('/edit/{id}','ProductController@edit')->name('edit');
		Route::put('/update/{id}','ProductController@update')->name('update');
		Route::get('/product_export', 'ProductController@product_export')->name('product_export');
		Route::post('/product_import', 'ProductController@product_import')->name('product_import');
	});

	Route::group(['prefix' => 'employee', 'as' => 'employee.'],function(){
		Route::get('/index/','EmployeeController@index')->name('index');
		Route::get('/create/','EmployeeController@create')->name('create');
		Route::post('/store/','EmployeeController@store')->name('store');
		Route::get('/edit/{id}','EmployeeController@edit')->name('edit');
		Route::put('/update/{id}','EmployeeController@update')->name('update');
		Route::get('/delete/{id}','EmployeeController@employeedelete')->name('deletechild');
		
	});

	
// Route::group(['prefix' => 'quotation', 'as' => 'quotations.'],function(){
// 		Route::get('/index/','QuotationController@index')->name('index');
// 		Route::get('/create/','QuotationController@create')->name('create');
// 		Route::post('/store/','QuotationController@store')->name('store');
// 		Route::get('/edit/{id}','QuotationController@edit')->name('edit');
// 		Route::put('/update/{id}','QuotationController@update')->name('update');
	
// 	});
	

	

	Route::resource('productcategory','ProductCategoryController');
	Route::resource('contact','ContactController');
	Route::resource('section','SectionController');
	Route::resource('amc','AMCController');
	Route::get('amc/delete/{id}','AMCController@childdelete')->name('amc.deletechild');
	Route::resource('task','TaskController');
	Route::resource('member','MemberController');
	Route::resource('complains','ComplainController');
	Route::resource('lead','LeadController');
	Route::post('/lead/basic','LeadController@leadbasic')->name('lead.basic');
	Route::get('/lead/edit/{id}','LeadController@edit')->name('lead.edit');
	Route::any('/lead/update/{id}','LeadController@update')->name('lead.update');
	Route::post('/lead/assign','LeadController@leadassign')->name('lead.assign');
	
	Route::post('/lead/followup','LeadController@leadfollowup')->name('lead.followup');
	Route::post('/lead/communication','LeadController@leadcommunication')->name('lead.communication');
	Route::get('lead/delete/{id}','LeadController@leadchilddelete')->name('lead.deletechild');
	
	Route::post('get/lead/product','LeadController@getproduct')->name('lead.getproduct');
	Route::resource('gst','GSTController');
	
	Route::resource('client','ClientController');
	Route::get('/client/edit/{id}','ClientController@edit')->name('client.edit');

	Route::post('/client/clientdocument','ClientController@clientdocument')->name('client.clientdocument');
	Route::resource('quotations','QuotationController');
	Route::post('quotations/getprice','QuotationController@getprice')->name('quotations.getprice');
	Route::get('quotations/delete/{id}','QuotationController@quotationdelete')->name('quotations.deletechild');
	// Route::post('/quotations/delivery','QuotationController@delivery')->name('quotations.delivery');
	Route::resource('quotationfooter','QuotationFooterController');
	Route::resource('leadsource','LeadSourceController');
	Route::resource('leadstage','LeadStageController');
	Route::resource('contactmaster','ContactMasterController');
	Route::get('/contcat_export', 'ContactMasterController@contact_export')->name('contactmaster.contact_export');
	Route::post('/contact_import', 'ContactMasterController@contact_import')->name('contactmaster.contact_import');
	Route::resource('supervisor','SupervisorController');
	Route::resource('template','TemplateController');
	Route::resource('group','GroupController');
	Route::resource('autoresponderkeyword','AutoresponderKeywordController');
	Route::resource('systemautoresponder','SystemAutoresponderController');
	Route::resource('autoresponder','AutoresponderController');
	Route::get('report/lead','ReportController@index')->name('report.leadreport');
	 // Route::resource('distributor','DistributorController');
	

	 // Route::resource('product','ProductController');

});
Route::group(['middleware' => 'auth:user,employee', 'as' => 'user.', 'prefix' => 'user'], function () {

	Route::name('home.')->group(function () {
		Route::get('/change-multiple-status', 'HomeController@changeMultipleStatus')->name('change-multiple-status');
		Route::get('/delete-multiple', 'HomeController@deleteMultiple')->name('delete-multiple');
    // Route::get('/discount-multiple', 'HomeController@discountMultiple')->name('discount-multiple');
    // Route::get('/change-order', 'HomeController@changeOrder')->name('change-order');
		Route::post('/change-status', 'HomeController@multiStatus')->name('change-status');
		

		
    // Route::post('ckeditor/upload', 'HomeController@upload')->name('ckeditor.image-upload');
	});

	Route::any('/dashboard', 'HomeController@index')->name('dashboard');
	// Route::any('/dashboardtest', 'HomeController@indexs')->name('dashboard');
	Route::any('sales/report','HomeController@salesreport')->name('sales.report');
	Route::any('leadsource/report','HomeController@leadsourcereport')->name('leadsource.report');

	Route::any('/dashboard/event', 'HomeController@event_data')->name('dashboard.event');
	Route::any('/dashboardtest/eventtest', 'HomeController@event_datatest')->name('dashboardtest.eventtest');



	Route::group(['prefix' => 'product', 'as' => 'product.'],function(){
		Route::get('/index/','ProductController@index')->name('index');
		Route::get('/create/','ProductController@create')->name('create');
		Route::post('/store/','ProductController@store')->name('store');
		Route::get('/edit/{id}','ProductController@edit')->name('edit');
		Route::put('/update/{id}','ProductController@update')->name('update');
		Route::get('/product_export', 'ProductController@product_export')->name('product_export');
		Route::post('/product_import', 'ProductController@product_import')->name('product_import');
		Route::get('/product_export_mrp', 'ProductController@product_exportmrp')->name('product_export_mrp');
		Route::post('/product_import_mrp', 'ProductController@product_importmrp')->name('product_import_mrp');
		Route::post('get/product/productcategory','ProductController@getproductcategory')->name('getproductcategory');
		Route::post('get/product/cronexportmrp','ProductController@productexportmrp')->name('cron.productexportmrp');
		Route::any('get/product/productdownload','ProductController@productdownload')->name('download');
		Route::any('get/product/productfiledelete/{id}','ProductController@downloaddelete')->name('download.delete');
		Route::any('/show/{id}','ProductController@show')->name('show');
	});
	Route::group(['prefix' => 'user', 'as' => 'user.'],function(){
		Route::get('/index/','UserController@index')->name('index');
		Route::get('/create/','UserController@create')->name('create');
		Route::post('/store/','UserController@store')->name('store');
		Route::get('/edit/{id}','UserController@edit')->name('edit');
		Route::put('/update/{id}','UserController@update')->name('update');
		Route::any('/show/{id}','UserController@show')->name('show');
	});

	Route::group(['prefix' => 'employee', 'as' => 'employee.'],function(){
		Route::get('/index/','EmployeeController@index')->name('index');
		Route::get('/create/','EmployeeController@create')->name('create');
		Route::post('/store/','EmployeeController@store')->name('store');
		Route::get('/edit/{id}','EmployeeController@edit')->name('edit');
		Route::any('/show/{id}','EmployeeController@show')->name('show');
		Route::put('/update/{id}','EmployeeController@update')->name('update');
		Route::get('/delete/{id}','EmployeeController@employeedelete')->name('deletechild');
		Route::get('/get_employees_by_branch/{id}','EmployeeController@getEmployeesByBranch')->name('employee_branch');
	});

	
	Route::resource('productcategory','ProductCategoryController');
	Route::any('productcategory/show/{id}','ProductCategoryController@show')->name('productcategory.show');

	Route::resource('project_type','ProjectTypeController');
	Route::any('/projecttype/deletes/{id}', 'ProjectTypeController@delete')->name('deletes.project.type');

	Route::resource('project_name','ProjectNameController');
	Route::any('/projectname/deletes/{id}', 'ProjectNameController@delete')->name('deletes.project.name');

	Route::resource('landmark','LandmarkController');
	Route::any('/landmark/deletes/{id}', 'LandmarkController@delete')->name('deletes.landmark');

	Route::resource('contact','ContactController');
	Route::resource('section','SectionController');
	Route::resource('sales','SalesController');
	
	Route::resource('task','TaskController');
	Route::any('task/show/{id}','TaskController@show')->name('task.show');
	
	Route::resource('complains','ComplainController');
	Route::post('get/complains/employee','ComplainController@getemployee')->name('complains.getemployee');
	Route::post('get/distributor/employee','DistributorController@getemployee')->name('distributor.getemployee');
	Route::resource('lead','LeadController');
	Route::post('/lead/basic','LeadController@leadbasic')->name('lead.basic');
	Route::get('/lead/edit/{id}','LeadController@edit')->name('lead.edit');
	Route::any('/lead/show/{id}','LeadController@show')->name('lead.show');
	Route::post('/lead/assign','LeadController@leadassign')->name('lead.assign');
	Route::post('/lead/followup','LeadController@leadfollowup')->name('lead.followup');


	Route::any('/lead/toshow','LeadController@leadtoshow')->name('lead.toshow');
	Route::any('/lead/toshow/delete/{id}','LeadController@deletetoshow')->name('lead.deletetoshow');

	Route::post('/lead/communication','LeadController@leadcommunication')->name('lead.communication');
	Route::get('lead/delete/{id}','LeadController@leadchilddelete')->name('lead.deletechild');
	Route::any('/lead/update/{id}','LeadController@update')->name('lead.update');
	Route::get('lead/main/delete/{id}','LeadController@leadchilmainddelete')->name('lead.deletemainchild');
	Route::get('design/delete/{id}','LeadController@leaddesigndelete')->name('delete.design');
	Route::get('quotation/delete/{id}','LeadController@leadquotationdelete')->name('delete.quotation');
	Route::post('get/lead/product','LeadController@getproduct')->name('lead.getproduct');
	Route::post('get/lead/employee','LeadController@getemployee')->name('lead.getemployee');
	Route::post('/lead/followup/model','LeadController@model')->name('lead.model');
	Route::post('get/so/employee','SalesOrderController@getemployee')->name('so.getemployee');
	Route::post('get/po/vendor','PurchaseOrderController@getemployee')->name('po.getemployee');
	Route::post('get/po/company','PurchaseOrderController@getcompany')->name('po.getcompany');
	Route::post('get/po/product','PurchaseOrderController@getproduct')->name('po.getproduct');
	Route::post('get/po/getdata','PurchaseOrderController@getdata')->name('po.getdata');
	Route::post('get/so/getdata','SalesOrderController@getdata')->name('so.getdata');
	Route::post('get/so/getso','SalesOrderInvoiceController@getsalesorder')->name('so.getso');
	Route::post('so/clientchange','SalesOrderController@clientchange')->name('so.clientchange');
	Route::post('so/getproductdata','SalesOrderController@getproductdata')->name('so.getproductdata');
	Route::post('get/service/employees','ServiceController@getemployees')->name('service.getemployee');
	// Route::post('get/lead/client','LeadController@getclient')->name('lead.getclient');
	Route::post('task/status','TaskController@status')->name('task.status');
	Route::resource('gst','GSTController');
	
	Route::resource('symbol','SymbolController');
	
	Route::resource('vendor','VendorController');

	Route::any('getstate','VendorController@getstate')->name('getstate');
	Route::any('getcity','VendorController@getcity')->name('getcity');
	
	Route::resource('pmc','PMCController');
	Route::resource('arid','AridController');
	Route::any('/arid/delete/{id}', 'AridController@delete')->name('delete.arid');

	Route::resource('consent_person','ConsentPersonController');
	Route::any('/consent_person/delete/{id}', 'ConsentPersonController@delete')->name('delete.consent_person');

	Route::any('symbols/delete/{id}','SymbolController@delete')->name('symbol.delete');
	Route::any('vendor/delete/{id}','VendorController@delete')->name('vendor.delete');
	Route::any('pmc/delete/{id}','PMCController@delete')->name('pmc.delete');
	Route::resource('client','ClientController');
	Route::get('/client/edit/{id}','ClientController@edit')->name('client.edit');
	Route::any('/client/show/{id}','ClientController@show')->name('client.show');
	Route::post('get/client/employee','ClientController@getemployee')->name('client.getemployee');
	Route::post('/client/clientdocument','ClientController@clientdocument')->name('client.clientdocument');
	Route::resource('quotations','QuotationController');

	Route::any('quotations/show/{lead_id}','QuotationController@show')->name('quotations.show');


	Route::post('quotations/getprice','QuotationController@getprice')->name('quotations.getprice');
	Route::post('quotations/getproduct','QuotationController@getproduct')->name('quotations.getproduct');
	Route::post('quotations/getemployee','QuotationController@getemployee')->name('quotations.getemployee');
	Route::get('quotations/delete/{id}','QuotationController@quotationdelete')->name('quotations.deletechild');
	Route::get('quotations/delete/main/{id}','QuotationController@quotationmaindelete')->name('quotations.deletemainchild');
	Route::any('leadreport/excel','ReportController@leadreportexport')->name('leadreport.export');
	Route::any('leadreport/pdf','ReportController@leadreportpdf')->name('leadreport.pdf');

	Route::any('todayreport/excel','ReportController@todayreportexport')->name('todayreport.export');
	Route::any('todayreport/pdf','ReportController@todayreportpdf')->name('todayreport.pdf');

	Route::any('yesterday/report/excel','ReportController@yesterdayreportexport')->name('yesterdayreport.export');
	Route::any('yesterday/todayreport/pdf','ReportController@yesterdayreportpdf')->name('yesterdayreport.pdf');

	Route::any('followupreport/excel','ReportController@followupreportexport')->name('followupreport.export');
	Route::any('followupreport/pdf','ReportController@followupreportpdf')->name('followupreport.pdf');
	Route::any('interiorvisit/excel','VisitsController@interiorvisitexport')->name('interiorvisit.export');
	Route::any('interiorvisit/pdf','VisitsController@interiorvisitpdf')->name('interiorvisit.pdf');
	Route::post('interiorvisit/getemaildata','VisitsController@getemaildata')->name('interiorvisit.getemaildata');
	Route::post('/visit/followup/model','VisitsController@model')->name('iv.followup.model');
	Route::post('/visit/followup/store','VisitsController@followup_store')->name('iv.followup.store');
	// Route::post('/quotations/delivery','QuotationController@delivery')->name('quotations.delivery');
	
	Route::resource('leadsource','LeadSourceController');
	Route::any('leadsource/show/{id}','LeadSourceController@show')->name('leadsource.show');

	Route::resource('leadstage','LeadStageController');
	Route::any('leadstage/show/{id}','LeadStageController@show')->name('leadstage.show');

	Route::post('service/getnumber','ServiceController@getnumber')->name('service.getnumber');

	Route::resource('pdf','PdfController');
	Route::post('pdf2/pdf', 'PdfController@pdf2')->name('pdf2.pdf');
	Route::post('pdf3/pdf', 'PdfController@pdf3')->name('pdf3.pdf');
	Route::post('pdf4/pdf', 'PdfController@pdf4')->name('pdf4.pdf');

	Route::resource('company','CompanyController');
	Route::any('company/show/{id}','CompanyController@show')->name('company.show');
	Route::post('getbranchcategory', 'CompanyController@getbranchcategory')->name('getbranchcategory');

	Route::resource('type','TypeController');
	Route::any('type/show/{id}','TypeController@show')->name('type.show');


	Route::resource('sourcename','SourceNameController');	
	
	Route::resource('warehouse','WarehouseController');	
	Route::resource('gallery','GalleryController');
	Route::resource('brandlogo','BrandLogoController');		
	Route::any('/inventory','InventoryController@index')->name('warehouse.inventory');
	Route::any('/inventory/store','InventoryController@update')->name('inventory.update');
	Route::get('/delivery/index/{id}', 'DeliveryController@index')->name('delivery.index');
	Route::post('lead/getcompany','LeadController@getcompany')->name('lead.getcompany');
	Route::post('lead/getwarehouse','LeadController@getwarehouse')->name('lead.getwarehouse');
	Route::post('lead/getwarehouse/from/product','LeadController@getwarehouse_from_product')->name('lead.getwarehouse.from.product');
	Route::post('quotations/getdata','QuotationController@getdata')->name('quotations.getdata');

	Route::any('/closing/stock','InventoryController@closing_stock')->name('closing.stock');

	Route::get('attendance/index','AttendanceController@index')->name('attendance.index');
	Route::get('attendance/image','AttendanceController@image')->name('attendance.image');
	Route::post('attendance/status','AttendanceController@attendance')->name('attendance.status');
	Route::any('attendance/create','AttendanceController@create')->name('attendance.create');
	Route::any('attendance/store','AttendanceController@store')->name('attendance.store');
	Route::any('attendance/show/{id}','AttendanceController@show')->name('attendance.show');
	Route::post('salesorder/status','SalesOrderController@status')->name('salesorder.status');
	Route::post('purchaseorder/status','PurchaseOrderController@status')->name('purchaseorder.status');
	Route::post('credit/status','CreditController@status')->name('credit.status');
	Route::any('credit/getinvoice','CreditController@getinvoice')->name('credit.getinvoice');
	Route::any('credit/pending/so/invoice/product','CreditController@pending_so_invoice_product')->name('credit.pendingso.invoice.product');
	Route::any('credit/getdata','CreditController@getdata')->name('credit.getdata');
	Route::post('debit/status','DebitController@status')->name('debit.status');

	Route::resource('leave','LeaveController');
	Route::resource('salesorder','SalesOrderController');
	Route::any('sales/order/sample/export','SalesOrderController@sample_export')->name('salesorder.sample.export');
	Route::any('sales/order/import','SalesOrderController@import')->name('salesorder.import');
	Route::resource('purchaseorder','PurchaseOrderController');
	Route::any('get/vendor/data','PurchaseOrderController@get_vendor_data')->name('get.vendor.data');
	Route::post('getdata','LeaveController@getdata')->name('employee.name');
	
	Route::resource('group','GroupController');
	Route::resource('service','ServiceController');
	Route::any('service/show/{id}','ServiceController@show')->name('service.show');
	
	Route::post('task/getemployee','TaskController@getemployee')->name('task.getemployee');
	Route::any('task/getclientdata','TaskController@getclientdata')->name('task.getclientdata');
	Route::post('lead/getsourcename','LeadController@getsourcename')->name('lead.getsourcename');
	Route::post('lead/getcompanyfromcategory','LeadController@getcompanyfromcategory')->name('lead.getcompanyfromcategory');
	Route::get('report/lead','ReportController@index')->name('report.leadreport');
	Route::get('report/amc','ReportController@amcreport')->name('report.amcreport');
	Route::get('report/quotations','ReportController@quotationreport')->name('report.quotationreport');

	Route::any('/report/index', 'ReportController@reportindex')->name('report.index');	

	Route::post('quotation/pdf', 'QuotationController@pdf')->name('quotation.pdf');
	Route::post('quotation2/pdf', 'QuotationController@pdf2')->name('quotation2.pdf');
	Route::post('quotation3/pdf', 'QuotationController@pdf3')->name('quotation3.pdf');
	Route::post('quotation4/pdf', 'QuotationController@pdf4')->name('quotation4.pdf');
	Route::get('/quotation/clone/create/{id}', 'QuotationController@clone')->name('quotation.clone.create');
	Route::get('/delivery/index/{quotation_id}', 'DeliveryController@index')->name('delivery.index');
	Route::get('/revised/index/{quotation_id}', 'RevisedController@index')->name('revisedquotation.index');
	Route::post('/delivery/update/{quotation_id}', 'DeliveryController@update')->name('delivery.update');
	Route::get('/quotation/create/{lead_id}', 'QuotationController@create')->name('quotation.create');
	Route::get('/quotation/update/{lead_id}', 'QuotationController@update')->name('quotation.update');
	Route::post('/quotation/getwarehouse', 'QuotationController@getwarehouse')->name('quotation.getwarehouse');
	Route::post('/quotations/getwarehouse/data', 'QuotationController@quotation_getwarehouse_data')->name('quotation.getwarehouse.data');
	Route::get('/delivery/log/{id}', 'DeliveryController@deliverylog')->name('delivery.log');
	Route::get('/revised/view/{id}', 'RevisedController@show')->name('revised.view');
	Route::get('/salesorder/view/{id}', 'SalesOrderController@show')->name('salesorder.view');
	Route::get('/purchaseorder/view/{id}', 'PurchaseOrderController@show')->name('purchaseorder.view');
	Route::get('salesorder/delete/{id}','SalesOrderController@childdelete')->name('salesorder.deletechild');
	Route::get('purchaseorder/delete/{id}','PurchaseOrderController@childdelete')->name('purchaseorder.deletechild');
	
	Route::any('gallery/assign/{id}','GalleryController@assign')->name('gallery.assign');
	Route::any('gallery/assign/store/{id}','GalleryController@assign_store')->name('gallery.assign.store');


	
	Route::post('lead/event','HomeController@event')->name('lead.event');
	Route::post('leave/status','LeaveController@status')->name('leave.status');
	Route::post('leave/approved','LeaveController@approved')->name('leave.approved');
	Route::post('product/getcompany','ProductController@getcompany')->name('product.getcompany');
	Route::any('category/getcategory','ProductController@getcategory')->name('getcategory');
	Route::post('leave/getemployee','ProductController@getemployee')->name('leave.getemployee');
	Route::post('quotation/price/{id}','QuotationController@price')->name('quotation.price');
	Route::get('/gallery_export', 'GalleryController@gallery_export')->name('gallery_export');
	Route::resource('visits','VisitsController');
	Route::resource('credit','CreditController');
	Route::resource('debit','DebitController');

	Route::post('debit/get/product/data','DebitController@debit_getdata')->name('debit.getdata');
	Route::any('get/vendor/data/debit','DebitController@get_vendor_data')->name('get.vendor.data.debit');
	Route::any('get/vendor/invoicedata/debit','DebitController@get_vendor_invoicedata')->name('get.vendor.invoicedata.debit');
	Route::any('get/vendor/invoicechilddata/debit','DebitController@get_vendor_invoicechilddata')->name('get.vendor.invoicechilddata.debit');

	Route::any('visit/change/status', 'VisitsController@statuschange')->name('change.status');
	Route::any('visit/clone/delete/{id}', 'VisitsController@clonedelete')->name('visit.clone.delete');
	Route::any('visit/report/lead','ReportController@visitreportindex')->name('visitreport.index');
	Route::any('service/clone/delete/{id}', 'ServiceController@clonedelete')->name('service.clone.delete');
	Route::post('service/status','ServiceController@status')->name('service.status');
	Route::any('/leadstage/deletes/{id}', 'LeadStageController@deleteclone')->name('deletes.clones');
	Route::any('/leadsource/deletes/{id}', 'LeadSourceController@deleteleadsourceclone')->name('deletesleadsource.clones');
	Route::any('/gst/deletes/{id}', 'GSTController@deletegstclone')->name('deletesgst.clones');
	Route::any('/types/deletes/{id}', 'TypeController@deletetypeclone')->name('deletestype.clones');
	Route::any('/get/leadsource/branch', 'SourceNameController@getleadsource')->name('getleadsource');
	Route::any('/sourcename/deletes/{id}', 'SourceNameController@deletesourcename')->name('deletesourcename');
	Route::any('/warehouse/deletes/{id}', 'WarehouseController@deletewarehouse')->name('deletewarehouse');
	Route::any('/gallery/deletes/{id}', 'GalleryController@deletegallery')->name('deletegallery');
	Route::any('/brandlogo/deletes/{id}', 'BrandLogoController@deletebrandlogo')->name('deletebrandlogo');
	Route::any('/client/deletes/{id}', 'ClientController@delete')->name('delete.client');
	Route::any('/emp/deletes/{id}', 'EmployeeController@delete')->name('delete.emp');
	Route::any('/user/deletes/{id}', 'UserController@delete')->name('delete.user');
	Route::any('lead/close/status/{id}','LeadController@active')->name('lead.close');
	Route::any('lead/active/status/{id}','LeadActiveController@active')->name('lead.active');
	Route::any('lead/active/index','LeadActiveController@index')->name('lead.activeindex');
	Route::any('/product/deletes/{id}', 'ProductController@delete')->name('deletes.product');
	Route::any('/productcategory/deletes/{id}', 'ProductCategoryController@delete')->name('deletes.productcategory');
	Route::post('visit/getemployee','VisitsController@getemployee')->name('visit.getemployee');
	Route::post('visit/getcategory','VisitsController@getcategory')->name('visit.getcategory');
	Route::post('report/getdata','ReportController@getdata')->name('report.getdata');
	Route::post('lead/getclient','LeadController@getclient')->name('lead.getclient');
	Route::post('service/getcompany','ServiceController@getcompany')->name('service.getcompany');
	Route::post('service/getproduct','ServiceController@getproduct')->name('service.getproduct');
	Route::any('followup/report','ReportController@followup_report')->name('followup.report');
	Route::any('todays/followup/report','ReportController@todays_followup_report')->name('todays.followup.report');
	Route::any('yesterday/followup/report','ReportController@yesterday_followup_report')->name('yesterday.followup.report');
	Route::any('/company/deletes/{id}', 'CompanyController@delete')->name('deletes.company');
	Route::any('/service/image/{id}', 'ServiceController@deleteimage')->name('servicedelete.image');

	Route::any('purchase/invoice/index','PurchaseInvoiceController@index')->name('purchaseinvoice.index');
	Route::any('purchase/invoice/create','PurchaseInvoiceController@create')->name('purchaseinvoice.create');
	Route::any('purchase/invoice/store','PurchaseInvoiceController@store')->name('purchaseinvoice.store');
	Route::any('purchase/invoice/getpo','PurchaseInvoiceController@getpo')->name('purchaseinvoice.getpo');
	Route::any('purchase/invoice/edit/{id}','PurchaseInvoiceController@edit')->name('purchaseinvoice.edit');
	Route::any('purchase/invoice/update/{id}','PurchaseInvoiceController@update')->name('purchaseinvoice.update');
	Route::any('purchase/invoice/status','PurchaseInvoiceController@status')->name('purchaseinvoice.status');
	Route::any('purchase/invoice/view/{id}','PurchaseInvoiceController@show')->name('purchaseinvoice.view');
	Route::get('purchase/delete/{id}','PurchaseInvoiceController@childdelete')->name('purchaseinvoice.deletechild');
	Route::post('get/pi/getdata','PurchaseInvoiceController@pi_getdata')->name('pi.getdata');

	Route::any('salesorder/invoice/index','SalesOrderInvoiceController@index')->name('salesorderinvoice.index');
	Route::any('salesorder/invoice/create','SalesOrderInvoiceController@create')->name('salesorderinvoice.create');
	Route::any('salesorder/invoice/store','SalesOrderInvoiceController@store')->name('salesorderinvoice.store');
	Route::any('salesorder/invoice/getso','SalesOrderInvoiceController@getso')->name('salesinvoice.getso');
	Route::any('salesorder/invoice/edit/{id}','SalesOrderInvoiceController@edit')->name('salesorderinvoice.edit');
	Route::any('salesorder/invoice/update/{id}','SalesOrderInvoiceController@update')->name('salesorderinvoice.update');
	Route::any('salesorder/invoice/status','SalesOrderInvoiceController@status')->name('salesorderinvoice.status');
	Route::any('salesorder/invoice/view/{id}','SalesOrderInvoiceController@show')->name('salesorderinvoice.view');
	Route::any('salesorder/invoice/getemployee','SalesOrderInvoiceController@getemployee')->name('salesorderinvoice.getemployee');
	Route::get('salesorder/invoice/delete/{id}','SalesOrderInvoiceController@childdelete')->name('salesorderinvoice.deletechild');
	Route::any('salesorders/report','SalesOrderReportController@salesorder_report')->name('salesorder.report');
	Route::any('salesorders/report/getdata','SalesOrderReportController@getdata')->name('salesorder.report.getdata');

	Route::any('purchaseinvoice/report','PurchaseInvoiceController@purchaseinvoice_report')->name('purchaseinvoice.report');
	Route::any('purchaseinvoice/report/getdata','PurchaseInvoiceController@get_report_data')->name('purchaseinvoice.report.getdata');

	Route::any('salesorderinvoice/report','SalesOrderInvoiceController@salesorderinvoice_report')->name('salesorderinvoice.report');
	Route::any('salesorderinvoice/report/getdata','SalesOrderInvoiceController@get_report_data')->name('salesorderinvoice.report.getdata');

	Route::get('salesorderinvoice//delete/{id}','SalesOrderInvoiceController@invoicedelete')->name('salesorderinvoice.invoicedelete');

	Route::any('salesorder/invoice/sample/export','SalesOrderInvoiceController@invoice_sample_export')->name('salesorder.invoice.sample.export');
	Route::any('salesorder/invoice/import','SalesOrderInvoiceController@invoice_import')->name('salesorder.invoice.import');

	Route::post('credit/getclient','CreditController@getclient')->name('credit.getclient');
	Route::get('credits/delete/{id}','CreditController@delete')->name('deletes.credit');
	Route::get('debit/delete/{id}','DebitController@delete')->name('deletes.debit');
	Route::get('debits/delete/{id}','DebitController@childdelete')->name('debit.deletechild');
	Route::get('credit/delete/{id}','CreditController@childdelete')->name('credit.deletechild');

	Route::any('clients/report','ClientReportController@client_report')->name('client.report');
	Route::any('client/report/export','ClientReportController@client_report_export')->name('client.report.export');

	Route::any('opening/stock','OpeningStockController@index')->name('opening.stock');
	Route::any('opening/stock/import','OpeningStockController@import')->name('opening.stock.import');
	Route::any('opening/stock/sample','OpeningStockController@sample')->name('opening.stock.sample');
	Route::any('opening/stock/childimport/{id}','OpeningStockController@childimport')->name('opening.stock.childimport');
	Route::any('opening/stock/childexport/{id}','OpeningStockController@childexport')->name('opening.stock.childexport');
	
	Route::resource('technician','TechnicianController');
	Route::post('technician/getcompany','TechnicianController@getcompany')->name('technician.getcompany');
	Route::post('get/technician/employees','TechnicianController@getemployees')->name('technician.getemployee');
	Route::get('ajax/products/search/', 'PurchaseInvoiceController@search')->name('ajax.products.search');

	// Stock Report
	Route::any('stock/report/{product_id}/{warehouse_id}','ReportController@stock_report')->name('stock.report');
	Route::any('stock/report/pdf/{product_id}/{warehouse_id}','ReportController@stock_report_pdf')->name('stock.report.pdf');

	// new route 
	Route::resource('travel_update','TravelUpdateController');
	Route::post('get/travel_update/employee','TravelUpdateController@getemployee')->name('travel_update.getemployee');
	Route::resource('meeting_entry_employee','MeetingEntryEmpController');
	Route::post('get/meeting_entry_employee/employee','MeetingEntryEmpController@getemployee')->name('meeting_entry_employee.getemployee');

	Route::resource('expense','ExpenseController');
	Route::any('expense_child/delete/{id}', 'ExpenseController@destroyChild')->name('deletechild');
	Route::post('get/expense/employee','ExpenseController@getemployee')->name('expense.getemployee');


	Route::resource('holiday','HolidayController');
	Route::resource('role','RoleController');
	Route::resource('appointment_letter','AppointmentLetterController');
	Route::resource('company_policy','CompanyPolicyController');

	Route::get('appointment-letter/pdf/{id}','AppointmentLetterController@pdf')->name('appointment_letter.pdf');
	Route::get('company-policy/pdf/{id}','CompanyPolicyController@pdf')->name('company_policy.pdf');

});

Route::group(['prefix' => 'employee'], function () {
	Route::get('/login', 'EmployeeAuth\LoginController@showLoginForm')->name('login');
	Route::post('/login', 'EmployeeAuth\LoginController@login');
	Route::post('/logout', 'EmployeeAuth\LoginController@logout')->name('logout');

	Route::get('/register', 'EmployeeAuth\RegisterController@showRegistrationForm')->name('register');
	Route::post('/register', 'EmployeeAuth\RegisterController@register');

	Route::post('/password/email', 'EmployeeAuth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
	Route::post('/password/reset', 'EmployeeAuth\ResetPasswordController@reset')->name('password.email');
	Route::get('/password/reset', 'EmployeeAuth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
	Route::get('/password/reset/{token}', 'EmployeeAuth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'user'], function () {
	Route::get('/login', 'UserAuth\LoginController@showLoginForm')->name('login');
	Route::post('/login', 'UserAuth\LoginController@login');
	
	Route::post('/logout', 'UserAuth\LoginController@logout')->name('logout');

	Route::get('/register', 'UserAuth\RegisterController@showRegistrationForm')->name('register');
	Route::post('/register', 'UserAuth\RegisterController@register');

	Route::post('/password/email', 'UserAuth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
	Route::post('/password/reset', 'UserAuth\ResetPasswordController@reset')->name('password.email');
	Route::get('/password/reset', 'UserAuth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
	Route::get('/password/reset/{token}', 'UserAuth\ResetPasswordController@showResetForm');

	Route::post('/get/transfe/-warehouse','InventoryController@getTransferWarehouse')->name('user.get.transfer.warehouse');
	Route::post('transfer/stock','InventoryController@transferStock')->name('user.transfer.stock');

	Route::get('inventory/export', 'InventoryController@export')->name('user.inventory.export');
	

});
