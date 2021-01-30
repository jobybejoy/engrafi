<?php

namespace App\Http\Controllers;


use Validator;
use App\Faculty;
use App\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventApproved;
use App\Mail\EventUnapproved;
use \Carbon\Carbon;
use PDF;

// use Illuminate\Support\Facades\Schema;
// use Illuminate\Database\Schema\Blueprint;

class EventController extends Controller
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

    /**
     * Display a listing of the event.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = Carbon::now()->toDateString();
        $events = Event::where('date','>=',$date)->orderBy('date', 'asc')->get();

        //Formating Date and Time 
        foreach($events as $event){
            $event->long_date = $event->date;
            $event->long_time =  Carbon::parse($event->time)->format('h:i');
            $event->format_date = $event->date;
            $event->format_time =  Carbon::parse($event->time)->format('H:i');
            // $event->date = Carbon::parse($event->date)->format('d M');
            $event->time = Carbon::parse($event->time)->format('h:i A');
            $event->short_date_number = Carbon::parse($event->date)->format('d');
            $event->short_date_month = Carbon::parse($event->date)->format('M');
            $event->long_date_day   = Carbon::parse($event->date)->format('l');
            $event->long_date_month = Carbon::parse($event->date)->format('F');
            $event->long_date_year = Carbon::parse($event->date)->format('Y');
            $staffHost = Faculty::where('staff_id',$event->staff_id)->first();
            $event->staff_name = $staffHost->name;
        }
        // $events = Event::orderBy('date', 'asc')->get();

        // //Formating Date and Time 
        // foreach($events as $event){
        //     $event->long_date = $event->date;
        //     $event->long_time = $event->time;
        //     $event->date = Carbon::parse($event->date)->format('d M');
        //     $event->time = Carbon::parse($event->time)->format('h:i:s A');
        // }

        return $events;
    }


    /**
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        //'poster' => $request->poster, was not added 
        // $event =  Event::create([
        //     'staff_id' => $staff->staff_id,
        //     'name' => $request->name,
        //     'description' => $request->description, 
        //     'date' => $request->date,
        //     'time' => $request->time,
        //     'venue' => $request->venue,
        //     'max_participant' => $request->max_participant,
        //     'resource_person' => $request->resource_person,
        //     'department' => $staff->department,
        //     'category' => $request->category
        // ]);
    }

    /**
     * Display the specified event.
     *
     *  #### MAY NOT NEED THIS ####
     * 
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::where('e_id',$id)->first();
        $event->date = Carbon::parse($event->date)->format('d M');
        $event->time = Carbon::parse($event->time)->format('h:i:s A');
        return $event;
    }


    /**
     * Update the specified event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$approval_id)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:190',
            'description'=> 'required',
            'date'  => 'required|date',
            'time'  => 'required',
            'sessions' => 'required|integer|min:1|max:6',
            'venue'     => 'nullable|string|max:190',
            'card_image' => 'nullable',
            'max_participant' => 'integer',
            'resource_person' => 'required|string|max:190',
            'category' => 'required|string|max:190',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }
    
        $user = $request->user();
        $staff = Faculty::select('staff_id')->where([ ['email','=',$user->email] , ['role','=','Event Coordinator'] ])->first();

        // return $staff->staff_id;
        $path = "";
        

        $inApproval = DB::table('event_approvals')->where([ ['approval_id','=',$approval_id] , ['staff_id','=',$staff->staff_id] ])->first();
        if($inApproval){
            if($request->hasFile('card_image')){
                $path = $request->file('card_image')->store('event','public');
            }else{
                $path = $inApproval->card_image;
            }
            DB::table('event_approvals')->where([ ['approval_id','=',$approval_id] , ['staff_id','=',$staff->staff_id] ])
                ->update([
                'name' => $request->name,
                'description' => $request->description, 
                'date' => $request->date,
                'time' => $request->time,
                'card_image' => $path,
                'sessions' => $request->sessions,
                'venue' => $request->venue,
                'max_participant' => $request->max_participant,
                'resource_person' => $request->resource_person,
                'category' => $request->category ,
                'denied' => false
            ]);
        }else{
                $event = Event::where([ ['e_id','=',$approval_id],['staff_id','=',$staff->staff_id] ])->first();
            if($event){
                if($request->hasFile('card_image')){
                    $path = $request->file('card_image')->store('event','public');
                }else{
                    $path = $event->card_image;
                }
                Event::where([ ['e_id','=',$approval_id],['staff_id','=',$staff->staff_id] ])
                    ->update([
                        'name' => $request->name,
                        'description' => $request->description, 
                        'date' => $request->date,
                        'time' => $request->time,
                        'card_image' => $path,
                        'sessions' => $request->sessions,
                        'venue' => $request->venue,
                        'max_participant' => $request->max_participant,
                        'resource_person' => $request->resource_person,
                        'category' => $request->category ,
                    ]);
            }
        }

         
        return response()->json([
            'success' => 'Event Updated !!',
        ]);

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
    

    public function markAttendance(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'session'       => 'required|string|max:190',
            'attending'     => 'required|array',
            
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $user = $request->user();
        $staff = Faculty::select('staff_id')->where([ ['email',$user->email] , ['role','=','Event Coordinator'] ])->first();
        $event = Event::where([ ['e_id','=',$id] , ['staff_id','=',$staff->staff_id] ])->first();
        if($staff){
            if($event){

                $studentsAttending = $request->attending;

                foreach($studentsAttending as $student){
                    DB::table('event_attendance')->insert([
                        'event_id'   => $event->e_id,
                        'student_id' => $student,
                        'session'    => $request->session,
                        'created_at' => Carbon::now() ,
                        'updated_at' => Carbon::now()
                    ]); 
                }
                
                return response()->json([
                    'success' => "Attendance for $request->session is Marked",
                ]);

            }else{
                return response()->json([
                    'error' => 'Event Does not exsist.',
                ],404);    
            }
        }else{
            return response()->json([
                'error' => 'Unauthorised Personal',
            ],404);
        }
    }

    public function updateAttendance(Request $request,$id){
        // $validator = Validator::make($request->all(), [
        //     'session'       => 'required|string|max:190',
        //     'attending'     => 'required|array',
            
        // ]);
        // if ($validator->fails()) {
        //     return response()->json(['error'=>$validator->errors()], 404);  
        //     exit(0);          
        // }

        $user = $request->user();
        $staff = Faculty::select('staff_id')->where([ ['email',$user->email] , ['role','=','Event Coordinator'] ])->first();
        $event = Event::where([ ['e_id','=',$id] , ['staff_id','=',$staff->staff_id] ])->first();
        if($staff){
            if($event){

               //TODO UPDATE ATTENDENCE


                // $studentsAttending = $request->attending;

                // foreach($studentsAttending as $student){
                //     DB::table('event_attendance')->insert([
                //         'event_id'   => $event->e_id,
                //         'student_id' => $student,
                //         'session'    => $request->session,
                //         'updated_at' => Carbon::now()
                //     ]); 
                // }
                
                // return response()->json([
                //     'success' => "Attendance for $request->session is Marked",
                // ]);

            }else{
                return response()->json([
                    'error' => 'Event Does not exsist.',
                ],404);    
            }
        }else{
            return response()->json([
                'error' => 'Unauthorised Personal',
            ],404);
        }
    }

    public function getAttendance(Request $request,$id){
        $user = $request->user();
        $staff = Faculty::select('staff_id')->where([ ['email',$user->email] , ['role','=','Event Coordinator'] ])->first();
        $event = Event::where([ ['e_id','=',$id] , ['staff_id','=',$staff->staff_id] ])->first();
        if($staff){
            if($event){
                $res = [];
                $sessionMarked = DB::table('event_attendance')->distinct()->where('event_id',$event->e_id)->pluck('session');
                
                foreach($sessionMarked as $session){

                    $students = DB::table('event_attendance')->where([ ['event_id','=',$event->e_id] , ['session','=',$session] ])->pluck('student_id');
                    $students = array("$session" => $students );
                    array_push($res,$students);
                }

                return $res;

            }else{
                return response()->json([
                    'error' => 'Event Does not exsist.',
                ],404);    
            }
        }else{
            return response()->json([
                'error' => 'Unauthorised Personal',
            ],404);
        }
    }

    public function usersRegisteredForEvent(Request $request ,$id){

        $user = $request->user();
        $staff = Faculty::select('staff_id')->where([ ['email',$user->email] , ['role','=','Event Coordinator'] ])->first();
         
        $event = Event::where([ ['e_id','=',$id] , ['staff_id','=',$staff->staff_id] ])->first();
        if($staff){
            if($event){
    
                $students = DB::table('event_registration')->select('registration_id as id','name','student_id','department','email')->where([ ['event_id','=',$id] , ['staff_id','=',null] ])->get();  
                $staff = DB::table('event_registration')->select('registration_id as id','name','staff_id','department','email')->where([ ['event_id','=',$id] , ['student_id','=',null] ])->get();
                
                $sLabel = [];
                $opS = json_decode($students, true);
                    foreach($opS as $a){
                        $changeEmail = array('label' => $a['email'] );
                        array_push($sLabel,$changeEmail);
                    }

                $fLabel = [];
                $opF = json_decode($staff, true);
                    foreach($opF as $a){
                        $changeEmail = array('label' => $a['email'] );
                        array_push($fLabel,$changeEmail);
                    }
                
                $fLabel = array('labelFaculties' => $fLabel);                    
                $sLabel = array('labelStudents' => $sLabel);

                $students = array('students' => $students );
                $staff    = array('faculties'  => $staff );
                
                $data = array_merge($students,$staff,$sLabel,$fLabel);
                return $data;

            }else{
                return response()->json([
                    'error' => 'Event Does not exsist.',
                ],404);    
            }
        }else{
            return response()->json([
                'error' => 'Unauthorised Personal',
            ],404);
        }

    }

    public function usersQueuedForEvent(Request $request ,$id){

        $user = $request->user();
        $staff = Faculty::select('staff_id')->where([ ['email',$user->email] , ['role','=','Event Coordinator'] ])->first();
         
        $event = Event::where([ ['e_id','=',$id] , ['staff_id','=',$staff->staff_id] ])->first();
        if($staff){
            if($event){
    
                $students = DB::table('event_queue')->select('id','name','student_id','department','email')->where([ ['event_id','=',$id] , ['staff_id','=',null] ])->get();  
                $staff = DB::table('event_queue')->select('id','name','staff_id','department','email')->where([ ['event_id','=',$id] , ['student_id','=',null] ])->get();
                
                $students = array('students' => $students );
                $staff    = array('faculties'  => $staff );
                
                $data = array_merge($students,$staff);
                return $data;

            }else{
                return response()->json([
                    'error' => 'Event Does not exsist.',
                ],404);    
            }
        }else{
            return response()->json([
                'error' => 'Unauthorised Personal',
            ],404);
        }
    }


    private function getHigherOrderPerson($role , $department){
        switch ($role){
            case 'Event Coordinator':
            case 'Faculty': 
                     $prog_cordinator = Faculty::select('staff_id','role')->where([ ['department','=', $department] , ['role','=','Program Coordinator'] ])->first();
                            if(!$prog_cordinator){
                                $HOD = Faculty::select('staff_id','role')->where([ ['department','=', $department] , ['role','=','HOD'] ])->first();
                            }else{
                                return $prog_cordinator;
                            }
                break;
            case 'Program Coordinator': 
                    return Faculty::select('staff_id','role')
                    ->where([ ['department','=', $department] , ['role','=','HOD'] ])->first();
            break;
            case 'Student': 
                return ['Unauthorised To make an Event']; 
            break;
            default: return 0;
        }
    }


    /**
     * //Change DOCS
     * 
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function requestApproval(Request $request){

        // 'poster'  => 'nullable|image', was removed
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:190',
            'description'=> 'required',
            'date'  => 'required|date',
            'time'  => 'required',
            'sessions' => 'required|integer|min:1|max:6',
            'venue'     => 'nullable|string|max:190',
            'card_image' => 'nullable|image',
            'max_participant' => 'integer',
            'resource_person' => 'required|string|max:190',
            'category' => 'required|string|max:190',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $user = $request->user();
        $staff = Faculty::select('staff_id','name','department')->where('email',$user->email)->first();
        $path = "";
        if($request->hasFile('card_image')){
            $path = $request->file('card_image')->store('event','public');
        }
    
        $higher = $this->getHigherOrderPerson('Faculty',$staff->department);

        //poster is not added
        $event =  DB::table('event_approvals')->insert([
            'staff_id' => $staff->staff_id,
            'staff_name' => $staff->name,
            'name' => $request->name,
            'description' => $request->description, 
            'date' => $request->date,
            'time' => $request->time,
            'card_image' => $path,
            'sessions' => $request->sessions,
            'venue' => $request->venue,
            'max_participant' => $request->max_participant,
            'resource_person' => $request->resource_person,
            'department' => $staff->department,
            'category' => $request->category,
            'approval_status' => $higher->role,
            'approval_personal' => $higher->staff_id,
        ]);

        return response()->json([
            'success' => 'Event Created ! In For Approval ',
        ]);

    }

    public function facultyEvents(Request $request){
        $user = $request->user();
        $staff = Faculty::select('staff_id')->where('email',$user->email)->first();
        $date = Carbon::now()->toDateString();
        $toApproveEvents = DB::table('event_approvals')
                            ->where([ ['staff_id','=',$staff->staff_id] ])->orderBy('date', 'asc')->get();
        foreach($toApproveEvents as $event){
            $event->format_date = $event->date;
            $event->format_time =  Carbon::parse($event->time)->format('H:i');
            $event->time = Carbon::parse($event->time)->format('h:i A');
            $event->short_date_number = Carbon::parse($event->date)->format('d');
            $event->short_date_month = Carbon::parse($event->date)->format('M');
            $event->long_date_day   = Carbon::parse($event->date)->format('l');
            $event->long_date_month = Carbon::parse($event->date)->format('F');
            $event->long_date_year = Carbon::parse($event->date)->format('Y');
            $staffHost = Faculty::where('staff_id',$event->staff_id)->first();
            $event->staff_name = $staffHost->name;
        }
        $toApproveEvents = array('unapproved' => $toApproveEvents );

        $toHappenEvents  = Event::where([ ['staff_id','=',$staff->staff_id] , ['date','>',$date] ])->orderBy('date', 'asc')->get();
        foreach($toHappenEvents as $event){
            $event->format_date = $event->date;
            $event->format_time =  Carbon::parse($event->time)->format('H:i');
            $event->time = Carbon::parse($event->time)->format('h:i A');
            $event->short_date_number = Carbon::parse($event->date)->format('d');
            $event->short_date_month = Carbon::parse($event->date)->format('M');
            $event->long_date_day   = Carbon::parse($event->date)->format('l');
            $event->long_date_month = Carbon::parse($event->date)->format('F');
            $event->long_date_year = Carbon::parse($event->date)->format('Y');
            $staffHost = Faculty::where('staff_id',$event->staff_id)->first();
            $event->staff_name = $staffHost->name;
        }
        $toHappenEvents = array('approved' => $toHappenEvents );

        // $toHappenAll = array_merge( $toHappenEvents , $toApproveEvents  ) ;
        // $toHappenAll = array('upcoming' => $toHappenAll );

        // $completedEvents  = Event::where([ ['staff_id','=',$staff->staff_id] , ['date','<',$date] ])->orderBy('date', 'desc')->get();
        $completedEvents= Event::where([ ['staff_id','=',$staff->staff_id] , ['date','<',$date] ])->orderBy('date', 'desc')->get();
        foreach($completedEvents as $event){
            $event->format_date = $event->date;
            $event->format_time =  Carbon::parse($event->time)->format('H:i');
            $event->time = Carbon::parse($event->time)->format('h:i A');
            $event->short_date_number = Carbon::parse($event->date)->format('d');
            $event->short_date_month = Carbon::parse($event->date)->format('M');
            $event->long_date_day   = Carbon::parse($event->date)->format('l');
            $event->long_date_month = Carbon::parse($event->date)->format('F');
            $event->long_date_year = Carbon::parse($event->date)->format('Y');
            $staffHost = Faculty::where('staff_id',$event->staff_id)->first();
            $event->staff_name = $staffHost->name;
        }
        $completedEvents = array('completed' => $completedEvents );

        $todaysEvents  = Event::where([ ['staff_id','=',$staff->staff_id] , ['date','=',$date] ])->orderBy('time', 'asc')->get();
        foreach($todaysEvents as $event){
            $event->format_date = $event->date;
            $event->format_time =  Carbon::parse($event->time)->format('H:i');
            $event->time = Carbon::parse($event->time)->format('h:i A');
            $event->short_date_number = Carbon::parse($event->date)->format('d');
            $event->short_date_month = Carbon::parse($event->date)->format('M');
            $event->long_date_day   = Carbon::parse($event->date)->format('l');
            $event->long_date_month = Carbon::parse($event->date)->format('F');
            $event->long_date_year = Carbon::parse($event->date)->format('Y');
            $staffHost = Faculty::where('staff_id',$event->staff_id)->first();
            $event->staff_name = $staffHost->name;
        }
        $todaysEvents = array('today' => $todaysEvents );

        $result = array_merge($todaysEvents,$toHappenEvents,$toApproveEvents,$completedEvents);
        // $result = json_encode(array('upcomming' => $toHappen));
        return $result ; 
    }

    public function toApproveList(Request $request){
        $user = $request->user();
        $staff = Faculty::select('staff_id','department','role')->where('email',$user->email)->first();
        $eventsToApprove = DB::table('event_approvals')
            ->where([ ['approval_status','=', $staff->role] , ['approval_personal','=',$staff->staff_id] ])->get();
        
        foreach($eventsToApprove as $event){
            $event->format_date = $event->date;
            $event->format_time =  Carbon::parse($event->time)->format('H:i');
            $event->time = Carbon::parse($event->time)->format('h:i A');
            $event->short_date_number = Carbon::parse($event->date)->format('d');
            $event->short_date_month = Carbon::parse($event->date)->format('M');
            $event->long_date_day   = Carbon::parse($event->date)->format('l');
            $event->long_date_month = Carbon::parse($event->date)->format('F');
            $event->long_date_year = Carbon::parse($event->date)->format('Y');
            $staffHost = Faculty::where('staff_id',$event->staff_id)->first();
            $event->staff_name = $staffHost->name;
        }
        return $eventsToApprove;
    }

    public function approve(Request $request){

        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'staff_id' => 'required',
            'category' => 'required|string|max:190',
            'message' => 'string|max:190|nullable'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $user = $request->user();
        $staff = Faculty::select('staff_id','department','role')->where('email',$user->email)->first();

        $approvingEventCount = DB::table('event_approvals')
            ->where([ ['approval_status','=', $staff->role] , ['approval_personal','=',$staff->staff_id] , ['name','=',$request->name] ])->count();

            $message = null;

        if(isset($request->message)){
            $message = $staff->role.':'.$request->message.","; 
        }

        if($approvingEventCount===1){
            if($staff->role!=="HOD"){
                $higher = $this->getHigherOrderPerson($staff->role,$staff->department);
                $event =  DB::table('event_approvals')->where('name','=',$request->name)->update([
                    'message' => $message ,
                    'approval_status' => $higher->role,
                    'approval_personal' => $higher->staff_id,
                    'denied' => false
                ]);
                return response()->json([
                    'success' => 'Event Approved ! Moved On to '.$higher->role,
                ]);
            }else{
                return $this->publishEvent($request->name,$request->staff_id);
            }
        }
    
    }


    public function deniedApproval(Request $request){
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'staff_id' => 'required',
            'category' => 'required|string|max:190',
            'message' => 'required|string|max:190'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);         
        } 

        $user = $request->user();
        $staff = Faculty::select('staff_id','department','role')->where('email',$user->email)->first();
    
        $event = DB::table('event_approvals')
                ->where([ ['approval_status','=', $staff->role], ['approval_personal','=',$staff->staff_id] , ['name','=',$request->name] ])->first();
        
        DB::table('event_approvals')->where('name','=',$request->name)->update([
                    'message' => $staff->role.':'.$request->message."," ,
                    'denied' => true
                ]);
        
        $toMailstaff = Faculty::select('email','name')->where('staff_id',$request->staff_id)->first();

        // return $staff->role;
        Mail::to($toMailstaff->email)->send(
                new EventUnapproved(
                    $event->name,
                    $event->date,
                    $event->category,
                    $request->message,
                    $staff->role));

        return response()->json([
            'success' => 'Message Passed to '.$toMailstaff->name,
        ]);

    }

    public function dropRegistration(Request $request, $id){
        $validator = Validator::make($request->all(), [
            "emails"    => 'required|array|min:1',
            'emails.*'  => 'required|email|distinct|max:190',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 404);  
            exit(0);          
        }

        $user = $request->user();
        $staff = Faculty::select('staff_id','department','role')->where('email',$user->email)->first();
        $event = Event::where([ ['staff_id',$staff->staff_id] , ['e_id',$id] ])->first();

        if($staff){
            if($event){
                foreach($request->emails as $e ){
                    $registered = DB::table('event_registration')->where([ ['email','=',$e] , ['event_id','=',$id] ])->first();    
                    if($registered){    
                        DB::table('event_registration')->where([ ['email','=',$e] , ['event_id','=',$id] ])->delete();
                        return "hello ".$registered->name;
                    }else{
                        return response()->json([
                            'error' => 'Not Registered for this event.',
                        ],404);  
                    }
                }

                return response()->json([
                    'success' => 'Success !! Registrations Droped. ',
                ]);

            }else{
                return response()->json([
                    'error' => 'No Such Event.',
                ],404);  
            }
        }else{
            return response()->json([
                'error' => 'Unauthorised Request.',
            ],404);    
        }
    }


    private function publishEvent($name,$staff_id){

        $approvedEvent = DB::table('event_approvals')
                        ->where([ ['name','=', $name] , ['staff_id','=',$staff_id] ])->first();

        $staff = Faculty::where('staff_id',$staff_id)->first();

        $event =  Event::create([
            'staff_id' => $approvedEvent->staff_id,
            'staff_name'=> $approvedEvent->name,
            'name' => $approvedEvent->name,
            'description' => $approvedEvent->description, 
            'date' => $approvedEvent->date,
            'time' => $approvedEvent->time,
            'venue' => $approvedEvent->venue,
            'card_image'=> $approvedEvent->card_image,
            'max_participant' => $approvedEvent->max_participant,
            'resource_person' => $approvedEvent->resource_person,
            'department' => $approvedEvent->department,
            'category' => $approvedEvent->category
        ]);

        
        Faculty::where('staff_id',$staff_id)->update(['role' => 'Event Coordinator']);
       
        // Mail::to($staff->email)->send(new EventApproved($approvedEvent->name,$approvedEvent->date,$approvedEvent->category));

        DB::table('event_approvals')->where([ ['name','=', $name] , ['staff_id','=',$staff_id] ])->delete();


        // Creating New TAble approach !!
        // $eventCreated = Event::where('name',$approvedEvent->name)->first();
        // $raw_name = preg_replace('/\s+/', '_', strtolower($eventCreated->name)); 
        // $table_name = $eventCreated->e_id.'_'.$raw_name;
        // Schema::connection('mysql')->create($table_name, function(Blueprint $table)
        // {
        //     $table->increments('id');
        //     $table->string('reg_num');
        //     $table->string('name');
        //     $table->string('department');   
        //     $table->string('email');
        // });

        return response()->json([
            'success' => 'Event Published !',
        ]);
    }

    public function downloadAttendingReport(Request $request,$id){

        $user = $request->user();
        $staff = Faculty::select('staff_id')->where([ ['email','=',$user->email] , ['role','=','Event Coordinator'] ])->first();
        $event = Event::where('e_id',$id)->first();

        if($staff){
            if($event->staff_id == $staff->staff_id){
                $students = DB::table('event_registration')
                            ->select('name','student_id','department','email')
                            ->where([ ['event_id','=',$id] , ['staff_id','=',null] ])
                            ->orderBy('student_id', 'asc')->get();

                view()->share('students',$students);
            
                // Set extra option
                PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
                // pass view file
                $pdf = PDF::loadView('pdf.attending', compact('students'));
                // download pdf
                return $pdf->download('attendence.pdf');
                }else{
                return response()->json([
                    'error' => 'Event Does not exsist.',
                ],404);   
            }
        }else{
            return response()->json([
                'error' => 'Unauthorised Access.',
            ],404);   
        }

    }

}
