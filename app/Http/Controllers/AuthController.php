<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request) :JsonResponse
    {
        $this->validate($request, [
           'first_name' => 'required|string',
           'last_name' => 'required|string',
           'email' => 'required|email|unique:users',
           'phone' => 'required|string',
           'password' => 'required|confirmed'
        ]);

        try {
            $user = new User;
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();

            //successful response
            return response()->json(['user' => $user, 'message' => 'Created'], 201);
        } catch (\Exception $e) {
            //return error
            return response()->json(['message' => 'User registration failed!'], 409);
        }
    }

    public function login(Request $request) :JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email','password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }



}
