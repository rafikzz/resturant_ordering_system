<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientDischargePaymentRecord;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PatientDischargePaymentRecordController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->title = 'Patient Discharge Payment';
    }
    public function index()
    {
        $title = $this->title;
        $breadcrumbs = ['Patient' => route('admin.patient_discharge_payments.index')];
        return view('admin.patients_discharged_payments.index', compact('title', 'breadcrumbs'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $data = PatientDischargePaymentRecord::select('table_patient_discharge_payment_records.*')->with('customer')->with('customer.patient');

            return DataTables::of($data)
            ->editColumn('created_at', function ($patient_discharge_payment) {
                return [
                    'display' => $patient_discharge_payment->created_at->diffForHumans(),
                    'timestamp' => $patient_discharge_payment->created_at
                ];
            })
                ->make(true);
        }
    }
}
