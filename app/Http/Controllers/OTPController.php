<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserOtp;
use Mail;

class OTPController extends Controller
{

public function loginwithotppost(Request $request)      // email OTP
    {
        //dd($request);
        $request->validate([
            'email'=>'required|email|max:50',
        ]);
        $checkIsUser = User::where('email', $request->email)->first();
        if(is_null($checkIsUser) ){
            return redirect()->route('login.with.otp')->with('error','Your account is not registered with us.');
        } else {
            $otp = rand(123456, 999999);
            $userupdate = User::where('email',$request->email)->update([
                'otp_token' => $otp,
            ]);

            Mail::send('emails.loginWithOTPEmail',['otp'=>$otp],function($message) use($request){
                $message->to($request->email);
                $message->subject('Login with OTP - liveskytech.com');
            });

            return redirect('confirm-login-with-otp')->withSuccess('OTP sent to your email address. Please check your Inbox/Spam Folder.');
        }
    }

    public function confirmloginwithotppost(Request $request)       // email OTP
    {
        //dd($request->all());
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $checkUser = User::where('otp_token',$request->otp)->where('email', $request->email)->first();
        if(is_null($checkUser) ){
            return redirect()->route('confirm.login.with.otp')->with('error','OTP or Email is incorrect.');
        } else {
            $user = User::where('email',$request->email)->first();
            if($user){
                $userupdate = User::where('email',$request->email)->update([
                    'otp_token' =>NULL
                ]);
                Auth::login($user);
                return redirect()->route('home')->with('success','Welcome to user dashboard.');
            }
            return redirect()->back()->with('error','Login with otp failed.');
        }
    }



    // login with Mobile OTP

    public function login()
    {
        return view('auth.otpLogin');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generate(Request $request)
    {
        /* Validate Data */
        $request->validate([
            'mobile_no' => 'required|exists:users,mobile_no'
        ]);
  
        /* Generate An OTP */
        $userOtp = $this->generateOtp($request->mobile_no);
        $response = $userOtp->sendSMS($request->mobile_no);

        if ($response['status'] == 'success') {
            return redirect()->route('otp.verification', ['user_id' => $userOtp->user_id])
                                ->with('success', $response['message']);
        } else {
            return redirect()->route('otp.verification', ['user_id' => $userOtp->user_id])
                                ->with('error', "Error: " . $response['message']);
        }
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generateOtp($mobile_no)
    {
        $user = User::where('mobile_no', $mobile_no)->first();
  
        /* User Does not Have Any Existing OTP */
        $userOtp = UserOtp::where('user_id', $user->id)->latest()->first();
  
        $now = now();
  
        if($userOtp && $now->isBefore($userOtp->expire_at)){
            return $userOtp;
        }
  
        /* Create a New OTP */
        return UserOtp::create([
            'user_id' => $user->id,
            'otp' => rand(123456, 999999),
            'expire_at' => $now->addMinutes(10)
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function verification($user_id)
    {
        return view('auth.otpVerification')->with([
            'user_id' => $user_id
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function loginWithOtp(Request $request)
    {
        /* Validation */
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        ]);  
  
        /* Validation Logic */
        $userOtp   = UserOtp::where('user_id', $request->user_id)->where('otp', $request->otp)->first();
  
        $now = now();
        if (!$userOtp) {
            return redirect()->back()->with('error', 'Your OTP is not correct');
        }else if($userOtp && $now->isAfter($userOtp->expire_at)){
            return redirect()->route('otp.login')->with('error', 'Your OTP has been expired');
        }
    
        $user = User::whereId($request->user_id)->first();
  
        if($user){
              
            $userOtp->update([
                'expire_at' => now()
            ]);
  
            Auth::login($user);
  
            return redirect('/home');
        }
  
        return redirect()->route('otp.login')->with('error', 'Your Otp is not correct');
    }

}
