<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        switch ($this->status) {
            case 'P':
                $this->status = 'Preparing';
                break;
            case 'R':
                $this->status = 'Ready';
                break;
            case 'D':
                $this->status = 'Delivered';
                break;
            case 'C':
                $this->status = 'Cancelled';
                break;
        }
        foreach ($this->order_items as $order_item) {
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
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'status' => $this->status,
            'customer_id' => $this->customer_id,
            'total_price' => $this->total_price,
            'total_paid' => $this->total_paid,
            'total_paid_with_points' => $this->total_paid_with_points,
            'points_gained' => $this->points_gained,
            'points_used_to_pay' => $this->points_used_to_pay,
            'payment_type' => $this->payment_type,
            'payment_reference' => $this->payment_reference,
            'date' => $this->date,
            'custom' => $this->custom,
            'order_items' => $this->order_items
        ];
    }
}
