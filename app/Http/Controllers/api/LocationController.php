<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\Location;
use Validator;

class LocationController extends Controller
{
    //
    public function createLocation(Request $request)
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

            $validate = Validator::make($request->all(), [
                'name' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'city_id' => 'required'
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors(),
                    ]
                );
            }

            $name = $request->get('name');
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $cityId = $request->get('city_id');

            $location = Location::create([
                'name' => $name,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'city_id' => $cityId
            ]);

            return response(
                [
                    'status' => true,
                    'message' => 'Location creation success',
                    'data' => $location,
                ], 201
            );
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }


    public function getAllLocation()
    {
        try {
            return response([
                'status' => true,
                'message' => 'Location fetch success',
                'data' => Location::with('city')->get(),
            ]);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }

    }

    public function deleteLocation(Request $request)
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
                'location_id' => 'required',
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors(),
                    ]
                );
            }

            $locationId = $request->get('location_id');
            $res = Location::destroy($locationId);
            if ($res == 1) {
                return response(
                    [
                        'status' => true,
                        'message' => 'Location deleted successfully',
                    ]
                );
            } else {
                return response(
                    [
                        'status' => false,
                        'message' => 'Location delete fails',
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
