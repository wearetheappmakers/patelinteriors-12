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





class SourceNameController extends Controller

{

   public function getsourcename()

    {

        $sourcename = SourceName::get();

        if(JWTAuth::user()->role == 2)

        {

            $sourcename = SourceName::where('branch_id',JWTAuth::user()->id)->get();

        }



        return response()->json(['success'=>1,'data' => $sourcename]);

    }



    public function createsourcename(Request $request)

    {

         $this->validate($request, [

            'name' => 'required',

            

         ]);

        $sourcename = new SourceName();
        if(JWTAuth::user()->role == 1)
        {
            $sourcename->branch_id = $request->branch_id;
             $sourcename->name = $request->name;

        }else
        {
             $sourcename->branch_id = JWTAuth::user()->id;
             $sourcename->name = $request->name;
        }

        $sourcename->address = $request->address;
        $sourcename->phoneno = $request->phoneno;
        $sourcename->email = $request->email;
        $sourcename->birth_date = $request->birth_date;
        $sourcename->anniversary_date = $request->anniversary_date;
        $sourcename->status = 1;
        $sourcename->save();



         return response()->json(['success'=>1,'message' => 'Created SourceName Successfully']);

    }



    public function updatesourcename(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'name' => 'required',

            

         ]);

        $sourcename = SourceName::where('id',$request->id)->first();

       if(JWTAuth::user()->role == 1)
        {
            $sourcename->branch_id = $request->branch_id;
             $sourcename->name = $request->name;

        }else
        {
             $sourcename->branch_id = JWTAuth::user()->id;
             $sourcename->name = $request->name;
        }

        $sourcename->save();



         return response()->json(['success'=>1,'message' => 'Updated SourceName Successfully']);

    }



    public function deletesourcename(Request $request)

    {

        $this->validate($request, [

            'id' => 'required',

            

            

         ]);



        $sourcename = SourceName::where('id',$request->id)->delete();



         return response()->json(['success'=>1,'message' => 'Deleted SourceName Successfully']);



    }



    public function statussourcename(Request $request)

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