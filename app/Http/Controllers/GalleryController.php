<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ProductCategory;
use App\Gallery;
use App\Product;
use App\User;
use App\Employee;
use App\Company;

use DataTables;
use App\Exports\GalleryExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
class GalleryController extends Controller
{
    public function __construct(Gallery $s)
    {
        $this->view = 'gallery';
        $this->route = 'gallery';
        $this->viewName = 'Gallery';
    }
    
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            if(Auth::user()->role == 2)
            {
                $query = Gallery::with('product')->where('branch_id',Auth::user()->id)->latest();
            }else{
                $query = Gallery::with('product')->orderby('id','DESC')->latest();
            }

            if($request->branch_id)
            {
                $query->where('branch_id',$request->branch_id);
            }
            if($request->category_id)
            {
                $query->where('productcategory_id',$request->category_id);
            }
            if($request->company_id)
            {
                $query->where('company_id',$request->company_id);
            }

            if (request()->has('product_id')) {
                $query->where('product_id', 'like', "%" . request('product_id') . "%");
            }

            return Datatables::of($query)
            ->addColumn('action', function ($row) {            
                // $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'gallery'])->render(); 
                $btn = '<a style="background: white;" href="'.route('user.gallery.assign', $row->id).'" title="Assign" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class=" flaticon-paper-plane"></i></a>&nbsp;&nbsp';
                if(Auth::user()->role == 1)
                {
                    $btn .= '<a style="background: white;" onclick="return confirm(`Are you sure?`)" href="'.route('user.deletegallery', $row->id).'" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                }
                return $btn;
            })
            ->addColumn('image', function ($row) {
                return '<a href="' . asset('gallery/' . $row->gallery) . '" target="_blank"> <img src="' . url('gallery/' . $row->gallery) . '" height=50 width=50></a>';
            })


            // ->editColumn('product_id', function ($row) {
            //     if(isset($row->product_id))
            //     {
            //         // $btn = Product::where('id',$row->product_id)->value('product');
            //         $btn = $row->product['product'];
            //     } else {
            //         $btn = '';
            //     }
            //     return $btn;
            // })

            ->addColumn('branch_id', function ($row) {
                if(isset($row->branch_id))
                {
                    $btn = User::where('id',$row->branch_id)->value('name');
                } else {
                    $btn = '';
                }
                return $btn;
            })

            ->addColumn('category_id', function ($row) {
                if(isset($row->productcategory_id))
                {
                    $btn = ProductCategory::where('id',$row->productcategory_id)->value('name');
                } else {
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('company_id', function ($row) {
                if(isset($row->company_id))
                {
                    $btn = Company::where('id',$row->company_id)->value('name');
                } else {
                    $btn = '';
                }
                return $btn;
            })
            ->rawColumns(['action','image','branch_id','product_id','category_id','comapny_id'])
            ->make(true);
        }

        // $data['branch'] = User::orderBy('name')->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['category'] = ProductCategory::orderBy('name')->where('branch_id', Auth::user()->id)->get();
        $data['company'] = Company::orderBy('name')->get();

        return view('gallery.index')->with($data);
    }
    
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('general.add_form')->with($data);
    }
    
    public function store(Request $request)
    {
        if($request->gallery)
        {
            foreach($request->gallery as $key => $value)
            {
                $gallery = new Gallery();
                if(Auth::guard('user')->check())
                {
                    if(Auth::user()->role == 1)
                    {
                        $gallery->branch_id = $request->branch_id;
                    }
                    else
                    {
                        $gallery->branch_id = Auth::user()->id;
                    }
                }
                else
                {
                    $gallery->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
                }
                $imageName = time().'-'.$key.'.'. $value->getClientOriginalExtension();
                $value->move(public_path('gallery/'),$imageName);
                $gallery['gallery'] = $imageName;
                // dd($gallery);
                $gallery->save();
                // dd()
            }
        }
        
        return response()->json(['status' => 'success']);
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        //
    }
    
    public function update(Request $request, $id)
    {
        //
    }
    public function deletegallery($id)
    {
        $data = Gallery::where('id',$id)->delete();
        return back();
    }
    public function assign($id)
    {
        $data['id'] = $id;
        $data['productcategory'] = ProductCategory::where('status',1)->get();
        $data['gallery'] = Gallery::where('id',$id)->first();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('gallery.edit')->with($data);
    }
    public function assign_store(Request $request,$id)
    {
        $gallery = Gallery::where('id',$id)->first();
        $gallery->productcategory_id = $request->productcategory_id;
        $gallery->product_id = $request->product_id;
        $gallery->company_id = $request->company_id;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $gallery->branch_id = $request->branch_id;
            }
            else
            {
                $gallery->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $gallery->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $gallery->save();
        $product = Product::where('id',$request->product_id)->where('productcategory_id',$request->productcategory_id)->where('company_id',$request->company_id)->first();
        $product->image1 = $gallery->gallery;
        $product->save();
        return response()->json(['status' => 'success']);
    }
    public function gallery_export() 
    {
        return Excel::download(new GalleryExport, 'gallery.xlsx');
    }
}
