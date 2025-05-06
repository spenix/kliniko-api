<?php

namespace App\Http\Controllers\API;

use App\Events\AttachmentEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AttachmentResource;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Attachment;
use Illuminate\Support\Facades\{Auth, Validator, Event};

class AttachmentController extends BaseController
{
    protected $folderName;
    public function __construct()
    {
        $this->folderName = 'patient-attachments';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Validator::make(['patient_id' => $request->patient_id], [
            'patient_id' => 'required|integer|exists:patients,id',
        ])->validate();

        $query = Attachment::with('activity', 'created_by', 'updated_by')
            ->where('patient_id', $request->patient_id);

        if (isset($request->search)) {
            $query->where(function ($query2) use ($request) {
                $query2->where('attachment_type', 'like', '%' . $request->search . '%');
                $query2->orWhere('title', 'like', '%' . $request->search . '%');
                $query2->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $attachments = $query->paginate(10);
        return $this->sendResponse($attachments, 'Attachment retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Attachment::rules());
        $payload = [
            'patient_id' => $request->patient_id,
            'attachment_type' => $request->attachment_type,
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::id()
        ];

        if (isset($request->activity_id)) {
            $payload['activity_id'] = $request->activity_id;
        }

        if ($request->isUploadNewFile) {
            Validator::make(['file' => $request->file], [
                // 'file' => 'required|mimes:png,jpg,jpeg,csv,xlx,xls,pdf|max:2000000',
                'file' => 'required',
            ])->validate();
            $fileName = 'attachment' . '-' . time() . '-' . sprintf('%08d', $request->patient_id) . "-" . date('Ymd');
            $image = convertBase64ToImage($request->file, $fileName, 'patient-attachments');
            $image_path = $image['path'];
            $payload['filename'] =  $image_path;
        }

        $attachment = Attachment::create($payload);

        if (isset($request->activity_id)) {
            $channel = "activity-attachment-" . $request->activity_id;
            Event::dispatch(new AttachmentEvent(new AttachmentResource($attachment), $channel));
        }

        return $this->sendResponse(new AttachmentResource($attachment), 'Attachment created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attachment = Attachment::find($id);

        if (is_null($attachment)) {
            return $this->sendError('Attachment not found.');
        }

        return $this->sendResponse(new AttachmentResource($attachment), 'Attachment retrieved successfully.');
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
        $this->validate($request, Attachment::rules());
        $payload = [
            'patient_id' => $request->patient_id,
            'attachment_type' => $request->attachment_type,
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::id()
        ];
        if ($request->isUploadNewFile) {
            Validator::make(['file' => $request->file], [
                // 'file' => 'required|mimes:png,jpg,jpeg,csv,xlx,xls,pdf|max:2000000',
                'file' => 'required',
            ])->validate();
            $fileName = 'attachment' . '-' . time() . '-' . sprintf('%08d', $request->patient_id) . "-" . date('Ymd');
            $image = convertBase64ToImage($request->file, $fileName, 'patient-attachments');
            $image_path = $image['path'];
            $payload['filename'] =  $image_path;
        }
        $attachment = Attachment::find($id);
        if ($attachment->filename && ($request->isUploadNewFile == 'true')) {
            $imgExplodeUrl = explode("/", $attachment->filename);
            if (count($imgExplodeUrl)) {
                $imgWithType = explode(".", end($imgExplodeUrl));
                removeFileExist($imgWithType[0], $this->folderName);
            }
        }
        $attachment->update($payload);
        return $this->sendResponse(new AttachmentResource($attachment), 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Attachment::find($id)->delete();

        return $this->sendResponse([], 'Attachment deleted successfully.');
    }

    public function attachment_by_types(Request $request)
    {
        try {

            $payload = [
                'patient_id' => $request->patientId,
                'attachment_type' => $request->attachmentType
            ];
            Validator::make($payload, [
                'patient_id' => 'required|integer|exists:patients,id',
                'attachment_type' => 'required|in:xray'
            ])->validate();
            $attachments = Attachment::where($payload)->get();
            return $this->sendResponse(AttachmentResource::collection($attachments),  ucfirst($request->attachmentType) . ' Attachments retrieved successfully.');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function attachments_by_paginate(Request $request)
    {
        $attachments = Attachment::where($request->only(['patient_id', 'activity_id']))
            ->paginate(10);

        return $this->sendResponse($attachments, 'Attachments retrieved successfully.');
    }
}
