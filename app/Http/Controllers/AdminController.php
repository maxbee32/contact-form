<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Mail\RejectMail;
use App\Mail\ApproveMail;
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
        $this->middleware('auth:api', ['except'=>['adminSignUp','adminLogin','getForms','sendDecision']]);
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
    $results=  DB::table('users')
    ->get(array(
          'id',
          'dob',
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


public function sendDecision(Request $request){
    $validator = Validator::make($request->all(), [
        'decision' => ['required','string'],
    ]);

    if($validator-> fails()){
        return $this->sendResponse([
            'success' => false,
            'data'=> $validator->errors(),
            'message' => 'Validation Error'
        ], 400);

    }
    $decision = ($request->decision);
    if($decision =="Approved"){
        $results=  DB::table('users')
        ->get(array(
              'id',
              'dob',
              'email',
              'phone',
              'address',
              'remarks',
              'status'
        ));
        $results->update(['status'=>$request->decision]);
        Mail::to($results->email)->send(new ApproveMail);
    }else{

        if($decision =="Rejected"){
            $results=  DB::table('users')
            ->get(array(
                  'id',
                  'dob',
                  'email',
                  'phone',
                  'address',
                  'remarks',
                  'status'
            ));
            $results->update(['status'=>$request->decision]);
            Mail::to($results->email)->send(new RejectMail);
}

    return $this ->sendResponse([
        'success' => true,
         'message' => $results,

       ],200);
}
}
}
