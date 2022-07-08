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
use Illuminate\Support\Facades\DB;
use Validator;
use function PHPUnit\Framework\isEmpty;

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
                'facility' => 'required|array',
                'price' => 'required',
                'discount' => 'required',
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
                'price' => $request->get('price'),
                'discount' => $request->get('discount'),
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
                    'data' => Property::
                    with(
                        'images',
                        'videos', 'facility',
                        'city',
                        'location',
                        'propertyCategory',
                        'propertyType'
                    )
                        ->cursorPaginate(20),
                ]
            );

        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }

    }

    public function searchProperty(Request $request)
    {

        try {
            // For validating fields
            $validate = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            // If validation fails
            if ($validate->fails()) {
                return response([
                    'status' => false,
                    'message' => $validate->errors(),
                ], 409);
            }

            // Creating query
            $query = Property::
            with(
                'images',
                'videos',
                'facility',
                'city',
                'location',
                'propertyCategory',
                'propertyType'
            );

            // Getting all values
            $name = $request->get('name');
            $city = $request->get('city');
            $location = $request->get('location');
            $category = $request->get('category');
            $type = $request->get('type');

            // For performing search
            if ($name) {
                $query->where(
                    'title',
                    'LIKE',
                    "%{$name}%"
                );
            }

            // For filtering with city
            if ($city) {
                $query->whereHas('city', function ($q) use ($request) {
                    $q->where('name', $request->get('city'));
                });
            }

            // For filtering with location
            if ($location) {
                $query->whereHas('location', function ($q) use ($request) {
                    $q->where('name', $request->get('location'));
                });
            }

            // For filtering with category
            if ($category) {
                $query->whereHas('propertyCategory', function ($q) use ($request) {
                    $q->where('name', $request->get('category'));
                });
            }

            // For filtering with type
            if ($type) {
                $query->whereHas('propertyType', function ($q) use ($request) {
                    $q->where('name', $request->get('type'));
                });
            }

            return response([
                'status' => true,
                'message' => 'Search success',
                'data' => $query
                    ->cursorPaginate(20),
            ]);


        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }


    }

    public function getPropertyByDistance(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            if ($validate->fails()) {
                return response([
                    'status' => false,
                    'message' => $validate->errors(),
                ], 409);
            }

            //

//            $latitude = $request->get('latitude');
//            $longitude = $request->get('longitude');
            $latitude = '28.4469';
            $longitude = '77.0106';


            $listOfProperty = Property::with(
                'images',
                'videos', 'facility',
                'city',
                'location',
                'propertyCategory',
                'propertyType'
            )
                ->select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                * cos(radians(latitude)) * cos(radians(longitude) - radians(" . $longitude . "))
                                + sin(radians(" . $latitude . ")) * sin(radians(latitude))) AS distance"));
            $listOfProperty = $listOfProperty->having('distance', '<', 30);
            $listOfProperty = $listOfProperty->orderBy('distance', 'asc');
            $listOfProperty = $listOfProperty->cursorPaginate(20);

            return response([
                'status' => true,
                'message' => 'Property fetch success',
                'data' => $listOfProperty,
            ]);


        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }


    }
}
