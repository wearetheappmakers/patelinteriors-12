<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\Employee;
use App\Attendance;
use App\Supervisor;
use App\ContactMaster;
use DB;
use JWTAuthException;
use App\User;
use App\Complain;
use App\Product;
use App\ProductCategory;
use Carbon\Carbon;

class AdminController extends Controller
{
	public function login(Request $request)
	{
		$credentials = request(['email', 'password']);
		if (! $token = JWTAuth::attempt($credentials)) {
			return response()->json(['success' => 0, 'message' => 'These credentials do not match our records.'], 200);
		}
		$user = JWTAuth::user();
		if(JWTAuth::user()->role == 1)
		{
			$check = User::where('email',$request->email)->first();
			if($request->push_token)
			{
				$check->push_token = $request->push_token;
				$check->save();
			}
			else
			{
				$check->push_token = NULL;
				$check->save();
			}
			return response()->json(['success' => 1, 'token' => $token,'user'=>$check,'message'=>'Login Successfully.']);
		}

		else if(JWTAuth::user()->role == 2)
		{
			$check = User::where('email',$request->email)->first();
			if($request->push_token)
			{
				$check->push_token = $request->push_token;
				$check->save();
			}
			else
			{
				$check->push_token = NULL;
				$check->save();
			}
			return response()->json(['success' => 1, 'token' => $token,'user'=>$check,'message'=>'Login Successfully.']);
		}
		else
		{
			return response()->json(['success' => 0, 'message'=>'Error.']);
		}


	}
	
	public function getattendance()
	{
		$data = Attendance::with('employee','branch')->latest()->get();
		if(JWTAuth::user()->role == 2)
		{
			$data = Attendance::with('employee','branch')->where('branch_id',JWTAuth::user()->id)->latest()->get();
		}
		return response()->json(['success'=>1,'data' => $data]);
	}
	
	public function getuser()
	{
		$data = User::where('id',JWTAuth::user()->id)->first();
		return response()->json(['success'=>1,'data' => $data]);
	}
	
	public function getproductcategory()
	{
		$productcategory = ProductCategory::get();
		return response()->json(['success'=>1,'data' => $productcategory]);
	}
	
