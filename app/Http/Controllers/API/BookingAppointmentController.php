<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingAppointment;
use App\Http\Resources\BookingAppointmentResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\{Auth, Validator};
use Carbon\Carbon;

class BookingAppointmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $booking = BookingAppointment::join('classifications', 'classifications.id', 'booking_appointments.classification_id')->selectRaw('booking_appointments.*, DATE_FORMAT(date_from, "%M %d, %Y %h:%i:%s %p") as date_start, DATE_FORMAT(date_to, "%M %d, %Y %h:%i:%s %p") as date_end, classifications.name as classification_name, classifications.color')->get();
        return $this->sendResponse(BookingAppointmentResource::collection($booking), 'Booking Records retrieved successfully.');
    }


    public function todays_appointment()
    {
        $booking = BookingAppointment::join('classifications', 'classifications.id', 'booking_appointments.classification_id')
            ->leftJoin('patients', 'patients.id', 'booking_appointments.patient_id')
            ->selectRaw('*, DATE_FORMAT(date_from, "%M %d, %Y %h:%i:%s %p") as dateStart, DATE_FORMAT(date_to, "%M %d, %Y %h:%i:%s %p") as dateEnd, DATE_FORMAT(date_from, "%h:%i:%s %p") as time_start, DATE_FORMAT(date_to, "%h:%i:%s %p") as time_end, classifications.name as classification_name, classifications.color, CONCAT(patients.first_name, " ", patients.last_name) as patient_name')
            ->whereDate('date_from', '>=', Carbon::today()->toDateString())
            ->get();
        return $this->sendResponse(BookingAppointmentResource::collection($booking), 'Booking Records for today retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, BookingAppointment::rules());
            $payload = [
                'classification_id' => $request->classification_id,
                'patient_id' => $request->patient_id,
                'title' => $request->title,
                'note' => $request->note,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'created_by' => Auth::id()
            ];

            $isExist = BookingAppointment::where(['title' => $request->title, 'date_from' => $request->date_from, 'date_to' => $request->date_to])->count();
            if ($isExist) {
                return $this->sendError('Booking was already exist.');
            }

            $bookingAppointment = BookingAppointment::create($payload);
            return $this->sendResponse(new BookingAppointmentResource($bookingAppointment), 'Booking Record was created successfully.');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $booking = BookingAppointment::join('classifications', 'classifications.id', 'booking_appointments.classification_id')
            ->leftJoin('patients', 'patients.id', 'booking_appointments.patient_id')
            ->selectRaw('booking_appointments.*, DATE_FORMAT(date_from, "%M %d, %Y %h:%i:%s %p") as dateStart, DATE_FORMAT(date_to, "%M %d, %Y %h:%i:%s %p") as dateEnd, DATE_FORMAT(date_from, "%h:%i:%s %p") as time_start, DATE_FORMAT(date_to, "%h:%i:%s %p") as time_end, classifications.name as classification_name, classifications.color, CONCAT(patients.first_name, " ", patients.last_name) as patient_name')
            ->where('booking_appointments.id', $id)
            ->first();

        if (is_null($booking)) {
            return $this->sendError('Booking not found.');
        }

        return $this->sendResponse(new BookingAppointmentResource($booking), 'Booking Record retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, BookingAppointment::rules());
            $payload = [
                'classification_id' => $request->classification_id,
                'patient_id' => $request->patient_id,
                'title' => $request->title,
                'note' => $request->note,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'created_by' => Auth::id()
            ];

            $isExist = BookingAppointment::where(['title' => $request->title, 'date_from' => $request->date_from, 'date_to' => $request->date_to])->where('id', '!=', $id)->count();
            if ($isExist) {
                return $this->sendError('Booking Record was already exist.');
            }

            $booking = BookingAppointment::find($id)->update($payload);
            return $this->sendResponse(new BookingAppointmentResource($booking), 'Booking Record was updated successfully.');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookingAppointment $bookingAppointment)
    {
        try {
            $bookingAppointment->delete();
            return $this->sendResponse([], 'Booking Record deleted successfully.');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
