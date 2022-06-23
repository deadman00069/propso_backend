<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Exception;
use Illuminate\Http\Request;
use Validator;

class FacilityController extends Controller
{
    //
    public function createFacility(Request $request)
    {

        try {

            // If user dont have admin role
            /** @noinspection DuplicatedCode */
            $user = auth('sanctum')->user();
            if (!$user->hasRole('admin')) {
                return response(
                    [
                        'status' => false,
                        'message' => 'Unauthenticated',
                    ], 409
                );
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validator->errors()
                    ]
                );
            }
            $name = $request->get('name');

            $facility = Facility::create([
                'name' => $name,
            ]);

            return response([
                'status' => true,
                'message' => 'Create facility success',
                'data' => $facility,
            ], 201);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }

    public function getAllFacilities()
    {
        try {
            return response([
                'status' => true,
                'message' => 'Facility fetch success',
                'data' => Facility::all(),
            ]);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }

    public function deleteFacility(Request $request){
        try {

            $user = auth('sanctum')->user();
            if (!$user->hasRole('admin')) {
                return response(
                    [
                        'status' => false,
                        'message' => 'Unauthenticated',
                    ], 409
                );
            }

            $validate = Validator::make($request->all(), [
                'facility_id' => 'required',
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors(),
                    ]
                );
            }

            $facilityId = $request->get('facility_id');
            $res = Facility::destroy($facilityId);
            if ($res == 1) {
                return response(
                    [
                        'status' => true,
                        'message' => 'Facility deleted successfully',
                    ]
                );
            } else {
                return response(
                    [
                        'status' => false,
                        'message' => 'Facility delete fails',
                    ],409
                );
            }

        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }
}
