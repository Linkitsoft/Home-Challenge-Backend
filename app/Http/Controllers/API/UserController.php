<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function signUp(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'contact' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAILURE',
                'message' => $request->path(),
                'requestKey' => $validator->messages()->first()
            ]);
        }

        $user = User::where('email', $input['email'])->get();

        if($user->isEmpty()){
            $user =  User::create([
                "name" => $input['name'],
                "email" => $input['email'],
                "password" => Hash::make($input['password']),
                "contact" => $input['contact'],
                
            ]);
    
            return response()->json([
                'status' => 'SUCCESS', 'message' =>  'User registered Successfully.',
            ]);
        }
        else{
            return response()->json([
                'status' => 'Faild', 'message' =>  'User already exists.',
            ]);
        }

    }

    public function signIn(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAILURE',
                'message' => $request->path(),
                'requestKey' => $validator->messages()->first()
            ]);
        }


        if(!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Invalid User Credentials', 'requestKey' => $validator->messages()->first()]);
        }

        else{
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            return response()->json([
                'status'=>200,
                "response"=>$user,
                "token"=>$token
            ]);
        }

        

    }
}