	public function createproductcategory(Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
		]);
		$productcategory = new ProductCategory();
		$productcategory->name = $request->name;
		$productcategory->save();
		return response()->json(['success'=>1,'message' => 'Created Product Category Successfully.']);
	}
	
	public function updateproductcategory(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'name' => 'required',
		]);
		$productcategory = ProductCategory::where('id',$request->id)->first();
		$productcategory->name = $request->name;
		$productcategory->save();
		return response()->json(['success'=>1,'message' => 'Updated Product Category Successfully.']);
	}
	
	public function deleteproductcategory(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
		]);
		$productcategory = ProductCategory::where('id',$request->id)->delete();


		return response()->json(['success'=>1,'message' => 'Deleted Product Category Successfully.']);
	}
	
	public function productcategorystatus(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'status' => 'required',
		]);
		$productcategory = ProductCategory::where('id',$request->id)->first();
		$productcategory->status = $request->status;
		$productcategory->save();
		return response()->json(['success'=>1,'message' => ' Status Updated Product Category Successfully.']);
	}
	
	public function getproduct()
	{
		$product = Product::get();
		return response()->json(['success'=>1,'data' => $product]);
	}
	
	public function createproduct(Request $request)
	{
		$this->validate($request, [
			'product' => 'required',

		]);
		$product = new Product();
		$product->productcategory_id = $request->productcategory_id;
		$product->product = $request->product;
		$product->unit = $request->unit;
		$product->code = $request->code;
		$product->price = $request->price;
		$product->description = $request->description;
		$product->discount = $request->discount;
		$product->discount_type = $request->discount_type;
		if(isset($request['image1']))
		{
			$imageName = time().'-'. $request->image1->getClientOriginalName();
			$request->image1->move(public_path('product/'),$imageName);
			$product->image1 = $imageName;
		}
		if(isset($request['image2']))
		{
			$imageName = time().'-'. $request->image2->getClientOriginalName();
			$request->image2->move(public_path('product/'),$imageName);
			$product->image2 = $imageName;
		}
		$product->save();
		return response()->json(['success'=>1,'data' => $product, 'message' => 'Created Product Successfully']);
	}
	
	public function updateproduct(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'product' => 'required',

		]);
		$product = Product::where('id',$request->id)->first();
		$product->productcategory_id = $request->productcategory_id;
		$product->product = $request->product;
		$product->unit = $request->unit;
		$product->code = $request->code;
		$product->price = $request->price;
		$product->description = $request->description;
		$product->discount = $request->discount;
		$product->discount_type = $request->discount_type;
		if(isset($request['image1']))
		{
			$imageName = time().'-'. $request->image1->getClientOriginalName();
			$request->image1->move(public_path('product/'),$imageName);
			$product->image1 = $imageName;
		}
		if(isset($request['image2']))
		{
			$imageName = time().'-'. $request->image2->getClientOriginalName();
			$request->image2->move(public_path('product/'),$imageName);
			$product->image2 = $imageName;
		}
		$product->save();
		return response()->json(['success'=>1,'data' => $product, 'message' => 'Updated Product Successfully']);
	}
	
	public function deleteproduct(Request $request)
	{
		$product = Product::where('id',$request->id)->delete();
		return response()->json(['success'=>1, 'message' => 'Deleted Product Successfully']);
	}
	
	public function productstatus(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'status' => 'required',
		]);
		$product = Product::where('id',$request->id)->first();
		$product->status = $request->status;
		$product->save();
		return response()->json(['success'=>1,'message' => ' Status Change Successfully.']);
	}
	
	public function getcomplain()
	{
		$complain = Complain::get();
		return response()->json(['success'=>1,'data' =>$complain]);
	}
	
	public function createcomplain(Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
		]);
		$complain = new Complain();

		if(JWTAuth::user()->role == 1)
		{
			$complain->branch_id = $request->branch_id;
			$complain->employee_id = $request->employee_id;
		}
		else if(JWTAuth::user()->role == 2)
		{
			$complain->branch_id = JWTAuth::user()->id;
			$complain->employee_id = $request->employee_id;
		}
		else
		{
			$complain->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
			$complain->employee_id = JWTAuth::user()->id;
		}
		$complain->name = $request->name;
		$complain->mobile_no = $request->mobile_no;
		$complain->company = $request->company;
		$complain->address = $request->address;
		$complain->problem = $request->problem;
		if(isset($request['image']))
		{
			$imageName = time().'-'. $request->image->getClientOriginalName();
			$request->image->move(public_path('complain/'),$imageName);
			$complain->image = $imageName;
		}
		$complain->reference_no = $request->reference_no;
		$complain->note = $request->note;
		$complain->save();
		return response()->json(['success'=>1,'message' => 'Created Complain Successfully.']);
	}
	
	public function updatecomplain(Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
			'id' => 'required',
		]);
		$complain = Complain::where('id',$request->id)->first();
		$complain->name = $request->name;
		if(JWTAuth::user()->role == 1)
		{
			$complain->branch_id = $request->branch_id;
			$complain->employee_id = $request->employee_id;
		}
		else if(JWTAuth::user()->role == 2)
		{
			$complain->branch_id = JWTAuth::user()->id;
			$complain->employee_id = $request->employee_id;
		}
		else
		{
			$complain->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
			$complain->employee_id = JWTAuth::user()->id;
		}
		$complain->mobile_no = $request->mobile_no;
		$complain->company = $request->company;
		$complain->address = $request->address;
		$complain->problem = $request->problem;
		$complain->reference_no = $request->reference_no;
		$complain->note = $request->note;
		if(isset($request['image']))
		{
			$imageName = time().'-'. $request->image->getClientOriginalName();
			$request->image->move(public_path('complain/'),$imageName);
			$complain->image = $imageName;
		}
		$complain->save();
		return response()->json(['success'=>1,'message' => 'Updated Complain Successfully.']);
	}
	
	public function deletecomplain(Request $request)
	{
		$complain = Complain::where('id',$request->id)->delete();
		return response()->json(['success'=>1,'message' => 'Deleted Complain Successfully.']);
	}
	
	public function statuscomplain(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'status' => 'required',
		]);
		$complain = Complain::where('id',$request->id)->first();
		$complain->status = $request->status;
		$complain->save();
		return response()->json(['success'=>1,'message' => ' Status Change Successfully.']);
	}
	
	public function getsupervisor()
	{
		$supervisor = Supervisor::get();
		return response()->json(['success'=>1,'data' => $supervisor]);
	}
	
	public function createsupervisor(Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
		]);
		$supervisor = new Supervisor();
		$supervisor->name = $request->name;
		$supervisor->save();
		return response()->json(['success'=>1,'message' => 'Created Supervisor Successfully.']);
	}
	
	public function updatesupervisor(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'name' => 'required',
		]);
		$supervisor = Supervisor::where('id',$request->id)->first();
		$supervisor->name = $request->name;
		$supervisor->save();
		return response()->json(['success'=>1,'message' => 'Updated Supervisor Successfully.']);
	}
	
	public function deletesupervisor(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
		]);
		$supervisor = Supervisor::where('id',$request->id)->delete();


		return response()->json(['success'=>1,'message' => 'Deleted Supervisor Successfully.']);
	}
	
	public function statussupervisor(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'status' => 'required',
		]);
		$supervisor = Supervisor::where('id',$request->id)->first();
		$supervisor->status = $request->status;
		$supervisor->save();
		return response()->json(['success'=>1,'message' => ' Status Change Successfully.']);
	}
	
	public function getcontact()
	{
		$contact = ContactMaster::get();
		return response()->json(['success'=>1,'data' => $contact]);
	}
	
	public function createcontact(Request $request)
	{
		$this->validate($request, [
			'lead_source_id' => 'required',
			'name' => 'required',
			'company' => 'required',
			'mobile_no' => 'required',

		]);
		$contact = new ContactMaster();
		if(JWTAuth::user()->role == 1)
		{
			$contact->branch_id = $request->branch_id;
			$contact->employee_id = $request->employee_id;
		}
		else if(JWTAuth::user()->role == 2)
		{
			$contact->branch_id = JWTAuth::user()->id;
			$contact->employee_id = $request->employee_id;
		}
		else
		{
			$contact->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
			$contact->employee_id = JWTAuth::user()->id;
		}
		$contact->lead_source_id = $request->lead_source_id;
		$contact->name = $request->name;
		$contact->company = $request->company;
		$contact->mobile_no = $request->mobile_no;
		$contact->email = $request->email;
		$contact->address = $request->address;
		$contact->save();
		return response()->json(['success'=>1,'message' => 'Created Contact Successfully']);
	}
	
	public function updatecontact(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'lead_source_id' => 'required',
			'name' => 'required',
			'company' => 'required',
			'mobile_no' => 'required',

		]);
		$contact = ContactMaster::where('id',$request->id)->first();
		if(JWTAuth::user()->role == 1)
		{
			$contact->branch_id = $request->branch_id;
			$contact->employee_id = $request->employee_id;
		}
		else if(JWTAuth::user()->role == 2)
		{
			$contact->branch_id = JWTAuth::user()->id;
			$contact->employee_id = $request->employee_id;
		}
		else
		{
			$contact->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
			$contact->employee_id = JWTAuth::user()->id;
		}
		$contact->lead_source_id = $request->lead_source_id;
		$contact->name = $request->name;
		$contact->company = $request->company;
		$contact->mobile_no = $request->mobile_no;
		$contact->email = $request->email;
		$contact->address = $request->address;
		$contact->save();
		return response()->json(['success'=>1,'message' => 'Updated Contact Successfully']);
	}
	
	public function deletecontact(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',


		]);
		$contact = ContactMaster::where('id',$request->id)->delete();

		return response()->json(['success'=>1,'message' => 'Deleted Contact Successfully']);
	}
	
	public function statuscontact(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'status' => 'required',
		]);
		$contact = ContactMaster::where('id',$request->id)->first();
		$contact->status = $request->status;
		$contact->save();
		return response()->json(['success'=>1,'message' => ' Status Change Successfully.']);
	}
	
	public function getemployeeattendance()
	{
		if(JWTAuth::user()->role == 1)
		{
			$attendance = Attendance::with('employee','branch')->orderBy('id', 'DESC')->get();
		}
		elseif(JWTAuth::user()->role == 2)
		{
			$attendance = Attendance::with('employee','branch')->where('branch_id',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
		}
		else
		{
			$attendance = Attendance::with('employee','branch')->where('employee_id',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
		}
		return response()->json(['success'=>1,'data' => $attendance]);
	}
}