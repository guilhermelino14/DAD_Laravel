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


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Order::get()->first();
    }

    public function getOrdersPreparingOrReady()
    {
        $orders = Order::where('status', 'P')->orWhere('status', 'R')->orderBy('created_at', 'asc')->paginate(10);
        return new OrderCollection($orders);
    }

    public function orderUpdate(Request $request){
        $order = Order::find($request->order['id']);
        switch ($order->status) {
            case 'P':
                $order->status = 'R';
                break;
            case 'R':
                $order->status = 'D';
                break;
        }
        $order->save();
        return response()->json(['order' => $order,'message' => "Order status updated"], 200);
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

        $order = Order::create([
            'ticket_number' => $ticket_number,
            'status' => 'P',
            'customer_id' => $request->customer_id == 0 ? null : $request->customer_id,
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

        if($request->customer_id != 0){
            $customer = Customer::find($request->customer_id);
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
        $orders = Order::where('customer_id', $id)->orderBy('created_at', 'desc')->paginate(5);
        return new OrderCollection($orders);
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
