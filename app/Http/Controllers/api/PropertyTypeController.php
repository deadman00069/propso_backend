<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Validator;

class PropertyTypeController extends Controller
{
    //
    public function createPropertyType(Request $request)
    {

        try {

            // If user dont have admin role
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

            $propertyType = PropertyType::create([
                'name' => $name,
            ]);

            return response([
                'status' => true,
                'message' => 'Create property type success',
                'data' => $propertyType,
            ], 201);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }

    public function getAllPropertyType()
    {
        try {
            return response([
                'status' => true,
                'message' => 'Property type fetch success',
                'data' => PropertyType::all(),
            ]);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }

    public function deletePropertyType(Request $request)
    {
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
                'type_id' => 'required',
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors(),
                    ]
                );
            }

            $typeId = $request->get('type_id');
            $res = PropertyType::destroy($typeId);
            if ($res == 1) {
                return response(
                    [
                        'status' => true,
                        'message' => 'Type deleted successfully',
                    ]
                );
            } else {
                return response(
                    [
                        'status' => false,
                        'message' => 'Type delete fails',
                    ], 409
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
