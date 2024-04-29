<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\CreateWalletRequest;
use Illuminate\Http\JsonResponse;
class WalletController extends Controller
{

    /**
     * transfer
     * @param array| $request
     * return json
     */
    // public function addMoney(CreateWalletRequest $request): JsonResponse{
    //     try {
    //         $request->user()->ledgers()->create(['debit'=> $request->amount]);
    //         return response()->json(['message'=>'Amount added.'], 201);
    //     } catch (\Throwable $th) {
    //         \Log::error($th->getMessage());
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage()
    //         ], 500);
    //     }
    // }
}
