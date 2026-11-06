<?php

namespace App\Http\Controllers;

use App\User;
use DataTables;
use App\Arid;
use App\Lead;
use App\Client;
use App\LeadSource;
use App\Section;
use App\AMC;
use App\Type;
use App\LeadStage;
use App\Employee;
use App\LeadFollowUp;
use App\LeadBasicMainChild;
use App\FollowupLog;
use App\LeadBasicChild;
use App\LeadCommunication;
use App\LeadAssign;
use App\ProductCategory;
use App\StockTransferHistory;
use App\Product;
use App\SourceName;
use App\Company;
use App\Landmark;
use App\Warehouse;
use App\ItemLedger;
use App\Exports\LeadExport;
use App\Exports\FollowupExport;
use App\Exports\TodayFollowupExport;
use App\Exports\YesterdayFollowupExport;
use Excel;
use PDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use App\Visits;
use App\VisitsChild;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Lead::with('client', 'lead_assign.stage', 'lead_assign.employee', 'lead_assign.section', 'lead_followup')->latest();
            if ($request->product_id) {
                $query = $query->where('product_id', $request->product_id);
            }
            if ($request->employee_id) {
                $query = $query->whereHas('lead_assign', function ($a) use ($request) {
                    $a->where('employee_id', $request->employee_id);
                });
            }
            if ($request->stage_id) {
                $query = $query->whereHas('lead_assign', function ($i) use ($request) {
                    $i->where('stage_id', $request->stage_id);
                });
            }
            if ($request->source_id) {
                $query = $query->whereHas('lead_assign', function ($q) use ($request) {
                    $q->where('source_id', $request->source_id);
                });
            }
            $query = $query->get();
            return Datatables::of($query)
                ->addColumn('client_id', function ($row) {
                    $btn = $row->client->member;
                    return $btn;
                })
                ->addColumn('source_id', function ($row) {
                    $btn = $row->source->name;
                    return $btn;
                })
                ->addColumn('product_id', function ($row) {
                    $btn = $row->product->product;
                    return $btn;
                })
                ->rawColumns(['client_id', 'source_id', 'product_id'])
                ->make(true);
        }
        if (Auth::guard('user')->check()) {
            if (Auth::user()->role == 1) {
                $data['product'] = Product::where('status', 1)->get();
                $data['stage'] = LeadStage::where('status', 1)->get();
                $data['employee'] = Employee::where('status', 1)->get();
                $data['source'] = LeadSource::where('status', 1)->get();
                $data['count'] = Lead::count();
            } else {
                $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['employee'] = Employee::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['count'] = Lead::where('branch_id', Auth::user()->id)->count();
            }
        } else {
            $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->id)->get();
            $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->id)->get();
            $data['employee'] = Employee::where('status', 1)->where('employee_id', Auth::user()->id)->get();
            $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->id)->get();
            $data['count'] = Lead::where('employee_id', Auth::user()->id)->count();
        }
        return view('reports.leadreport')->with($data);
    }

    public function amcreport(Request $request)
    {
        if ($request->ajax()) {
            $query = AMC::latest();
            if ($request->product_id) {
                $query = $query->where('product_id', $request->product_id);
            }
            if ($request->client_id) {
                $query = $query->where('client_id', $request->client_id);
            }
            return Datatables::of($query)
                ->addColumn('client_id', function ($row) {
                    $btn = $row->client->member;
                    return $btn;
                })
                ->addColumn('product_id', function ($row) {
                    $btn = $row->product->product;
                    return $btn;
                })
                ->rawColumns(['client_id', 'product_id'])
                ->make(true);
        }
        $data['product'] = Product::get();
        $data['client'] = Client::get();
        $data['count'] = AMC::count();
        $data['sales_amount'] = AMC::sum('sales_amount');
        $data['amc_amount'] = AMC::sum('amc_amount');
        return view('reports.amcreport')->with($data);
    }

    public function reportindex(Request $request)
    {
        if (Auth::guard('employee')->check()) {
            if ($request->ajax()) {
                $array = [];
                $assign = LeadAssign::get();
                foreach ($assign as $dt) {
                    $ex_arr = explode(',', $dt->employee_id);
                    if (in_array(Auth::user()->id, $ex_arr)) {
                        array_push($array, $dt->lead_id);
                    }
                }
                $query = Lead::orderBy('date', 'DESC')->where('active', 1)->with('client', 'lead_assign.stage', 'lead_assign.employee', 'sourcename', 'source')->whereIn('id', $array);
                if ($request->from_date && $request->to_date) {
                    $query->whereBetween('date', [$request->from_date, $request->to_date]);
                }
                if ($request->client_id) {
                    $query->where('client_id', $request->client_id);
                }
                if ($request->lead_name) {
                    $data = $query;
                    $data = $data->get();
                    $array_id = [];
                    foreach ($data as $dt) {
                        $co_co = explode(',', $dt['lead_name']);
                        foreach ($co_co as $co) {
                            if ($co == $request->lead_name) {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query->whereIn('id', $array_id);
                }
                if ($request->company_name) {
                    $data = $query;
                    $data = $data->get();
                    $array_id = [];
                    foreach ($data as $dt) {
                        $co_co = explode(',', $dt['company_name']);
                        foreach ($co_co as $co) {
                            if ($co == $request->company_name) {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query->whereIn('id', $array_id);
                }
                if ($request->source_id) {
                    $query->where('source_id', $request->source_id);
                }
                if ($request->source_name) {
                    $query->where('source_name', $request->source_name);
                }
                if ($request->stage_id) {
                    $query->whereHas('lead_assign', function ($i) use ($request) {
                        $i->where('stage_id', $request->stage_id);
                    });
                }
                $query = $query->latest();
                return Datatables::of($query)
                    ->addColumn('employee_id', function ($row) {
                        $leads = LeadAssign::where('lead_id', $row->id)->first();
                        $exp = explode(',', $leads->employee_id);
                        $emp = Employee::whereIn('id', $exp)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $btn = implode(', ', $arr);
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('branch_id', function ($row) {
                        $btn = $row->branch->name;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('client_id', function ($row) {
                        $btn = $row->client->member;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('source_ids', function ($row) {
                        $followup = LeadFollowUp::where('lead_id', $row->id)->where('employee_id', Auth::user()->id)->first();
                        if ($followup) {
                            $date = date('d/m/Y', strtotime($followup->date));
                            $status = '';
                            if ($followup->is_completed == 1) {
                                $status = 'Pending';
                            } elseif ($followup->is_completed == 2) {
                                $status = 'Discussion';
                            } elseif ($followup->is_completed == 3) {
                                $status = 'Rivision';
                            } elseif ($followup->is_completed == 4) {
                                $status = 'Win';
                            } elseif ($followup->is_completed == 5) {
                                $status = 'Lost';
                            } elseif ($followup->is_completed == 6) {
                                $status = "Showroom Visit";
                            } elseif ($followup->is_completed == 7) {
                                $status = "Site Measurement";
                            } elseif ($followup->is_completed == 8) {
                                $status = "Designing";
                            } elseif ($followup->is_completed == 9) {
                                $status = "Quotation";
                            }
                        } else {
                            $date = '-';
                            $status = '-';
                        }
                        $data = $status;
                        $btn = $data;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('sourcename_ids', function ($row) {
                        $btn = $row->sourcename->name;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('leaddate', function ($row) {
                        $btn = $row->date;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('leadtitles', function ($row) {
                        $btn = explode(',', $row->lead_name);
                        $cat = ProductCategory::whereIn('id', $btn)->select('name')->get();
                        $arr = [];
                        foreach ($cat as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $imp = implode(', ', $arr);
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $imp . '</a>';
                    })
                    ->addColumn('lead_name', function ($row) {
                        $exp = explode(',', $row->lead_name);
                        $emp = ProductCategory::whereIn('id', $exp)->where('status', 1)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $lead_name = implode(', ', $arr);
                        $btn = '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $lead_name . '</a>';
                        return $btn;
                    })
                    ->addColumn('company_name', function ($row) {
                        $exp = explode(',', $row->company_name);
                        $emp = Company::whereIn('id', $exp)->where('status', 1)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $company_name = implode(', ', $arr);
                        $btn = '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $company_name . '</a>';
                        return $btn;
                    })
                    ->addColumn('showroomvisit', function ($row) {
                        if ($row->showroom == 1) {
                            $btn = "Yes";
                        } else {
                            $btn = "No";
                        }
                        return $btn;
                    })
                    ->rawColumns(['client_id', 'source_ids', 'sourcename_ids', 'leaddate', 'leadtitles', 'lead_name', 'company_name', 'showroomvisit'])
                    ->make(true);
            }
        } else {
            if ($request->ajax()) {
                $query = Lead::with('client', 'lead_assign.stage', 'lead_assign.employee', 'lead_assign.section', 'lead_followup', 'sourcename', 'source')->where('active', 1);
                if (Auth::user()->role == 2) {
                    $query = Lead::with('client', 'lead_assign.stage', 'lead_assign.employee', 'sourcename', 'source')->where('branch_id', Auth::user()->id)->where('active', 1)->latest();
                    $query = $query->latest();
                }
                if ($request->employee_id) {
                    $data = Lead::with('client', 'lead_assign.stage', 'lead_assign.employee', 'sourcename', 'source')->where('active', 1)->orderBy('date', 'DESC')->latest();
                    $array = [];
                    foreach ($data as $d) {
                        $explode = explode(',', $d->employee_id);
                        if (in_array($request->employee_id, $explode)) {
                            array_push($array, $d->id);
                        }
                    }
                    $data = LeadAssign::get();
                    $arr = [];
                    foreach ($data as $dt) {
                        $ex_arr = explode(',', $dt->employee_id);
                        if (in_array($request->employee_id, $ex_arr)) {
                            array_push($array, $dt->lead_id);
                        }
                    }
                    $query = $query->whereIn('id', $array);
                }
                if ($request->branch_id) {
                    $query->where('branch_id', $request->branch_id);
                }
                if ($request->client_id) {
                    $query->where('client_id', $request->client_id);
                }
                if ($request->lead_name) {
                    $data = $query;
                    $data = $data->get();
                    $array_id = [];
                    foreach ($data as $dt) {
                        $co_co = explode(',', $dt['lead_name']);
                        foreach ($co_co as $co) {
                            if ($co == $request->lead_name) {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query = $query->whereIn('id', $array_id);
                }
                if ($request->company_name) {
                    $data = $query;
                    $data = $data->get();
                    $array_id = [];
                    foreach ($data as $dt) {
                        $co_co = explode(',', $dt['company_name']);
                        foreach ($co_co as $co) {
                            if ($co == $request->company_name) {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query = $query->whereIn('id', $array_id);
                }
                if ($request->from_date && $request->to_date) {
                    $query->whereBetween('date', [$request->from_date, $request->to_date]);
                }
                if ($request->source_id) {
                    $query->where('source_id', $request->source_id);
                }
                if ($request->source_name) {
                    $query->where('source_name', $request->source_name);
                }
                if ($request->stage_id) {
                    $query = $query->whereHas('lead_assign', function ($i) use ($request) {
                        $i->where('stage_id', $request->stage_id);
                    });
                }
                $query = $query->latest();
                return Datatables::of($query)
                    ->addColumn('employee_id', function ($row) {
                        $leads = LeadAssign::where('lead_id', $row->id)->first();
                        $exp = explode(',', $leads->employee_id);
                        $emp = Employee::whereIn('id', $exp)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $btn = implode(', ', $arr);
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('branch_id', function ($row) {
                        $btn = $row->branch->name;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('client_id', function ($row) {
                        $btn = $row->client->member;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('source_ids', function ($row) {
                        $leads = LeadAssign::where('lead_id', $row->id)->first();
                        if ($leads) {
                            $exp = explode(',', $leads->employee_id);
                            $emp = Employee::whereIn('id', $exp)->get();
                            $arr = [];
                            foreach ($emp as $keys => $values) {
                                $name = LeadBasicMainChild::where('lead_id', $row->id)->where('employee_id', $values->id)->first();
                                if ($name) {
                                    $n = $name->name;
                                } else {
                                    $n = '';
                                }
                                $followup = LeadFollowUp::where('lead_id', $row->id)->where('employee_id', $values->id)->first();
                                if ($followup) {
                                    $date = date('d/m/Y', strtotime($followup->date));
                                    $status = '';
                                    if ($followup->is_completed == 1) {
                                        $status = 'Pending';
                                    } elseif ($followup->is_completed == 2) {
                                        $status = 'Discussion';
                                    } elseif ($followup->is_completed == 3) {
                                        $status = 'Rivision';
                                    } elseif ($followup->is_completed == 4) {
                                        $status = 'Win';
                                    } elseif ($followup->is_completed == 5) {
                                        $status = 'Lost';
                                    } elseif ($followup->is_completed == 6) {
                                        $status = "Showroom Visit";
                                    } elseif ($followup->is_completed == 7) {
                                        $status = "Site Measurement";
                                    } elseif ($followup->is_completed == 8) {
                                        $status = "Designing";
                                    } elseif ($followup->is_completed == 9) {
                                        $status = "Quotation";
                                    }
                                } else {
                                    $date = '-';
                                    $status = '-';
                                }
                                $data = $values->name . ' - ' . $status;
                                array_push($arr, $data);
                            }
                            $btn = implode(', <br>', $arr);
                        } else {
                            $btn = '-';
                        }
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('sourcename_ids', function ($row) {

                        return $row->sourcename
                            ? '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $row->sourcename->name . '</a>'
                            : '-';
                    })
                    ->addColumn('leaddate', function ($row) {
                        $btn = $row->date;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('leadtitles', function ($row) {
                        $btn = explode(',', $row->lead_name);
                        $cat = ProductCategory::whereIn('id', $btn)->select('name')->get();
                        $arr = [];
                        foreach ($cat as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $imp = implode(', ', $arr);
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $imp . '</a>';
                    })
                    ->addColumn('lead_name', function ($row) {
                        $exp = explode(',', $row->lead_name);
                        $emp = ProductCategory::whereIn('id', $exp)->where('status', 1)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $lead_name = implode(', ', $arr);
                        $btn = '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $lead_name . '</a>';
                        return $btn;
                    })
                    ->addColumn('company_name', function ($row) {
                        $exp = explode(',', $row->company_name);
                        $emp = Company::whereIn('id', $exp)->where('status', 1)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $company_name = implode(', ', $arr);
                        $btn = '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $company_name . '</a>';
                        return $btn;
                    })
                    ->rawColumns(['client_id', 'source_ids', 'branch_id', 'employee_id', 'sourcename_ids', 'leaddate', 'leadtitles', 'lead_name', 'company_name'])
                    ->make(true);
            }
        }
        if (Auth::guard('user')->check()) {
            if (Auth::user()->role == 1) {
                $data['client'] = Client::get();
                $data['product'] = Product::where('status', 1)->get();
                $data['stage'] = LeadStage::where('status', 1)->get();
                $data['employee'] = Employee::where('status', 1)->get();
                $data['source'] = LeadSource::where('status', 1)->get();
                $data['count'] = Lead::count();
                $data['sourcename'] = SourceName::where('status', 1)->get();
                $data['branch'] = User::where('role', 2)->where('status', 1)->get();
            } else {
                $data['client'] = Client::where('branch_id', Auth::user()->id)->get();
                $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['employee'] = Employee::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['count'] = Lead::where('branch_id', Auth::user()->id)->count();
                $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('branch_id', Auth::user()->id)->get();
                $data['company'] = Company::where('branch_id', Auth::user()->id)->get();
            }
        } else {
            $data['client'] = Client::where('branch_id', Auth::user()->branch_id)->get();
            $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('status', 1)->where('id', Auth::user()->id)->get();
            $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['count'] = Lead::where('employee_id', Auth::user()->id)->count();
            $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['productcategory'] = ProductCategory::where('branch_id', Auth::user()->branch_id)->get();
            $data['company'] = Company::where('branch_id', Auth::user()->branch_id)->get();
        }
        return view('report.leadreports')->with($data);
    }

    public function visitreportindex(Request $request)
    {
        if ($request->ajax()) {
            $query = Visits::orderBy('id', 'DESC')->with('branch', 'employee', 'sourcenm');
            if ($request->from_date && $request->to_date) {
                $startDate = Carbon::parse($request->from_date)->format('Y-m-d');
                $endDate = Carbon::parse($request->to_date)->format('Y-m-d');
                $query = $query->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
            }
            if ($request->employee_id) {
                $query = $query->where('employee_id', $request->employee_id);
            }
            if ($request->branch_id) {
                $query = $query->where('branch_id', $request->branch_id);
            }
            if ($request->source_name) {
                $query = $query->where('architect_id', $request->source_name);
            }
            if (Auth::user()->role == 2) {
                $query = $query->where('branch_id', Auth::user()->id);
            }
            $query = $query->latest();
            return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: white;" href="' . route('user.visits.show', $row->id) . '"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: blue;" class="la la-eye"></i></a>';
                    return $btn;
                })
                ->addColumn('employee_id', function ($row) {
                    $btn = Employee::where('id', $row->employee_id)->value('name');
                    return $btn;
                })
                ->addColumn('architect_id', function ($row) {
                    $btn = SourceName::where('id', $row->architect_id)->value('name');
                    return $btn;
                })
                ->addColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->addColumn('date', function ($row) {
                    $btn = date('d-m-Y', strtotime($row->date));
                    return $btn;
                })
                ->rawColumns(['employee_id', 'branch_id', 'architect_id', 'date', 'action'])
                ->make(true);
        }
        if (Auth::guard('user')->check()) {
            if (Auth::user()->role == 1) {
                $data['client'] = Client::get();
                $data['employee'] = Employee::where('status', 1)->get();
                $data['source'] = LeadSource::where('status', 1)->get();
                $data['sourcename'] = SourceName::where('status', 1)->get();
                $data['branch'] = User::where('role', 2)->where('status', 1)->get();
            } else {
                $data['client'] = Client::where('branch_id', Auth::user()->id)->get();
                $data['employee'] = Employee::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->id)->get();
            }
        } else {
            $data['client'] = Client::where('branch_id', Auth::user()->id)->get();
            $data['employee'] = Employee::where('status', 1)->where('employee_id', Auth::user()->id)->get();
            $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->id)->get();
            $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->id)->get();
        }
        return view('report.visitreports')->with($data);
    }

    public function getdata(Request $request)
    {
        $data = Employee::where('branch_id', $request->branch_id)->where('status', 1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=" . $value->id . ">" . $value->name . "</option>";
        }
        $data1 = Client::where('branch_id', $request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $html1 .= "<option value=" . $value->id . ">" . $value->member . "</option>";
        }
        $data2 = LeadSource::where('branch_id', $request->branch_id)->where('status', 1)->get();
        $html2 = "<option value=''>Select</option>";
        foreach ($data2 as $key => $value) {
            $html2 .= "<option value=" . $value->id . ">" . $value->name . "</option>";
        }
        $data3 = SourceName::where('branch_id', $request->branch_id)->where('status', 1)->get();
        $html3 = "<option value=''>Select</option>";
        foreach ($data3 as $key => $value) {
            $html3 .= "<option value=" . $value->id . ">" . $value->name . "</option>";
        }
        $data4 = ProductCategory::where('branch_id', $request->branch_id)->where('status', 1)->get();
        $html4 = "<option value=''>Select</option>";
        foreach ($data4 as $key => $value) {
            $html4 .= "<option value=" . $value->id . ">" . $value->name . "</option>";
        }
        $data5 = Company::whereRaw("FIND_IN_SET(?, branch_id)", [$request->branch_id])
                ->where('status', 1)
                ->when($request->category_id, function ($query) use ($request) {
                    $query->where('pro_category_id', $request->category_id);
                })->get();
        $html5 = "<option value=''>Select</option>";
        foreach ($data5 as $key => $value) {
            $html5 .= "<option value=" . $value->id . ">" . $value->name . "</option>";
        }
        $data6 = Landmark::where('branch_id', $request->branch_id)->get();
        $html6 = "<option value=''>Select</option>";
        foreach ($data6 as $key => $value) {
            $html6 .= "<option value=" . $value->id . ">" . $value->name . "</option>";
        }
        $data7 = Warehouse::where('branch_id', $request->branch_id)->where('status', 1)->get();
        $html7 = "<option value=''>Select</option>";
        foreach ($data7 as $key => $value) {
            $html7 .= "<option value=" . $value->id . ">" . $value->name . "</option>";
        }
        $data8 = Arid::where('branch_id', $request->branch_id)->get();
        $html8 = "<option value=''>Select</option>";
        foreach ($data8 as $key => $value) {
            $html8 .= "<option value=" . $value->id . ">" . $value->name . "</option>";
        }
        return response()->json(['data' => $html, 'data1' => $html1, 'data2' => $html2, 'data3' => $html3, 'data4' => $html4, 'data5' => $html5, 'data6' => $html6, 'data7' => $html7, 'data8' => $html8]);
    }

    public function followup_report(Request $request)
    {
        if (Auth::guard('employee')->check()) {
            if ($request->ajax()) {
                $data = LeadAssign::get();
                $array = [];
                foreach ($data as $dt) {
                    $ex_arr = explode(',', $dt->employee_id);
                    if (in_array(Auth::user()->id, $ex_arr)) {
                        array_push($array, $dt->lead_id);
                    }
                }
                $query = Lead::leftjoin('lead_followup as f', 'f.lead_id', 'lead.id')->where('lead.active', 1)->whereIn('lead.id', $array)->select('lead.*')->get();
                if ($request->employee_id) {
                    $array = [];
                    $lead = LeadAssign::get();
                    $arr = [];
                    foreach ($lead as $dt) {
                        $ex_arr = explode(',', $dt->employee_id);
                        if (in_array($request->employee_id, $ex_arr)) {
                            array_push($array, $dt->lead_id);
                        }
                    }
                    $query = $query->whereIn('id', $array);
                }
                if ($request->client_id) {
                    $query = $query->where('client_id', $request->client_id);
                }
                if ($request->source_id) {
                    $query = $query->where('source_id', $request->source_id);
                }
                if ($request->source_name) {
                    $query = $query->where('client_id', $request->source_name);
                }
                if ($request->lead_name) {
                    $array_id = [];
                    foreach ($query as $dt) {
                        $co_co = explode(',', $dt['lead_name']);
                        foreach ($co_co as $co) {
                            if ($co == $request->lead_name) {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query = $query->whereIn('id', $array_id);
                }
                if ($request->company_name) {
                    $array_id = [];
                    foreach ($query as $dt) {
                        $co_co = explode(',', $dt['company_name']);
                        foreach ($co_co as $co) {
                            if ($co == $request->company_name) {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query = $query->whereIn('id', $array_id);
                }
                if ($request->from_date && $request->to_date) {
                    $query =  $query->where('date', '>=', $request->from_date)->where('date', '<=', $request->to_date);
                }
                return Datatables::of($query)
                    ->addColumn('employee_id', function ($row) {
                        $leads = LeadAssign::where('lead_id', $row->id)->first();
                        if ($leads) {
                            $exp = explode(',', $leads->employee_id);
                            $emp = Employee::whereIn('id', $exp)->get();
                            $arr = [];
                            foreach ($emp as $key => $value) {
                                array_push($arr, $value->name);
                            }
                            $btn = implode(', ', $arr);
                        } else {
                            $btn = '';
                        }
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('branch_id', function ($row) {
                        $btn = $row->branch->name;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('client_id', function ($row) {
                        $btn = $row->client->member;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('source_ids', function ($row) {
                        $btn = $row->source->name;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('sourcename_ids', function ($row) {
                        $btn = $row->sourcename->name;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('leaddate', function ($row) {
                        $btn = $row->date;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('leadtitles', function ($row) {
                        $btn = explode(',', $row->lead_name);
                        $cat = ProductCategory::whereIn('id', $btn)->select('name')->get();
                        $arr = [];
                        foreach ($cat as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $imp = implode(', ', $arr);
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $imp . '</a>';
                    })
                    ->addColumn('followup_status', function ($row) {
                        $followup = LeadFollowUp::where('lead_id', $row->id)->where('employee_id', Auth::user()->id)->first();
                        $followup_log = FollowupLog::where('lead_id', $row->id)->where('employee_id', Auth::user()->id)->latest()->first();
                        if ($followup) {
                            if ($followup_log) {
                                $date = date('d/m/Y', strtotime($followup_log->date));
                            } else {
                                $date = '';
                            }
                            $status = '';
                            if ($followup->is_completed == 1) {
                                $status = 'Pending';
                            } elseif ($followup->is_completed == 2) {
                                $status = 'Discussion';
                            } elseif ($followup->is_completed == 3) {
                                $status = 'Rivision';
                            } elseif ($followup->is_completed == 4) {
                                $status = 'Win';
                            } elseif ($followup->is_completed == 5) {
                                $status = 'Lost';
                            } elseif ($followup->is_completed == 6) {
                                $status = 'Showroom Visit';
                            } elseif ($followup->is_completed == 7) {
                                $status = 'Site Measurement';
                            } elseif ($followup->is_completed == 8) {
                                $status = 'Designing';
                            } elseif ($followup->is_completed == 9) {
                                $status = 'Quotation';
                            }
                        } else {
                            $date = '-';
                            $status = '-';
                        }
                        $data = Auth::user()->name . ' - ' . $status . '-' . $date;
                        $btn = $data;
                        return $btn;
                    })
                    ->addColumn('lead_name', function ($row) {
                        $exp = explode(',', $row->lead_name);
                        $emp = ProductCategory::whereIn('id', $exp)->where('status', 1)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $lead_name = implode(', ', $arr);
                        $btn = '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $lead_name . '</a>';
                        return $btn;
                    })
                    ->addColumn('company_name', function ($row) {
                        $exp = explode(',', $row->company_name);
                        $emp = Company::whereIn('id', $exp)->where('status', 1)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $company_name = implode(', ', $arr);
                        $btn = '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $company_name . '</a>';
                        return $btn;
                    })
                    ->rawColumns(['client_id', 'source_ids', 'sourcename_ids', 'leaddate', 'leadtitles', 'followup_status', 'lead_name', 'company_name'])
                    ->make(true);
            }
        } else {
            if ($request->ajax()) {
                if (Auth::guard('user')->check()) {
                    if (Auth::user()->role == 1) {
                        $query = Lead::leftjoin('lead_followup as f', 'f.lead_id', 'lead.id')->where('lead.active', 1)->select('lead.*')->get();
                    } else if (Auth::user()->role == 2) {
                        $query = Lead::leftjoin('lead_followup as f', 'f.lead_id', 'lead.id')->where('lead.active', 1)->where('lead.branch_id', Auth::user()->id)->select('lead.*')->get();
                    }
                }
                if ($request->branch_id) {
                    $query = $query->where('branch_id', $request->branch_id);
                }
                if ($request->employee_id) {
                    $array = [];
                    $lead = LeadAssign::get();
                    $arr = [];
                    foreach ($lead as $dt) {
                        $ex_arr = explode(',', $dt->employee_id);
                        if (in_array($request->employee_id, $ex_arr)) {
                            array_push($array, $dt->lead_id);
                        }
                    }
                    $query = $query->whereIn('id', $array);
                }
                if ($request->client_id) {
                    $query = $query->where('client_id', $request->client_id);
                }
                if ($request->source_id) {
                    $query = $query->where('source_id', $request->source_id);
                }
                if ($request->source_name) {
                    $query = $query->where('client_id', $request->source_name);
                }
                if ($request->lead_name) {
                    $array_id = [];
                    foreach ($query as $dt) {
                        $co_co = explode(',', $dt['lead_name']);
                        foreach ($co_co as $co) {
                            if ($co == $request->lead_name) {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query = $query->whereIn('id', $array_id);
                }
                if ($request->company_name) {
                    $array_id = [];
                    foreach ($query as $dt) {
                        $co_co = explode(',', $dt['company_name']);
                        foreach ($co_co as $co) {
                            if ($co == $request->company_name) {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query = $query->whereIn('id', $array_id);
                }
                if ($request->from_date && $request->to_date) {
                    $query =  $query->where('date', '>=', $request->from_date)->where('date', '<=', $request->to_date);
                }
                return Datatables::of($query)
                    ->addColumn('employee_id', function ($row) {
                        $leads = LeadAssign::where('lead_id', $row->id)->first();
                        if ($leads) {
                            $exp = explode(',', $leads->employee_id);
                            if ($exp) {
                                $emp = Employee::whereIn('id', $exp)->get();
                                if ($emp) {
                                    $arr = [];
                                    foreach ($emp as $key => $value) {
                                        array_push($arr, $value->name);
                                    }
                                    $btn = implode(', ', $arr);
                                } else {
                                    $btn = '-';
                                }
                            } else {
                                $btn = '-';
                            }
                        } else {
                            $btn = '-';
                        }
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('branch_id', function ($row) {
                        if ($row->branch) {
                            $btn = $row->branch->name;
                        } else {
                            $btn = '-';
                        }
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('client_id', function ($row) {
                        if ($row->client) {
                            $btn = $row->client->member;
                        } else {
                            $btn = '-';
                        }
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('source_ids', function ($row) {
                        $btn = $row->source->name;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('sourcename_ids', function ($row) {
                        if ($row->sourcename) {
                            $btn = $row->sourcename->name;
                        } else {
                            $btn = '-';
                        }
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('leaddate', function ($row) {
                        $btn = $row->date;
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $btn . '</a>';
                    })
                    ->addColumn('leadtitles', function ($row) {
                        $btn = explode(',', $row->lead_name);
                        $cat = ProductCategory::whereIn('id', $btn)->select('name')->get();
                        $arr = [];
                        foreach ($cat as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $imp = implode(', ', $arr);
                        return '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $imp . '</a>';
                    })
                    ->addColumn('followup_status', function ($row) {
                        $leads = LeadAssign::where('lead_id', $row->id)->first();
                        $exp = explode(',', $leads->employee_id);
                        $emp = Employee::whereIn('id', $exp)->get();
                        $arr = [];
                        foreach ($emp as $keys => $values) {
                            $followup = LeadFollowUp::where('lead_id', $row->id)->where('employee_id', $values->id)->first();
                            $followup_log = FollowupLog::where('lead_id', $row->id)->where('employee_id', $values->id)->latest()->first();
                            if ($followup) {
                                if ($followup_log) {
                                    $date = date('d/m/Y', strtotime($followup_log->date));
                                } else {
                                    $date = '';
                                }
                                $status = '';
                                if ($followup->is_completed == 1) {
                                    $status = 'Pending';
                                } elseif ($followup->is_completed == 2) {
                                    $status = 'Discussion';
                                } elseif ($followup->is_completed == 3) {
                                    $status = 'Rivision';
                                } elseif ($followup->is_completed == 4) {
                                    $status = 'Win';
                                } elseif ($followup->is_completed == 5) {
                                    $status = 'Lost';
                                }
                            } else {
                                $date = '-';
                                $status = '-';
                            }
                            $data = $values->name . ' - ' . $status . '-' . $date;
                            array_push($arr, $data);
                        }
                        $btn = implode(', <br>', $arr);
                        return $btn;
                    })
                    ->addColumn('lead_name', function ($row) {
                        $exp = explode(',', $row->lead_name);
                        $emp = ProductCategory::whereIn('id', $exp)->where('status', 1)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $lead_name = implode(', ', $arr);
                        $btn = '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $lead_name . '</a>';
                        return $btn;
                    })
                    ->addColumn('company_name', function ($row) {
                        $exp = explode(',', $row->company_name);
                        $emp = Company::whereIn('id', $exp)->where('status', 1)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr, $value->name);
                        }
                        $company_name = implode(', ', $arr);
                        $btn = '<a href="' . route("user.lead.edit", $row->id) . '" style="color: black;">' . $company_name . '</a>';
                        return $btn;
                    })
                    ->rawColumns(['client_id', 'source_ids', 'branch_id', 'employee_id', 'sourcename_ids', 'leaddate', 'leadtitles', 'followup_status', 'lead_name', 'company_name'])
                    ->make(true);
            }
        }
        if (Auth::guard('user')->check()) {
            if (Auth::user()->role == 1) {
                $data['client'] = Client::get();
                $data['product'] = Product::where('status', 1)->get();
                $data['stage'] = LeadStage::where('status', 1)->get();
                $data['employee'] = Employee::where('status', 1)->get();
                $data['source'] = LeadSource::where('status', 1)->get();
                $data['count'] = Lead::count();
                $data['sourcename'] = SourceName::where('status', 1)->get();
                $data['branch'] = User::where('role', 2)->where('status', 1)->get();
            } else {
                $data['client'] = Client::where('branch_id', Auth::user()->id)->get();
                $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['employee'] = Employee::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['count'] = Lead::where('branch_id', Auth::user()->id)->count();
                $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('branch_id', Auth::user()->id)->get();
                $data['company'] = Company::where('branch_id', Auth::user()->id)->get();
            }
        } else {
            $data['client'] = Client::where('branch_id', Auth::user()->branch_id)->get();
            $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('status', 1)->where('id', Auth::user()->id)->get();
            $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['count'] = Lead::where('employee_id', Auth::user()->id)->count();
            $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['productcategory'] = ProductCategory::where('branch_id', Auth::user()->branch_id)->get();
            $data['company'] = Company::where('branch_id', Auth::user()->branch_id)->get();
        }
        return view('report.followup')->with($data);
    }

    public function todays_followup_report(Request $request)
    {
        if ($request->ajax()) {
            if (Auth::guard('user')->check()) {
                if (Auth::user()->role == 1) {
                    $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.active', 1)->whereDate('followup_log.date', date('Y-m-d'))->select('followup_log.*');
                } else if (Auth::user()->role == 2) {
                    $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.branch_id', Auth::user()->id)->where('ld.active', 1)->whereDate('followup_log.date', date('Y-m-d'))->select('followup_log.*');
                }
            }
            if (Auth::guard('employee')->check()) {
                $data = Lead::with('client', 'lead_assign.stage', 'lead_assign.employee', 'sourcename', 'source')->where('active', 1);
                $data = LeadAssign::get();
                $array = [];
                foreach ($data as $dt) {
                    $ex_arr = explode(',', $dt->employee_id);
                    if (in_array(Auth::user()->id, $ex_arr)) {
                        array_push($array, $dt->lead_id);
                    }
                }
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.employee_id', $array)->where('ld.active', 1)->whereDate('followup_log.date', date('Y-m-d'))->select('followup_log.*');
            }
            if ($request->branch_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.branch_id', $request->branch_id)->select('followup_log.*');
            }
            if ($request->employee_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('followup_log.employee_id', $request->employee_id)->select('followup_log.*');
            }
            if ($request->client_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.client_id', $request->client_id)->select('followup_log.*');
            }
            if ($request->source_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.source_id', $request->source_id)->select('followup_log.*');
            }
            if ($request->source_name) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.source_name', $request->source_name)->select('followup_log.*');
            }
            if ($request->stage_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.stage_id', $request->stage_id)->select('followup_log.*');
            }
            $query = $data->latest();
            return Datatables::of($query)
                ->addColumn('branch', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $branch = User::where('id', $lead->branch_id)->value('name');
                    if ($branch) {
                        $btn = $branch;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('employee', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $employee = Employee::where('id', $lead->employee_id)->value('name');
                    if ($employee) {
                        $btn = $employee;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('client', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $client = Client::where('id', $lead->client_id)->value('member');
                    if ($client) {
                        $btn = $client;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('leaddate', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->value('date');
                    if ($lead) {
                        $btn = date('d-m-Y', strtotime($lead));
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('source', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $source = LeadSource::where('id', $lead->source_id)->value('name');
                    if ($source) {
                        $btn = $source;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('sourcename', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $source_name = SourceName::where('id', $lead->source_name)->value('name');
                    if ($source_name) {
                        $btn = $source_name;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('time', function ($row) {
                    $ac = date('H:i A', strtotime($row->time));
                    return $ac;
                })
                ->addColumn('type', function ($row) {
                    $type = Type::where('id', $row->type)->value('name');
                    if ($type) {
                        $btn = $type;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->is_completed;
                    if ($status == 1) {
                        $btn = "Pending";
                    } elseif ($status == 2) {
                        $btn = "Discussion";
                    } elseif ($status == 3) {
                        $btn = "Rivision";
                    } elseif ($status == 4) {
                        $btn = "Win";
                    } elseif ($status == 5) {
                        $btn = "Lost";
                    } elseif ($status == 6) {
                        $btn = "Showroom Visit";
                    } elseif ($status == 7) {
                        $btn = "Site Measurement";
                    } elseif ($status == 8) {
                        $btn = "Designing";
                    } elseif ($status == 9) {
                        $btn = "Quotation";
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->rawColumns(['branch', 'employee', 'client', 'leaddate', 'source', 'sourcename', 'time', 'type', 'status'])
                ->make(true);
        }
        if (Auth::guard('user')->check()) {
            if (Auth::user()->role == 1) {
                $data['client'] = Client::get();
                $data['product'] = Product::where('status', 1)->get();
                $data['stage'] = LeadStage::where('status', 1)->get();
                $data['employee'] = Employee::where('status', 1)->get();
                $data['source'] = LeadSource::where('status', 1)->get();
                $data['count'] = Lead::count();
                $data['sourcename'] = SourceName::where('status', 1)->get();
                $data['branch'] = User::where('role', 2)->where('status', 1)->get();
            } else {
                $data['client'] = Client::where('branch_id', Auth::user()->id)->get();
                $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['employee'] = Employee::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['count'] = Lead::where('branch_id', Auth::user()->id)->count();
                $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('branch_id', Auth::user()->id)->get();
                $data['company'] = Company::where('branch_id', Auth::user()->id)->get();
            }
        } else {
            $data['client'] = Client::where('branch_id', Auth::user()->branch_id)->get();
            $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('status', 1)->where('id', Auth::user()->id)->get();
            $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['count'] = Lead::where('employee_id', Auth::user()->id)->count();
            $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['productcategory'] = ProductCategory::where('branch_id', Auth::user()->branch_id)->get();
            $data['company'] = Company::where('branch_id', Auth::user()->branch_id)->get();
        }
        return view('report.todaysfollowup')->with($data);
    }

    public function yesterday_followup_report(Request $request)
    {
        if ($request->ajax()) {
            $yesterday = date("Y-m-d", strtotime("- 1 day"));
            if (Auth::guard('user')->check()) {
                if (Auth::user()->role == 1) {
                    $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.active', 1)->whereDate('followup_log.date', $yesterday)->select('followup_log.*');
                } else if (Auth::user()->role == 2) {
                    $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.branch_id', Auth::user()->id)->where('ld.active', 1)->whereDate('followup_log.date', $yesterday)->select('followup_log.*');
                }
            }
            if (Auth::guard('employee')->check()) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.employee_id', Auth::user()->id)->where('ld.active', 1)->whereDate('followup_log.date', $yesterday)->select('followup_log.*');
            }
            if ($request->employee_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('followup_log.employee_id', $request->employee_id)->select('followup_log.*');
            }
            if ($request->branch_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.branch_id', $request->branch_id)->select('followup_log.*');
            }
            if ($request->client_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.client_id', $request->client_id)->select('followup_log.*');
            }
            if ($request->source_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.source_id', $request->source_id)->select('followup_log.*');
            }
            if ($request->source_name) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.source_name', $request->source_name)->select('followup_log.*');
            }
            if ($request->stage_id) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.stage_id', $request->stage_id)->select('followup_log.*');
            }
            $query = $data->latest();
            return Datatables::of($query)
                ->addColumn('branch', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $branch = User::where('id', $lead->branch_id)->value('name');
                    if ($branch) {
                        $btn = $branch;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('employee', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $employee = Employee::where('id', $lead->employee_id)->value('name');
                    if ($employee) {
                        $btn = $employee;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('client', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $client = Client::where('id', $lead->client_id)->value('member');
                    if ($client) {
                        $btn = $client;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('leaddate', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->value('date');
                    if ($lead) {
                        $btn = date('d-m-Y', strtotime($lead));
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('source', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $source = LeadSource::where('id', $lead->source_id)->value('name');
                    if ($source) {
                        $btn = $source;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('sourcename', function ($row) {
                    $lead = Lead::where('active', 1)->where('id', $row->lead_id)->first();
                    $source_name = SourceName::where('id', $lead->source_name)->value('name');
                    if ($source_name) {
                        $btn = $source_name;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('time', function ($row) {
                    $ac = date('H:i A', strtotime($row->time));
                    return $ac;
                })
                ->addColumn('type', function ($row) {
                    $type = Type::where('id', $row->type)->value('name');
                    if ($type) {
                        $btn = $type;
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->is_completed;
                    if ($status == 1) {
                        $btn = "Pending";
                    } elseif ($status == 2) {
                        $btn = "Discussion";
                    } elseif ($status == 3) {
                        $btn = "Rivision";
                    } elseif ($status == 4) {
                        $btn = "Win";
                    } elseif ($status == 5) {
                        $btn = "Lost";
                    } elseif ($status == 6) {
                        $btn = "Showroom Visit";
                    } elseif ($status == 7) {
                        $btn = "Site Measurement";
                    } elseif ($status == 8) {
                        $btn = "Designing";
                    } elseif ($status == 9) {
                        $btn = "Quotation";
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->rawColumns(['branch', 'employee', 'client', 'leaddate', 'source', 'sourcename', 'time', 'type', 'status'])
                ->make(true);
        }
        if (Auth::guard('user')->check()) {
            if (Auth::user()->role == 1) {
                $data['client'] = Client::get();
                $data['product'] = Product::where('status', 1)->get();
                $data['stage'] = LeadStage::where('status', 1)->get();
                $data['employee'] = Employee::where('status', 1)->get();
                $data['source'] = LeadSource::where('status', 1)->get();
                $data['count'] = Lead::count();
                $data['sourcename'] = SourceName::where('status', 1)->get();
                $data['branch'] = User::where('role', 2)->where('status', 1)->get();
            } else {
                $data['client'] = Client::where('branch_id', Auth::user()->id)->get();
                $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['employee'] = Employee::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['count'] = Lead::where('branch_id', Auth::user()->id)->count();
                $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('branch_id', Auth::user()->id)->get();
                $data['company'] = Company::where('branch_id', Auth::user()->id)->get();
            }
        } else {
            $data['client'] = Client::where('branch_id', Auth::user()->branch_id)->get();
            $data['product'] = Product::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['stage'] = LeadStage::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('status', 1)->where('id', Auth::user()->id)->get();
            $data['source'] = LeadSource::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['count'] = Lead::where('employee_id', Auth::user()->id)->count();
            $data['sourcename'] = SourceName::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data['productcategory'] = ProductCategory::where('branch_id', Auth::user()->branch_id)->get();
            $data['company'] = Company::where('branch_id', Auth::user()->branch_id)->get();
        }
        return view('report.yesterdayfollowup')->with($data);
    }

    public function leadreportexport(Request $request)
    {
        $param = $request->all();
        return Excel::download(new LeadExport($param), 'leadreport.xlsx');
    }

    public function followupreportexport(Request $request)
    {
        $param = $request->all();
        return Excel::download(new FollowupExport($param), 'followupreport.xlsx');
    }

    public function todayreportexport(Request $request)
    {
        $param = $request->all();
        return Excel::download(new TodayFollowupExport($param), 'todayfollowupreport.xlsx');
    }

    public function yesterdayreportexport(Request $request)
    {
        $param = $request->all();
        return Excel::download(new YesterdayFollowupExport($param), 'yesterdayfollowupreport.xlsx');
    }

    public function leadreportpdf(Request $request)
    {
        $param = $request->all();
        if (Auth::guard('employee')->check()) {
            $query = LeadFollowUp::leftJoin('lead as l', 'l.id', 'lead_followup.lead_id')->where('l.active', 1)->where('lead_followup.employee_id', Auth::user()->id);
            if ($request->from_date) {
                $query->where('l.date', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->where('l.date', '<=', $request->to_date);
            }
            if ($request->client_id) {
                $query->where('l.client_id', $request->client_id)->latest();
            }
            if ($request->lead_name) {
                $data = $query->select('lead_followup.*')->get();
                $array_id = [];
                foreach ($data as $dt) {
                    $lead = Lead::where('id', $dt->lead_id)->first();
                    $co_co = explode(',', $lead->lead_name);
                    foreach ($co_co as $co) {
                        if ($co == $request->lead_name) {
                            array_push($array_id, $dt->id);
                        }
                    }
                }
                $query->whereIn('lead_followup.id', $array_id);
            }
            if ($request->company_name) {
                $data = $query->select('lead_followup.*')->get();
                $array_id = [];
                foreach ($data as $dt) {
                    $lead = Lead::where('id', $dt->lead_id)->first();
                    $co_co = explode(',', $lead->company_name);
                    foreach ($co_co as $co) {
                        if ($co == $request->company_name) {
                            array_push($array_id, $dt->id);
                        }
                    }
                }
                $query->whereIn('lead_followup.id', $array_id);
            }
            if ($request->source_id) {
                $query->where('l.source_id', $request->source_id);
            }
            if ($request->source_name) {
                $query->where('l.source_name', $request->source_name);
            }
            $data['data'] = $query->select('lead_followup.*')->get();
        } else {
            $query = LeadFollowUp::leftJoin('lead as l', 'l.id', 'lead_followup.lead_id')->where('l.active', 1);
            if (Auth::user()->role == 2) {
                $query = LeadFollowUp::leftjoin('lead as l', 'l.id', 'lead_followup.lead_id')->where('l.active', 1)->where('l.branch_id', Auth::user()->id);
            }
            if ($request->employee_id) {
                $query->where('lead_followup.employee_id', $request->employee_id);
            }
            if ($request->branch_id) {
                $query->where('l.branch_id', $request->branch_id);
            }
            if ($request->client_id) {
                $query->where('l.client_id', $request->client_id);
            }
            if ($request->lead_name) {
                $data = $query->select('lead_followup.*')->get();
                $array_id = [];
                foreach ($data as $dt) {
                    $lead = Lead::where('id', $dt->lead_id)->first();
                    $co_co = explode(',', $lead->lead_name);
                    foreach ($co_co as $co) {
                        if ($co == $request->lead_name) {
                            array_push($array_id, $dt->id);
                        }
                    }
                }
                $query->whereIn('lead_followup.id', $array_id);
            }
            if ($request->company_name) {
                $data = $query->select('lead_followup.*')->get();
                $array_id = [];
                foreach ($data as $dt) {
                    $lead = Lead::where('id', $dt->lead_id)->first();
                    $co_co = explode(',', $lead->company_name);
                    foreach ($co_co as $co) {
                        if ($co == $request->company_name) {
                            array_push($array_id, $dt->id);
                        }
                    }
                }
                $query->whereIn('lead_followup.id', $array_id);
            }
            if ($request->from_date) {
                $query->where('l.date', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->where('l.date', '<=', $request->to_date);
            }
            if ($request->source_id) {
                $query->where('l.source_id', $request->source_id);
            }
            if ($request->source_name) {
                $query->where('l.source_name', $request->source_name);
            }
            $data['data'] = $query->select('lead_followup.*')->get();
        }
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.leadreportpdf', $data)->setPaper('Legal', 'landscape');
        return $pdf->download('LeadReport.pdf');
    }

    public function followupreportpdf(Request $request)
    {
        $param = $request->all();
        if (Auth::guard('employee')->check()) {
            $data = LeadAssign::get();
            $array = [];
            foreach ($data as $dt) {
                $ex_arr = explode(',', $dt->employee_id);
                if (in_array(Auth::user()->id, $ex_arr)) {
                    array_push($array, $dt->lead_id);
                }
            }
            $query = Lead::leftjoin('lead_followup as f', 'f.lead_id', 'lead.id')->where('lead.active', 1)->whereIn('lead.id', $array);
            if ($request->from_date) {
                $query->where('lead.date', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->where('lead.date', '<=', $request->to_date);
            }
            if ($request->client_id) {
                $query->where('lead.client_id', $request->client_id);
            }
            if ($request->lead_name) {
                $data = $query->select('lead.*')->get();
                $array_id = [];
                foreach ($data as $dt) {
                    $co_co = explode(',', $dt['lead_name']);
                    foreach ($co_co as $co) {
                        if ($co == $request->lead_name) {
                            array_push($array_id, $dt->id);
                        }
                    }
                }
                $query->whereIn('lead.id', $array_id);
            }
            if ($request->company_name) {
                $data = $query->select('lead.*')->get();
                $array_id = [];
                foreach ($data as $dt) {
                    $co_co = explode(',', $dt['company_name']);
                    foreach ($co_co as $co) {
                        if ($co == $request->company_name) {
                            array_push($array_id, $dt->id);
                        }
                    }
                }
                $query->whereIn('lead.id', $array_id);
            }
            if ($request->source_id) {
                $query->where('lead.source_id', $request->source_id);
            }
            if ($request->source_name) {
                $query->where('lead.source_name', $request->source_name);
            }
            if ($request->stage_id) {
                $query = $query->whereHas('lead_assign', function ($i) use ($request) {
                    $i->where('stage_id', $request->stage_id);
                });
            }
            $data['data'] = $query->select('lead.*')->get();
        } else {
            $query = Lead::leftjoin('lead_followup as f', 'f.lead_id', 'lead.id')->where('lead.active', 1);
            if (Auth::user()->role == 2) {
                $query = Lead::leftjoin('lead_followup as f', 'f.lead_id', 'lead.id')->where('lead.active', 1)->where('lead.branch_id', Auth::user()->id);
            }
            if ($request->employee_id) {
                $array = [];
                $lead = LeadAssign::get();
                $arr = [];
                foreach ($lead as $dt) {
                    $ex_arr = explode(',', $dt->employee_id);
                    if (in_array($request->employee_id, $ex_arr)) {
                        array_push($array, $dt->lead_id);
                    }
                }
                $query->whereIn('lead.id', $array);
            }
            if ($request->branch_id) {
                $query->where('lead.branch_id', $request->branch_id);
            }
            if ($request->client_id) {
                $query->where('lead.client_id', $request->client_id);
            }
            if ($request->lead_name) {
                $data = $query->select('lead.*')->get();
                $array_id = [];
                foreach ($data as $dt) {
                    $co_co = explode(',', $dt['lead_name']);
                    foreach ($co_co as $co) {
                        if ($co == $request->lead_name) {
                            array_push($array_id, $dt->id);
                        }
                    }
                }
                $query->whereIn('lead.id', $array_id);
            }
            if ($request->company_name) {
                $data = $query->select('lead.*')->get();
                $array_id = [];
                foreach ($data as $dt) {
                    $co_co = explode(',', $dt['company_name']);
                    foreach ($co_co as $co) {
                        if ($co == $request->company_name) {
                            array_push($array_id, $dt->id);
                        }
                    }
                }
                $query->whereIn('lead.id', $array_id);
            }
            if ($request->from_date && $request->to_date) {
                $query->where('lead.date', '>=', $request->from_date)->where('lead.date', '<=', $request->to_date);
            }
            if ($request->source_id) {
                $query->where('lead.source_id', $request->source_id);
            }
            if ($request->source_name) {
                $query->where('lead.source_name', $request->source_name);
            }
            $data['data'] = $query->select('lead.*')->get();
        }
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.followupreportpdf', $data)->setPaper('A4', 'landscape');
        return $pdf->download('FollowupReport.pdf');
    }

    public function todayreportpdf(Request $request)
    {
        if (Auth::guard('user')->check()) {
            if (Auth::user()->role == 1) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.active', 1)->whereDate('followup_log.date', date('Y-m-d'))->select('followup_log.*');
            } else if (Auth::user()->role == 2) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.branch_id', Auth::user()->id)->where('ld.active', 1)->whereDate('followup_log.date', date('Y-m-d'))->select('followup_log.*');
            }
        }
        if (Auth::guard('employee')->check()) {
            $data = Lead::with('client', 'lead_assign.stage', 'lead_assign.employee', 'sourcename', 'source')->where('active', 1);
            $data = LeadAssign::get();
            $array = [];
            foreach ($data as $dt) {
                $ex_arr = explode(',', $dt->employee_id);
                if (in_array(Auth::user()->id, $ex_arr)) {
                    array_push($array, $dt->lead_id);
                }
            }
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.employee_id', $array)->where('ld.active', 1)->whereDate('followup_log.date', date('Y-m-d'))->select('followup_log.*');
        }
        if ($request->branch_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.branch_id', $request->branch_id)->select('followup_log.*');
        }
        if ($request->employee_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('followup_log.employee_id', $request->employee_id)->select('followup_log.*');
        }
        if ($request->client_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.client_id', $request->client_id)->select('followup_log.*');
        }
        if ($request->source_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.source_id', $request->source_id)->select('followup_log.*');
        }
        if ($request->source_name) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.source_name', $request->source_name)->select('followup_log.*');
        }
        if ($request->stage_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', date('Y-m-d'))->where('ld.active', 1)->where('ld.stage_id', $request->stage_id)->select('followup_log.*');
        }
        $query['query'] = $data->get();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.todayreportpdf', $query)->setPaper('A4', 'landscape');
        return $pdf->download('TodayFollowupReport.pdf');
    }

    public function yesterdayreportpdf(Request $request)
    {
        $yesterday = date("Y-m-d", strtotime("- 1 day"));
        if (Auth::guard('user')->check()) {
            if (Auth::user()->role == 1) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.active', 1)->whereDate('followup_log.date', $yesterday)->select('followup_log.*');
            } else if (Auth::user()->role == 2) {
                $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.branch_id', Auth::user()->id)->where('ld.active', 1)->whereDate('followup_log.date', $yesterday)->select('followup_log.*');
            }
        }
        if (Auth::guard('employee')->check()) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('ld.employee_id', Auth::user()->id)->where('ld.active', 1)->whereDate('followup_log.date', $yesterday)->select('followup_log.*');
        }
        if ($request->branch_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.branch_id', $request->branch_id)->select('followup_log.*');
        }
        if ($request->employee_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('followup_log.employee_id', $request->employee_id)->select('followup_log.*');
        }
        if ($request->client_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.client_id', $request->client_id)->select('followup_log.*');
        }
        if ($request->source_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.source_id', $request->source_id)->select('followup_log.*');
        }
        if ($request->source_name) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.source_name', $request->source_name)->select('followup_log.*');
        }
        if ($request->stage_id) {
            $data = FollowupLog::leftjoin('lead as ld', 'ld.id', 'followup_log.lead_id')->where('followup_log.date', $yesterday)->where('ld.active', 1)->where('ld.stage_id', $request->stage_id)->select('followup_log.*');
        }
        $query['query'] = $data->get();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.yesterdayreportpdf', $query)->setPaper('A4', 'landscape');
        return $pdf->download('YesterdayFollowupReport.pdf');
    }

    public function stock_report(Request $request, $product_id, $warehouse_id)
    {
        $data['product'] = Product::where('id', $product_id)->first();
         $data['warehouse_id'] = $warehouse_id;

        $data['stock_data'] = ItemLedger::with('vendors', 'clients', 'products')
            ->where('product_id', $product_id)
            ->get();

                $data['transferHistory'] = StockTransferHistory::with(
                'branch',
                'product.productcategory',
                'product.company',
                'fromWarehouse',
                'toWarehouse'
            )
            ->where('product_id', $product_id)
            ->where(function ($q) use ($warehouse_id) {

                $q->where(function ($sub) use ($warehouse_id) {
                    $sub->where('from_warehouse_id', $warehouse_id)
                        ->where('outward', '>', 0);
                })
                ->orWhere(function ($sub) use ($warehouse_id) {
                    $sub->where('to_warehouse_id', $warehouse_id)
                        ->where('inward', '>', 0);
                });

            })
            ->orderBy('transfer_date', 'desc')
            ->get();
        return view('report.stock_report')->with($data);
    }

    public function stock_report_pdf(Request $request, $product_id,$warehouse_id)
    {
        $data['product'] = Product::findOrFail($product_id);
         $data['warehouse_id'] = $warehouse_id;

        if ($request->report_type == 'stock_report') {

            $data['stock_data'] = ItemLedger::with('vendors', 'clients', 'products')
                ->where('product_id', $product_id)
                ->get();

            $pdf = PDF::setOptions([
                'logOutputFile' => storage_path('logs/log.htm'),
                'tempDir' => storage_path('logs/'),
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ])->loadView('report.stock_report_pdf', $data)
            ->setPaper('A4', 'landscape');

            return $pdf->download('Stock-Report.pdf');
        }

        if ($request->report_type == 'transfer_history') {

         
            $data['transferHistory'] = StockTransferHistory::with([
                'branch',
                'product.productcategory',
                'product.company',
                'fromWarehouse',
                'toWarehouse'
            ])
            ->where('product_id', $product_id)
            ->where(function ($q) use ($warehouse_id) {

                $q->where(function ($sub) use ($warehouse_id) {
                    $sub->where('from_warehouse_id', $warehouse_id)
                        ->where('outward', '>', 0);
                })
                ->orWhere(function ($sub) use ($warehouse_id) {
                    $sub->where('to_warehouse_id', $warehouse_id)
                        ->where('inward', '>', 0);
                });

            })
            ->orderBy('transfer_date', 'desc')
            ->get();
            $pdf = PDF::setOptions([
                'logOutputFile' => storage_path('logs/log.htm'),
                'tempDir' => storage_path('logs/'),
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ])->loadView('report.stock_transfer_history_pdf', $data)
            ->setPaper('A4', 'landscape');

            return $pdf->download('Stock-Transfer-History.pdf');
        }
    }
    
}
