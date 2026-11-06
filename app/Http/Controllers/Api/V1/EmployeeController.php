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





class EmployeeController extends Controller

{

    





    public function getemployee()

    {

        $employee = Employee::latest()->get();



        if(JWTAuth::user()->role == 2)

            {

               $employee = Employee::where('branch_id',JWTAuth::user()->id)->latest()->get();

            }



        return response()->json(['success'=>1,'data' => $employee]);



    }



    public function createemployee(Request $request)

    {

        $this->validate($request, [

            'first_name' => 'required',

            'last_name' => 'required',

            'email' => 'required',

            'confirm_password' => 'required',

        ]);

        $employee = new Employee();

         

            if(JWTAuth::user()->role == 1)

            {

                $employee->branch_id = $request->branch_id;

                $employee->employee_id = $request->employee_id;

            }

            else if(JWTAuth::user()->role == 2)

            {

                $employee->branch_id = JWTAuth::user()->id;

                $employee->employee_id = $request->employee_id;

            }

            

        

        else

        {

            $employee->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');

            $employee->employee_id = JWTAuth::user()->id;

        }

        $employee->first_name = $request->first_name;

        $employee->supervisor_id = $request->supervisor_id;

        $employee->last_name = $request->last_name;

        $employee->name = $request->name;

        $employee->role = $request->role;

        $employee->gender = $request->gender;

        $employee->email = $request->email;

        $employee->mobile_no = $request->mobile_no;

        $employee->other_mobile_no = $request->other_mobile_no;

        $employee->address = $request->address;

        $employee->password = $request->password;

        $employee->confirm_password = $request->confirm_password;

        $employee->dob = $request->dob;

        $employee->join_date = $request->join_date;

        if(isset($request['image']))

        {

            $imageName = time().'-'. $request->image->getClientOriginalName();

            $request->image->move(public_path('employee/'),$imageName);

            $employee->image = $imageName;

        }

        $employee->save();



        return response()->json(['success'=>1,'message' => 'Created Employee Successfully.']);

    }



    public function createemployeechild(Request $request)

    {

        $this->validate($request, [

            'employee_id' => 'required',

            

        ]);

        $employeechild = new EmployeeChild();

        $employeechild->employee_id = $request->employee_id;

        $employeechild->name = $request->name;

        $employeechild->save();



        return response()->json(['success'=>1,'message' => 'Created Employee Child Successfully.']);

    }



    public function updateemployee(Request $request)

    {

        $this->validate($request, [

            'id' => 'required',

            'first_name' => 'required',

            'last_name' => 'required',

            'email' => 'required',

            'confirm_password' => 'required',

        ]);

        $employee = Employee::where('id',$request->id)->first();

         

            if(JWTAuth::user()->role == 1)

            {

                $employee->branch_id = $request->branch_id;

                $employee->employee_id = $request->employee_id;

            }

            else if(JWTAuth::user()->role == 2)

            {

                $employee->branch_id = JWTAuth::user()->id;

                $employee->employee_id = $request->employee_id;

            }

            

        

        else

        {

            $employee->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');

            $employee->employee_id = JWTAuth::user()->id;

        }

        $employee->first_name = $request->first_name;

        $employee->supervisor_id = $request->supervisor_id;

        $employee->last_name = $request->last_name;

        $employee->name = $request->name;

        $employee->gender = $request->gender;

        $employee->email = $request->email;

        $employee->role = $request->role;

        $employee->mobile_no = $request->mobile_no;

        $employee->other_mobile_no = $request->other_mobile_no;

        $employee->address = $request->address;

        $employee->password = $request->password;

        $employee->confirm_password = $request->confirm_password;

        $employee->dob = $request->dob;

        $employee->join_date = $request->join_date;

        if(isset($request['image']))

        {

            $imageName = time().'-'. $request->image->getClientOriginalName();

            $request->image->move(public_path('employee/'),$imageName);

            $employee->image = $imageName;

        }

        $employee->save();



        return response()->json(['success'=>1,'message' => 'Updated Employee Successfully.']);

    }



