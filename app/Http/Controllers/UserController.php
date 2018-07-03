<?php

namespace App\Http\Controllers;

use App\User;
use App\Faculty;
use App\Student;
use App\Event;
use Validator;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;

use Illuminate\Http\Request;

class UserController extends Controller
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

    public function getUser(Request $request){
        $user = $request->user();
        $userData = User::where('email',$user->email)->first();
        if($userData->first_login===1){
            $user['first_login'] = 1 ;
        }
        $domain = explode('@', $user->email, 2);
        if($domain[1]==="karunya.edu.in"){
            $user['role'] = "Student";
            return $user;
        }else{
            if($domain[1]==="karunya.edu"){
                $staff = Faculty::select('role','staff_id')->where('email',$user->email)->first();
                if($staff){
                    $user['role'] = $staff->role;
                    $user['staff_id'] = $staff->staff_id;
                    return $user;
                }else{
                    $user['role'] = "Faculty";
                    $user['first_login'] = 1 ;
                    return $user;
                }
            }else{
                $user['role'] = 'External Participant';
                return $user;
            }   
        }
    }

    /**
     * Display a listing of the event.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return User::all();
    }

    /**
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified event.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::where('e_id',$id)->first();
        $event->date = \Carbon\Carbon::parse($event->date)->format('d M');
        $event->time = \Carbon\Carbon::parse($event->time)->format('h:i:s A');
        return $event;
    }


    /**
     * Update the specified event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified event from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }

    public function getProfile(Request $request){
        $user = $this->getUser($request);

        if($user->role=="Student"){
            return Student::where('email',$user->email)->first();
        }
        if($user->role=="Faculty" || $user->role=="Event Coordinator" || $user->role=="Program Coordinator" || $user->role=="HOD" || $user->role="Admin" ){
            return Faculty::where('email',$user->email)->first();
        }
    }

    public function firstLoginStudent(Request $request){

        //Register number regex to be added

        $validator = Validator::make($request->all(), [      
            'register_number' =>  'required',
            'phone_number'    =>  array('required','regex:/^(\+\d{2}[- ]?)?\d{10}$/'),
            'degree'          => 'required|string',
            'department'      => 'required|string',
            'year'            => 'required|string',
            'address'         => 'nullable'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $user = $request->user();

       $student =  Student::create([
            'register_number'   =>  strtoupper($request->register_number),
            'name'              =>  $user->name,
            'email'             =>  $user->email,
            'phone_number'      =>  $request->phone_number,
            'degree'            =>  $request->degree,
            'department'        =>  $request->department,
            'year'              =>  $request->year,
            'address'           =>  $request->address
        ]);

        User::where('email',$user->email)->update(['first_login'=>false]);

        return response()->json([
            'success' => 'Its  a pleasure to have you here. ',
        ]);
        
    }

    public function firstLoginFaculty(Request $request){

        $validator = Validator::make($request->all(), [      
            'staff_id'        =>  array('required'),
            'phone_number'    =>  array('required','regex:/^(\+\d{2}[- ]?)?\d{10}$/'),
            'department'      => 'required|string',
            'designation'     => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $user = $request->user();

        $faculty =  Faculty::create([
            'staff_id'      =>  $request->staff_id,
            'name'          =>  $user->name,
            'email'         =>  $user->email,
            'phone'         =>  $request->phone_number,
            'department'    =>  $request->department,
            'designation'   =>  $request->designation,
            'role'          => "Faculty"
        ]);

        User::where('email',$user->email)->update(['first_login'=>false]);

        return response()->json([
            'success' => 'Its  a pleasure to have you here. ',
        ]);

    }

    public function registeredEvents(Request $request){

        $user = $this->getUser($request);

        return DB::table('event_registration')->select('event_id')->where('email',$user->email)->get();

    }


    public function queuedEvents(Request $request){

        $user = $this->getUser($request);

        return DB::table('event_queue')->select('event_id')->where('email',$user->email)->get();

    }

    public function registerEvent( Request $request , $event_id ){
        
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:190',
            'email'      => 'required|email'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $user = $this->getUser($request);

        // return $registered->count();
        // exit(0);

        $event = Event::where('e_id',$event_id)->get();

        $demo = $event[0];

        if($event->count()===1){
            if( $demo->registered < $demo->max_participant ){
                $registeredThisEvent = DB::table('event_registration')->where([
                    ['event_id','=',$event_id],['email','=',$user->email] 
                ])->get();
    
                if($registeredThisEvent->count()<=0){
                    
                    if($user->role=="Student"){
                        $student = Student::where('email',$user->email)->first();
        
                        DB::table('event_registration')->insert([
                            'event_id' => $event_id,
                            'student_id' => $student->register_number,
                            'name' => $user->name,
                            'department' => $student->department,
                            'email' => $user->email,
                            'registered_at' => now()
                        ]);
    
                        Event::where('e_id',$event_id)->increment('registered');

                        // $date = Carbon::parse($demo->date)->format('d F');
                        // Mail::to($student->email)->send(new EventApproved($demo->name,$date));

                        return response()->json([
                            'success' => 'Registered for '.$request->event_name,
                        ]);
                    }
                    else{
                        if($user->role=="Faculty" || $user->role=="Event Coordinator"){
                            $faculty = Faculty::where('email',$user->email)->first();
    
                            DB::table('event_registration')->insert([
                                'event_id' => $event_id,
                                'staff_id' => $faculty->staff_id,
                                'name' => $user->name,
                                'department' => $faculty->department,
                                'email' => $user->email,
                                'registered_at' => now()
                            ]);
    
                            Event::where('e_id',$event_id)->increment('registered');
                            
                            // Mail::to($staff->email)->send(new EventApproved($approvedEvent->name,$approvedEvent->date));
                            
                            return response()->json([
                                'success' => 'Registered for '.$request->event_name,
                            ]);
                        }else{
                            return response()->json(['error'=>"You are unauthorized to register for this event"], 404);  
                            exit(0); 
                        }           
                    }
                }else{
                    return response()->json(['error'=>"HeY! you have already registered for this event"], 404);  
                    exit(0);
                }
            }else{
                return response()->json(['error'=>"OH! The Seats are all filled up !! "],404);
                exit(0);
            }
        }else{
            return response()->json(['error'=>"OHH! I found no such of an event"], 404);  
            exit(0);
        }
    }

    public function queueUser(Request $request,$event_id){
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:190',
            'email'      => 'required|email'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $user = $this->getUser($request);
        
        $event = Event::where('e_id',$event_id)->first();

        // $demo = $event[0];
        if($event){
            if( $event->registered == $event->max_participant ){
                if($user->role=="Student"){
                    $student = Student::where('email',$user->email)->first();
                    DB::table('event_queue')->insert([
                        'event_id' => $event_id,
                        'student_id' => $student->register_number,
                        'name' => $user->name,
                        'department' => $student->department,
                        'email' => $user->email,
                        'queued_at' => now()
                    ]);
    
                    return response()->json([
                        'success' => 'Queued for '.$request->event_name,
                    ]);
                }
                else{
                    if($user->role=="Faculty" || $user->role=="Event Coordinator"){
                        $faculty = Faculty::where('email',$user->email)->first();

                        DB::table('event_queue')->insert([
                            'event_id' => $event_id,
                            'staff_id' => $faculty->staff_id,
                            'name' => $user->name,
                            'department' => $faculty->department,
                            'email' => $user->email,
                            'queued_at' => now()
                        ]);
                        
                        return response()->json([
                            'success' => 'Queued for '.$request->event_name,
                        ]);
                    }else{
                        return response()->json(['error'=>"You are unauthorized to register for this event"], 404);  
                        exit(0); 
                    }           
                }
            }else{
                return response()->json(['error'=>"Hey! You can still register for this event "], 404);  
                exit(0);
            }
        }else{
            return response()->json(['error'=>"OHH! I found no such of an event"], 404);  
            exit(0);
        }
    }


}
