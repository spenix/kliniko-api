<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Classification;
use App\Http\Resources\ClassificationResource;
use Exception;
use Illuminate\Support\Facades\{Auth, Validator};

class ClassificationsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classification = Classification::all();
        return $this->sendResponse(ClassificationResource::collection($classification), 'Classifications retrieved successfully.');
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
            $this->validate($request, Classification::rules());
            $payload = [
                'name' => $request->name,
                'color' => $request->color,
                'isRequiredPatient' => $request->isRequiredPatient ? 'Y' : 'N',
                'created_by' => Auth::id()
            ];
            $isExist = Classification::where('name', $request->name)->count();
            if ($isExist) {
                return $this->sendError('Classification was already exist.');
            }

            $classification = Classification::create($payload);
            return $this->sendResponse(new ClassificationResource($classification), 'Classification was created successfully.');
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
        $classification = Classification::find($id);
    
        if (is_null($classification)) {
            return $this->sendError('Classification not found.');
        }
     
        return $this->sendResponse(new ClassificationResource($classification), 'Classification retrieved successfully.');
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
            $this->validate($request, Classification::rules());
            $payload = [
                'name' => $request->name,
                'color' => $request->color,
                'isRequiredPatient' => $request->isRequiredPatient ? 'Y' : 'N',
                'updated_by' => Auth::id()
            ];

            $isExist = Classification::where('name', $request->name)->where('id', '!=', $id)->count();
            if ($isExist) {
                return $this->sendError('Classification was already exist.');
            }
            
            $res = Classification::find($id)->update($payload);
            $classification = [];
            if($res){
                $classification = Classification::find($id);
            }
            
            
            return $this->sendResponse(new ClassificationResource($classification), 'Classification was updated successfully.');
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
    public function destroy(Classification $classification)
    {
        try{
            $classification->delete();
            return $this->sendResponse([], 'Classification deleted successfully.');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
