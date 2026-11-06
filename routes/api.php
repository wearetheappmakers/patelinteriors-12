<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) { 
    $api->group(['namespace' => 'App\Http\Controllers\Api\V1', 'prefix' => 'v1'], function ($api) {
        $api->post('user-login', 'AdminController@login');
        $api->post('employee-login','EmployeeLoginController@employee_login');
        $api->group(['middleware' => 'jwt.verify'], function ($api_child) {

            $api_child->get('get-user','AdminController@getuser');
            $api_child->post('get-quotation-product','ClientController@getquotationproduct');
            $api_child->post('get-quotation-price','ClientController@getquotationprice');
            $api_child->get('get-product-category','AdminController@getproductcategory');
            $api_child->post('get-lead-product','LeadController@getleadproduct');
            $api_child->post('get-lead-employee','LeadController@getleademployee');
            $api_child->post('create-product-category','AdminController@createproductcategory');
            $api_child->post('update-product-category','AdminController@updateproductcategory');
            $api_child->post('delete-product-category','AdminController@deleteproductcategory');
            $api_child->post('status-product-category','AdminController@productcategorystatus');
            $api_child->get('get-product','AdminController@getproduct');
            $api_child->post('create-product','AdminController@createproduct');
            $api_child->post('update-product','AdminController@updateproduct');
            $api_child->post('delete-product','AdminController@deleteproduct');
            $api_child->post('status-product','AdminController@productstatus');
            $api_child->get('get-complain','AdminController@getcomplain');
            $api_child->post('create-complain','AdminController@createcomplain');
            $api_child->post('update-complain','AdminController@updatecomplain');
            $api_child->post('delete-complain','AdminController@deletecomplain');
            $api_child->post('status-complain','AdminController@statuscomplain');
            $api_child->get('get-supervisor','AdminController@getsupervisor');
            $api_child->post('create-supervisor','AdminController@createsupervisor');
            $api_child->post('update-supervisor','AdminController@updatesupervisor');
            $api_child->post('delete-supervisor','AdminController@deletesupervisor');
            $api_child->post('status-supervisor','AdminController@statussupervisor');
            $api_child->get('get-contact','AdminController@getcontact');
            $api_child->post('create-contact','AdminController@createcontact');
            $api_child->post('update-contact','AdminController@updatecontact');
            $api_child->post('delete-contact','AdminController@deletecontact');
            $api_child->post('status-contact','AdminController@statuscontact');
            $api_child->get('get-client','ClientController@getclient');
            $api_child->post('create-client','ClientController@createclient');
            $api_child->post('update-client','ClientController@updateclient');
            $api_child->post('delete-client','ClientController@deleteclient');
            $api_child->get('get-amc','ClientController@getamc');
            $api_child->post('create-amc','ClientController@createamc');
            $api_child->post('update-amc','ClientController@updateamc');
            $api_child->post('delete-amc','ClientController@deleteamc');
            $api_child->post('create-amc-child','ClientController@createamcchild');
            $api_child->post('update-amc-child','ClientController@updateamcchild');
            $api_child->post('delete-amc-child','ClientController@deleteamcchild');
            $api_child->get('admin-get-quotation','ClientController@getquotation');
            $api_child->post('create-quotation-admin','ClientController@createquotation');
            $api_child->post('create-admin-quotation','ClientController@createadminquotation');
            $api_child->post('update-admin-quotation','ClientController@updateadminquotation');
            $api_child->post('admin-update-quotation-child','ClientController@updatequotation');
            $api_child->post('delivery','ClientController@delivery');
            $api_child->post('admin-create-quotation-main-child','ClientController@createquotationchild');
            $api_child->post('update-delivery','ClientController@updatedelivery');
            $api_child->post('admin-delete-quotation','ClientController@deletequotation');
            $api_child->post('admin-update-quotation-main','ClientController@updatequotationmainchild');
            $api_child->post('delete-quotation-child','ClientController@deletequotationchild');
            $api_child->get('get-employee','EmployeeController@getemployee');
            $api_child->post('create-employee','EmployeeController@createemployee');
            $api_child->post('update-employee','EmployeeController@updateemployee');
            $api_child->post('create-employee-child','EmployeeController@createemployeechild');
            $api_child->post('update-employee-child','EmployeeController@updateemployeechild');
            $api_child->post('delete-employee-child','EmployeeController@deleteemployeechild');
            $api_child->post('delete-employee','EmployeeController@deleteemployee');
            $api_child->get('get-distributor','EmployeeController@getdistributor');
            $api_child->post('create-distributor','EmployeeController@createdistributor');
            $api_child->post('update-distributor','EmployeeController@updatedistributor');
            $api_child->post('delete-distributor','EmployeeController@deletedistributor');
            $api_child->get('get-section','EmployeeController@getsection');
            $api_child->post('create-section','EmployeeController@createsection');
            $api_child->post('update-section','EmployeeController@updatesection');
            $api_child->post('delete-section','EmployeeController@deletesection');
            $api_child->post('status-section','EmployeeController@sectionstatus');
            $api_child->post('get-leadstage','EmployeeController@getleadstage');
            $api_child->post('create-leadstage','EmployeeController@craeteleadstage');
            $api_child->post('update-leadstage','EmployeeController@updateleadstage');
            $api_child->post('delete-leadstage','EmployeeController@deleteleadstage');
            $api_child->post('status-leadstage','EmployeeController@statusleadstage');

            $api_child->get('get-leadsource','EmployeeController@getleadsource');
            $api_child->post('create-leadsource','EmployeeController@createleadsource');
            $api_child->post('update-leadsource','EmployeeController@updateleadsource');
            $api_child->post('delete-leadsource','EmployeeController@deleteleadsource');
            $api_child->post('status-leadsource','EmployeeController@statusleadsource');

             $api_child->get('employee-get-leadsource','EmployeeSourceController@employeegetleadsource');
            $api_child->post('employee-create-leadsource','EmployeeSourceController@employeecreateleadsource');
            $api_child->post('employee-update-leadsource','EmployeeSourceController@employeeupdateleadsource');
            $api_child->post('employee-delete-leadsource','EmployeeSourceController@employeedeleteleadsource');
            $api_child->post('employee-status-leadsource','EmployeeSourceController@employeestatusleadsource');

            $api_child->get('get-gst','LeadController@getgst');
            $api_child->post('create-gst','LeadController@creategst');
            $api_child->post('update-gst','LeadController@updategst');
            $api_child->post('delete-gst','LeadController@deletegst');
            $api_child->post('status-gst','LeadController@statusgst');

            $api_child->get('get-quotationfooter','LeadController@getquotationfooter');
            $api_child->post('create-quotationfooter','LeadController@quotationfootercreate');
            $api_child->post('update-quotationfooter','LeadController@quotationfooterupdate');
            $api_child->post('delete-quotationfooter','LeadController@quotationfooterdelete');
            $api_child->post('status-quotationfooter','LeadController@quotationfooterstatus');

            $api_child->get('get-lead','LeadController@getlead');
            $api_child->post('create-lead-basic','LeadController@createleadbasic');
            $api_child->post('create-lead-assign','LeadController@createleadassign');
            $api_child->post('admin-update-lead-followup','LeadController@createleadfollowup');
            $api_child->post('create-lead-communication','LeadController@createleadcommunication');
            $api_child->post('update-lead-basic','LeadController@updateleadbasic');
            $api_child->post('update-lead-assign','LeadController@updateleadassign');
            $api_child->post('create-basic-child','LeadController@createbasicchild');
            $api_child->post('update-basic-child','LeadController@updatebasicchild');
            $api_child->post('delete-basic-child','LeadController@deletebasicchild');
            $api_child->post('delete-lead','LeadController@deletelead');

            $api_child->get('get-task-admin','LeadController@gettask');
            $api_child->post('create-task-admin','LeadController@taskcreate');
            $api_child->post('update-task-admin','LeadController@taskupdate');
            $api_child->post('delete-task-admin','LeadController@taskdelete');

            $api_child->get('get-branch','LeadController@getbranch');
            $api_child->post('create-branch','BranchController@createbranch');
            $api_child->post('update-branch','BranchController@updatebranch');
            $api_child->post('delete-branch','BranchController@deletebranch');

            $api_child->get('get-user-attendance','AdminController@getattendance');

            $api_child->get('get-interior-visit','TaskController@get_interiorvisit');
            $api_child->post('create-interior-visit','TaskController@create_interiorvisit');
            $api_child->post('update-interior-visit','TaskController@updatevisit');
            $api_child->post('create-interior-visit-child','TaskController@createvisitchild');
            $api_child->post('update-interior-visit-child','TaskController@updatevisitchild');
            $api_child->post('delete-interior-visit-child','TaskController@visitdeletechild');
            $api_child->post('delete-interior-visit','TaskController@visitdelete');
            $api_child->post('status-interior-visit','TaskController@visitstatus');


            $api_child->post('create-client-document','ClientController@createclientdocument');
            $api_child->post('admin-update-quotation-child','ClientController@updatequotation');
            $api_child->post('create-quotation-child','ClientController@quotationchild');

            $api_child->get('get-employee-lead','EmployeeLoginController@getemployeelead');
            $api_child->get('get-employee-gst','EmployeeSalesOrderController@getemployeegst');
            $api_child->post('employee-create-lead-basic','EmployeeLoginController@employeecreateleadbasic');
            $api_child->post('employee-create-lead-assign','EmployeeLoginController@employeecreateleadassign');
            $api_child->post('employee-update-lead-followup','EmployeeLoginController@employeecreateleadfollowup');
            $api_child->post('employee-create-lead-communication','EmployeeLoginController@employeecreateleadcommunication');
            $api_child->post('employee-update-lead-basic','EmployeeLoginController@employeeupdateleadbasic');
            $api_child->post('employee-create-lead-basic-child','EmployeeLoginController@employeecreatebasicchild');
            $api_child->post('employee-update-basic-child','EmployeeLoginController@employeeupdatebasicchild');
            $api_child->post('employee-delete-basic-child','EmployeeLoginController@employeedeletebasicchild');
            $api_child->post('employee-get-lead-company','EmployeeLoginController@employeegetleadcompany');
            $api_child->post('employee-get-lead-product','EmployeeLoginController@employeegetleadproduct');
            $api_child->post('employee-get-lead-warehouse','EmployeeLoginController@employeegetleadwarehouse');
            $api_child->post('employee-lead-create-main-child','EmployeeLoginController@employeeleadcreatemainchild');
            $api_child->post('employee-lead-update-main-child','EmployeeLoginController@employeeleadmainchildupdate');
            $api_child->post('employee-lead-delete-main-child','EmployeeLoginController@employeeleadmainchilddelte');
            $api_child->post('employee-lead-create-design','EmployeeLoginController@employeecreatedesign');
            $api_child->post('employee-lead-delete-design','EmployeeLoginController@employeedeletedesign');

            $api_child->post('employee-create-quotation','EmployeeLoginController@employeecreatequotation');
            $api_child->post('employee-update-quotation','EmployeeLoginController@employeeupdatequotation');
            $api_child->post('employee-admin-update-quotation-main','EmployeeLoginController@employeeupdatequotationmainchild');
            $api_child->post('employee-admin-update-quotation-child','EmployeeLoginController@employeeupdatequotationchild');
            $api_child->post('employee-admin-create-quotation-main-child','EmployeeLoginController@employee_createquotationchild');
            $api_child->post('employee-admin-delete-quotation','EmployeeLoginController@employee_deletequotation');
            $api_child->post('employee-admin-delete-quotation-main','EmployeeLoginController@employee_deletequotationmain');
            $api_child->post('employee-admin-delete-quotation-child','EmployeeLoginController@employee_deletequotationchild');
            // $api_child->get('admin-get-quotation','EmployeeLoginController@employee_getquotation');
            // $api_child->get('admin-get-quotation','EmployeeLoginController@employees_getquotations');
            $api_child->get('employee-get-quotation','EmployeeLoginController@employeegetquotation');

            $api_child->get('get-employee-quotation','EmployeeLoginController@getemployeequotation');
            $api_child->post('employee-get-quotation-product','EmployeeLoginController@employeegetquotationproduct');
            $api_child->post('employee-get-quotation-price','EmployeeLoginController@employeegetquotationprice');
            $api_child->post('employee-create-quotation-child','EmployeeLoginController@employeecreatequotationchild');
            $api_child->post('employee-delivery','EmployeeLoginController@employeedelivery');
            $api_child->post('employee-delete-quotation-child','EmployeeLoginController@employeedeletequotationchild');
            $api_child->get('get-employee-profile','EmployeeLoginController@getemployeeprofile');
            $api_child->post('update-employee-profile','EmployeeLoginController@updateemployeeprofile');
            $api_child->get('employee-get-client','EmployeeLoginController@employeegetclient');
            $api_child->get('dashboard','BranchController@dashboard');
            $api_child->get('employee-dashboard','EmployeeLoginController@employee_dashboard');
            $api_child->get('employee-get-task','EmployeeLoginController@employeegettask');
            $api_child->post('employee-task-create','EmployeeLoginController@employeetaskcreate');
            $api_child->post('employee-task-update','EmployeeLoginController@employeetaskupdate');
            $api_child->post('employee-task-status','EmployeeLoginController@employeetaskstatus');

            $api_child->post('task-status-admin','LeadController@taskstatus');
            $api_child->post('get-revised','ClientController@getrevised');
            $api_child->post('get-delivery','ClientController@getdelivery');
            $api_child->post('update-delivery','ClientController@update_delivery');


            $api_child->get('get-attendance','BranchController@getattendance');
            $api_child->post('attendance-status','BranchController@attendancestatus');

            $api_child->get('get-leave','BranchController@getleave');
            $api_child->post('create-leave','BranchController@createleave');
            $api_child->post('status-leave','BranchController@statusleave');

            $api_child->post('get-leadsource-name','LeadController@getleadsource_name');
            $api_child->post('get-lead-company','LeadController@getleadcompany');
            $api_child->post('get-lead-warehouse','LeadController@getleadwarehouse'); 

            $api_child->get('admin-get-salesorder','SalesOrderController@getsalesorder');
            $api_child->post('admin-get-category','SalesOrderController@getcategory');
            $api_child->post('admin-create-salesorder','SalesOrderController@createsalesorder');
            $api_child->post('admin-update-salesorder','SalesOrderController@updatesalesorder');
            $api_child->post('admin-status-salesorder','SalesOrderController@salesorderstatus');
            $api_child->post('admin-update-child-salesorder','SalesOrderController@updatechildsalesorder');
            $api_child->post('admin-create-salesorder-child','SalesOrderController@createchildsalesorder');
            $api_child->post('admin-delete-salesorder','SalesOrderController@deletesalesorder');
            $api_child->post('admin-delete-salesorder-child','SalesOrderController@deletesalesorderchild');

            $api_child->get('employee-get-salesorder','EmployeeSalesOrderController@employeegetsalesorder');
            $api_child->get('employee-get-category','EmployeeSalesOrderController@getcategory');
            $api_child->get('employee-get-client','EmployeeSalesOrderController@getclient');
            $api_child->post('employee-create-salesorder','EmployeeSalesOrderController@employeecreatesalesorder');
            $api_child->post('employee-update-salesorder','EmployeeSalesOrderController@employeeupdatesalesorder');
            $api_child->post('employee-status-salesorder','EmployeeSalesOrderController@employeesalesorderstatus');
            $api_child->post('employee-update-salesorder-child','EmployeeSalesOrderController@employeeupdatechildsalesorder');
            $api_child->post('employee-create-salesorder-child','EmployeeSalesOrderController@employeecreatechildsalesorder');
            $api_child->post('employee-delete-salesorder','EmployeeSalesOrderController@deletesalesorder');

            $api_child->get('employee-get-attendance','AdminController@getemployeeattendance');
            $api_child->any('employee-create-attendance','EmployeeLoginController@employee_create_attendance');
            $api_child->post('employee-attendance-status','BranchController@attendancestatus');

            $api_child->get('employee-get-leave','EmployeeLoginController@getemployeeleave');
            $api_child->post('employee-create-leave','EmployeeLoginController@employeecreateleave');
            $api_child->post('employee-status-leave','EmployeeLoginController@employeestatusleave');
            $api_child->post('quotation-pdf','SalesOrderController@quotationpdf');
            $api_child->post('employee-quotation-pdf','EmployeeSalesOrderController@employeequotationpdf');

            $api_child->post('get-employee-admin','ClientController@getemployeeadmin');
            $api_child->get('get-employee-client','EmployeeLoginController@getemployeeclient');
            $api_child->post('employee-create-client','EmployeeLoginController@employeecreateclient');
            $api_child->post('employee-update-client','EmployeeLoginController@employeeupdateclient');
            $api_child->post('employee-client-document','EmployeeLoginController@employeecreateclientdocument');
            $api_child->post('employee-delete-client','EmployeeLoginController@employeedeleteclient');

            $api_child->post('lead-create-main-child','LeadController@leadcreatemainchild');
            $api_child->post('lead-update-main-child','LeadController@leadmainchildupdate');
            $api_child->post('lead-delete-main-child','LeadController@leadmainchilddelte');
            $api_child->post('lead-create-design','LeadController@createdesign');
            $api_child->post('lead-delete-design','LeadController@deletedesign');

            $api_child->get('employee-get-interior-visit','EmployeeVisitController@employeeget_interiorvisit');
            $api_child->get('employee-get-assign','EmployeeLoginController@getassign');
            $api_child->post('employee-create-interior-visit','EmployeeVisitController@employeecreate_interiorvisit');
            $api_child->post('employee-update-interior-visit','EmployeeVisitController@employeeupdatevisit');
            $api_child->post('employee-create-interior-visit-child','EmployeeVisitController@employeecreatevisitchild');
            $api_child->post('employee-update-interior-visit-child','EmployeeVisitController@employeeupdatevisitchild');
            $api_child->post('employee-delete-interior-visit-child','EmployeeVisitController@employeevisitdeletechild');
            $api_child->post('employee-delete-interior-visit','EmployeeVisitController@employeevisitdelete');
            $api_child->post('employee-status-interior-visit','EmployeeVisitController@employeevisitstatus');

            $api_child->get('employee-get-sourcename','EmployeeSalesOrderController@employeegetsourcename');
            $api_child->post('get-sourcename','SalesOrderController@getsourcename');
            $api_child->post('get-source','SalesOrderController@getsource');
            $api_child->post('get-client','SalesOrderController@getclient');
            $api_child->post('get-followuptype','LeadController@getfollowuptype');
            $api_child->get('employee-get','EmployeeLoginController@employeeget');
            $api_child->post('delete-quotation-main','ClientController@deletequotationmain');
            $api_child->get('employee-leadstage','EmployeeLoginController@employeegetstage');
            $api_child->get('employee-followuptype','EmployeeLoginController@employeefollowuptype');
            $api_child->get('employee-get-leadsource','EmployeeLoginController@employee_leadsource');

            $api_child->post('get-data-lead','LeadController@getdatalead');
            $api_child->post('get-data-leave','LeadController@getdataleave');
            $api_child->post('employee-get-data-leave','EmployeeLoginController@employeegetdataleave');
            $api_child->post('get-data-task','EmployeeLoginController@getdatatask');
            $api_child->post('employee-get-data-lead','EmployeeLoginController@employeegetdatalead');
            $api_child->post('lead-filter','BranchController@leadfilter');


            $api_child->get('admin-get-service','AdminServiceController@admingetservice');
            $api_child->post('admin-service','AdminServiceController@adminservice');
            $api_child->post('admin-create-service','AdminServiceController@admincreateservice');
            $api_child->post('admin-update-service','AdminServiceController@adminupdateservice');
            $api_child->post('admin-create-service-child','AdminServiceController@admincreateservicechild');
            $api_child->post('admin-update-service-child','AdminServiceController@adminupdateservicechild');
            $api_child->post('admin-delete-service','AdminServiceController@adminservicedelete');
            $api_child->post('admin-delete-service-child','AdminServiceController@adminservicedeletechild');
            $api_child->post('admin-delete-service-image','AdminServiceController@adminservicedeleteimage');
            $api_child->post('admin-service-status','AdminServiceController@adminservicestatus');

            $api_child->get('get-service','ServiceController@getservice');
            $api_child->post('service','ServiceController@service');
            $api_child->post('create-service','ServiceController@createservice');
            $api_child->post('update-service','ServiceController@updateservice');
            $api_child->post('create-service-child','ServiceController@createservicechild');
            $api_child->post('update-service-child','ServiceController@updateservicechild');
            $api_child->post('delete-service','ServiceController@servicedelete');
            $api_child->post('delete-service-child','ServiceController@servicedeletechild');
            $api_child->post('delete-service-image','ServiceController@servicedeleteimage');
            $api_child->post('service-status','ServiceController@servicestatus');
            $api_child->post('service-filter','AdminServiceController@servicefilter');
            $api_child->post('task-filter','AdminServiceController@taskfilter');
            $api_child->post('quotation-filter','AdminServiceController@quotationfilter');
            $api_child->post('attendance-filter','AdminServiceController@attendancefilter');
            $api_child->post('leave-filter','AdminServiceController@leavefilter');
            $api_child->post('client-filter','AdminServiceController@clientfilter');

            $api_child->get('get-sourcename','SourceNameController@getsourcename');
            $api_child->post('create-sourcename','SourceNameController@createsourcename');
            $api_child->post('update-sourcename','SourceNameController@updatesourcename');
            $api_child->post('delete-sourcename','SourceNameController@deletesourcename');
            $api_child->post('status-sourcename','SourceNameController@statussourcename');

            $api_child->get('employee-get-sourcename','EmployeeSourceController@employeegetsourcename');
            $api_child->post('employee-create-sourcename','EmployeeSourceController@employeecreatesourcename');
            $api_child->post('employee-update-sourcename','EmployeeSourceController@employeeupdatesourcename');
            $api_child->post('employee-delete-sourcename','EmployeeSourceController@employeedeletesourcename');
            $api_child->post('employee-status-sourcename','EmployeeSourceController@employeestatussourcename');

        });
});
});