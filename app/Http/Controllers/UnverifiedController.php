<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\Permit;
use App\Models\Shop;
use App\Models\UserProfile;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UnverifiedController extends Controller
{
    public function resubmission_form(Request $request)
    {
        $userId = $request->session()->get('loginId');
        $shopDetails = Shop::join('user_profiles', 'shops.user_id', 'user_profiles.id')
            ->select(
                'shops.*',
                'user_profiles.email',
                'user_profiles.contact_num',
            )
            ->where('user_id', $userId)
            ->first();
        $applicationId = $shopDetails ? Permit::where('shop_id', $shopDetails->id)
            ->orderByDesc('created_at')
            ->first() : null;

        if ($applicationId && $applicationId->status == 'Approved') {
            $user = UserProfile::find($userId);

            // Automatically log the user in
            $request->session()->put('user', $user);
            $request->session()->put('loginId', $user->id);
            $request->session()->put('username', $user->username);
            $request->session()->put('email', $user->email);

            // Redirect the user based on their user type
            switch ($user->user_type_id) {
                case 3:
                    return redirect()->route('seller.dashboard')->with('success', 'Registration successful! You are now logged in.');
                default:
                    return redirect()->route('login.form')->with('error', 'Unauthorized Access!');
            }
        }

        return view('main.unverified.unverified', compact('applicationId', 'shopDetails'));
    }

    public function submit_application(Request $request)
    {
        $userId = $request->session()->get('loginId');
        $shopId = Shop::where('user_id', $userId)->value('id');

        try {
            $validator = Validator::make(request()->all(), [
                'shop_name' => 'required|unique:shops,shop_name',
                'email' => 'required|email|unique:user_profiles,email',
                'contact_num' => 'required|numeric|digits:11|starts_with:09|unique:user_profiles,contact_num',
                'mayors' => 'required|file|mimes:jpeg,png,pdf|max:51200',
                'bir' => 'required|file|mimes:jpeg,png,pdf|max:51200',
                'dti' => 'required|file|mimes:jpeg,png,pdf|max:51200',
                'contract' => 'required|file|mimes:jpeg,png,pdf|max:51200',
            ], [
                'shop_name.required' => 'Please enter shop name',
                'shop_name.unique' => 'Shop name already exists',
                'email.required' => 'Please enter email',
                'email.email' => 'Invalid email',
                'email.unique' => 'Email already exists',
                'contact_num.required' => 'Please enter contact number',
                'contact_num.min' => 'Invalid Phone number',
                'contact_num.max' => 'Invalid Phone number',
                'contact_num.starts_with' => 'Invalid Phone number',
                'contact_num.unique' => 'Phone number already used',
                'mayors.required' => 'Please upload mayors permit',
                'mayors.file' => 'Please upload mayors permit',
                'mayors.mimes' => 'Please upload mayors permit in jpeg, png or pdf',
                'mayors.max' => 'Please upload mayors permit less than 50MB',
                'bir.required' => 'Please upload BIR',
                'bir.file' => 'Please upload BIR',
                'bir.mimes' => 'Please upload BIR in jpeg, png or pdf',
                'bir.max' => 'Please upload BIR less than 50MB',
                'dti.required' => 'Please upload DTI ',
                'dti.file' => 'Please upload DTI ',
                'dti.mimes' => 'Please upload DTI in jpeg, png or pdf',
                'dti.max' => 'Please upload DTI  less than 50MB',
                'contract.required' => 'Please upload AdU contract',
                'contract.file' => 'Please upload contract',
                'contract.mimes' => 'Please upload contract in jpeg, png or pdf',
                'contract.max' => 'Please upload contract less than 50MB',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $mayorFile = 'mayors-permit_' . time() . '_' . $request->shop_name . '_' . $request->mayors->getClientOriginalName();
            $birFile = 'bir_' . time() . '_' . $request->shop_name . '_' . $request->bir->getClientOriginalName();
            $dtiFile = 'dti_' . time() . '_' . $request->shop_name . '_' . $request->dti->getClientOriginalName();
            $contractFile = 'adu-contract_' . time() . '_' . $request->shop_name . '_' . $request->contract->getClientOriginalName();

            $request->file('mayors')->storeAs('permits', $mayorFile, 'public');
            $request->file('bir')->storeAs('permits', $birFile, 'public');
            $request->file('dti')->storeAs('permits', $dtiFile, 'public');
            $request->file('contract')->storeAs('permits', $contractFile, 'public');

            DB::beginTransaction();

            $user = UserProfile::findOrFail($userId);
            $user->email = $request->email;
            $user->contact_num = $request->contact_num;
            $user->updated_at = now();
            $user->save();

            $shop = Shop::findOrFail($shopId);
            $shop->shop_name = $request->shop_name;
            $shop->status = 'Processing';
            $shop->updated_at = now();
            $shop->save();

            $permit = new Permit();
            $permit->mayors = $mayorFile;
            $permit->bir = $birFile;
            $permit->dti = $dtiFile;
            $permit->contract = $contractFile;
            $permit->shop_id = $shop->id;
            $permit->status = 'Pending';
            $permit->is_rejected = false;
            $permit->created_at = now();
            $permit->updated_at = now();
            $permit->save();

            DB::commit();
            return redirect()->route('resubmission.form')->with('success', 'Resubmission Success');
        } catch (QueryException $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (FileException $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->with('error', 'File upload error: ' . $e->getMessage());
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function resubmit_application(Request $request)
    {
        $userId = $request->session()->get('loginId');
        $shopId = Shop::where('user_id', $userId)->value('id');
        $shopDetails = Shop::where('id', $shopId)->first();

        try {
            $validator = Validator::make(request()->all(), [
                'mayors' => 'required|file|mimes:jpeg,png,pdf|max:51200',
                'bir' => 'required|file|mimes:jpeg,png,pdf|max:51200',
                'dti' => 'required|file|mimes:jpeg,png,pdf|max:51200',
                'contract' => 'required|file|mimes:jpeg,png,pdf|max:51200',
            ], [
                'mayors.required' => 'Please upload mayors permit',
                'mayors.file' => 'Please upload mayors permit',
                'mayors.mimes' => 'Please upload mayors permit in jpeg, png or pdf',
                'mayors.max' => 'Please upload mayors permit less than 50MB',
                'bir.required' => 'Please upload BIR',
                'bir.file' => 'Please upload BIR',
                'bir.mimes' => 'Please upload BIR in jpeg, png or pdf',
                'bir.max' => 'Please upload BIR less than 50MB',
                'dti.required' => 'Please upload DTI ',
                'dti.file' => 'Please upload DTI ',
                'dti.mimes' => 'Please upload DTI in jpeg, png or pdf',
                'dti.max' => 'Please upload DTI  less than 50MB',
                'contract.required' => 'Please upload AdU contract',
                'contract.file' => 'Please upload contract',
                'contract.mimes' => 'Please upload contract in jpeg, png or pdf',
                'contract.max' => 'Please upload contract less than 50MB',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $mayorFile = 'mayors-permit_' . time() . '_' . $shopDetails->shop_name . '_' . $request->mayors->getClientOriginalName();
            $birFile = 'bir_' . time() . '_' . $shopDetails->shop_name . '_' . $request->bir->getClientOriginalName();
            $dtiFile = 'dti_' . time() . '_' . $shopDetails->shop_name . '_' . $request->dti->getClientOriginalName();
            $contractFile = 'adu-contract_' . time() . '_' . $shopDetails->shop_name . '_' . $request->contract->getClientOriginalName();

            $request->file('mayors')->storeAs('permits', $mayorFile, 'public');
            $request->file('bir')->storeAs('permits', $birFile, 'public');
            $request->file('dti')->storeAs('permits', $dtiFile, 'public');
            $request->file('contract')->storeAs('permits', $contractFile, 'public');

            DB::beginTransaction();

            $shop = Shop::findOrFail($shopId);
            $shop->status = 'Processing';
            $shop->updated_at = now();
            $shop->save();

            $permit = new Permit();
            $permit->mayors = $mayorFile;
            $permit->bir = $birFile;
            $permit->dti = $dtiFile;
            $permit->contract = $contractFile;
            $permit->shop_id = $shopId;
            $permit->status = 'Pending';
            $permit->created_at = now();
            $permit->updated_at = now();
            $permit->is_rejected = false;
            $permit->save();

            DB::commit();

            return redirect()->route('resubmission.form')->with('success', 'Resubmission Success');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (FileException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'File upload error: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function unv_change_password()
    {
        return view('main.unverified.password');
    }

    public function update_password(Request $request)
    {
        $userId = $request->session()->get('loginId');
        $credential = Credential::where('user_id', $userId)
            ->where('is_deleted', false)
            ->first();

        if (empty($request->current_password)) {
            return redirect()->back()->with('error', 'Enter Current Password.');
        }

        if (!Hash::check($request->input('current_password'), $credential->password)) {
            return redirect()->back()->with('error', 'The provided current password does not match our records.');
        }

        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[A-Z])(?=.*[\W_]).+$/'
                ],
                'confirm_password' => 'required|same:new_password',
            ], [
                'current_password.required' => 'Current password is required.',
                'new_password.required' => 'New password is required.',
                'new_password.min' => 'New password must be at least 8 characters.',
                'new_password.regex' => 'New password must include at least one uppercase letter and one special character.',
                'confirm_password.required' => 'New password confirmation is required.',
                'confirm_password.same' => 'New password and confirmation do not match.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

            DB::beginTransaction();

            // Mark the old password as deleted
            $credential->is_deleted = true;
            $credential->save();

            // Store the new password
            $newCredential = new Credential();
            $newCredential->user_id = $userId;
            $newCredential->password = Hash::make($request->input('new_password'));
            $newCredential->is_deleted = false;
            $newCredential->created_at = now();
            $newCredential->updated_at = now();
            $newCredential->save();

            DB::commit();
            return redirect()->route('unv.change.password')->with('success', 'Password changed successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
