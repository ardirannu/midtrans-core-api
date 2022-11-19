<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateLogin;
use App\Http\Requests\ValidateRegister;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //register manual
    public function register(ValidateRegister $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        
        //create data
        $user = User::create($input);
        
        //return response success register
        return response()->json([
            'success' => true,
            'message' => 'User register successfully.', 
            'data' => [
                'token' => $user->createToken('MyApp')->accessToken,
                'name'  => $user->name,
            ]
        ]);
    }

    //login manual
    public function login(ValidateLogin $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            return response()->json([
                'success' => true,
                'message' => 'User login successfully.', 
                'data' => [
                    //create token
                    'token' => $user->createToken('MyApp')->accessToken,
                    'user_id'  => $user->id,
                    'name'  => $user->name,
                ]
            ]);
        } 
        else{ 
            //return response failed login
            return  response()->json([ 
                'success' => false,
                'message' => 'Failed Login, Email or Password dont match',  
            ], 401);
        } 
    }

    public function logout (Request $request) 
    {
        $token = $request->user()->token();
        $token->revoke();

        //return response success logout
        return response()->json([
            'success' => true,
            'message' => 'You have been successfully logged out!', 
        ]);
    }
}
