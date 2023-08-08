<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;


class UserController extends Controller
{
    // Signup function
    public function showSignupForm()
    {
        return view('user.signup');
    }

    /**
     * @throws GuzzleException
     */
    public function signup(Request $request)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create a new zendesk user
        $userData = [
            'user' => [
                'name' => $request->name,
                'email' => $request->email,
                'skip_verify_email' => true
            ],
        ];

        // Initialize cURL session
        $jsonUserData = json_encode($userData);
        $apiUrl = config('constants.SUB_DOMAIN').'/api/v2/users.json';

        // Set your Zendesk API credentials
        $email = config('constants.ADMIN_EMAIL_ADDRESS');
        $password = config('constants.ADMIN_PASSWORD');

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
                CURLOPT_USERPWD => "$email:$password",
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $jsonUserData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true, // Disable SSL verification if needed
            ]);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                return 'cURL Error: ' . curl_error($ch);
            }
            curl_close($ch);
        }
        catch (\Exception $exception){
            return redirect()->route('signup.form')->with('error', $exception->getMessage());
        }

        // Process the response data
        $responseData = json_decode($response, true);
        if( isset($responseData['error'])){
            if( isset($responseData['details']['email'][0]['description'])){
                return redirect()->route('signup.form')->with('error', strip_tags($responseData['details']['email'][0]['description']));
            }
            else{
                return redirect()->route('signup.form')->with('error', json_encode($responseData['details']));
            }
        }
        $userId = $responseData['user']['id'];
        try {
            $responseBody = $this->setPasswordByAdmin($userId, $request->password);
        }
        catch (\Exception $exception){
            return redirect()->route('signup.form')->with('error', $exception->getMessage());
        }

        // Create a new Guzzle Response instance with the response body
        $response = new Response(200, [], $responseBody);
        if ($response->getStatusCode() === 200) {

            // Check if a user with the provided email already exists
            $existingUser = User::where('email', $request->email)->first();

            if (!$existingUser) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'is_admin' => $request->has('is_admin'), // Set the is_admin field based on checkbox
                ]);
                if ($user) {
                    // User sign up successful, you can redirect the user to a success page
                    return redirect()->route('login')->with('success', 'Signup successful! Please login.');
                }
                else {
                    // User creation failed
                    return redirect()->route('signup.form')->with('error', 'User creation failed on site. Please try again.');
                }
            }
            else{
                // User creation failed
                return redirect()->route('signup.form')->with('error', 'User creation failed on site. Already exists.');
            }
        }
        else {
            // If there was an error, you can handle it accordingly
            // For example, display an error message to the user
            return redirect()->route('signup.form')->with('error', 'Failed to sign up. Please try again.');
        }
    }

    // set user's password by admin
    public function setPasswordByAdmin($userId, $password){
        // Set your Zendesk API credentials
        $email = config('constants.ADMIN_EMAIL_ADDRESS');
        $password = config('constants.ADMIN_PASSWORD');

        $passwordData = [
            'password' => $password,
        ];
        $jsonPasswordData = json_encode($passwordData);
        $apiUrl = config('constants.SUB_DOMAIN').'/api/v2/users/'.$userId.'/password.json';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_USERPWD => "$email:$password",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $jsonPasswordData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true, // Disable SSL verification if needed
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'cURL Error: ' . curl_error($ch);
        }
        curl_close($ch);

        return $response;
    }

    // Login function
    public function showLoginForm()
    {
        return view('user.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $userName = $user->name;

            // Code from https://github.com/zendesk/zendesk_jwt_sso_examples/blob/master/php_jwt.php
            $key       = config('constants.SHARED_SECRET');
            $subdomain = config('constants.SUB_DOMAIN');
            $now       = time();

            $header = [
                "typ" => "JWT",
                "alg" => "HS256" // Specify the JWT algorithm as HS256
            ];

            $token = array(
                "jti"   => md5($now . rand()),
                "iat"   => $now,
                "name"  => $userName,
                "email" => $request->email
            );

            $jwt = JWT::encode($token, $key, 'HS256', null, $header); // Include the header

            $location = $subdomain . "/access/jwt?jwt=" . $jwt;
            $location .= "&return_to=" . url(route('admin'));
//echo $jwt.'<br>';
            $location = $subdomain . "/access/jwt?jwt=" . $jwt;
            //$location .= "&return_to=" . url(route('admin'));
//echo $location;

            // Redirect
            return redirect(url($location));
        }
        else {
            // Authentication failed
            return redirect()->route('login')->withErrors(['login' => 'Invalid credentials']);
        }
    }

    // Change password function
    public function showChangePasswordForm()
    {
        return view('change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('home')->with('success', 'Password changed successfully!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
