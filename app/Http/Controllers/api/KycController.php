<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\KYC;
use Exception;
use Illuminate\Http\Request;
use Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class KycController extends Controller
{
    //
    public function createKyc(Request $request)
    {
        try {

            // If user dont have admin role
            $user = auth('sanctum')->user();
            if (!$user->hasRole(['admin', 'lead_generator'])) {
                return response(
                    [
                        'status' => false,
                        'message' => 'Unauthenticated',
                    ], 409
                );
            }

            $validate = Validator::make($request->all(), [
                'full_name' => 'required',
                'aadhaar_no' => 'required',
                'pan_card_no' => 'required',
                'account_name' => 'required',
                'account_no' => 'required',
                'ifsc_code' => 'required',
                'bank_name' => 'required',
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors(),
                    ]
                );
            }

            $userId = auth('sanctum')->user()->id;
            $fullName = $request->get('full_name');
            $aadhaarNo = $request->get('aadhaar_no');
            $panCardNo = $request->get('pan_card_no');
            $accountName = $request->get('account_name');
            $accountNo = $request->get('account_no');
            $ifscCode = $request->get('ifsc_code');
            $bankName = $request->get('bank_name');

            $kyc = KYC::create([
                'user_id' => $userId,
                'uid' => $this->createUid(),
                'full_name' => $fullName,
                'aadhaar_no' => $aadhaarNo,
                'pan_card_no' => $panCardNo,
                'account_name' => $accountName,
                'account_no' => $accountNo,
                'ifsc_code' => $ifscCode,
                'bank_name' => $bankName,
            ]);

            return response(
                [
                    'status' => true,
                    'message' => 'KYC create success',
                    'data' => $kyc,
                ], 201
            );

        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }


    public function getAllKyc()
    {
        try {
            return response(
                [
                    'status' => true,
                    'message' => 'KYC fetch success',
                    'data' => KYC::with('user')->get(),
                ]
            );
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }

    }

    public function verifyKyc(Request $request)
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
                'kyc_id' => 'required',
            ]);

            if ($validate->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => $validate->errors(),
                    ]
                );
            }
            $id = $request->get('kyc_id');
            $kyc = KYC::find($id);
            if (!$kyc) {
                return response(
                    [
                        'status' => false,
                        'message' => 'No KYC found',
                    ], 409
                );
            }
            $kyc->is_verified = true;
            $kyc->save();
            return response(
                [
                    'status' => true,
                    'message' => 'KYC update success',
                ]
            );


        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => '' . $e,
            ], 409);
        }
    }

    public function createUid(): string
    {
        return IdGenerator::generate(['table' => 'k_y_c_s', 'length' => 10, 'field' => 'uid', 'prefix' => 'SP-']);
    }
}
