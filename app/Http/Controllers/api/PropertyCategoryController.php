<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;
use Validator;


class PropertyCategoryController extends Controller
{
    //
    public function createCategory(Request $request)
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
            ]);

            if ($validate->fails()) {
                return response([
                    'status' => false,
                    'message' => $validate->errors(),
                ]);

            }

            $name = $request->get('name');

            $category = PropertyCategory::create([
                'name' => $name,
            ]);

            return response([
                'status' => true,
                'message' => 'Category create success',
                'data' => $category,
            ], 201);


        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }

    }

    public function getAllCategory()
    {
        try {
            return response([
                'status' => true,
                'message' => 'Category fetch success',
                'data' => PropertyCategory::all(),
            ]);

        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }

    public function deleteCategory(Request $request)
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
                'category_id' => 'required',
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors(),
                    ]
                );
            }

            $categoryId = $request->get('category_id');
            $res = PropertyCategory::destroy($categoryId);
            if ($res == 1) {
                return response(
                    [
                        'status' => true,
                        'message' => 'Category deleted successfully',
                    ]
                );
            } else {
                return response(
                    [
                        'status' => false,
                        'message' => 'Category delete fails',
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
