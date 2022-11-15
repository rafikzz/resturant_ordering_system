<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerWalletTransaction;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PatientController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:patient_list|patient_create|patient_edit|patient_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:patient_create', ['only' => ['create','store']]);
        $this->middleware('permission:patient_discharge', ['only' => ['discharge']]);

        $this->title = 'Patient Management';
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Patient'=>route('admin.patients.index')];
        return view('admin.patients.index', compact('title','breadcrumbs'));
    }

    public function create()
    {
        $title = $this->title;
        $breadcrumbs =[ 'Patient'=>route('admin.patients.index'),'Create'=>'#'];

        return view('admin.patients.create', compact('title','breadcrumbs'));

    }

    public function store(StoreCustomerRequest $request)
    {

        Customer::create([
            'name' => $request->name,
            'phone_no'=> $request->phone_no,
            'is_staff'=> 0,
            'status'=>1,
            'room_no'=>$request->room_no,
        ]);
        if(isset($request->new))
        {
            return redirect()->route('admin.patients.create')->with("success", "Patient saved successfully");

        }else{
            return redirect()->route('admin.patients.index')->with("success", "Patient saved successfully");

        }
    }

    public function edit($id)
    {
        $customer =Customer::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'Patient'=>route('admin.patients.index'),'Edit'=>'#'];

        return view('admin.patients.edit', compact('title','customer','breadcrumbs'));

    }

    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer =Customer::findOrFail($id);
        $customer->name =$request->name;
        $customer->phone_no =$request->phone_no;
        $customer->save();
        return redirect()->route('admin.patients.index')->with("success", "Patient updated successfully");
    }

    public function show($id)
    {
        $customer =Customer::where('is_staff',0)->findOrFail($id);

        $title = $this->title;
        $breadcrumbs =[ 'Patient'=>route('admin.patients.index'), 'Show'=>'#'];


       return view('admin.patients.show',compact('title','customer','breadcrumbs'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select('table_customers.*')->where('is_staff',0);
            $canEdit = auth()->user()->can('patient_edit');
            $canDischarge = auth()->user()->can('patient_discharge');



            return DataTables::of($data)
                ->editColumn('created_at', function (Customer $status) {
                    return [
                        'display' => $status->created_at->diffForHumans(),
                        'timestamp' => $status->created_at
                    ];
                })

                ->addColumn(
                    'action',
                    function ($row)use ($canEdit,$canDischarge) {
                        $editBtn = $canEdit? '<a href="'.route('admin.patients.edit', $row->id).'"
                        class="btn btn-xs btn-warning"><i class="fa fa-pencil-alt"></i></a>':'';
                        if($row->status)
                        {
                            $dischargeBtn =  $canDischarge ? '<button type="submit" class="btn btn-xs btn-success"><i class="fa fa-directions"> Discharge</i></button>' : '';

                        }
                        else
                        {
                            $dischargeBtn=null;
                        }

                        $formStart = '<form action="' . route('admin.patients.discharge', $row->id) . '" method="POST">
                        ' . csrf_field();

                        $formEnd = '</form>';
                        $showBtn ='<a href="'.route('admin.patients.show', $row->id).'"
                        class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>';
                        $btn = $formStart .$showBtn.' '. $editBtn . ' ' . $dischargeBtn . $formEnd;

                        return $btn;
                    }
                )
                ->make(true);
        }
    }

    public function discharge($id)
    {
        $customer =Customer::where('is_staff',0)->where('status',1)->findOrFail($id);
        DB::beginTransaction();
        try{
            if($customer instanceof Customer)
            {
                $previous_amount =$customer->balance;
                $transaction_type = TransactionType::find(4);
                $previous_amount= $customer->wallet_balance();
                $current_balance=0;

                    $transaction =CustomerWalletTransaction::create([
                    'previous_amount'=>$previous_amount,
                    'amount'=>-$previous_amount,
                    'total_amount'=> -$previous_amount,
                    'current_amount'=> $current_balance,
                    'transaction_type_id'=>4,
                    'customer_id'=>$customer->id,
                    'author_id'=>auth()->id(),
                ]);

            $customer->update([
                    'balance'=>0,
                    'status'=>0,
                ]);

            }
            else{
                return redirect()->route('admin.patients.index')->with('failed','Patient already Discharged');
            }
        }catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();


        return redirect()->route('admin.patients.index')->with('success','Patient Discharged Successfully');
    }
}
