<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Mail\ContactMail;
use App\Mail\ReceiveMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{


    public function sendResponse($data, $message, $status = 200){
        $response =[
            'data' => $data,
            'message' => $message
        ];
        return response()->json($response, $status);
     }

     public function __construct(){
        $this->middleware('auth:api', ['except'=>['contactForm']]);
    }



     public function contactForm(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>['required','email:rfc,filter,dns','unique:users'],
            'dob'=>['required','date'],
            'name'=>['required','string'],
            'phone'=>['required','regex:/^(\+\d{1,3}[- ]?)?\d{10}$/','min:10'],
            'address'=>['required','string'],
            'remarks'=>['required', 'string']

        ]);


        if($validator->stopOnFirstFailure()-> fails()){
            return $this->sendResponse([
                'success' => false,
                'data'=> $validator->errors(),
                'message' => 'Validation Error'
            ], 400);


        }

        User::create(array_merge(
            $validator-> validated()
        ));


        $admin =Admin::select('email')->get();
        Mail::to($request->email)->send(new ReceiveMail);
        Mail::to($admin)->send(new ContactMail);

        return $this->sendResponse([
            'success' => true,
            'message' => 'Details submitted successfully.'
        ], 200);

     }
}
