<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Validator;

class UserController extends Controller
{
    //

    //
    public function signUp(Request $request)
    {
        try {
            $roles = json_decode(Role::all());
            if (empty($roles)) {
                Role::create(['name' => 'admin']);
                Role::create(['name' => 'user']);
                Role::create(['name' => 'lead_generator']);
            }

            //validating request
            $validate = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8',
                'phone_no' => 'required|max:10|min:10',
                'role' => 'required|numeric'
            ]);

            //if validation fails
            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors()
                    ], 409
                );
            }


            $name = $request->get('name');
            $email = $request->get('email');
            $password = $request->get('password');
            $phone_no = $request->get('phone_no');
            $role = $request->get('role');

            //checking if email or phoneNo already exist
            $checkEmail = User::where('email', $email)->first();
            $checkPhone = User::where('phone_no', $phone_no)->first();
            if ($checkEmail !== null || $checkPhone !== null) {
                return response([
                    'status' => false,
                    'message' => 'email or phone no already exist'
                ], 409);
            }

            //creating user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'phone_no' => $phone_no,
                'avatar_url' => 'addsa',
            ]);


            $token = $user->createToken('access-token');

            //if role == 0 , its admin
            if ($role == 0) {
                $user->assignRole('admin');
            }

            //if role == 1 , its customer
            if ($role == 1) {
                $user->assignRole('user');
            }

            //if role == 2, its CP
            if ($role == 2) {
                $user->assignRole('lead_generator');
            }
            return response(
                [
                    'status' => true,
                    'message' => 'sign up success',
                    'data' => $user,
                    'token' => $token,
                ], 201
            );
        } catch (Exception $e) {
            return response(
                [
                    'status' => false,
                    'message' => '' . $e
                ], 409
            );
        }
    }


    public function login(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:8'
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors()
                    ], 409
                );
            }


            $email = $request->get('email');
            $password = $request->get('password');

            //Fetching user with email
            $user = User::where('email', $email)->first();

            //if user not found or credentials don't match
            if (!$user || !Hash::check($password, $user->password)) {
                return response([
                    'success' => false,
                    'message' => 'These credentials do not match our records.',
                ], 409
                );
            }

            //
            $token = $user->createToken('access-token')->plainTextToken;
            return response([
                    'success' => true,
                    'message' => 'Login success',
                    'data' => $user,
                    'token' => $token,
                ]
            );

        } catch (Exception $e) {
            return response(
                [
                    'status' => false,
                    'message' => '' . $e
                ], 409
            );
        }

    }


    public function adminLogin(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if ($validate->fails()) {
            return response(
                [
                    'status' => false,
                    'message' => $validate->errors()
                ], 409
            );
        }

        try {
            $email = $request->get('email');
            $password = $request->get('password');

            //Fetching user with email
            $user = User::where('email', $email)->first();

            //if user not found or credentials don't match
            if (!$user || !Hash::check($password, $user->password)) {
                return response([
                    'success' => false,
                    'message' => 'These credentials do not match our records.',
                ], 409
                );
            }

            // If user dont have admin role
            if (!$user->hasRole('admin')) {
                return response(
                    [
                        'status' => false,
                        'message' => 'Unauthenticated',
                    ], 409
                );
            }

            //
            $token = $user->createToken('access-token')->plainTextToken;
            return response([
                    'success' => true,
                    'message' => 'Login success',
                    'data' => $user,
                    'token' => $token,
                ]
            );

        } catch (Exception $e) {
            return response(
                [
                    'status' => false,
                    'message' => '' . $e
                ], 409
            );
        }
    }

    public function checkIfTokenValid()
    {
        $response = auth('sanctum')->check();
        if ($response == 1) {
            return response([
                'status' => true,
                'message' => 'Token validate',
            ]);
        }
        return response([
            'status' => false,
            'message' => 'Token InValidate',
        ]);
    }

    public function getPhoneNo(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors()
                    ], 409
                );
            }

            $email = $request->get('email');

            $user = User::where('email', $email)->first();

            if (!$user) {
                return response([
                    'status' => false,
                    'message' => 'No user found with this email id',
                ], 409);
            }

            return response([
                'status' => true,
                'message' => 'phone no fetch success',
                'data' => $user->phone_no,
            ]);

        } catch (Exception $e) {
            return response(
                [
                    'status' => false,
                    'message' => '' . $e
                ], 409
            );
        }

    }

    public function getAllUsers()
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

            return response([
                'status' => true,
                'message' => 'user  fetch success',
                'data' => User::role(['lead_generator', 'user'])->get(),
            ]);
        } catch (Exception $e) {
            return response(
                [
                    'status' => false,
                    'message' => '' . $e
                ], 409
            );
        }
    }


}
