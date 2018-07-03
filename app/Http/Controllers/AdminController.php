<?php

namespace App\Http\Controllers;

use App\User;
use App\Faculty;
use App\Student;
use App\Event;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class AdminController extends Controller
{

    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
         
    }

    // public function test(Request $request){
    //     if($request->hasFile('test')){
    //         $path = $request->file('test')->store('avatars');
    //     }
    //     //Storage::files('public')
    //     return $path;
    // }

    // public function testImage($file){
        // $image = Storage::get('avatars/' . $file);
        // return response()->make($image, 200, ['content-type' => 'image/jpg']);
    // }

    private function isAdmin(Request $request){
        $user = $request->user();
        $userStaff = Faculty::select('staff_id')->where([ ['email',$user->email] , ['role','=','Admin'] ])->first();

        if($userStaff){
            return true;
        }else{
            return false;
        }
    }

    public function getAllUsers(Request $request){
        $isAdmin = $this->isAdmin($request);

        if($isAdmin){
            $students = DB::table('students')->select('register_number as id','register_number as student_id','name','department','email')->get();
            $staff = DB::table('faculties')->select('staff_id as id','staff_id','name','department','email','role')
                            ->where([ ['role','<>','HOD'] , ['role','<>','Admin'] ])->get();
            $hod = DB::table('faculties')->select('staff_id as id','staff_id','name','department','email','role')->where('role','hod')->get();

            $students = array('students'   => $students );
            $staff    = array('faculties'  => $staff    );
            $hod      = array('hod'        => $hod      );
                
            $data = array_merge($students,$staff,$hod);
            return $data;

        }else{
            return response()->json([
                'error' => 'Unauthorised Personal',
            ],404);  
        }
    }
    
    public function getSuggestions(Request $request){
        
        $isAdmin = $this->isAdmin($request);

        $data = [];
        if($isAdmin){
            $staff = DB::table('faculties')->select('staff_id as id','staff_id','name','department','email','role')->get();
            $op = json_decode($staff, true);
            foreach($op as $a){
                // $id = $a['staff_id'];
                // $name = $a['name'];
                $email = $a['email'];

                // $changeId = array('label' => $id );
                // $changeName = array('label'=> $name );
                $changeEmail = array('label' => $email );

                // array_push($data,$changeId,$changeName,$changeEmail);
                array_push($data,$changeEmail);
            }
            return $data ;
        }else{
            return response()->json([
                'error' => 'Unauthorised Personal',
            ],404);  
        }
    }

    public function updateUserRole(Request $request){

        $validator = Validator::make($request->all(), [
            "emails"    => 'required|array|min:1',
            'emails.*'  => 'required|email|distinct|max:190',
            'role'      => 'required|string|max:190',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $isAdmin = $this->isAdmin($request);
        $data=[];

        $user = $request->user();

        if($isAdmin){
            foreach($request->emails as $email){
                if($email!==$user->email){
                    $staffResponse = DB::table('faculties')->where('email',$email)->update(['role' => $request->role]);
                    // array_push($data ,$staffResponse);
                }else{
                    return response()->json([
                        'error' => 'Naah!! Cannot change the role of <b> Admin </b>.',
                    ],404); 
                }
            }
            return response()->json([
                'success' => 'User Role Updated !!',
            ]);
        }else{
            return response()->json([
                'error' => 'Unauthorised Personal',
            ],404);  
        }
    }
    
}
