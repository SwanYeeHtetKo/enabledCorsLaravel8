<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use App\User;
use Validator;

class AuthController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request)
    {
        try {
            $request->validate([
            'email' => 'email|required',
            'password' => 'required'
            ]);
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized'
            ]);
            }
            $user = User::where('email', $request->email)->first();
            if ( ! Hash::check($request->password, $user->password, [])) {
            throw new \Exception('Error in Login');
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return response()->json([
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            ]);
        } catch (Exception $error) {
            return response()->json([
            'status_code' => 500,
            'message' => 'Error in Login',
            'error' => $error,
            ]);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'email|required',
                'password' => 'required'
            ]);

            try{
                $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' =>Hash::make($request['password']),
                ]);
               
    
                $success['token'] = $user->createToken('authToken')->plainTextToken;
                $success['user'] =  $user;

                return response()->json(['success'=>$success], $this->successStatus);
            }catch (Exception $e){
                return response()->json(['error'=> $e->getMessage()]);
            }            
            
        } catch (Exception $e) {
            return response()->json(['error'=> $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {        
        try{
            $request->validate([
                'id' => 'required',
            ]);
            $user =Auth::user()->tokens()->where('id', $request['id'])->delete();
            return response()->json(['success'=>'Logout Successfully'], $this->successStatus);
        }catch (Exception $e){
            return response()->json(['error'=> $e->getMessage()]);
        }
    }
}
