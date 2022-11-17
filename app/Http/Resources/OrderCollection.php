<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        foreach ($this->collection as $order) {
            $order->order_items = $order->order_items;
            switch ($order->status) {
                case 'P':
                    $order->status = 'Preparing';
                    break;
                case 'R':
                    $order->status = 'Ready';
                    break;
                case 'D':
                    $order->status = 'Delivered';
                    break;
                case 'C':
                    $order->status = 'Cancelled';
                    break;
            }
            foreach ($order->order_items as $order_item) {
                $order_item->product = $order_item->products;
                switch ($order_item->status) {
                    case 'W':
                        $order_item->status = 'Waiting';
                        break;
                    case 'P':
                        $order_item->status = 'Preparing';
                        break;
                    case 'R':
                        $order_item->status = 'Ready';
                        break;
                }
            }
        }
        
        return [
            'data' => $this->collection,
            'links' => [
                'self' => 'link-value',
            ],
            // 'id' => $this->id,
            // 'ticket_number' => $this->ticket_number,
            // 'status' => $this->status,
            // 'customer_id' => $this->customer_id,
            // 'total_price' => $this->total_price,
            // 'total_paid' => $this->total_paid,
            // 'total_paid_with_points' => $this->total_paid_with_points,
            // 'points_gained' => $this->points_gained,
            // 'points_user_to_pay' => $this->points_user_to_pay,
            // 'payment_type' => $this->payment_type,
            // 'payment_reference' => $this->payment_reference,
            // 'date' => $this->date,
            // 'delivery_by' => $this->delivery_by,
            // 'custom' => $this->custom,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
            
        ];
    }
}
