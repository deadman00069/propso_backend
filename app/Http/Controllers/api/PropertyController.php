<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityProperty;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyVideo;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PropertyController extends Controller
{
    //
    public function createProperty(Request $request)
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
                'city_id' => 'required',
                'location_id' => 'required',
                'property_category_id' => 'required',
                'property_type_id' => 'required',
                'title' => 'required',
                'description' => 'required',
                'short_summery' => 'required',
                'area' => 'required',
                'how_many_beds' => 'required',
                'how_many_bathroom' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'image' => 'required',
                'image.*' => 'mimes:jpg,jpeg,png|max:10024',
                'video' => 'required',
                'video.*' => 'mimes:mp4|max:20024',
                'facility' => 'required|array'
            ]);

            if ($validate->fails()) {
                return response([
                    'status' => false,
                    'message' => $validate->errors(),
                ], 409);

            }

            // Creating property
            $property = Property::create([
                'city_id' => $request->get('city_id'),
                'location_id' => $request->get('location_id'),
                'property_category_id' => $request->get('property_category_id'),
                'property_type_id' => $request->get('property_type_id'),
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'short_summery' => $request->get('short_summery'),
                'area' => $request->get('area'),
                'how_many_beds' => $request->get('how_many_beds'),
                'how_many_bathroom' => $request->get('how_many_bathroom'),
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude'),
            ]);

            //Getting all images
            $listOfImages = $request->file('image');

            //Storing images in file
            foreach ($listOfImages as $value) {
                $fileName = time() . '_' . pathinfo($value->getClientOriginalName())['filename'] . '.' . $value->getClientOriginalExtension();
                $path = public_path('/uploads/images/property/');
                $value->move($path, $fileName);

                // Saving image url into db
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_url' => '/uploads/images/property/' . $fileName,
                ]);
            }


            //Getting all videos
            $listOfVideos = $request->file('video');

            //Storing videos in file
            foreach ($listOfVideos as $value) {
                $fileName = time() . '_' . pathinfo($value->getClientOriginalName())['filename'] . '.' . $value->getClientOriginalExtension();
                $path = public_path('/uploads/videos/property/');
                $value->move($path, $fileName);

                // Saving image url into db
                PropertyVideo::create([
                    'property_id' => $property->id,
                    'video_url' => '/uploads/videos/property/' . $fileName,
                ]);
            }


            //Storing facilities
            $listOfFacility = $request->get('facility');
            foreach ($listOfFacility as $value) {
                FacilityProperty::create([
                    'facility_id' => $value,
                    'property_id' => $property->id,
                ]);
            }

            return response(
                [
                    'status' => true,
                    'message' => 'Property create success',
                    'data' => $property,
                ]
            );

        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }

    }

    public function getAllProperty()
    {

        try {
            return response(
                [
                    'status' => true,
                    'message' => 'Property fetch success',
                    'data' => Property::with('images', 'videos', 'facility', 'city', 'location', 'propertyCategory', 'propertyType')->get(),
                ]
            );

        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }

    }
}
