<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // User Register
    public function register(RegisterRequest $request) : JsonResponse
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
           
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            $account_details['account_name'] = $data['name'];
            $account_details['owner_id'] = $user->id;
            $account = Account::createNewAccount($account_details);

            $token = $user->createToken(User::USER_TOKEN);
            DB::commit();
            
            return $this->success([
                'user' => $user,
                'token' => $token->plainTextToken,
            ], 'User has been register successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return $this->error($data["message"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    // User login for Authorization: Bearer +  AuthToken to get all informations
    public function login(LoginRequest $request) : JsonResponse
    {
        $isValid = $this->isValidateCredencial($request); 
        if(!$isValid['success']){
            return $this->error($isValid['message'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user   =  $isValid['user'];
        $token  =  $user->createToken(User::USER_TOKEN);
        return  $this->success([
            'user' => $user,
            'token' => $token->plainTextToken],
            'login successfully'
        );
          
    }
    
    private function isValidateCredencial(Request $request) : array
    {
        $data = $request->validated();
        $user  =  User::where("email", $data['email'])->first();
        if(is_null($user)) {
            return  ["success" => false, "message" => "invalide credencial"];
        }
        if (Hash::check($data['password'] , $user->password)) {
            return [
                'success' => true,
                'user' => $user
            ];
        }
        return  ["success" => false, "message" => "Failed! email not found"];
    }

    // User Detail with Authorization: Bearer AuthToken
    public function loginWithToken() : JsonResponse
    {
        return $this->success(
            auth()->user(),
            'login successfully'
        );
    }

    // User logout with Authorization: Bearer AuthToken
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null,'login successfully'
        );
        return response()->json(['message' => 'User successfully signed out']);
    }


}