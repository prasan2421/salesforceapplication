<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\User;

use App\Helpers\Common;

use stdClass;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('app.version');
        $this->middleware('token', [ 'only' => [ 'getProfile', 'updateProfile', 'changePassword' ] ]);
    }

    /**
     * Login existing user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*public function login(Request $request)
    {
        $email = trim($request->email);
        $password = $request->password;

        $errors = [];
        
        if(!$email) {
            $errors['email'] = 'Email is required';
        }

        if(!$password) {
            $errors['password'] = 'Password is required';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $user = User::where('role', 'dsm')
                    ->where('email', $email)
                    ->first();

        if(!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'email' => 'Incorrect email and/or password'
                ]
            ]);
        }

        $user->verification_code = $this->generateRandomString();
        $user->save();

        $this->sendVerificationEmail($user->name, $user->email, $user->verification_code);

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }*/

    public function login(Request $request)
    {
        $email = trim($request->email);
        $password = $request->password;

        $errors = [];
        
        if(!$email) {
            $errors['email'] = 'Email is required';
        }

        if(!$password) {
            $errors['password'] = 'Password is required';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        // $user = User::where('role', 'dsm')
        //             ->where(function($query) use ($email) {
        //                 $query->where('email', $email)
        //                     ->orWhere('username', $email);
        //             })
        //             ->first();

        $user = User::whereIn('role', ['dsm', 'sales-officer'])
                    ->where('username', $email)
                    ->where('is_active', true)
                    ->first();

        if(!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'email' => 'Incorrect email and/or password'
                ]
            ]);
        }

        $user->token = $this->generateToken($user->id);
        $user->save();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $user->token
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Verify login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyLogin(Request $request)
    {
        $email = trim($request->email);
        $verification_code = trim($request->verification_code);

        $errors = [];
        
        if(!$email) {
            $errors['email'] = 'Email is required';
        }

        if(!$verification_code) {
            $errors['verification_code'] = 'Verification Code is required';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $user = User::where('role', 'dsm')
                    ->where('email', $email)
    				->where('verification_code', $verification_code)
    				->first();

        if(!$user) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'verification_code' => 'Incorrect verification code'
                ]
            ]);
        }

        $user->token = $this->generateToken($user->id);
        $user->verification_code = null;
        $user->save();

        return response()->json([
            'success' => true,
            'data' => [
            	'token' => $user->token
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get profile of current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProfile(Request $request)
    {
        $user = $request->user;

        $verticals = $user->verticals()
                        ->orderBy('name')
                        ->pluck('name')
                        ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'emp_code' => $user->emp_code,
                'name' => $user->name,
                'gender' => $user->gender,
                'date_of_birth' => $user->date_of_birth,
                'email' => $user->email,
                'contact_number' => $user->contact_number,
                'address' => $user->address,
                'verticals' => implode(', ', $verticals)
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Update profile of current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $gender = trim($request->gender);
        $date_of_birth = trim($request->date_of_birth);
        $email = trim($request->email);
        $contact_number = trim($request->contact_number);
        $address = trim($request->address);

        $errors = [];

        if($gender && !in_array($gender, ['male', 'female'])) {
            $errors['gender'] = 'Gender is invalid';
        }

        if($date_of_birth && !Common::isDateValid($date_of_birth)) {
            $errors['date_of_birth'] = 'Date of Birth is invalid';
        }

        if($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is invalid';
        }

        if(!$contact_number) {
            $errors['contact_number'] = 'Contact Number is required';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $user = $request->user;
        $user->gender = Common::nullIfEmpty($gender);
        $user->date_of_birth = Common::nullIfEmpty($date_of_birth);
        $user->email = Common::nullIfEmpty($email);
        $user->contact_number = Common::nullIfEmpty($contact_number);
        $user->address = Common::nullIfEmpty($address);
        $user->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Change password of current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $current_password = $request->current_password;
        $new_password = $request->new_password;
        // $new_password_confirmation = $request->new_password_confirmation;

        $errors = [];

        if(!$current_password) {
            $errors['current_password'] = 'Current Password is required';
        }

        if(!$new_password) {
            $errors['new_password'] = 'New Password is required';
        }

        // if(!$new_password_confirmation) {
        //     $errors['new_password_confirmation'] = 'New Password Confirmation is required';
        // }

        // if(!isset($errors['new_password']) && !isset($errors['new_password_confirmation'])) {
        //     if($new_password != $new_password_confirmation) {
        //         $errors['new_password_confirmation'] = 'New Password Confirmation does not match';
        //     }
        // }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $user = $request->user;
        if(!Hash::check($current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'current_password' => 'Current Password is incorrect'
                ]
            ]);
        }

        $user->password = bcrypt($new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    private function sendVerificationEmail($name, $email, $verification_code) {
        $to = $email;
        $subject="Verify Login for Patanjali";
        $from = 'noreply@ordermanagement.com';

        $message = 'Dear ' . $name . ',<br/><br/>';
        $message .= 'Please use the following code to verify your login:<br/>';
        $message .= '<strong>' . $verification_code . '</strong><br/><br/>';
        $message .= 'Thanks,<br/>';
        $message .= 'Patanjali';

        $headers = "From: Patanjali <" . $from . ">\r\n";
        // $headers .= "Reply-To: ". $from . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        mail($to, $subject, $message, $headers);
    }

    private function generateToken($userID)
    {
        return md5('token' . $userID . time());        
    }

    private function generateRandomString($length = 6)
    {
        $str = '';
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
}
