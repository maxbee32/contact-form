<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Mail\BulkMail;
use App\Mail\RejectMail;
use App\Mail\ApproveMail;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //
    public function sendResponse($data, $message, $status = 200){
        $response =[
            'data' => $data,
            'message' => $message
        ];
        return response()->json($response, $status);
     }

     public function __construct(){
        $this->middleware('auth:api', ['except'=>['adminSignUp','adminLogin','getForms','sendApproveDecision','bulkEmail']]);
    }


     public function adminSignUp(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>['required','email:rfc,filter,dns','unique:admins'],
            'password'=>['required'],


        ]);


        if($validator->stopOnFirstFailure()-> fails()){
            return $this->sendResponse([
                'success' => false,
                'data'=> $validator->errors(),
                'message' => 'Validation Error'
            ], 400);

        }
           Admin::create(array_merge(
                    $validator-> validated(),
                    ['password'=>bcrypt($request->password)]
                ));


                return $this->sendResponse([
                    'success' => true,
                    'message' => "Admin created successfully."
                ],200);
}


public function adminLogin(Request $request){
    $validator = Validator::make($request->all(), [
        'email'=>['required','email:rfc,filter,dns'],
        'password'=> ['required','string'],

    ]);


    if($validator->stopOnFirstFailure()-> fails()){
        return $this->sendResponse([
            'success' => false,
            'data'=> $validator->errors(),
            'message' => 'Validation Error'
        ], 400);


    }
    return $this->sendResponse([
        'success' => true,
        'message' => "Logged in successfully"
    ],200);


}



public function getForms(){
    $results= DB::table('users')
    ->get(array(
          'id',
          'dob',
          'name',
          'email',
          'phone',
          'address',
          'remarks'
    ));

    return $this ->sendResponse([
        'success' => true,
         'message' => $results,

       ],200);
}


public function sendApproveDecision(Request $request, $id){
    $validator = Validator::make($request->all(), [
        'status' => ['required','string'],
    ]);

    if($validator-> fails()){
        return $this->sendResponse([
            'success' => false,
            'data'=> $validator->errors(),
            'message' => 'Validation Error'
        ], 400);

    }
    // $status =($request->status);
    $user = User::where('id',$id)->first();
    if($request->status == 'Approved'){
        $user->update(['status' => $request->status]);
        Mail::to($user->email)->send(new ApproveMail);

    }
        return $this ->sendResponse([
            'success' => true,
              'message' => 'Admin approved contact form',

           ],200);

}



public function bulkEmail(Request $request){
  $validator = Validator::make($request->all(), [
        'message' => ['required','string']
    ]);

    if($validator-> fails()){
        return $this->sendResponse([
            'success' => false,
            'data'=> $validator->errors(),
            'message' => 'Validation Error'
        ], 400);

    }

     Message::create(array_merge(
        $validator-> validated(),
    ));
     $msgg = Message::select('*')->first();

       $user =User::where('status','=','Approved')->get('email');



       foreach ($user as $key =>$user){

        $email = $user->email;

        Mail::to($email)->send(new BulkMail($msgg));


       }
       DB::table('messages')->delete();

return $this->sendResponse([
    'success' => true,
    'message' => 'bulk email submitted'
], 200);
 }

}