    public function updateemployeechild(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'employee_id' => 'required',

            

        ]);

        $employeechild = EmployeeChild::where('id',$request->id)->first();

        $employeechild->employee_id = $request->employee_id;

        $employeechild->name = $request->name;

        $employeechild->save();



        return response()->json(['success'=>1,'message' => 'Updated Employee Child Successfully.']);

    }



    public function deleteemployee(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

         ]);

        $employee = Employee::where('id',$request->id)->delete();



         return response()->json(['success'=>1,'message' => 'Deleted Employee Successfully.']);

    }

    

     public function deleteemployeechild(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

         ]);

        $employeechild = EmployeeChild::where('id',$request->id)->delete();



         return response()->json(['success'=>1,'message' => 'Deleted Employee Child Successfully.']);

    }



    public function createdistributor(Request $request)

    {

         $this->validate($request, [

            'first_name' => 'required',

            'last_name' => 'required',

            'email' => 'required',

            'confirm_password' => 'required',

         ]);



         $distributor = new Distributor();

          

            if(JWTAuth::user()->role == 1)

            {

                $distributor->branch_id = $request->branch_id;

                $distributor->employee_id = $request->employee_id;

            }

            else if(JWTAuth::user()->role == 2)

            {

                $distributor->branch_id = JWTAuth::user()->id;

                $distributor->employee_id = $request->employee_id;

            }

            

        

        else

        {

            $distributor->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');

            $distributor->employee_id = JWTAuth::user()->id;

        }

         $distributor->first_name = $request->first_name;

         $distributor->supervisor_id = $request->supervisor_id;

         $distributor->last_name = $request->last_name;

         $distributor->name = $request->name;

         $distributor->gender = $request->gender;

         $distributor->email = $request->email;

         $distributor->mobile_no = $request->mobile_no;

         $distributor->other_mobile_no = $request->other_mobile_no;

         $distributor->address = $request->address;

         $distributor->password = $request->password;

         $distributor->confirm_password = $request->confirm_password;

         $distributor->dob = $request->dob;

         $distributor->join_date = $request->join_date;

         if(isset($request['image']))

         {

            $imageName = time().'-'. $request->image->getClientOriginalName();

            $request->image->move(public_path('distributor/'),$imageName);

            $distributor->image = $imageName;

        }

        $distributor->save();



         return response()->json(['success'=>1,'message' => 'Created Distributor Successfully.']);

    }



    public function getdistributor()

    {

        $distributor = Distributor::get();

         return response()->json(['success'=>1,'data' => $distributor]);

    }



    public function updatedistributor(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'first_name' => 'required',

            'last_name' => 'required',

            'email' => 'required',

            'confirm_password' => 'required',

         ]);



         $distributor = Distributor::where('id',$request->id)->first();

          

            if(JWTAuth::user()->role == 1)

            {

                $distributor->branch_id = $request->branch_id;

                $distributor->employee_id = $request->employee_id;

            }

            else if(JWTAuth::user()->role == 2)

            {

                $distributor->branch_id = JWTAuth::user()->id;

                $distributor->employee_id = $request->employee_id;

            }

            

        

        else

        {

            $distributor->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');

            $distributor->employee_id = JWTAuth::user()->id;

        }

         $distributor->first_name = $request->first_name;

         $distributor->supervisor_id = $request->supervisor_id;

         $distributor->last_name = $request->last_name;

         $distributor->name = $request->name;

         $distributor->gender = $request->gender;

         $distributor->email = $request->email;

         $distributor->mobile_no = $request->mobile_no;

         $distributor->other_mobile_no = $request->other_mobile_no;

        // dd($request->other_mobile_no);

         $distributor->address = $request->address;

         $distributor->password = $request->password;

         $distributor->confirm_password = $request->confirm_password;

         $distributor->dob = $request->dob;

         $distributor->join_date = $request->join_date;

         if(isset($request['image']))

         {

            $imageName = time().'-'. $request->image->getClientOriginalName();

            $request->image->move(public_path('distributor/'),$imageName);

            $distributor->image = $imageName;

        }

        $distributor->save();



         return response()->json(['success'=>1,'message' => 'Updated Distributor Successfully']);

    }



    public function deletedistributor(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            

         ]);

        $distributor = Distributor::where('id',$request->id)->delete();



         return response()->json(['success'=>1,'message' => 'Deleted Distributor Successfully']);



    }



    public function getsection()

    {

        $section = Section::get();



        return response()->json(['success'=>1,'data' => $section]);

    }



    public function createsection(Request $request)

    {

         $this->validate($request, [

            'name' => 'required',

            

         ]);

        $section = new Section();

        $section->name = $request->name;

        $section->save();



         return response()->json(['success'=>1,'message' => 'Created Section Successfully']);

    }



    public function updatesection(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'name' => 'required',

            

         ]);

        $section = Section::where('id',$request->id)->first();

        $section->name = $request->name;

        $section->save();



         return response()->json(['success'=>1,'message' => 'Updated Section Successfully']);

    }



    public function deletesection(Request $request)

    {

        $this->validate($request, [

            'id' => 'required',

            

            

         ]);



        $section = Section::where('id',$request->id)->delete();



         return response()->json(['success'=>1,'message' => 'Deleted Section Successfully']);



    }



    public function sectionstatus(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'status' => 'required',

            

         ]);

        $section = Section::where('id',$request->id)->first();

        $section->status = $request->status;

        $section->save();



         return response()->json(['success'=>1,'message' => 'Updated Status Successfully']);

    }



    public function getleadstage(Request $request)

    {

        if(JWTAuth::user()->role == 1)

        {

             $leadstage = LeadStage::where('status',1)->where('branch_id',$request->branch_id)->get();

        }

        else if(JWTAuth::user()->role == 2)

        {

             $leadstage = LeadStage::where('status',1)->where('branch_id',JWTAuth::user()->id)->get();

        }



       



         return response()->json(['success'=>1,'data' => $leadstage]);



    }



    public function craeteleadstage(Request $request)

    {

        $this->validate($request, [

            

            'name' => 'required',

            

         ]);

        $leadstage = new LeadStage();

        $leadstage->name = $request->name;

        $leadstage->color = $request->color;

        $leadstage->is_completed = $request->is_completed;

        $leadstage->is_canceled = $request->is_canceled;



        $leadstage->save();



         return response()->json(['success'=>1,'message' => 'Created LeadStage Successfully']);



    }



    public function updateleadstage(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'name' => 'required',

            

         ]);



        $leadstage = LeadStage::where('id',$request->id)->first();

        $leadstage->name = $request->name;

        $leadstage->color = $request->color;

        $leadstage->is_completed = $request->is_completed;

        $leadstage->is_canceled = $request->is_canceled;



        $leadstage->save();



         return response()->json(['success'=>1,'message' => 'Updated LeadStage Successfully']);

        

    }



    public function deleteleadstage(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            

            

         ]);



         $leadstage = LeadStage::where('id',$request->id)->delete();



          return response()->json(['success'=>1,'message' => 'Deleted LeadStage Successfully']);





    }



    public function statusleadstage(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'status' => 'required',

            

            

         ]);



         $leadstage = LeadStage::where('id',$request->id)->first();

         $leadstage->status = $request->status;

         $leadstage->save();



          return response()->json(['success'=>1,'message' => 'Updated Status Successfully']);

    }



     public function getleadsource()

    {

        $leadsource = LeadSource::get();

        if(JWTAuth::user()->role == 2)

        {

            $leadsource = LeadSource::where('branch_id',JWTAuth::user()->id)->get();

        }



        return response()->json(['success'=>1,'data' => $leadsource]);

    }



    public function createleadsource(Request $request)

    {

         $this->validate($request, [

            'name' => 'required',

            

         ]);

        $leadsource = new LeadSource();
        if(JWTAuth::user()->role == 1)
        {
            $leadsource->branch_id = $request->branch_id;
             $leadsource->name = $request->name;

        }else
        {
             $leadsource->branch_id = JWTAuth::user()->id;
             $leadsource->name = $request->name;
        }


        $leadsource->save();



         return response()->json(['success'=>1,'message' => 'Created LeadSource Successfully']);

    }



    public function updateleadsource(Request $request)

    {

         $this->validate($request, [

            'id' => 'required',

            'name' => 'required',

            

         ]);

        $leadsource = LeadSource::where('id',$request->id)->first();

       if(JWTAuth::user()->role == 1)
        {
            $leadsource->branch_id = $request->branch_id;
             $leadsource->name = $request->name;

        }else
        {
             $leadsource->branch_id = JWTAuth::user()->id;
             $leadsource->name = $request->name;
        }

        $leadsource->save();



         return response()->json(['success'=>1,'message' => 'Updated LeadSource Successfully']);

    }



    public function deleteleadsource(Request $request)

    {

        $this->validate($request, [

            'id' => 'required',

            

            

         ]);



        $leadsource = LeadSource::where('id',$request->id)->delete();



         return response()->json(['success'=>1,'message' => 'Deleted LeadSource Successfully']);



    }



    public function statusleadsource(Request $request)

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



    

    



}