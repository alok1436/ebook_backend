<?php

namespace App\Http\Controllers\Api;
use Auth;
use Hash;
use Validator;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterUser;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\OtpVarificationRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\EmailVarificationRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="name",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 *                 example={"name":"abc", "email": "abc@gr.la", "password": "123456"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             @OA\Examples(example="result", value={"status": true,"message": "User Created Successfully","token": "6|QHTXDUZjs456BJjbq6hRmF2SDa5FzoCdvs9xxnfqd47ba26c"}, summary="An result object.")
 *         )
 *     )
 * )
 */
    public function createUser(RegisterUser $request) : JsonResponse {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("LaravelToken")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="login user",
 *      tags={"Auth"},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="email",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 *                 example={"email": "abc@gr.la", "password": "123456"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Examples(example="result", value={"status": true,"message": "User Logged In Successfully","token": "55|EcDYaWfHnpNhhI8Zc32ZOPaN8aHmmA5P5zT0pZkie800607f","user": {"id": 6,"name": "Alok Singh","email": "me1@gmail.com","email_verified_at": null,"created_at": "2024-04-29T03:15:48.000000Z","updated_at": "2024-04-29T12:44:03.000000Z", "balance": 9843}}, summary="An result object.")
 *         )
 *     ),
 *      @OA\Response(
 *         response=401,
 *         description="failed",
 *         @OA\JsonContent(
 *             @OA\Examples(example="result", value={"status": false,"message": "Email & Password does not match with our record."}, summary="An result object.")
 *         )
 *     )
 * )
 */
    public function loginUser(LoginRequest $request) : JsonResponse {
        try {
            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }
            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("LaravelToken")->plainTextToken,
                'user' => $user
            ], 200);

        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * ForgotPassword The Customer
     * @param Request $request
     * @return json
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
       try{
       
        $user = User::where('email',$request->email)->first();
        if($user){

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->save();
            $incryptedId = Crypt::encrypt($user->id);
            $url = route('otp-varification', $incryptedId);
            // Mail::to($user->email)->send(new ForgotPassword($user,$url));
            //Mail::to('admin@gbi.com.au')->cc(['orders@gbi.com.au','dheeraj@techdilation.com'])->send(new ForgotPassword($user));

           
            return response()->json([
                'success'       => true,
                'message'       => 'OTP send you register email.',
                'redirect_url'  => $url,
            ],200);
        }
       }
       catch (\Throwable $th) {
        \Log::error($th->getMessage());
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
       }
   
    }

     /**
     * OtpVarification The Customer
     * @param Request $request
     * @return json
     */
    public function otpVarification(OtpVarificationRequest $request, $incryptedId): JsonResponse
    {
            try{
                 
            $decryptedId = Crypt::decrypt($incryptedId);
            
            $user = User::find($decryptedId);
            $url =  route('reset-password',$incryptedId);
             // Mail::to($user->email)->send(new ResetPassword($user,$url));
                if($user->otp==$request->otp){

                    return response()->json([
                        'success' => true,
                        'message' => 'OTP matched.',
                        'redirect_url'  => route('reset-password',$incryptedId),
                    ],200);
                }
            }
            catch (\Throwable $th) {
                \Log::error($th->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                    // 'message' => 'Your otp not matched, Please try again.'
                ], 500);
               }
         
    }

    /**
     * ResetPassword The Customer
     * @param Request $request
     * @return json
     */
    public function resetPassword(ResetPasswordRequest $request, $incryptedId): JsonResponse
    {
        try{
     
            $decryptedId = Crypt::decrypt($incryptedId);
            $user = User::find($decryptedId);
            if($user){
                $user->password = Hash::make($request->password);
                // $user->is_invited = 0;
                $user->save();

                return response()->json([
                    'success'       => true,
                    'message'       => 'Yout password has been changes successfully.',
                    'redirect_url'  => route('login'),
                ],200);
            }
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                // 'message' => 'Account not found.'
            ], 500);
           }
       
    }

     /**
     * EmailVarification The Customer
     * @param Request $request
     * @return json
     */
    public function emailVarification(EmailVarificationRequest $request): JsonResponse
    {
            try{    
            $user = User::where('email',$request->email)->first();
            if($user->email==$request->email){
                $user->email_verify = 1; //add column in database
                return response()->json([
                    'success' => true,
                    'message' => 'Email verified.',
                ],200);
            }
            }
            catch (\Throwable $th) {
                \Log::error($th->getMessage());
                return response()->json([
                    'status' => false,
                    // 'message' => $th->getMessage(),
                    'message' => 'Email not found.'
                ], 500);
               }
         
    }
}
