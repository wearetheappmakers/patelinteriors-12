<?php



namespace App\Http\Controllers\Api\V1;



use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use JWTAuth;

use App\Employee;

use App\Section;

use App\AMCChild;

use App\LeadStage;

use App\LeadSource;

use App\ContactMaster;

use DB;

use App\Distributor;

use App\Product;

use JWTAuthException;

use App\User;

use App\Client;

use App\EmployeeChild;

use App\QuotationChild;

use Carbon\Carbon;
use App\SourceName;





class EmployeeSourceController extends Controller

{
    public function __construct(Employee $employee)

    {

        $this->user = $employee;

        \Config::set('jwt.user', "App\Employee");

        \Config::set('auth.providers', ['users' => [

            'driver' => 'eloquent',

            'model' => \App\Employee::class,

        ]]);

    }

      public function employeegetleadsource()

    {

       

            $leadsource = LeadSource::where('branch_id',JWTAuth::user()->branch_id)->get();

        



        return response()->json(['success'=>1,'data' => $leadsource]);

    }



    public function employeecreateleadsource(Request $request)

    {

         $this->validate($request, [

            'name' => 'required',

            

         ]);
            $leadsource = new LeadSource();
       
             $leadsource->branch_id = JWTAuth::user()->branch_id;
             $leadsource->name = $request->name;
        


            $leadsource->save();



         return response()->json(['success'=>1,'message' => 'Created LeadSource Successfully']);

    }



    public function employeeupdateleadsource(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'name' => 'required',

            

         ]);

        $leadsource = LeadSource::where('id',$request->id)->first();

      
             $leadsource->branch_id = JWTAuth::user()->branch_id;
             $leadsource->name = $request->name;
        

        $leadsource->save();



         return response()->json(['success'=>1,'message' => 'Updated LeadSource Successfully']);

    }



    public function employeedeleteleadsource(Request $request)

    {

        $this->validate($request, [

            'id' => 'required',

            

            

         ]);



        $leadsource = LeadSource::where('id',$request->id)->delete();



         return response()->json(['success'=>1,'message' => 'Deleted LeadSource Successfully']);



    }



    public function employeestatusleadsource(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'status' => 'required',

            

         ]);

        $leadsource = leadsource::where('id',$request->id)->first();

        $leadsource->status = $request->status;

        $leadsource->save();



         return response()->json(['success'=>1,'message' => 'Updated Status Successfully']);

    }



   public function employeegetsourcename()

    {

       

            $sourcename = SourceName::where('branch_id',JWTAuth::user()->branch_id)->get();

        



        return response()->json(['success'=>1,'data' => $sourcename]);

    }



    public function employeecreatesourcename(Request $request)

    {

         $this->validate($request, [

            'name' => 'required',

            

         ]);

        $sourcename = new SourceName();
       
        $sourcename->branch_id = JWTAuth::user()->branch_id;
        $sourcename->name = $request->name;
        

        $sourcename->address = $request->address;
        $sourcename->phoneno = $request->phoneno;
        $sourcename->email = $request->email;
        $sourcename->birth_date = $request->birth_date;
        $sourcename->anniversary_date = $request->anniversary_date;
        $sourcename->status = 1;
        $sourcename->save();



         return response()->json(['success'=>1,'message' => 'Created SourceName Successfully']);

    }



    public function employeeupdatesourcename(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'name' => 'required',

            

         ]);

        $sourcename = SourceName::where('id',$request->id)->first();

      
        $sourcename->branch_id = JWTAuth::user()->branch_id;
        $sourcename->name = $request->name;
        

        $sourcename->save();



         return response()->json(['success'=>1,'message' => 'Updated SourceName Successfully']);

    }



    public function employeedeletesourcename(Request $request)

    {

        $this->validate($request, [

            'id' => 'required',

            

            

         ]);



        $sourcename = SourceName::where('id',$request->id)->delete();



         return response()->json(['success'=>1,'message' => 'Deleted SourceName Successfully']);



    }



    public function employeestatussourcename(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'status' => 'required',

            

         ]);

        $sourcename = SourceName::where('id',$request->id)->first();

        $sourcename->status = $request->status;

        $sourcename->save();



         return response()->json(['success'=>1,'message' => 'Updated Status Successfully']);

    }



    

	



}