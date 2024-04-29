<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/orders",
     * summary="Get orders",
     * tags={"Orders"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *
     *    @OA\JsonContent(
     *
     *       @OA\Property(property="status", type="string", example="Success"),
     *       @OA\Property(property="message", type="string", example=""),
     *      ),
     *    )
     * ),
     *)
     */
    public function get(Request $request){

        $orders = $request->user()->orders()->with('items')->orderBy('id','desc')->paginate(50);
        return response()->json(['status' => true,'data' => $orders]);
    }

/**
 * @OA\Post(
 *     path="/api/create_order",
 *     summary="Create new order",
 *     tags={"Orders"},
 *     security={{"bearer_token":{}}},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="items",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="string"),
 *                         @OA\Property(property="quantity", type="string")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             @OA\Examples(example="result", value={"status": true,"message": "Order created successfully.","orderId": "1"}, summary="An result object.")
 *         )
 *     )
 * )
 */

    public function create(Request $request){
        try{
            $user = $request->user();
            
            $items = $request->get('items');
            $total = $this->calculateOrderTotal($items);

            if($user->balance < $total){
                return response()->json(['status' => false,'message' => 'You dont have enough points to place the order.'], 400);
            }
            
            $order = new Order();
            $order->user_id = $user->id;
            $order->total = $total;
            $order->status = 'In-progress';
            $order->date = \Carbon\Carbon::now();
            $order->save();

            foreach ($items as $key => $item) {
                $product = Product::find($item['id']);
                if($product){
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $product->id;
                    $orderItem->price = $product->price;
                    $orderItem->quantity = $item['quantity'];
                    $orderItem->save();
                }
            }

            return response()->json(['status' => true,'message' => 'Order created successfully.','orderId' => $order->id], 201);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json(['status' => false,'message' => $th->getMessage()], 500);
        }
    }

/**
 * @OA\Post(
 *     path="/api/cancel_order",
 *     summary="Cancel order",
 *     tags={"Orders"},
 *     security={{"bearer_token":{}}},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="id",
 *                     type="integer"
 *                 ),
 *                 example={"id":"1"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             @OA\Examples(example="result", value={"status": true,"message": "Order cancelled successfully."}, summary="An result object.")
 *         )
 *     )
 * )
 */
    public function cancelOrder(Request $request){
        try{
            $order = Order::find($request->id);

            if($order->user_id != $request->user()->id){
                return response()->json(['status' => false,'message' => 'Invalid access.'], 401);
            }

            if($order->status !='Cancelled'){
                $order->status = 'Cancelled';
                $order->save();
                return response()->json(['status' => true,'message' => 'Order cancelled successfully.']);
            }else{
                return response()->json(['status' => false,'message' => 'This order has been cancelled already.'], 400);
            }
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json(['status' => false,'message' => $th->getMessage()], 500);
        }
    }


    public function calculateOrderTotal(array $data){
        $total = 0;
        foreach ($data as $key => $item) {
            $product = Product::find($item['id']);
            if($product){
                $total += ($product->price * $item['quantity']);
            }
        }
        return $total;
    }
}
