<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Quotation;
use App\Client;
use App\QuotationChild;
use App\QuotationMainChild;
use App\GST;
use App\Employee;
use App\RevisedQuotation;
use App\RevisedChild;
use App\RevisedMainChild;
use App\Lead;
use App\Symbol;
use App\LeadBasicChild;
use App\LeadBasicMainChild;
use Auth;
use App\Product;
use App\ProductCategory;
use App\WarehouseInventory;
use App\Delivery;
use App\Company;
use App\Warehouse;
use App\Pdfs;
use Illuminate\Http\Request;
use PDF;
class QuotationController extends Controller
{
    public function __construct(Quotation $s)
    {
        $this->view = 'quotations';
        $this->route = 'quotations';
        $this->viewName = 'Quotation';
    }   
    public function index(Request $request)
    {
        if(Auth::guard('employee')->check())
        {
            if ($request->ajax()) {
                $query = Quotation::where('employee_id',Auth::user()->id)->latest();
                if($request->status == 1)
                {
                    $query = $query->where('status',1);
                }
                else if($request->status == 0)
                {
                    $query = $query->where('status',0);
                }
                else if($request->status == 2) 
                {
                    $query = $query->where('status',2);
                }
                else if($request->status == 3) 
                {
                    $query = $query->where('status',3);
                }
                else if($request->status == 4) 
                {
                    $query = $query->where('status',4);
                }
                else
                {
                    $query = $query->latest();
                }
                return Datatables::of($query)
                ->addColumn('action', function ($row) {

                    $lead_id = $row->lead_id;

                    $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'quotations','per_value'=>'quot'])->render();      

                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="' . route('user.quotations.show', ['lead_id' => $lead_id]) . '" title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';

                    return $btn;
                })
                ->addColumn('client_id', function ($row) {
                    $btn = $row->client->member;
                    return $btn;
                })
                ->addColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->addColumn('employee_id', function ($row) {
                    $btn = $row->employee->name;
                    return $btn;
                })
                ->addColumn('date', function ($row) {
                    $ac = date('d-m-Y',strtotime($row->quotation_date));
                    return $ac;
                })
                ->addColumn('status', function ($row) {
                    $btn = '';
                    if ($row->status == 1) {
                        $btn = 'Pending';
                    }elseif ($row->status == 2) {
                        $btn = 'Discussion';
                    }elseif ($row->status == 3) {
                        $btn = 'Rivision';
                    }elseif ($row->status == 4) {
                        $btn = 'Win';
                    }elseif ($row->status == 5) {
                        $btn = 'Lost';
                    }
                    return $btn;
                })
                ->addColumn('lead', function ($row) {
                    $leads = Lead::where('id',$row->lead_id)->value('lead_name');
                    $exp = explode(',',$leads);
                    $emp = ProductCategory::whereIn('id',$exp)->get();
                    $arr = [];
                    foreach ($emp as $key => $value) {
                        array_push($arr,$value->name);
                    }
                    $btn = implode(', ',$arr);
                    return $btn;
                })
                ->rawColumns(['numbers','action','status','date','branch_id','employee_id','lead'])
                ->make(true);
            } 
        }
        else
        {
            if ($request->ajax()) {
                $query = Quotation::latest();
                if(Auth::user()->role == 2)
                {
                    $query = Quotation::where('branch_id',Auth::user()->id)->latest();
                }
                if($request->branch_id)
                {
                    $query = $query->where('branch_id',$request->branch_id);
                }
                if($request->employee_id)
                {
                    $query = $query->where('employee_id',$request->employee_id);
                }
                if($request->status == 1)
                {
                    $query = $query->where('status',1);
                }
                else if($request->status == 0)
                {
                    $query = $query->where('status',0);
                }
                else if($request->status == 2) 
                {
                    $query = $query->where('status',2);
                }
                else if($request->status == 3) 
                {
                    $query = $query->where('status',3);
                }
                else if($request->status == 4) 
                {
                    $query = $query->where('status',4);
                }
                else
                {
                    $query = $query->latest();
                }
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $lead_id = $row->lead_id;
                    $btn = view('layouts.actionbtnpermission')->with(['id' => $row->id, 'route' => 'quotations', 'per_value' => 'quot'])->render();
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="' . route('user.quotations.show', ['lead_id' => $lead_id]) . '" title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';
                    return $btn;
                })

                ->addColumn('client_id', function ($row) {
                    $btn = $row->client->member;
                    return $btn;
                })
                ->addColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->addColumn('employee_id', function ($row) {
                    $btn = $row->employee->name;
                    return $btn;
                })
                ->addColumn('date', function ($row) {
                    $ac = date('d-m-Y',strtotime($row->quotation_date));
                    return $ac;
                })
                ->addColumn('status', function ($row) {
                    $btn = '';
                    if ($row->status == 1) {
                        $btn = 'Pending';
                    }elseif ($row->status == 2) {
                        $btn = 'Discussion';
                    }elseif ($row->status == 3) {
                        $btn = 'Rivision';
                    }elseif ($row->status == 4) {
                        $btn = 'Win';
                    }elseif ($row->status == 5) {
                        $btn = 'Lost';
                    }
                    return $btn;
                })
                ->addColumn('lead', function ($row) {
                    $leads = Lead::where('id',$row->lead_id)->value('lead_name');
                    $exp = explode(',',$leads);
                    $emp = ProductCategory::whereIn('id',$exp)->get();
                    $arr = [];
                    foreach ($emp as $key => $value) {
                        array_push($arr,$value->name);
                    }
                    $btn = implode(', ',$arr);
                    return $btn;
                })
                ->rawColumns(['numbers','action','status','date','branch_id','employee_id','lead'])
                ->make(true);
            } 
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['employee'] = Employee::where('status',1)->get();
        if(Auth::user()->role == 2)
        {
            $data['employee'] = Employee::where('status',1)->where('branch_id',Auth::user()->id)->get();
        }
        return view('quotations.index')->with($data);
    }
    public function create($id)
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = Lead::where('id',$id)->first();

        $data['leadmain'] = LeadBasicMainChild::where('lead_id',$id)->get();
        $data['quotation'] = Quotation::where('lead_id',$id)->first();
        if(isset($data['quotation']))
        {
            $data['quotation_main'] = QuotationMainChild::where('quot_id',$data['quotation']->id)->get();
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['client'] = Client::where('branch_id',$data['data']->branch_id)->get();
        $data['gst'] = GST::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['product'] = Product::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['company'] = Company::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['warehouse'] = Warehouse::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['employee'] = Employee::where('branch_id',$data['data']->branch_id)->where('status',1)->get();
        $data['symbol'] = Symbol::where('branch_id',$data['data']->branch_id)->where('status',1)->get();

        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $quotation = new Quotation();
        $quotation->client_id = $request->client_id;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $quotation->branch_id = $request->branch_id;
                $quotation->employee_id = $request->employee_id;
            }
            else
            {
                $quotation->branch_id = Auth::user()->id;
                $quotation->employee_id = $request->employee_id;
            }
        }
        else
        {
            $quotation->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $quotation->employee_id = Auth::user()->id;
        }
        $quotation->lead_id = $request->lead_id; 
        $quotation->quotation_date = $request->quotation_date;
        $quotation->status = $request->status ?? 1;
        $quotation->final_amount = $request->final_amount;
        $quotation->save();
        if($request->product_id)
        {
            foreach ($request->product_id as $key => $value) {
                if($value != '')
                {
                    $child = new QuotationChild();
                    $child->quotation_id = $quotation->id;
                    $child->productcategory_id = $request->productcategory_id[$key];
                    $child->company_id = $request->company_id[$key];
                    $child->product_id = $value;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->price = $request->price[$key];
                    $child->qty = $request->qty[$key];
                    $child->value_of_amount = $request->value_of_amount[$key];
                    $child->discount = $request->discount[$key];
                    $child->gst_id = $request->gst_id[$key];
                    $child->amount = $request->amount[$key];
                    $child->total = $request->total[$key];
                    $child->delivery_date = $request->delivery_date[$key];
                    $child->value_of_amount = $request->value_of_amount[$key];
                    if(isset($request['image'][$key]))
                    {
                        $imageName = time().'-'. $request->image[$key]->getClientOriginalName();
                        $request->image[$key]->move(public_path('quotation/'),$imageName);
                        $child->image = $imageName;
                    }
                    $child->save();
                }
            }
        }
        if($quotation)
        {
            return response()->json(['status' => 'success', 'data' =>$quotation]);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function delivery(Request $request)
    {
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
        if($delivery)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Quotation::where('id', $id)->first();

        $data['quotationchild'] = QuotationMainChild::where('quot_id',$id)->get();
        $data['delivery'] = Delivery::where('quotation_id',$id)->first();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        if(Auth::guard('employee')->check())
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['lead'] = Lead::where('branch_id',$branch)->get();
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['client'] = Client::where('branch_id',$branch)->get();
            $data['gst'] = GST::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['product'] = Product::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['warehouse'] = Warehouse::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('id',Auth::user()->id)->where('status',1)->get();
        }
        else
        {
            $data['lead'] = Lead::get();
            $data['client'] = Client::get();
            $data['gst'] = GST::where('status',1)->get();
            $data['product'] = Product::where('status',1)->get();
            $data['company'] = Company::where('status',1)->get();
            $data['warehouse'] = Warehouse::where('status',1)->get();
            $data['productcategory'] = ProductCategory::where('status',1)->get();
            $data['employee'] = Employee::where('status',1)->get();
            if(Auth::user()->role == 2)
            {
                $data['gst'] = GST::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['product'] = Product::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['warehouse'] = Warehouse::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
                $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
                $data['lead'] = Lead::where('branch_id',Auth::user()->id)->get();
            }
        }
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        return view('general.edit_form')->with($data);
    }
    public function show($id)
    {
        $data['title'] = 'View' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['quotation'] = Quotation::where('lead_id',$id)->first();
        if(isset($data['quotation']))
        {
            $data['quotation_main'] = QuotationMainChild::where('quot_id',$data['quotation']->id)->get();
        }
        return view('quotations.show')->with($data);
    }
    public function update(Request $request, $id)
    {
        if ($request->name) 
        {
            foreach ($request->name as $key => $value)
            {
                if ($value != '')
                {
                    if (!empty($request['productcategory_id'][$key]))
                    {
                        foreach ($request['productcategory_id'][$key] as $pc_key => $pc_val)
                        {
                            if (!empty($pc_val))
                            {
                                if (empty($request->warehouse_id[$key][$pc_key]))
                                {
                                    return response()->json(['status' => 'required', 'message' => 'Warehouse Is Required !!!']); 
                                }

                                if (empty($request->qty[$key][$pc_key]))
                                {
                                    return response()->json(['status' => 'required', 'message' => 'Qty Is Required !!!']); 
                                }
                            }
                        }
                    }
                }
            }
        }


        if($request->quotation_id)
        {
            $quotation = Quotation::where('id',$request->quotation_id)->first();
        }   
        else
        {
            $quotation = new Quotation();
            $quotation->lead_id = $id;
            $quotation->client_id = $request->client_id;
            
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 1)
                {
                    $quotation->branch_id = $request->branch_id;
                }
                else
                {
                    $quotation->branch_id = Auth::user()->id;
                }
                $quotation->employee_id = $request->employee_id;
            }
            else
            {
                $quotation->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
                $quotation->employee_id = Auth::user()->id;
            }

            $quotation->source_name = $request->source_name;
        }
        $quotation->status = $request->status ?? 1;
        $quotation->quotation_date = $request->quotation_date;
        $quotation->save();
            

        if ($request->name) 
        {
            foreach ($request->name as $key => $value)
            {
                if ($value != '')
                {
                    if (!empty($request['hidden_main'][$key]))
                    {
                        $quotation_main_child = QuotationMainChild::where('id',$request['hidden_main'][$key])->first();
                    }
                    else
                    {
                        $quotation_main_child = new QuotationMainChild();
                        $quotation_main_child->quot_id = $quotation->id;
                    }
                    
                    $quotation_main_child->name = $value;
                    $quotation_main_child->employee_id = $request->main_employee_id[$key];
                    $quotation_main_child->symbol = $request->symbol[$key];
                    $quotation_main_child->save();

                    if (!empty($request['productcategory_id'][$key])) {
                        foreach ($request['productcategory_id'][$key] as $pc_key => $pc_val) {
                            if (!empty($pc_val)) {
                                $child = null;

                                if (!empty($request->hidden_child[$key][$pc_key])) {
                                    $child = QuotationChild::find($request->hidden_child[$key][$pc_key]);
                                }
                                
                                if (!$child) {
                                    $child = new QuotationChild();
                                    $child->quotation_id = $quotation->id;
                                    $child->quot_main_child_id = $quotation_main_child->id;
                                    $child->productcategory_id = $pc_val;
                                    $child->company_id = $request->company_id[$key][$pc_key] ?? null;
                                    $child->product_id = $request->product_id[$key][$pc_key] ?? null;
                                    $child->warehouse_id = $request->warehouse_id[$key][$pc_key] ?? null;
                                    $child->price = $request->price[$key][$pc_key] ?? null;
                                }

                                $child->qty = $request->qty[$key][$pc_key] ?? null;
                                $child->value_of_amount = $request->value_of_amount[$key][$pc_key] ?? null;
                                $child->discount = $request->discount[$key][$pc_key] ?? null;
                                $child->gst_id = $request->gst_id[$key][$pc_key] ?? null;
                                $child->amount = $request->amount[$key][$pc_key] ?? null;
                                $child->total = $request->total[$key][$pc_key] ?? null;
                                $child->delivery_date = $request->delivery_date[$key][$pc_key] ?? null;
                                $child->save();
                            }
                        }
                    }
                }
            }
        }


        $revised = new RevisedQuotation();
        $revised->branch_id = $quotation->branch_id;
        $revised->employee_id = $quotation->employee_id;
        $revised->quotation_id = $quotation->id;
        $revised->client_id = $request->client_id;
        $revised->revised = $request->revised;
        $revised->quotation_date = $request->quotation_date;
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

        if($quotation)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function getprice(Request $request)
    {
        $html = Product::where('id',$request->product_id)->value('price');
        return response()->json(['data'=>$html]);
    }
    public function getproduct(Request $request)
    {
        $data = Product::where('productcategory_id',$request->productcategory_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->product."-".$value->product."</option>";
        }
        echo $html;
    }
    public function quotationdelete($id)
    {
        $quotationchild = QuotationChild::where('id',$id)->delete();
        return redirect()->back();
    }
    public function quotationmaindelete($id)
    {
        $quotation_child = QuotationChild::where('quot_main_child_id',$id)->delete();
        $quotation_main_child = QuotationMainChild::where('id',$id)->delete();
        return redirect()->back();
    }
    public function getemployee(Request $request)
    {
        $data = Employee::where('branch_id',$request->branch_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $client = Client::where('branch_id',$request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($client as $keys => $values) {
            $html1 .= "<option value=".$values->id.">".$values->member."</option>";
        }
        return response()->json(['data'=>$html,'data1'=>$html1]);
    }
    public function getdata(Request $request)
    {
        $data = Lead::where('id',$request->lead_id)->first();
        $child = LeadBasicChild::where('lead_id',$data->id)->get();
        $html = $data->branch_id;
        $html1 = $data->employee_id;
        $html2 = $data->client_id;
        $html3 = '';
        foreach($child as $key => $value){
            $html3 .=   '
            <td>
            <div style="display: inline-flex !important;">
            <button type="button" id="remove" data-value='.$value->id.' class="btn btn-sm btn-icon btn-icon-md btn-clean remove remove" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
            </div>
            </td>
            <td>
            <input type="hidden" name="productcategory_id[]" value="'.$value->productcategory_id.'" >
            <input type="text" id="productcategory_id" value="'.$value->productcategory->name.'" class="form-control" readonly>
            </td>
            <td>
            <input type="hidden" name="company_id[]" value="'.$value->company_id.'" >
            <input type="text" id="company_id_0" value="'.$value->company->name.'" class="form-control" readonly>
            </td>
            <td>
            <input type="hidden" name="product_id[]" value="'.$value->product_id.'" >
            <input type="text" id="product_id_0" value="'.$value->product->product.'" class="form-control" readonly>
            </td>
            <td>
            <input type="hidden" name="warehouse_id[]" value="'.$value->warehouse_id.'" >
            <input type="text" id="warehouse_id_0" value="'.$value->warehouse->name.'" class="form-control" readonly>
            </td>
            <td>
            <input type="number" class="form-control price" id="price_0"  value="'.$value->product->price.'" name="price[]" readonly   >
            </td>
            <td>
            <input type="number" class="form-control qty" id="qty_0"  value="'.$value->qty.'" name="qty[]" data-id="0" readonly >
            </td>
            <td style="width:7%">
            <select class="form-control value_of_amount" name="value_of_amount[]" id="value_of_amount_0" data-id="0" >
            <option value="">Select </option>
            <option value="1">Project</option>
            <option value="2">Retail</option>
            </select>
            </td>
            <td>
            <input type="number" class="form-control discount" id="discount_0" name="discount[]" data-id="0"  >
            </td>
            <td>
            <select name="gst_id[]"  data-id="0"  id="gst_id_0" class="form-control gst_id" >';
            $gst = GST::where('status',1)->get();
            foreach($gst as $keys => $values)
            {
                $html3 .= '<option value="">Select GST</option>';
                $html3 .= '<option value="'.$values->id.'">'.$values->gst.'</option>';
            }
            $html3  .= '</select>
            </td>
            <td>
            <input type="number" class="form-control amount" id="amount_0" name="amount[]" data-id="0" readonly  >
            </td>
            <td>
            <input type="number" class="form-control total" id="total_0" name="total[]" data-id="0" readonly >
            </td>
            <td>
            <input type="date" class="form-control delivery_date" id="delivery_date_0"  name="delivery_date[]" data-id="0"  >
            </td>
            <td>';
            if($value->product->image1)
            {
                $html3 .= '<img src="{{ asset("/product/".'.$value->product->image1.') }}" height="70px" width="70px">';
            }
            $html3 .= '</td>
            ';
        }
        return response()->json(['data'=>$html , 'data1'=>$html1 , 'data2'=>$html2 ,'data3'=>$html3]);
    }
    public function pdf(Request $request)
    {
        $data = Quotation::where('id',$request->id)->first();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('quotationpdf')->setPaper('A4', 'landscape');
        $pdf->save('public/pdf/'.$data->id.'-1.pdf');
        return $pdf->download('Quotation.pdf');
    }
    public function pdf2(Request $request)
    {
        $data = Quotation::where('id',$request->id)->first();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('quotationpdf2')->setPaper('A4', 'portrait');
        $pdf->save('public/pdf/'.$data->id.'-2.pdf');
        return $pdf->download('Quotation.pdf');
    }
    public function pdf3(Request $request)
    {
        $data = Quotation::where('id',$request->id)->first();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('quotationpdf3')->setPaper('A4', 'portrait');
        $pdf->save('public/pdf/'.$data->id.'-3.pdf');
        return $pdf->download('Quotation.pdf');
    }
    public function pdf4(Request $request)
    {
        $data = Quotation::where('id',$request->id)->first();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('quotationpdf4')->setPaper('A4', 'portrait');
        $pdf->save('public/pdf/'.$data->id.'-4.pdf');
        return $pdf->download('Quotation.pdf');
    }
    public function getwarehouse(Request $request)
    {
        $data = WarehouseInventory::where('product_id',$request->product_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $values) {

            $html .= "<option value=".$values->warehouse_id.">".$values->warehouse->name."</option>";
        }
        $html1 = Product::where('id',$request->product_id)->value('price');
        $html2 = \App\Gallery::where('productcategory_id',$request->productcategory_id)->where('product_id',$request->product_id)->where('company_id',$request->company_id)->latest()->first();
        if($html2)
        {
            $html2 = url('gallery/'.$html2->gallery);
        }
        return response()->json(['data'=>$html1,'data1'=>$html, 'data2' => $html2]);
    }

    public function quotation_getwarehouse_data(Request $request)
    {
        $whinve = WarehouseInventory::where('product_id',$request->product_id);
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $whinve->where('branch_id',$request->branch_id);
            }
            else
            {
                $whinve->where('branch_id',Auth::user()->id);
            }
        }
        else
        {
            $whinve->where('branch_id',Auth::user()->branch_id);
        }
        $whinve = $whinve->pluck('warehouse_id')->toArray();

        $data = Warehouse::whereIn('id',$whinve)->get();

        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $html1 = Product::where('id',$request->product_id)->value('price');
        $html2 = \App\Gallery::where('productcategory_id',$request->productcategory_id)->where('product_id',$request->product_id)->where('company_id',$request->company_id)->latest()->first();
        if($html2)
        {
            $html2 = url('gallery/'.$html2->gallery);
        }

        return response()->json(['data' => $html, 'data1' => $html1, 'data2' => $html2]);
    }

    public function clone($id)
    {
        $data['id'] = $id;
        return view('quotations.clone')->with($data);
    }
    public function price(Request $request,$id)
    {
        $id = $id;
        $quotations = Quotation::where('id',$id)->first();
        if($request->quotation_price)
        {
            $quotations->q_price = $request->quotation_price;
        }
        if($request->q_price2)
        {
            $quotations->q_price2 = $request->q_price2;
        }
        if($request->q_price3)
        {
            $quotations->q_price3 = $request->q_price3;
        }
        if($request->q_price4)
        {
            $quotations->q_price4 = $request->q_price4;
        }
        $quotations->image = $request->image ?? 0;
        $quotations->code = $request->code ?? 0;
        $quotations->item_name = $request->item_name ?? 0;
        $quotations->description = $request->description ?? 0;
        $quotations->qty = $request->qty ?? 0;
        $quotations->mrp = $request->mrp ?? 0;
        $quotations->discount = $request->discount ?? 0;
        $quotations->discount_price = $request->discount_price ?? 0;
        $quotations->sp_price = $request->sp_price ?? 0;
        $quotations->gst = $request->gst ?? 0;
        $quotations->gst_amt = $request->gst_amt ?? 0;
        $quotations->sp_price_total = $request->sp_price_total ?? 0;
        $quotations->save();
        if($quotations)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
}