<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\AMC;
use App\ClientDocument;
use App\AMCChild;
use App\Delivery;
use App\ContactMaster;
use App\RevisedQuotation;
use App\RevisedChild;
use App\RevisedMainChild;
use DB;
use App\GST;
use App\Product;
use JWTAuthException;
use App\User;
use App\Client;
use App\Employee;
use App\Quotation;
use App\QuotationChild;
use App\QuotationMainChild;
use Carbon\Carbon;
use Auth;

class ClientController extends Controller
{
    public function getclient()
    {

        if(JWTAuth::user()->role == 1)
        {
            $client = Client::with('branch','employee','clientdocument')->latest()->get();
        }

        else
        {
            $client = Client::with('employee','branch','clientdocument')->where('branch_id',JWTAuth::user()->id)->latest()->get();
        }
        return response()->json(['success'=>1,'data' => $client]);
    }

    public function createclient(Request $request)
    {
        $this->validate($request, [
            'member' => 'required',
        ]);
        $client = new Client();

        if(JWTAuth::user()->role == 1)
        {
            $client->branch_id = $request->branch_id;
            $client->employee_id = $request->employee_id;
        }
        else if(JWTAuth::user()->role == 2)
        {
            $client->branch_id = JWTAuth::user()->id;
            $client->employee_id = $request->employee_id;
        }


        else
        {
            $client->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
            $client->employee_id = JWTAuth::user()->id;
        }
        $client->member = $request->member;
        $client->address = $request->address;
        $client->city = $request->city;
        $client->mobile_no = $request->mobile_no;
        $client->other_mobile_no = $request->other_mobile_no;
        $client->email = $request->email;
        $client->referred_type = $request->referred_type;
        $client->dob = $request->dob;
        $client->is_archive = $request->is_archive ?? 0;

        $client->save();

        return response()->json(['success'=>1,'message' => 'Created Client Successfully.']);
    }

