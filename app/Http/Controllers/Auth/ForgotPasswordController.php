<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getResetToken(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:190|exists:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);  
            exit(0);          
        }

        $user = \App\User::where('email', $request->email)->first();

        $token = $this->broker()->createToken($user);
        Mail::to($request->email)->send(new ResetPassword($token));

        return response()->json(['success'=> 'Check Email to Reset Password'], 200);  
    }
    
}
