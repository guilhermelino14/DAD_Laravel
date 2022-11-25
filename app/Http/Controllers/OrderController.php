<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Pagination\Paginator;
use App\Http\Resources\OrderCollection;
use App\Models\Order_item;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderUpdateResource;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::paginate(10);
        return new OrderCollection($orders);
    }

    public function getOrdersPreparingOrReady()
    {
        $orders = Order::where('status', 'P')->orWhere('status', 'R')->orderBy('created_at', 'asc')->paginate(10);
        return new OrderCollection($orders);
    }

    public function orderUpdate(Request $request){
        $order = Order::find($request->order['id']);
        foreach($order->order_items as $order_item){
            if($order_item->status != 'R'){
                return response()->json(['message' => "Order is not ready"], 400);
            }
        }
        switch ($order->status) {
            case 'P':
                $order->status = 'R';
                break;
            case 'R':
                $order->status = 'D';
                break;
        }
        $order->save();
        return response()->json(['order' => new OrderUpdateResource($order),'message' => "Order status updated"], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // only can be created afther a successful payment
        // order may include more than one product (products of the menu)
        // and a ticket number (cycle 1 to 99)
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $response = Http::post('https://dad-202223-payments-api.vercel.app/api/payments', [
            'type' => Str::lower($request->payment_type),
            'reference' => $request->payment_reference,
            'value' => floatval($request->value),
        ]);

        if($response['status'] != "valid"){
            return response()->json(['message' => "Payment not valid"], 400);
        }

        $last_order = Order::orderBy('created_at', 'desc')->first();
        $ticket_number = $last_order->ticket_number == 99 ? 1 : $last_order->ticket_number + 1;

        $points = intval($request->value / 10);

        $customer = Customer::where('user_id', $request->customer_id)->first();
        $order = Order::create([
            'ticket_number' => $ticket_number,
            'status' => 'P',
            'customer_id' => $customer == null ? 0 : $customer->id,
            'total_price' => $request->value,
            'total_paid' => $request->value,
            'total_paid_with_points' => 0,
            'points_gained' => $points,
            'points_used_to_pay' => 0,
            'payment_type' => $request->payment_type,
            'payment_reference' => $request->payment_reference,
            'date' => now(),
            
        ]);
        $localNumber = 1;
        foreach($request->products as $product){
            Order_item::create([
                'order_id' => $order->id,
                'order_local_number' => $localNumber,
                'product_id' => $product['id'],
                'status' => $product['type'] == "hot dish" ? "W" : "R",
                'price' => $product['price'],
                'preparation_by' => null,
            ]);
            $localNumber++;
        }

        if($customer->id != null){
            $customer->points += $points;
            $customer->save();
        }


        return response()->json(['status' => "Created",'message' => "Payment valid"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::where('user_id', $id)->first();
        $orders = Order::where('customer_id', $customer->id)->orderBy('created_at', 'desc')->paginate(5);
        return new OrderCollection($orders);
    }

    public function showOrderwithId($id){
        $order = Order::find($id);
        return new OrderUpdateResource($order);
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
        //
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
