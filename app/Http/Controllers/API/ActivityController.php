<?php

namespace App\Http\Controllers\API;

use App\Events\ActivityEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Activity;
use App\Http\Resources\ActivityResource;
use App\Models\ActivityDiscount;
use App\Models\ActivityPayment;
use App\Models\ActivityProcedure;
use App\Models\BalanceHistory;
use App\Models\Discount;
use App\Models\Patient;
use App\Models\UserBranch;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\{Validator, Hash, Auth, Event};

class ActivityController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $branch_id = $user->branch_id;

        if ($role == "RC" || $role == "DA") {
            $branches = $user->branches;

            $branch_id = $branches[0]->branch_id;
        }

        $activitys = Activity::where('branch_id', $branch_id)->orderBy('id', 'desc')->get();

        return $this->sendResponse(ActivityResource::collection($activitys), 'Activity retrieved successfully.');
    }

    public function branch_activities(Request $request)
    {
        Validator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();

        $user = Auth::user();
        $activitys = [];
        $query = Activity::with('patient')->where('branch_id', $request->branchId);

        if ($user->role == 'DA') {
            $query->where(['is_dentist_required' => 'Y']);
        }

        if (isset($request->date_from)) {
            $query->whereDate('created_at', '>=', date($request->date_from));
        }
        if (isset($request->date_to)) {
            $query->whereDate('created_at', '<=', date($request->date_to));
        }

        if (isset($request->control_no)) {
            $query->orWhere('control_no', 'like', '%' . $request->control_no . '%');
        }

        $activitys = $query->orderBy('id', 'desc')->paginate(10);
        return $this->sendResponse($activitys, 'Activity retrieved successfully.');
    }

    public function get_activity_by_patient_id($id, Request $request)
    {

        $query = Activity::with(['services' => function ($query) {
            $query->where('activity_services.is_delete', 'N');
            $query->orWhere('activity_services.is_voided', 'N');
        }])->leftJoin('doctors', 'doctors.id', 'activities.doctor_id')->orderBy('id', 'desc')
            ->where('patient_id', $id);
        if (isset($request->search)) {
            $query->where(function ($query2) use ($request) {
                $query2->orWhereRaw('DATE_FORMAT(activities.created_at, "%M %d, %Y") like ? ', '%' . $request->search . '%');
                $query2->orWhere('activities.remarks', 'like', '%' . $request->search . '%');
                $query2->orWhereRaw('CONCAT(doctors.first_name, " ",doctors.middle_name, " ", doctors.last_name) like ? ', '%' . $request->search . '%');
            });
        }

        $activities = $query->selectRaw('activities.*, CONCAT(doctors.first_name, " ",doctors.middle_name, " ", doctors.last_name) as doctor_name')->paginate(10);
        return $this->sendResponse($activities, 'Activity retrieved successfully.');
    }

    public function attachment_by_types(Request $request)
    {
        dd($request->all());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = Auth::user();
        $role = $user->role;

        $branch_id = $user->branch_id;

        if ($role == "RC" || $role == "DA") {
            $branches = $user->branches;

            $branch_id = $branches[0]->branch_id;
        }

        $this->validate($request, Activity::rules());

        $payload = [];

        $control_no = IdGenerator::generate(
            [
                'table' => 'activities',
                'field' => "control_no",
                'length' => 14,
                'prefix' => date('Y') . "-" . date('m') .  date('d') . "-",
                'reset_on_prefix_change' => true
            ]
        );
        $payload['patient_id'] = $request->patient_id;
        $payload['is_dentist_required'] = $request->is_dentist_required ? 'Y' : 'N';
        $payload['rc_notes'] = $request->rc_notes;
        $payload['control_no'] = $control_no;
        $payload['branch_id'] = isset($request->branch_id) ? $request->branch_id : $branch_id;
        if ($request->clinic_id) {
            $payload['clinic_id'] = $request->clinic_id;
        }
        $activity = Activity::create($payload);
        $channel = "branch-activity-" . $branch_id;
        Event::dispatch(new ActivityEvent(new ActivityResource($activity), $channel));

        return $this->sendResponse(new ActivityResource($activity), 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        Validator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();

        $userBranch = UserBranch::where(['user_id' => Auth::id(), 'branch_id' => $request->branchId])->count();
        if ($userBranch) {
            $activity = Activity::where(['branch_id' => $request->branchId, 'id' => $id])->first();
            if (is_null($activity)) {
                return $this->sendError('404 Error', 'Activity not found.', 404);
            }
            return $this->sendResponse(new ActivityResource($activity), 'Activity retrieved successfully.');
        } else {
            return $this->sendError('404 Error', 'Activity not found.', 404);
        }
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
        $input = $request->only(['status', 'remarks', 'doctor_id', 'is_paid']);

        $activity->status = $input['status'];
        $activity->remarks = $input['remarks'];
        $activity->doctor_id = $input['doctor_id'];
        $activity->is_paid = $input['is_paid'];

        if (isset($request->dental_assistant)) {
            $activity->dental_assistant = $request->dental_assistant;
        }

        if (count($activity->services) > 0) {
            $total_commission = $activity->services->where('is_delete', 'N')->where('is_voided', 'N')->sum('commission_amount');
            $total_amount = $activity->services->where('is_delete', 'N')->where('is_voided', 'N')->sum('amount');

            $activity->total_amount = $total_amount;
            $activity->total_commission = $total_commission;
        }

        if (count($activity->discounts) > 0) {
            $total_discount_amount = $activity->discounts->sum('discount_amount');
            $activity->total_discount_amount = $total_discount_amount;
        }

        if ($input['is_paid'] === 'Y') {
            if (count($activity->additional_payables) > 0) {
                $total_balance_amount = $activity->additional_payables->where('is_delete', 'N')->where('type', 'balance')->sum('amount');

                if ($total_balance_amount > 0) {
                    $patient_id = $activity->patient_id;

                    $balance_history = new BalanceHistory();
                    $balance_history->activity_id = $activity->id;
                    $balance_history->patient_id = $patient_id;
                    $balance_history->is_payment = 'Y';
                    $balance_history->before_balance = $activity->patient->balance;
                    $balance_history->amount = $total_balance_amount;

                    $after_balance = (int)$activity->patient->balance - $total_balance_amount;
                    $balance_history->after_balance = $after_balance;
                    $balance_history->description = "payment happened on Activity #" . $activity->control_no;

                    if ($balance_history->save()) {
                        $patient = Patient::find($patient_id);
                        $patient->balance = $after_balance;
                        $patient->save();
                    }
                }
            }
        }

        $activity->save();

        $channel = "branch-activity-" . $activity->branch_id;
        Event::dispatch(new ActivityEvent(1, $channel));


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
        ActivityPayment::where("activity_id", $activity->id)->delete();
        $data = $request->all();
        $payment_data = [];
        if (count($data) > 0) {
            foreach ($data as $dta) {
                $payment_data[] = array(
                    'activity_id' => $activity->id,
                    'payment_type_id' => $dta['id'],
                    'amount' => $dta['amount'],
                    'account_name' => $dta['account_name'] ?? "",
                    'reference_num' => $dta['reference_number'] ?? "",
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
    public function save_activity_custom_discount(Request $request, Activity $activity)
    {
        $data = $request->all();

        $total_amount = $activity->total_amount;
        $discount_amount = $request->discount_amount;
        if ((!$request->is_fixed_amount)) {
            $discount_amount = $total_amount * ($request->discount_rate / 100);
        }

        $discount_data = array(
            'activity_id' => $activity->id,
            // 'discount_id' => $data['discount_id'],
            'discount_amount' => $discount_amount ?: 0,
            // 'id_number' => $data['id_number'],
            'name' => $data['name'],
            'is_fixed_amount' => $data['is_fixed_amount'] ? 'Y' : 'N',
            'discount_rate' => $data['discount_rate'] ?: 0
        );

        $discount_data = ActivityDiscount::create($discount_data);

        return $this->sendResponse($discount_data, 'Activity discount added successfully.');
    }

    public function save_activity_discount(Request $request, Activity $activity)
    {
        $data = $request->all();
        $discount = Discount::find($data['discount_id']);
        $total_amount = $activity->total_amount;

        $discount_amount = $discount->discount_amount;

        if ($discount->is_fixed_amount === 'N') {
            $discount_amount = $total_amount * ($discount->discount_rate / 100);
        }

        $discount_data = array(
            'activity_id' => $activity->id,
            'discount_id' => $data['discount_id'],
            'discount_amount' => $discount_amount,
            'id_number' => $data['id_number']
        );

        $discount_data = ActivityDiscount::create($discount_data);

        return $this->sendResponse($discount_data, 'Activity discount added successfully.');
    }

    public function delete_activity_discount($id)
    {
        $activity_discount = ActivityDiscount::findOrFail($id);

        if ($activity_discount) {
            $activity_discount->delete();
        } else {
            $this->sendError("Discount cannot be found");
        }

        return $this->sendResponse([], "Discount is deleted");
    }

    public function settle_with_balance(Request $request, Activity $activity)
    {

        $data = $request->all();
        $payment_data = [];
        $total_amount = $activity->total_amount;
        $accumulated_payment_amount = 0;
        $patient = Patient::find($activity->patient_id);
        if (count($data) > 0) {
            foreach ($data as $dta) {
                $payment_data[] = array(
                    'activity_id' => $activity->id,
                    'payment_type_id' => $dta['id'],
                    'amount' => $dta['amount'],
                    'account_name' => $dta['account_name'],
                    'reference_num' => $dta['reference_number'],
                );
                $accumulated_payment_amount += $dta['amount'];
            }
            ActivityPayment::insert($payment_data);
        }

        $patient_balance = $patient->balance;

        $activity_balance = $total_amount - $accumulated_payment_amount;

        $activity->is_settle_with_balance = 'Y';
        $activity->is_paid = 'N';
        $activity->status = 'done';
        $activity->balance = $activity_balance;
        $activity->save();


        $channel = "branch-activity-" . $activity->branch_id;
        Event::dispatch(new ActivityEvent(new ActivityResource($activity), $channel));
        $patient_balance = $patient_balance + ($total_amount - $accumulated_payment_amount);

        $balance_history = new BalanceHistory();
        $balance_history->activity_id = $activity->id;
        $balance_history->patient_id = $activity->patient_id;
        $balance_history->is_payment = 'N';
        $balance_history->before_balance = $activity->patient->balance;
        $balance_history->amount = $activity_balance;

        $after_balance = (int)$activity->patient->balance + $activity_balance;
        $balance_history->after_balance = $patient_balance;
        $balance_history->description = "balance accumulated on Activity #" . $activity->control_no;

        if ($balance_history->save()) {
            $patient = Patient::find($activity->patient_id);
            $patient->balance = $patient_balance;
            $patient->save();
        }
        return $this->sendResponse($payment_data, 'Settled with balance successfully!');
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_activity_discount(Activity $activity)
    {

        $payments = ActivityDiscount::where('activity_discounts.activity_id', $activity->id)
            ->leftJoin('discounts', 'activity_discounts.discount_id', '=', 'discounts.id')
            ->selectRaw(
                'activity_discounts.id,
                IFNULL(discounts.name, activity_discounts.name) as name,
                IFNULL(discounts.is_fixed_amount, activity_discounts.is_fixed_amount) as is_fixed_amount,
                IFNULL(discounts.discount_rate, activity_discounts.discount_rate) as discount_rate,
                IF(discounts.discount_amount > 0, discounts.discount_amount, activity_discounts.discount_amount) as discounted_amount'
            )->get();

        return $this->sendResponse($payments, 'Activity discounts retrieved successfully.');
    }

    public function update_remarks(Request $request, Activity $activity)
    {
        $activity->remarks = $request->remarks;
        $activity->save();
        return $this->sendResponse(new ActivityResource($activity), 'Activity remarks updated successfully.');
    }

    public function additional_commission(Request $request, Activity $activity)
    {
        $activity->additional_commission = $request->additional_commission;
        $activity->additional_commission_remarks = $request->additional_commission_remarks;

        $activity->save();
        return $this->sendResponse($activity, 'Additional commission successfully.');
    }
}
