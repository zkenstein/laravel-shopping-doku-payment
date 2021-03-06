<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use Mail;
use Form;
use URL;
use Image;

class UserController extends Controller
{
    public function getSignup()
    {
    	return view('user.signup');
    }

    public function postSignup(Request $req)
    {
    	$this->validate($req, [
    			'email' => 'email|required|unique:users',
    			'password' => 'required|min:6',
    			'password_verify' => 'required|min:6|same:password',
    			'captcha' => 'required|captcha'
    		]);

		$verificationToken = str_random();
		
    	$user = new User([
			'email' => $req->email,
			'password' => bcrypt($req->password),
			'verification_token' => $verificationToken
    		]);
			
		// echo 'best match!!';
		if($user->save()){

			$verificationLink = route('user.verifyUser', ['id' => $user->id, 'token' => $verificationToken]);
			Mail::send('mails.userVerification', ['verificationLink' => $verificationLink], function($message) use($req){
                    $message->to($req->email)->subject('Email Verifikasi');
                    $message->from('pfw2017k05@gmail.com', 'KC');
                }
            );

		}

    	// Auth::login($user);

    	return redirect()->route('user.askVerification');
	}
	
	public function askVerification(){

		return view('user.askVerification');
		
	}

	public function resendVerificationToken(Request $request){

		$this->validate($request, [
			'email' => 'email|required'
		]);

		$email = $request->email;

		$verificationToken = str_random();

		$user = User::where('email', $email)->first();
		$user->verification_token = $verificationToken;
		$user->save();

		$verificationLink = route('user.verifyUser', ['id' => $user->id, 'token' => $verificationToken]);
		Mail::send('mails.userVerification', ['verificationLink' => $verificationLink], function($message) use($email){
				$message->to($email)->subject('Email Verifikasi');
				$message->from('muktikun@gmail.com', 'Roppa');
			}
		);

		return redirect()->route('user.askVerification');

	}

	public function verifyUser(Request $request){

		$user_id = $request->id;
		$token = $request->token;

		$user = User::find($user_id);

		if($user->verification_token === $token){
			$user->verified = 1;
			$user->save();
			Auth::login($user);
		}

		return redirect()->route('user.setbio');

	}

	public function setBio(){

		return view('user.setBio');

	}

	public function storeBio(Request $request){

		$this->validate($request, [
			'fullname' => 'required',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required',
			'country' => 'required',
			'zipcode' => 'required|numeric',
			'phone' => 'required|numeric',
			'birthdate' => 'required|date'
		]);

		$user = Auth::user();
		
		$user->fullname = $request->fullname;
		$user->address = $request->address;
		$user->city = $request->city;
		$user->state = $request->state;
		$user->country = $request->country;
		$user->zipcode = $request->zipcode;
		$user->phone = $request->phone;
		$user->birthdate = $request->birthdate;
		
		if($user->save()){
			return redirect()->route('user.profile');
		}

	}

    public function getSignin()
    {
    	return view('user.signin');
    }

    public function postSignin(Request $req)
    {
    	$this->validate($req, [
    			'email' => 'email|required',
    			'password' => 'required|min:6'
    		]);

    	if (Auth::attempt([
    	    			'email' => $req->email,
    	    			'password' =>$req->password
    	    		])) {
			$user = Auth::user();
			if($user->role == 2){
				return redirect()->route('user.admin');
			}
			return redirect()->route('user.profile');
    	}
    	return redirect()->back();
    }

    public function getProfile()
    {
		$user = Auth::user();

		$data = [
			'fullname' => $user->fullname,
			'address' => $user->address,
			'city' => $user->city,
			'state' => $user->state,
			'country' => $user->country,
			'zipcode' => $user->zipcode,
			'phone' => $user->phone,
			'birthdate' => date('d F Y', strtotime($user->birthdate)),
		];

    	return view('user.profile', $data);
	}
	
    public function editProfile()
    {
		$user = Auth::user();

		$data = [
			'fullname' => $user->fullname,
			'address' => $user->address,
			'city' => $user->city,
			'state' => $user->state,
			'country' => $user->country,
			'zipcode' => $user->zipcode,
			'phone' => $user->phone,
			'birthdate' => $user->birthdate,
		];

    	return view('user.edit-profile', $data);
	}

	public function editProfilePicture(Request $request){

		if($request->hasFile('file')){
			$user = Auth::user();

			$image       = $request->file('file');
			// $filename    = $image->getClientOriginalName();
			$filename = 'profile_'.$user->id.'_'.date('now');
			
			$image_resize = Image::make($image->getRealPath());         
			$extension = str_replace('image/', '.', $image_resize->mime());
			// if($image_resize->height() > $image_resize->width()){
			// 	$image_resize->resize(null, 150, function ($constraint) {
			// 		$constraint->aspectRatio();
			// 	});
			// }else{
			// 	$image_resize->resize(150, null, function ($constraint) {
			// 		$constraint->aspectRatio();
			// 	});
			// }
			$image_resize->fit(150);

			if($image_resize->save('img/user_profile/' .$filename.$extension)){
				$user->image = '/img/user_profile/' .$filename.$extension;
				$user->save();
			}
		}

		return redirect(route('user.profile'));

	}

	public function getSetting(){
		return view('user.user-setting');
	}

	public function updatePass(Request $request){
		$this->validate($request, [
			'old_pass' => 'required',
			'new_pass' => 'required',
			'verify_pass' => 'required|same:new_pass'
		]);
		
		$user = Auth::user();

		if(!password_verify($request->old_pass, $user->password)){
			return back();
		}

		$user->password = bcrypt($request->new_pass);
		return ($user->save()) ? redirect()->route('user.profile') : back();	
	}

	public function removePicture(){
		$user = Auth::user();
		if(is_file(public_path($user->image))){
			if(unlink(public_path($user->image))){
				$user->image = null;
				$user->save();
			}
		}

		return back();
	}

	public function getTransactions(){
		$orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
		return view('user.transactions', ['orders' => $orders]);
		// dd($orders);
	}

    public function getLogout()
    {
    	Auth::logout();
    	return redirect()->back();
	}
	
}
