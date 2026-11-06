<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pdfs;
use App\User;
use Auth;
use App\Employee;
use DataTables;
use PDF;



class PdfController extends Controller
{

    public function __construct(Pdfs $s)
    {
        $this->view = 'pdf';
        $this->route = 'pdf';
        $this->viewName = 'Pdf';

    }

    public function index(Request $request)
    {
        if(Auth::guard('user')->check())
        {
        
        if ($request->ajax())
        {
            $query = Pdfs::with('branch','employee')->latest();
            if(Auth::user()->role == 2){
                    $query = Pdfs::with('employee','branch')->where('branch_id',Auth::user()->id)->latest();
                }

                if($request->branch_id)
                {
                   $query = $query->where('branch_id',$request->branch_id);
               }

               if($request->employee_id)
               {
                   $query = $query->where('employee_id',$request->employee_id);
               }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {  

                $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'pdf'])->render();           
                return $btn;
            })

             ->addColumn('pdf_name', function ($row) {
                if($row->pdf_name)
                {
                    if($row->pdf_name == 2)
                    {
                        $btn = "Kuche";
                    }
                    else if($row->pdf_name == 3)
                    {
                        $btn = "Elementra";
                    }
                    else
                    {
                        $btn = "Nolte";
                    }
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })

           

            ->rawColumns(['action','pdf_name'])
            ->make(true);
        }

    }

        if(Auth::guard('employee')->check())
        {        
        if ($request->ajax())
        {
            $query = Pdfs::with('employee','branch')->where('employee_id',Auth::user()->id)->latest();
            if(Auth::user()->role == 2){
                    $query = Pdfs::with('employee','branch')->where('branch_id',Auth::user()->id)->latest();
                }

                if($request->branch_id)
                {
                   $query = $query->where('branch_id',$request->branch_id);
               }

               if($request->employee_id)
               {
                   $query = $query->where('employee_id',$request->employee_id);
               }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {  

                $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'pdf'])->render();           
                return $btn;
            })

             ->addColumn('pdf_name', function ($row) {
                if($row->pdf_name)
                {
                    if($row->pdf_name == 2)
                    {
                        $btn = "Kuche";
                    }
                    else if($row->pdf_name == 3)
                    {
                        $btn = "Elementra";
                    }
                    else
                    {
                        $btn = "Nolte";
                    }
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })

           

            ->rawColumns(['action','pdf_name'])
            ->make(true);
        }


    }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        // $data['employee'] = Employee::where('status',1)->get();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['employee'] = Employee::where('status',1)->get();
            }
            else
            {
               $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
           }
       }
        return view('pdf.index')->with($data);
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('status',1)->where('role',2)->get();
        // $data['employee'] = Employee::where('status',1)->get();
         if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['employee'] = Employee::where('status',1)->get();
            }
            else
            {
               $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
           }
       }
        return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        $param = $request->all();
         if(Auth::guard('user')->check())
         {
            if(Auth::user()->role == 1)
            {
                $param['branch_id'] = $request->branch_id;
                
                $param['employee_id'] = $request->employee_id;
            }
            else
            {
                $param['branch_id']= Auth::user()->id;
                // dd('sdvfd');
                $param['employee_id'] = $request->employee_id;
            }

        }
        else
        {
            $param['branch_id'] = Employee::where('id',Auth::user()->id)->value('branch_id');
            $param['employee_id'] = Auth::user()->id;
        }

        $pdf = Pdfs::create($param);
        $pdf->save();

        if ($pdf){
         return response()->json(['status'=>'success']);
     }else{
         return response()->json(['status'=>'error']);
     }
 }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Pdfs::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('status',1)->where('role',2)->get();
        // $data['employee'] = Employee::where('status',1)->get();
         if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['employee'] = Employee::where('status',1)->get();
            }
            else
            {
               $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
           }
       }
        return view('general.edit_form')->with($data);

    }

    public function update(Request $request, $id)
    {

        $param = $request->all();


        unset($param['_token'], $param['_method']);
          if(Auth::guard('user')->check())
         {
            if(Auth::user()->role == 1)
            {
                $param['branch_id'] = $request->branch_id;
                
                $param['employee_id'] = $request->employee_id;
            }
            else
            {
                $param['branch_id']= Auth::user()->id;
                // dd('sdvfd');
                $param['employee_id'] = $request->employee_id;
            }

        }
        else
        {
            $param['branch_id'] = Employee::where('id',Auth::user()->id)->value('branch_id');
            $param['employee_id'] = Auth::user()->id;
        }


        $pdf = Pdfs::where('id', $id)->first();
        $pdf->update($param);

        if ($pdf){
           return response()->json(['status'=>'success']);
       }else{
           return response()->json(['status'=>'error']);
       }
   }

    public function destroy($id)
    {
        //
    }

    public function pdf2(Request $request)
    {
        $data = Pdfs::where('id',$request->id)->first();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf2')->setPaper('A4', 'portrait');

        $pdf->save('public/pdf/'.$data->id.'-2.pdf');
        return $pdf->download('Quotation.pdf');
    }

    public function pdf3(Request $request)
    {
        $data = Pdfs::where('id',$request->id)->first();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf3')->setPaper('A4', 'portrait');
        $pdf->save('public/pdf/'.$data->id.'-3.pdf');
        return $pdf->download('Quotation.pdf');
    }

    public function pdf4(Request $request)
    {
        $data = Pdfs::where('id',$request->id)->first();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf4')->setPaper('A4', 'portrait');
        $pdf->save('public/pdf/'.$data->id.'-4.pdf');
        return $pdf->download('Quotation.pdf');
    }
}
