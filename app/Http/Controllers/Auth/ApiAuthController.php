<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivation;
use Carbon\Carbon;
use App\User;

class ApiAuthController extends Controller
{
    /**
     * @var object
     */
    private $client;

    /**
     * ApiAuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function login(Request $request)
    {
        //This validator is not complete  ' have to add more rules' . 
        //Login with non karunya.edu.in will freez so chech it outasd   
        
        // => array(
        //     'required',
        //     'min:6',
        //     'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'
        //    )

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:190',
            'password' => 'required|max:190',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);  
            exit(0);          
        }

        $user = DB::table('users')->where('email',$request->email)->first();
        
        if($user){
            if(!$user->active){
                return response()->json(['error'=> ['Check Email to Activate Account']], 401);  
                exit(0);
            }
        }else{
            
            // $err = (object) [
            //     'error' => 'invalid_credentials',
            //     'message' => 'The user credentials were incorrect.'
            // ];

            $error = array(
                "error" => "invalid_credentials",
                "message" => "The user credentials were incorrect."
            );
            
            return response()->json($error, 401);  
            exit(0);
        }

        $this->client = DB::table('oauth_clients')->where('id', 2)->first();

        $request->request->add([
            'username' => $request->email,
            'password' => $request->password,
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'scope' => '*'
        ]);

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        return Route::dispatch($proxy);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function refreshToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);  
            exit(0);          
        }

        $this->client = DB::table('oauth_clients')->where('id', 2)->first();

        $request->request->add([
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
        ]);

        $proxy = Request::create(
            '/oauth/token',
            'POST'
        );

        return Route::dispatch($proxy);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function register(Request $request)
    {
        //Mobile number can be included 
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:190',
            // 'email'     => 'required|string|email|max:190|unique:users|regex:/^[A-Za-z0-9\.]*@(karunya)[.](edu)?([.](in))?$/',
            'email'     => 'required|string|email|max:190|unique:users',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        // karunya.edu | karunya.edu.in
        // regex:/^[A-Za-z0-9\.]*@(karunya)[.](edu)?([.](in))?$/',

        // karunya.edu.in
        // regex:/^[A-Za-z0-9\.]*@(karunya)[.](edu)[.](in)?$/'

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);  
            exit(0);          
        }


        //To Send Email

        $confirmation_code = str_random(30) ;
        Mail::to($request->email)->send(new AccountActivation($request->name,$confirmation_code));
        // $mailer->to($request->email)->send(new AccountActivation($user->name,$confirmation_code));

        DB::table('users_activation')->insert(
            ['email' => $request->email, 'token' => $confirmation_code , 'created_at' => Carbon::now()]
        );

        //Have to validate email && karunya email 

        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
        ]);

        

        return response()->json(['success'=>'Check email to validate registration']);  

        // $request->request->add([
        //     'username' => $request->email,
        //     'password' => $request->password,
        //     'grant_type' => 'password',
        //     'client_id' => $this->client->id,
        //     'client_secret' => $this->client->secret,
        //     'scope' => '*'
        // ]);

        // $proxy = Request::create(
        //     'oauth/token',
        //     'POST'
        // );

        // return Route::dispatch($proxy);
    }

    public function verifyUser(Request $request){

        $validator = Validator::make($request->all(), [
            'token'      => 'required|string|max:190',
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);  
            exit(0);          
        }

        // return $request->token;

        $result = DB::table('users_activation')->where('token',$request->token)->get();
        
        if(count($result) > 0){
            $user = $result[0];
            DB::table('users')->where('email',$user->email)->update(['active' => true,'first_login' => true]);
        
            // Del Acc Activation Token from table
            
            DB::table('users_activation')->where('token',$request->token)->delete();

            return response()->json(['success'=> 'Wallah !! Account has been Verified'], 200);  
        }else{
            return response()->json(['error'=> "Oh ! We 've an Invalid Request "], 404);
        }
        
    }


    public function forgotpassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:190|exists:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);  
            exit(0);          
        }


        // return response()->json(['success'=> 'Wallah !! Account has been Verified'], 200);  
    }

}


