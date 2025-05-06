<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Expense;
use Validator;
use App\Http\Resources\ExpenseResource;
use App\Models\ExpensePayment;
use App\Models\ExpenseProcedure;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends BaseController
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

        if($role == "RC" || $role == "DA") {
            $branches = $user->branches;

            $branch_id = $branches[0]->branch_id;
        }
        $expenses = Expense::where('branch_id', $branch_id)->orderBy('id','desc')->get();
      
        return $this->sendResponse(ExpenseResource::collection($expenses), 'Expense retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, Expense::rules());

        $user = Auth::user();
        $role = $user->role;

        $branch_id = $user->branch_id;

        if($role == "RC" || $role == "DA") {
            $branches = $user->branches;

            $branch_id = $branches[0]->branch_id;
        }

        $data = $request->all();
        $data['branch_id'] = $branch_id;

        $expense = Expense::create($data);
     
        return $this->sendResponse(new ExpenseResource($expense), 'Expense created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense = Expense::find($id);
    
        if (is_null($expense)) {
            return $this->sendError('Expense not found.');
        }
     
        return $this->sendResponse(new ExpenseResource($expense), 'Expense retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {

        $this->validate($request, Expense::rules());
        $input = $request->all();
     
        $expense->expense_type_id = $input['expense_type_id'];
        $expense->other = isset($input['other']) ? $input['other'] : '';
        $expense->description = $input['description'];
        $expense->amount = $input['amount'];
        $expense->expense_date = $input['expense_date'];
        $expense->save();
     
        return $this->sendResponse(new ExpenseResource($expense), 'Expense updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
     
        return $this->sendResponse([], 'Expense deleted successfully.');
    }
}