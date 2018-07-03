<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

     /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:190|exists:password_resets,email',
            'token' => 'required',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);  
            exit(0);          
        }

        $user = \App\User::where('email',$request->email)->first();

        // return "".$this->broker()->tokenExists($user,$request->token);

        if($this->broker()->tokenExists($user,$request->token)){

            $newPassword = Hash::make($request->password);
            \App\User::where('email',$request->email)->update([
                'password' => $newPassword,
                'updated_at' => now()
            ]);

            DB::table('password_resets')->where('email',$request->email)->delete();

            return response()->json(['success'=> 'Success !! Password is Reset '], 200);
            exit(0);
        }else{
            return response()->json(['error'=> ['token' => ["Oh ! We 've an Invalid Token "]]], 404);
            exit(0);
        }        
    }

}
