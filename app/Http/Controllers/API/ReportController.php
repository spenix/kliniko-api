<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Activity;
use Validator;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\ExpenseResource;
use App\Models\ActivityPayment;
use App\Models\ActivityProcedure;
use App\Models\Expense;
use App\Models\PaymentType;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function daily_activity_report(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $branch_id = $user->branch_id;

        if ($role == "RC" || $role == "DA") {
            $branches = $user->branches;

            $branch_id = $branches[0]->branch_id;
        }
        if (isset($request->branchId)) {
            $branch_id = $request->branchId;
        }
        $from = $request->from . " 00:00:00";
        $to = $request->to . " 23:59:59";
        $activities = Activity::where('is_paid', 'Y')
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->where('branch_id', $branch_id)
            ->get();

        $payment_types = PaymentType::select('name', 'id')->get();

        $generated_data = [];
        $payment_breakdown = [];

        foreach ($activities as $activity) {
            $data = array();
            $data["control_no"] = $activity->control_no;
            $data["patient_name"] = $activity->patient->first_name . " " . $activity->patient->last_name;

            $data["doctor"] = (!empty($activity->doctor)) ? $activity->doctor->first_name . " " . $activity->doctor->last_name : null;

            $data["commission"] = $activity->total_commission;

            $services = $activity->services;

            $service_list = array();

            foreach ($services as $service) {
                $service_list[] = $service->service->name;
            }

            $data["treatment"] = implode(', ', $service_list);

            foreach ($payment_types as $payment_type) {
                $filter_payment = [...array_filter(json_decode($activity->payments), function ($fp) use ($payment_type) {
                    return $fp->payment_type_id == $payment_type->id;
                })];

                $payment_amount = 0;
                if (count($filter_payment) > 0) {
                    $payment_amount = $filter_payment[0]->amount;
                }

                $data["payment_" . $payment_type->id] = $payment_amount;

                if (isset($payment_breakdown[$payment_type->name])) {
                    $payment_breakdown[$payment_type->name] += $payment_amount;
                } else {
                    $payment_breakdown[$payment_type->name] = $payment_amount;
                }
            }

            $generated_data[] = $data;
        }

        $total_payment_breakdown = [];

        foreach ($payment_breakdown as $key => $val) {
            $total_payment_breakdown[] = array(
                "payment_type" => $key,
                "amount" => $val
            );
        }

        $expenses = Expense::where('expense_date', '>=', $from)
            ->where('expense_date', '<=', $to)
            ->where('is_overhead', 'N')
            ->where('branch_id', $branch_id)
            ->with('expense_type')
            ->get();


        $result = array(
            "generated_data" => $generated_data,
            "payment_types" => $payment_types,
            "total_payment_breakdown" => $total_payment_breakdown,
            "expenses" => $expenses
        );

        return $this->sendResponse($result, 'Activity Report successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function daily_expense_report(Request $request)
    {
        $from = $request->from . " 00:00:00";
        $to = $request->to . " 23:59:59";
        $expenses = Expense::where('expense_date', '>=', $from)
            ->where('expense_date', '<=', $to)
            ->get();

        return $this->sendResponse(ExpenseResource::collection($expenses), 'Expenses retrieve successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function commission_report(Request $request)
    {
        $from = $request->from . " 00:00:00";
        $to = $request->to . " 23:59:59";
        $activities = Activity::with(['services' => function ($query) {
            $query->where('activity_services.is_delete', 'N');
            $query->orWhere('activity_services.is_voided', 'N');
        }])->join('doctors', 'doctors.id', 'activities.doctor_id')
            ->join('patients', 'patients.id', 'activities.patient_id')
            ->where('activities.is_paid', 'Y')
            ->where('activities.created_at', '>=', $from)
            ->where('activities.created_at', '<=', $to)
            ->where('activities.doctor_id', $request->doctor)
            ->selectRaw("
                activities.*, 
                CONCAT(doctors.first_name, ' ', doctors.last_name) as doctor_name, 
                CONCAT(patients.first_name, ' ', patients.last_name) as patient_name, 
                DATE_FORMAT(activities.created_at, '%M %d, %Y') as activity_date
            ")
            ->orderBy('activities.id')
            ->get()->groupBy('activity_date', 'asc');

        return $this->sendResponse($activities, 'Total Commission retrieve successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, Activity::rules());

        $data = $request->all();
        $control_no = IdGenerator::generate(
            [
                'table' => 'activities',
                'field' => "control_no",
                'length' => 10,
                'prefix' => date('Y') . "-" . date('m') . "-",
                'reset_on_prefix_change' => true
            ]
        );
        $data['control_no'] = $control_no;

        $activity = Activity::create($data);

        return $this->sendResponse(new ActivityResource($activity), 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activity = Activity::find($id);

        if (is_null($activity)) {
            return $this->sendError('Activity not found.');
        }

        return $this->sendResponse(new ActivityResource($activity), 'Activity retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {

        $this->validate($request, Activity::rules());
        $input = $request->all();

        $activity->status = $input['status'];
        $activity->remarks = $input['remarks'];
        $activity->doctor_id = $input['doctor_id'];
        $activity->is_paid = $input['is_paid'];
        $activity->save();

        return $this->sendResponse(new ActivityResource($activity), 'Activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return $this->sendResponse([], 'Activity deleted successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save_activity_payment(Request $request, Activity $activity)
    {
        $data = $request->all();
        $payment_data = [];
        if (count($data) > 0) {
            foreach ($data as $dta) {
                $payment_data[] = array(
                    'activity_id' => $activity->id,
                    'payment_type_id' => $dta['id'],
                    'amount' => $dta['amount']
                );
            }

            ActivityPayment::insert($payment_data);
        }

        return $this->sendResponse($payment_data, 'Activity payment added successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_activity_payment(Activity $activity)
    {

        $payments = ActivityPayment::where('activity_payments.activity_id', $activity->id)
            ->leftJoin('payment_types', 'activity_payments.payment_type_id', '=', 'payment_types.id')
            ->select(
                'payment_types.id',
                'payment_types.name',
                'activity_payments.amount'
            )->get();

        return $this->sendResponse($payments, 'Activity payments retrieved successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function daily_overhead_expense_report(Request $request)
    {
        $from = $request->from . " 00:00:00";
        $to = $request->to . " 23:59:59";
        $expenses = Expense::where('expense_date', '>=', $from)
            ->where('expense_date', '<=', $to)
            ->where('is_overhead', 'Y')
            ->get();

        return $this->sendResponse(ExpenseResource::collection($expenses), 'Expenses retrieve successfully.');
    }
}
