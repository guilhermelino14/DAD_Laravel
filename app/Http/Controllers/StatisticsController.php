<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Http\Resources\OrderCollection;

class StatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        if($customer == null){
            return response()->json(['message' => "Customer not found"], 400);
        }
        $orders = Order::where('customer_id', $customer->id)->orderBy('created_at', 'desc')->get();
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

    public function totalEarn(){
        $totalEarn = Order::where('status', 'D')->sum('total_price');
        return response()->json(['totalEarn' => $totalEarn], 200);
    }

    public function totalEarnLast3Months(){
        $totalEarn = Order::where('status', 'D')->where('created_at', '>=', now()->subMonths(3))->sum('total_price');
        // create a array with the wins of the last 3 months
        $totalEarnLast3Months = [];
        for ($i = 0; $i < 12; $i++) {
            $totalEarnLast3Months[] = Order::where('status', 'D')->whereMonth('created_at', now()->subMonths($i)->month)->sum('total_price');
        }
        return response()->json(['totalEarn' => $totalEarn, 'earnsImMounts' => $totalEarnLast3Months], 200);
    }
}