    public function updateclient(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'member' => 'required',
        ]);

        $client = Client::where('id',$request->id)->first();

        if(JWTAuth::user()->role == 1)
        {
            $client->branch_id = $request->branch_id;
            $client->employee_id = $request->employee_id;
        }
        else if(JWTAuth::user()->role == 2)
        {
            $client->branch_id = JWTAuth::user()->id;
            $client->employee_id = $request->employee_id;
        }


        else
        {
            $client->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
            $client->employee_id = JWTAuth::user()->id;
        }
        $client->member = $request->member;
        $client->address = $request->address;
        $client->city = $request->city;
        $client->mobile_no = $request->mobile_no;
        $client->other_mobile_no = $request->other_mobile_no;
        $client->email = $request->email;
        $client->dob = $request->dob;
        $client->is_archive = $request->is_archive ?? 0;

        $client->save();

        return response()->json(['success'=>1,'message' => 'Updated Client Successfully.']);
    }

    public function deleteclient(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $client = Client::where('id',$request->id)->delete();



        return response()->json(['success'=>1,'message' => 'Deleted Client Successfully.']);
    }

    public function createclientdocument(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required',
        ]);

        $clientdocument = ClientDocument::where('client_id',$request->client_id)->first();
        if($clientdocument)
        {
            $clientdocument->client_id = $request->client_id;
            $clientdocument->distributor_id = $request->distributor_id;
            $clientdocument->status = $request->status;
            $clientdocument->tracking_no = $request->tracking_no;
            $clientdocument->tracking_link = $request->tracking_link;
            $clientdocument->date = $request->date;
            $clientdocument->remark = $request->remark;
            $clientdocument->aadhar_card = $request->aadhar_card;
            if($request->aadhar_img)
            {
                $imageName = time().'.'.$request->aadhar_img->getClientOriginalExtension();
                $request->aadhar_img->move(public_path('aadharimage/'), $imageName);
                $clientdocument['aadhar_img'] = $imageName;
            }
            $clientdocument->pan_card = $request->pan_card;
            if($request->pan_img)
            {
                $imageName = time().'.'.$request->pan_img->getClientOriginalExtension();
                $request->pan_img->move(public_path('panimage/'), $imageName);
                $clientdocument['pan_img'] = $imageName;
            }
            $clientdocument->cancelled_cheque = $request->cancelled_cheque;
            if($request->cancelledcheque_img)
            {
                $imageName = time().'.'.$request->cancelledcheque_img->getClientOriginalExtension();
                $request->cancelledcheque_img->move(public_path('cancelledchequeimage/'), $imageName);
                $clientdocument['cancelledcheque_img'] = $imageName;
            }
            $clientdocument->rtgs_details = $request->rtgs_details;
            if($request->rtgs_img)
            {
                $imageName = time().'.'.$request->rtgs_img->getClientOriginalExtension();
                $request->rtgs_img->move(public_path('rtgsimage/'), $imageName);
                $clientdocument['rtgs_img'] = $imageName;
            }
            $clientdocument->form_submission = $request->form_submission;
            if($request->form_submission_img)
            {
                $imageName = time().'.'.$request->form_submission_img->getClientOriginalExtension();
                $request->form_submission_img->move(public_path('formsubmissionimage/'), $imageName);
                $clientdocument['form_submission_img'] = $imageName;
            }
            $clientdocument->machine_delivered = $request->machine_delivered;
            if($request->machine_delivered_img)
            {
                $imageName = time().'.'.$request->machine_delivered_img->getClientOriginalExtension();
                $request->machine_delivered_img->move(public_path('machinedeliveredimage/'), $imageName);
                $clientdocument['machine_delivered_img'] = $imageName;
            }
            $clientdocument->gst = $request->gst;
            if($request->gst_img)
            {
                $imageName = time().'.'.$request->gst_img->getClientOriginalExtension();
                $request->gst_img->move(public_path('gstimage/'), $imageName);
                $clientdocument['gst_img'] = $imageName;
            }
            $clientdocument->save();

        }
        else
        {
            $clientdocument = new ClientDocument();
            $clientdocument->client_id = $request->client_id;
            $clientdocument->product_id = $request->product_id;
            $clientdocument->lead_id = $request->lead_id;
            $clientdocument->distributor_id = $request->distributor_id;
            $clientdocument->status = $request->status;
            $clientdocument->tracking_no = $request->tracking_no;
            $clientdocument->tracking_link = $request->tracking_link;
            $clientdocument->date = $request->date;
            $clientdocument->remark = $request->remark;
            $clientdocument->aadhar_card = $request->aadhar_card;
            if($request->aadhar_img)
            {
                $imageName = time().'.'.$request->aadhar_img->getClientOriginalExtension();
                $request->aadhar_img->move(public_path('aadharimage/'), $imageName);
                $clientdocument['aadhar_img'] = $imageName;
            }
            $clientdocument->pan_card = $request->pan_card;
            if($request->pan_img)
            {
                $imageName = time().'.'.$request->pan_img->getClientOriginalExtension();
                $request->pan_img->move(public_path('panimage/'), $imageName);
                $clientdocument['pan_img'] = $imageName;
            }
            $clientdocument->cancelled_cheque = $request->cancelled_cheque;
            if($request->cancelledcheque_img)
            {
                $imageName = time().'.'.$request->cancelledcheque_img->getClientOriginalExtension();
                $request->cancelledcheque_img->move(public_path('cancelledchequeimage/'), $imageName);
                $clientdocument['cancelledcheque_img'] = $imageName;
            }
            $clientdocument->rtgs_details = $request->rtgs_details;
            if($request->rtgs_img)
            {
                $imageName = time().'.'.$request->rtgs_img->getClientOriginalExtension();
                $request->rtgs_img->move(public_path('rtgsimage/'), $imageName);
                $clientdocument['rtgs_img'] = $imageName;
            }
            $clientdocument->form_submission = $request->form_submission;
            if($request->form_submission_img)
            {
                $imageName = time().'.'.$request->form_submission_img->getClientOriginalExtension();
                $request->form_submission_img->move(public_path('formsubmissionimage/'), $imageName);
                $clientdocument['form_submission_img'] = $imageName;
            }
            $clientdocument->machine_delivered = $request->machine_delivered;
            if($request->machine_delivered_img)
            {
                $imageName = time().'.'.$request->machine_delivered_img->getClientOriginalExtension();
                $request->machine_delivered_img->move(public_path('machinedeliveredimage/'), $imageName);
                $clientdocument['machine_delivered_img'] = $imageName;
            }
            $clientdocument->gst = $request->gst;
            if($request->gst_img)
            {
                $imageName = time().'.'.$request->gst_img->getClientOriginalExtension();
                $request->gst_img->move(public_path('gstimage/'), $imageName);
                $clientdocument['gst_img'] = $imageName;
            }
            $clientdocument->save();
        }
        return response()->json(['success'=>1,'message' => 'Created Client Document Successfully.']);
    }

    public function getemployeeadmin(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required',
        ]);

        $employee = Employee::where('branch_id',$request->branch_id)->get();
        return response()->json(['success'=>1,'data' => $employee]);

    }

    public function getamc()
    {


        if(JWTAuth::user()->role == 1)
        {
            $amc = AMC::with('child.employee','product','client')->latest()->get();
        }

        else if(JWTAuth::user()->role == 2)
        {
            $amc = AMC::with('child.employee','product','client')->where('branch_id',JWTAuth::user()->id)->latest()->get();

        }
        else
        {
            $branch_id = Employee::where('id',JWTAuth::user()->id)->first();
            $amc = AMC::with('child.employee','product','client')->where('branch_id',$branch_id)->latest()->get();
        }


        return response()->json(['success'=>1,'data' => $amc]);

    }

    public function createamc(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required',
            'product_id' => 'required',
            'sales_date' => 'required',
            'sales_amount' => 'required',
            'amc_date' => 'required',
            'amc_amount' => 'required',
        ]);
        $amc = new AMC();

        if(JWTAuth::user()->role == 1)
        {
            $amc->branch_id = $request->branch_id;
            $amc->employee_id = $request->employee_id;
        }
        else if(JWTAuth::user()->role == 2)
        {
            $amc->branch_id = JWTAuth::user()->id;
            $amc->employee_id = $request->employee_id;
        }


        else
        {
            $amc->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
            $amc->employee_id = JWTAuth::user()->id;
        }
        $amc->client_id = $request->client_id;
        $amc->product_id = $request->product_id;
        $amc->sales_date = $request->sales_date;
        $amc->sales_amount = $request->sales_amount;
        $amc->amc_date = $request->amc_date;
        $amc->amc_amount = $request->amc_amount;
        $amc->status = $request->status;
        $amc->save();




        return response()->json(['success'=>1,'message' => 'Created AMC Successfully.']);
    }

    public function createamcchild(Request $request)
    {
        $this->validate($request, [

            'amc_id' => 'required',

        ]);

        $child = new AMCChild();
        $child->amc_id = $request->amc_id;
        $child->schedule_type = $request->schedule_type;
        $child->employee_id = $request->employee_id;
        $child->schedule_date = $request->schedule_date;
        $child->schedule_time = $request->schedule_time;
        $child->status = $request->status ?? 0;
        $child->remark = $request->remark;
        $child->save();


        return response()->json(['success'=>1,'message' => 'Created AMC Child Successfully.']);
    }

    public function updateamc(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'client_id' => 'required',
            'product_id' => 'required',
            'sales_date' => 'required',
            'sales_amount' => 'required',
            'amc_date' => 'required',
            'amc_amount' => 'required',
        ]);
        $amc = AMC::where('id',$request->id)->first();

        if(JWTAuth::user()->role == 1)
        {
            $amc->branch_id = $request->branch_id;
            $amc->employee_id = $request->employee_id;
        }
        else if(JWTAuth::user()->role == 2)
        {
            $amc->branch_id = JWTAuth::user()->id;
            $amc->employee_id = $request->employee_id;
        }


        else
        {
            $amc->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
            $amc->employee_id = JWTAuth::user()->id;
        }
        $amc->client_id = $request->client_id;
        $amc->product_id = $request->product_id;
        $amc->sales_date = $request->sales_date;
        $amc->sales_amount = $request->sales_amount;
        $amc->amc_date = $request->amc_date;
        $amc->amc_amount = $request->amc_amount;
        $amc->status = $request->status;
        $amc->save();

        return response()->json(['success'=>1,'message' => 'Updated AMC Successfully.']);
    }

    public function updateamcchild(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'amc_id' => 'required',

        ]);

        $child = AMCChild::where('id',$request->id)->first();
        $child->amc_id = $request->amc_id;
        $child->schedule_type = $request->schedule_type;
        $child->employee_id = $request->employee_id;
        $child->schedule_date = $request->schedule_date;
        $child->schedule_time = $request->schedule_time;
        $child->status = $request->status ?? 0;
        $child->remark = $request->remark;
        $child->save();


        return response()->json(['success'=>1,'message' => 'Updated AMC Child Successfully.']);
    }

    public function deleteamc(Request $request)
    {
        $amc = AMC::where('id',$request->id)->delete();

        return response()->json(['success'=>1,'message' => 'Deleted AMC Successfully.']);

    }

    public function deleteamcchild(Request $request)
    {
        $amcchild = AMCChild::where('id',$request->id)->delete();

        return response()->json(['success'=>1,'message' => 'Deleted AMC Child Successfully.']);
    }

    public function getquotationproduct(Request $request)
    {
        $this->validate($request, [
            'productcategory_id' => 'required',


        ]);

        $product = Product::where('productcategory_id',$request->productcategory_id)->get();

        return response()->json(['success'=>1,'data' => $product]);

    }

    public function getquotationprice(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required',


        ]);

        $price = Product::where('id',$request->product_id)->get();

        return response()->json(['success'=>1,'data' => $price]);

    }

    public function createadminquotation(Request $request)
    {
        $this->validate($request, [
            'lead_id' => 'required',			

        ]);
        $quotation = new Quotation();
        $quotation->lead_id = $request->lead_id;

        if(JWTAuth::user()->role == 1)
        {
            $quotation->branch_id = $request->branch_id;
            $quotation->employee_id = $request->employee_id;
        }
        else if(JWTAuth::user()->role == 2)
        {
            $quotation->branch_id = JWTAuth::user()->id;
            $quotation->employee_id = $request->employee_id;
        }

        $quotation->client_id = $request->client_id;
        $quotation->quotation_date = $request->quotation_date;
        $quotation->status = $request->status ?? 1;
        $quotation->save();

        return response()->json(['success'=>1,'message' => 'Created Quotation Successfully']);
    }


    public function updateadminquotation(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'quotation_date' => 'required',
        ]);

        $quotation =  Quotation::where('id',$request->id)->first();
        $quotation->quotation_date = $request->quotation_date;
        $quotation->status = $request->status;
        $quotation->save();

         $quotation = Quotation::where('id',$request->id)->first();
       $revised = new RevisedQuotation();
       
            $revised->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
            $revised->employee_id = JWTAuth::user()->id;
        
        $revised->quotation_id = $quotation->id;
        $revised->client_id = $quotation->client_id;
        $revised->revised = $request->revised;
        $revised->quotation_date = $quotation->quotation_date;
        $revised->status = $request->status;
        $revised->lead_id = $quotation->lead_id;
        $revised->save();

        $quotationmainchild = QuotationMainChild::where('quot_id',$quotation->id)->get();
        foreach($quotationmainchild as $qmc)
        {
            $revisedmainchild['revised_id'] = $revised->id;
            $revisedmainchild['name'] = $qmc->name;
            $revisedmainchild['employee_id'] = $qmc->employee_id;
            $store = RevisedMainChild::create($revisedmainchild);

            $quotationchild = QuotationChild::where('quot_main_child_id',$qmc->id)->get();
            foreach($quotationchild as $qc)
            {
                $childss['revised_id'] = $revised->id;
                $childss['revised_main_child_id'] = $store->id;
                $childss['productcategory_id'] = $qc->productcategory_id;
                $childss['company_id'] = $qc->company_id;
                $childss['product_id'] = $qc->product_id;
                $childss['warehouse_id'] = $qc->warehouse_id;
                $childss['price'] = $qc->price;
                $childss['qty'] = $qc->qty;
                $childss['value_of_amount'] = $qc->value_of_amount;
                $childss['discount'] = $qc->discount;
                $childss['gst_id'] = $qc->gst_id;
                $childss['amount'] = $qc->amount;
                $childss['total'] = $qc->total;
                $childss['delivery_date'] = $qc->delivery_date;
                $childss_store = RevisedChild::create($childss);
            }
        }
        return response()->json(['success'=>1,'message' => 'Quotation updated successfully.']);
    }

    public function updatequotationmainchild(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'quot_id' => 'required',
        ]);
        $quot_main_child = QuotationMainChild::where('id',$request->id)->first();
        $quot_main_child->name = $request->name;
        $quot_main_child->employee_id = $request->employee_id;
        $quot_main_child->is_hide = $request->is_hide;
        $quot_main_child->save();
        return response()->json(['success'=>1,'message' => 'Quotation-main updated successfully.']);
    }

    public function updatequotation(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'quotation_id' => 'required',
        ]);
        $child = QuotationChild::where('id',$request->id)->first();
        $child->productcategory_id = $request->productcategory_id;
        $child->company_id = $request->company_id;
        $child->product_id = $request->product_id;
        $product = Product::where('id',$request->product_id)->first();
        $child->warehouse_id = $request->warehouse_id;
        $child->price = $product->price;
        $child->qty = $request->qty;
        $child->value_of_amount = $request->value_of_amount;
        $child->discount = $request->discount;
        $child->gst_id = $request->gst_id;

        $amount = $product->price * $request->qty;

        $discount_amount = $request->discount * $product->price / 100;

        $discount = $amount - ($discount_amount * $request->qty);


        $total_amount = $product->price - $discount_amount;
        $gst = GST::where('id',$request->gst_id)->first();

        $gst_amount = $total_amount * $gst->gst / 100;

        $amount = $total_amount + $gst_amount;
        $total = $discount + ($gst_amount * $request->qty);

        $child->amount = $amount;
        $child->total = $total;


        // $amount = $product->price * $request->qty;
        // $gst_amount = $request->discount * $amount / 100;
        // $discount = $amount - $gst_amount;
        // $total_amount = $product->price - $gst_amount;

        // $child->amount = $total_amount;
        // $child->total = $discount;
        $child->delivery_date = $request->delivery_date;
        if(isset($request['image']))
        {
            $imageName = time().'-'. $request->image->getClientOriginalName();
            $request->image->move(public_path('quotation/'),$imageName);
            $child->image = $imageName;
        }
        $child->save();


        $quotation = Quotation::where('id',$request->quotation_id)->first();
       $revised = new RevisedQuotation();
       
            $revised->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
            $revised->employee_id = JWTAuth::user()->id;
        
        $revised->quotation_id = $quotation->id;
        $revised->client_id = $quotation->client_id;
        $revised->revised = $request->revised;
        $revised->quotation_date = $quotation->quotation_date;
        $revised->status = $quotation->status;
        $revised->lead_id = $quotation->lead_id;
        $revised->save();

        $quotationmainchild = QuotationMainChild::where('quot_id',$quotation->id)->get();
        foreach($quotationmainchild as $qmc)
        {
            $revisedmainchild['revised_id'] = $revised->id;
            $revisedmainchild['name'] = $qmc->name;
            $revisedmainchild['employee_id'] = $qmc->employee_id;
            $store = RevisedMainChild::create($revisedmainchild);

            $quotationchild = QuotationChild::where('quot_main_child_id',$qmc->id)->get();
            foreach($quotationchild as $qc)
            {
                $childss['revised_id'] = $revised->id;
                $childss['revised_main_child_id'] = $store->id;
                $childss['productcategory_id'] = $qc->productcategory_id;
                $childss['company_id'] = $qc->company_id;
                $childss['product_id'] = $qc->product_id;
                $childss['warehouse_id'] = $qc->warehouse_id;
                $childss['price'] = $qc->price;
                $childss['qty'] = $qc->qty;
                $childss['value_of_amount'] = $qc->value_of_amount;
                $childss['discount'] = $qc->discount;
                $childss['gst_id'] = $qc->gst_id;
                $childss['amount'] = $qc->amount;
                $childss['total'] = $qc->total;
                $childss['delivery_date'] = $qc->delivery_date;
                $childss_store = RevisedChild::create($childss);
            }
       }
        return response()->json(['success'=>1,'message' => 'Quotation-child updated successfully.']);
    }

    public function createquotation(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required',
            'lead_id' => 'required',
            'client_id' => 'required',
            'quotation_date' => 'required',
            
        ]);
        $quotation = new Quotation();
        $quotation->client_id = $request->client_id;
        if(Auth::user()->role == 1)
        {
            $quotation->branch_id = $request->branch_id;
            $quotation->employee_id = $request->employee_ids;
        }
        else
        {
            $quotation->branch_id = Auth::user()->id;
            $quotation->employee_id = $request->employee_ids;
        }
        $quotation->lead_id = $request->lead_id; 
        $quotation->quotation_date = $request->quotation_date;
        $quotation->status = $request->status ?? 1;
        // $quotation->final_amount = $request->final_amount;
        $quotation->save();

        // foreach($request['name'] as $key=>$value)
        // {
        //     if($value != '')
        //     {
        //         $quot_main_child['quot_id'] = $quotation->id;
        //         $quot_main_child['employee_id'] = $request->employee_id[$key];
        //         $quot_main_child['name'] = $value;
        //         $quot_main_child_store = QuotationMainChild::create($quot_main_child);

        //         foreach($request['productcategory_id'][$key] as $keys=>$values)
        //         {
        //             if ($key == $request['main_id'][$key][$keys]) 
        //             {
        //                 if($values != '')
        //                 {
        //                     $child = new QuotationChild();
        //                     $child->quotation_id = $quotation->id;
        //                     $child->quot_main_child_id = $quot_main_child_store->id;
        //                     $child->productcategory_id = $values;
        //                     $child->company_id = $request->company_id[$key][$keys];
        //                     $child->product_id = $request->product_id[$key][$keys];
        //                     $product = Product::where('id',$request->product_id[$key][$keys])->first();
        //                     $child->warehouse_id = $request->warehouse_id[$key][$keys];
        //                     $child->price = $product->price;
        //                     $child->qty = $request->qty[$key][$keys];
        //                     $child->value_of_amount = $request->value_of_amount[$key][$keys];
        //                     $child->discount = $request->discount[$key][$keys];
        //                     $child->gst_id = $request->gst_id[$key][$keys];

        //                     $amount = $product->price * $request->qty[$key][$keys];
        //                     $gst_amount = $request->discount[$key][$keys] * $amount / 100;
        //                     $discount = $amount - $gst_amount;
        //                     $total_amount = $product->price - $gst_amount;

        //                     $child->amount = $total_amount;
        //                     $child->total = $discount;
        //                     $child->delivery_date = $request->delivery_date[$key][$keys];
        //                     if(isset($request['image'][$key][$keys]))
        //                     {
        //                         $imageName = time().'-'. $request->image[$key][$keys]->getClientOriginalName();
        //                         $request->image[$key][$keys]->move(public_path('quotation/'),$imageName);
        //                         $child->image = $imageName;

        //                     }
        //                     $child->save();
        //                 }
        //             }
        //         }
        //     }
        // }
        return response()->json(['success'=>1,'message' => 'Quotation created successfully.']);
    }

    public function createquotationchild(Request $request)
    {
        $this->validate($request, [
            'quotation_id' => 'required',
        ]);
        $quotation = Quotation::where('id',$request->quotation_id)->first();
        foreach($request['name'] as $key=>$value)
        {
            if($value != '')
            {
                $quot_main_child['quot_id'] = $quotation->id;
                $quot_main_child['name'] = $value;
                $quot_main_child['employee_id'] = $request->employee_id[$key];

                $quot_main_child_store = QuotationMainChild::create($quot_main_child);

                // foreach($request['productcategory_id'][$key] as $keys=>$values)
                // {
                //     // dd($values);
                //     if ($key == $request['main_id'][$key][$keys]) 
                //     {
                //         if($values != '')
                //         {
                //             $child = new QuotationChild();
                //             $child->quotation_id = $quotation->id;
                //             $child->quot_main_child_id = $quot_main_child_store->id;
                //             $child->productcategory_id = $values;
                //             $child->company_id = $request->company_id[$key][$keys];
                //             $child->product_id = $request->product_id[$key][$keys];
                //             $product = Product::where('id',$request->product_id[$key][$keys])->first();
                //             $child->warehouse_id = $request->warehouse_id[$key][$keys];
                //             $child->price = $product->price;
                //             $child->qty = $request->qty[$key][$keys];
                //             $child->value_of_amount = $request->value_of_amount[$key][$keys];
                //             $child->discount = $request->discount[$key][$keys];
                //             $child->gst_id = $request->gst_id[$key][$keys];

                //             $amount = $product->price * $request->qty[$key][$keys];
                //             $gst_amount = $request->discount[$key][$keys] * $amount / 100;
                //             $discount = $amount - $gst_amount;
                //             $total_amount = $product->price - $gst_amount;

                //             $child->amount = $total_amount;
                //             $child->total = $discount;
                //             $child->delivery_date = $request->delivery_date[$key][$keys];
                //             if(isset($request['image'][$key][$keys]))
                //             {
                //                 $imageName = time().'-'. $request->image[$key][$keys]->getClientOriginalName();
                //                 $request->image[$key][$keys]->move(public_path('quotation/'),$imageName);
                //                 $child->image = $imageName;

                //             }
                //             $child->save();
                //         }
                //     }
                // }
            }
        }
        return response()->json(['success'=>1,'message' => 'Quotation created successfully.']);
    }

    public function quotationchild(Request $request)
    {
       $this->validate($request, [
        'quotation_id' => 'required',
        'quot_main_child_id' => 'required',
    ]);
       if($request->productcategory_id)
       {
           foreach($request['productcategory_id'] as $keys=>$values)
           {
                    // dd($values);
            if($values != '')
            {
                $child = new QuotationChild();
                $child->quotation_id = $request->quotation_id;
                $child->quot_main_child_id = $request->quot_main_child_id;
                $child->productcategory_id = $values;
                $child->company_id = $request->company_id[$keys];
                $child->product_id = $request->product_id[$keys];
                $product = Product::where('id',$request->product_id[$keys])->first();
                $child->warehouse_id = $request->warehouse_id[$keys];
                $child->price = $product->price;
                $child->qty = $request->qty[$keys];
                $child->value_of_amount = $request->value_of_amount[$keys];
                $child->discount = $request->discount[$keys];
                $child->gst_id = $request->gst_id[$keys];


                $amount = $product->price * $request->qty[$keys];

                $discount_amount = $request->discount[$keys] * $product->price / 100;
               
                $discount = $amount - ($discount_amount * $request->qty[$keys]);

                
                $total_amount = $product->price - $discount_amount;
                $gst = GST::where('id',$request->gst_id[$keys])->first();
              
                $gst_amount = $total_amount * $gst->gst / 100;

                $amount = $total_amount + $gst_amount;
                $total = $discount + ($gst_amount * $request->qty[$keys]);

                $child->amount = $amount;
                $child->total = $total;
                $child->delivery_date = $request->delivery_date[$keys];
                if(isset($request['image'][$keys]))
                {
                    $imageName = time().'-'. $request->image[$keys]->getClientOriginalName();
                    $request->image[$keys]->move(public_path('quotation/'),$imageName);
                    $child->image = $imageName;

                }
                // dd($child);
                $child->save();
            }
        }
    }

    return response()->json(['success'=>1,'message' => 'Quotation child Updated successfully.']);
}

         
    

    public function deletequotation(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $lead =Quotation::where('id',$request->id)->delete();
        $main = QuotationMainChild::where('quot_id',$request->id)->delete();
        $child = QuotationChild::where('quotation_id',$request->id)->delete();
        return response()->json(['success' => 1, 'message' => 'Quotation deleted successfully.']);
    }

    public function deletequotationmain(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $quotation = QuotationMainChild::where('id',$request->id)->delete();
        $child = QuotationChild::where('quot_main_child_id',$request->id)->delete();
        return response()->json(['success'=>1,'message' => 'Quotation-main & child deleted successfully.']);
    }

    public function deletequotationchild(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $child = QuotationChild::where('id',$request->id)->delete();
        return response()->json(['success'=>1,'message' => 'Quotation-child deleted successfully.']);
    }

    public function getquotation()
    {
        // $quotation = Quotation::with('client','branch','employee','quotationchild','delivery')->with('quotationchild.products','quotationchild.productcategory','quotationchild.companys','quotationchild.warehouses','quotationchild.mainchild')->latest()->get();
         $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->latest()->get();

        if(JWTAuth::user()->role == 2)
        {
            // $quotation = Quotation::with('client','branch','employee','quotationchild','delivery')->with('quotationchild.products','quotationchild.productcategory','quotationchild.companys','quotationchild.warehouses','quotationchild.mainchild')->where('branch_id',JWTAuth::user()->id)->latest()->get();
              $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('branch_id',JWTAuth::user()->id)->latest()->get();
        }
        return response()->json(['success'=>1,'data' => $quotation]);
    }

    public function delivery(Request $request)
    {
        $this->validate($request, [

            'quotation_id' => 'required',


        ]);

        $delivery = Delivery::where('quotation_id',$request->quotation_id)->first();
        if($delivery)
        {
            $delivery->quotation_id = $request->quotation_id;
            $delivery->date = $request->date;
            $delivery->client_id = $request->client_id;


            $delivery->note = $request->note;


            $delivery->save();
        }
        else
        {
            $delivery = new Delivery();
            $delivery->quotation_id = $request->quotation_id;
            $delivery->date = $request->date;
            $delivery->client_id = $request->client_id;


            $delivery->note = $request->note;


            $delivery->save();
        }


        return response()->json(['success'=>1,'message' => 'Created Delivery Successfully']);
    }

    public function updatedelivery(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'quotation_id' => 'required',


        ]);

        $delivery = Delivery::where('id',$request->id)->first();

        $delivery->quotation_id = $request->quotation_id;
        $delivery->date = $request->date;
        $delivery->client_id = $request->client_id;


        $delivery->note = $request->note;


        $delivery->save();

        return response()->json(['success'=>1,'message' => 'Updated Delivery Successfully']);
    }

    public function getrevised(Request $request)
    {
        $this->validate($request, [
            'quotation_id' => 'required',



        ]);
        $revised = RevisedQuotation::with('client','branch','employee','revisedchild')->where('quotation_id',$request->quotation_id)->latest()->get();

        return response()->json(['success'=>1,'data' => $revised]);
    }

    public function getdelivery(Request $request)
    {
        $this->validate($request, [
            'quotation_id' => 'required',



        ]);
        $delivery = Delivery::with('product')->where('quotation_id',$request->quotation_id)->latest()->get();

        return response()->json(['success'=>1,'data' => $delivery]);
    }

    public function update_delivery(Request $request)
    {
        $this->validate($request, [
            'update' => 'required',
            'return' => 'required',
            'quotation_id' => 'required',

        ]);

        $update_inventory = $request->update;
        $update_return = $request->return;
        $qty = 0;
        foreach ($update_inventory as $key => $value) 
        {
            if($value != '')
            {

                $child = QuotationChild::where('id',$key)->first();
                $delivery = new Delivery();
                $delivery->product_id = $child->product_id;
                $delivery->quotation_id = $child->quotation_id;
                $deliverylog = Delivery::where('quotation_id',$request->quotation_id)->where('product_id',$child->product_id)->latest()->first();
                if($deliverylog)
                {

                    $delivery->qty = $deliverylog->qty + $value;
                }
                else
                {
                    $delivery->qty = $value;

                }

                $delivery->return = $deliverylog->return ?? 0;
                $delivery->used = $value;
                $delivery->date = now();
                $delivery->save();

            }

        }


        foreach ($update_inventory as $key => $value) {
            if($value != '')
            {
                $child = QuotationChild::where('id',$key)->first();
                $stock = WarehouseInventory::where('product_id',$child->product_id)->where('warehouse_id',$child->warehouse_id)->first();
                $stock->used = $stock->used + $value;
                $stock->save();
            }
        }

        foreach ($update_return as $key => $value) {
            if($value != '')
            {
                $child = QuotationChild::where('id',$key)->first();
                $stock = WarehouseInventory::where('product_id',$child->product_id)->where('warehouse_id',$child->warehouse_id)->first();
                $stock->used = $stock->used - $value;
                $stock->stock = $stock->stock + $value;
                $stock->save();
            }
        }

        foreach ($update_return as $key => $value) 
        {
            if($value != '')
            {

                $child = QuotationChild::where('id',$key)->first();
                $delivery = new Delivery();
                $delivery->product_id = $child->product_id;
                $delivery->quotation_id = $child->quotation_id;
                $deliverylog = Delivery::where('quotation_id',$request->quotation_id)->where('product_id',$child->product_id)->latest()->first();
                $delivery->return = $deliverylog->return + $value;
                $delivery->qty = $deliverylog->qty;
                $delivery->used = $delivery->qty;
                $delivery->date = now();
                $delivery->save();

            }

        }
    }
}