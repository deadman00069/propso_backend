<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Validator;

class CityController extends Controller
{
    //
    public function createCity(Request $request)
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

            $cityName = $request->get('name');
            $city = City::create([
                'name' => $cityName,
            ]);

            return response(
                [
                    'status' => true,
                    'message' => 'City created success',
                    'data' => $city,
                ], 201
            );
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }

    public function getAllCities()
    {
        try {
            return response(
                [
                    'status' => true,
                    'data' => City::all(),
                ]
            );
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }

    public function deleteCity(Request $request)
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
                'city_id' => 'required',
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors(),
                    ]
                );
            }

            $cityId = $request->get('city_id');
            $res = City::destroy($cityId);
            if ($res == 1) {
                return response(
                    [
                        'status' => true,
                        'message' => 'City deleted successfully',
                    ]
                );
            } else {
                return response(
                    [
                        'status' => false,
                        'message' => 'City delete fails',
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
