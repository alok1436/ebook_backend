<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class UserController extends Controller
{
    /**
     * buyACookie
     * @param array| $request
     * return json
     */
    public function buyACookie(Request $request): JsonResponse{
        try {  
            $user = $request->user(); 
            $cookies = $request->filled('cookies') ? $request->get('cookies') : 1;
            if($user->wallet > 0){
                if($user->wallet >= $cookies){
                    $request->user()->ledgers()->create(['credit'=> $cookies]);
                    return response()->json(['message'=>'Cookie purcased.'], 201);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Insufficient wallet balance for purchasing the cookies.',
                    ], 400);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient wallet balance',
                ], 400);
            }
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }  
}
