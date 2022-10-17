<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LoginWithOtpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('loginWithOtp');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'mobile' => 'required_without:email|nullable|digits:10|numeric',
            'email' => 'required_without:mobile|nullable|email',
        ]);

        $email = User::where('email', $request->email)->first();
        $mobile = User::where('mobile', $request->mobile)->first();

        if (!empty($request->mobile) && !empty($request->email)) {
            if (!empty($email) && !empty($mobile)) {
                $six_digit_random_number = random_int(100000, 999999);

                $data = [
                    'user_otp' => $six_digit_random_number,
                ];

                User::where('email', $request->email)->where('mobile', $request->mobile)->update($data);

                $data['email']         = User::where('email', $request->email)->where('mobile', $request->mobile)->first()->email;
                $data['subject']    = 'Your OTP';
                $data['otp']         = $six_digit_random_number;
                $data['view']        = 'mail.otp';

                Mail::to($data['email'])->send(new \App\Notifications\OTPMail($data));

                $row = User::where('email', $request->email)->where('mobile', $request->mobile)->first()->id;

                return response()->json(['status' => 200, 'message' => 'check otp in your mail!', 'data' => $row]);
            } else {
                return response()->json(['status' => 402, 'message' => 'the email or phone have not registered!']);
            }
        } else if (!empty($mobile) || !empty($email)) {
            $six_digit_random_number = random_int(100000, 999999);

            $data = [
                'user_otp' => $six_digit_random_number,
            ];

            User::where('email', $request->email)->orwhere('mobile', $request->mobile)->update($data);

            $data['email']         = User::where('email', $request->email)->orwhere('mobile', $request->mobile)->first()->email;
            $data['subject']    = 'Your OTP';
            $data['otp']         = $six_digit_random_number;
            $data['view']        = 'mail.otp';

            Mail::to($data['email'])->send(new \App\Notifications\OTPMail($data));

            $row = User::where('email', $request->email)->orwhere('mobile', $request->mobile)->first()->id;

            return response()->json(['status' => 200, 'message' => 'check otp in your mail!', 'data' => $row]);
        } else {
            return response()->json(['status' => 402, 'message' => 'the email or phone have not registered!']);
        }
    }

    public function verifyOtp(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'user_otp' => 'required|digits:6|numeric',
        ]);

        $user_id = User::where('user_otp', $request->user_otp)->where('id', $request->id)->first();

        if (!empty($user_id)) {
            if (Auth::loginUsingId($user_id->id)) {
                return response()->json(['status' => 200, 'message' => 'Logged In!']);
            } else {
                return response()->json(['status' => 402, 'message' => 'wrong otp entered!']);
            }
        } else {
            return response()->json(['status' => 402, 'message' => 'wrong otp entered!']);
        }
    }

    public function resendOtp(Request $request)
    {
        $six_digit_random_number = random_int(100000, 999999);

        $data = [
            'user_otp' => $six_digit_random_number,
        ];

        User::where('id', $request->id)->update($data);

        $data['email']         = User::where('id', $request->id)->first()->email;
        $data['subject']    = 'Your Resended OTP';
        $data['otp']         = $six_digit_random_number;
        $data['view']        = 'mail.otp';

        Mail::to($data['email'])->send(new \App\Notifications\OTPMail($data));

        return response()->json(['status' => 200, 'message' => 'check resended otp in your mail!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
