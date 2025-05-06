<?php

use App\Http\Controllers\API\{
    ClinicController,
    ActivityController,
    ActivityRecommendations,
    ActivityServiceController,
    AdditionalPayableController,
    BranchController,
    DoctorController,
    ExpenseController,
    ExpenseTypeController,
    MedicalConditionController,
    PatientController,
    PaymentTypeController,
    PrescriptionController,
    AttachmentController,
    PatientDiagramRecordController,
    RegisterController,
    ReportController,
    ServiceController,
    UserController,
    ClassificationsController,
    BookingAppointmentController,
    PatientIntraOralController,
    BusinessRulesController,
    DiscountController,
    UserProfileController
};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [RegisterController::class, 'logout']);
    Route::get('users-by-paginations', [UserController::class, 'users_paginations']);
    Route::get('users/user_details', [UserController::class, 'user_details']);
    Route::get('booking-appointment/today', [BookingAppointmentController::class, 'todays_appointment']);
    Route::post('business-rule-users', [BusinessRulesController::class, 'role_users']);
    Route::post('change-password', [UserProfileController::class, 'change_password']);
    Route::post('user-personal-info', [UserProfileController::class, 'update_personal_info']);
    Route::get('branch-patients', [PatientController::class, 'index']);
    Route::get('patient/{patient}/balance-history', [PatientController::class, 'patient_balance_history']);
    Route::get('branch-patients-by-pagination', [PatientController::class, 'patient_list']);
    Route::get('branch-doctors-by-pagination', [DoctorController::class, 'doctors_list']);
    Route::post('recommendations-by-activity', [ActivityRecommendations::class, 'recommendations_by_activity']);
    Route::post('recommendation-from-prev-activity', [ActivityRecommendations::class, 'recom_from_prev_activity']);
    Route::post('hide-recommendation', [ActivityRecommendations::class, 'hide_recommendation']);
    Route::get('branch-doctors', [DoctorController::class, 'branch_doctors']);
    Route::post('search-patients', [PatientController::class, 'paginated_patient_list']);
    Route::get('search-patients-with-balance', [PatientController::class, 'patient_with_balance_list']);
    Route::get('patients-grand-total-balance', [PatientController::class, 'patients_grand_total_balance']);
    Route::get('branch-activities', [ActivityController::class, 'branch_activities']);
    Route::post('attachment-by-types', [AttachmentController::class, 'attachment_by_types']);
    Route::post('prescriptions-by-paginate', [PrescriptionController::class, 'prescriptions_by_paginate']);
    Route::post('attachments-by-paginate', [AttachmentController::class, 'attachments_by_paginate']);
    Route::get('branch-discounts-paginated', [DiscountController::class, 'branch_discounts_paginated']);
    Route::get('branch-discounts', [DiscountController::class, 'branch_discounts']);
    Route::get('branch-expense-types', [ExpenseTypeController::class, 'branch_expense_type']);
    Route::resource('business-rule', BusinessRulesController::class);
    Route::resource('booking-appointment', BookingAppointmentController::class);
    Route::resource('patient-intra-oral-records', PatientIntraOralController::class);
    Route::resource('patient-diagram-records', PatientDiagramRecordController::class);
    Route::resource('attachments', AttachmentController::class);
    Route::resource('patients', PatientController::class);
    Route::resource('medical-conditions', MedicalConditionController::class);
    Route::resource('activity-recommendations', ActivityRecommendations::class);
    Route::resource('activities', ActivityController::class);
    Route::resource('services', ServiceController::class);
    Route::get('branch-services-paginated', [ServiceController::class, 'branch_services_paginated']);
    Route::get('other-branch-services', [ServiceController::class, 'other_branch_services']);
    Route::get('branch-services', [ServiceController::class, 'branch_services']);
    Route::post('copy-other-branch-services', [ServiceController::class, 'copy_other_branch_services']);
    Route::resource('doctors', DoctorController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('expense-types', ExpenseTypeController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('activity-services', ActivityServiceController::class);
    Route::put('activity-services/commission/{activity_service}', [ActivityServiceController::class, 'commission_activity_service']);
    Route::put('activity-services/void/{activity_service}', [ActivityServiceController::class, 'void_activity_service']);
    Route::get('activity-services/services-by-activity/{activity}', [ActivityServiceController::class, 'services_by_activity']);
    Route::get('activity/additional-payables/{activity}', [AdditionalPayableController::class, 'get_additional_payable_by_activity']);
    Route::resource('additional-payable', AdditionalPayableController::class);
    Route::resource('clinics', ClinicController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('prescriptions', PrescriptionController::class);
    Route::resource('users', UserController::class);
    Route::resource('classifications', ClassificationsController::class);
    Route::get('clinic/{id}/branches', [BranchController::class, 'branches_per_clinic']);
    Route::resource('payment-types', PaymentTypeController::class);
    Route::post('activities/payments/{activity}', [ActivityController::class, 'save_activity_payment']);
    Route::post('activities/discounts/{activity}', [ActivityController::class, 'save_activity_discount']);
    Route::post('activities/custom-discounts/{activity}', [ActivityController::class, 'save_activity_custom_discount']);
    Route::delete('activities/discounts/{id}', [ActivityController::class, 'delete_activity_discount']);
    Route::post('activities/settle-with-balance/{activity}', [ActivityController::class, 'settle_with_balance']);
    Route::post('activities/additional-commission/{activity}', [ActivityController::class, 'additional_commission']);
    Route::post('activities/update-remarks/{activity}', [ActivityController::class, 'update_remarks']);
    Route::get('activities/payment-list/{activity}', [ActivityController::class, 'get_activity_payment']);
    Route::get('activities/discount-list/{activity}', [ActivityController::class, 'get_activity_discount']);
    Route::get('patient/activities/{id}', [ActivityController::class, 'get_activity_by_patient_id']);
    Route::get('reports/daily-activity-report', [ReportController::class, 'daily_activity_report']);
    Route::get('reports/daily-expenses-report', [ReportController::class, 'daily_expense_report']);
    Route::get('reports/daily-overhead-expenses-report', [ReportController::class, 'daily_overhead_expense_report']);
    Route::get('reports/commission-report', [ReportController::class, 'commission_report']);
    Route::get('users/by_role/{role}', [UserController::class, 'user_by_role']);
});
