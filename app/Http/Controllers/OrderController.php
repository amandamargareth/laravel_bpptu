<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        return Order::select('id','name', 'address', 'city', 'phone', 'variety', 'quantity', 'to', 'status', 'created_at')
        ->orderBy('status', 'asc')
        ->get();
    }

    public function page(): View
    {
        return view('order.index', [
            'orders' => DB::table('orders')->paginate(15)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'address'=>'required',
            'city'=>'required',
            'phone'=>'required',
            'variety'=>'required',
            'quantity'=>'required',
            'to'=>'required'
        ]);

        try{
             Order::create($request->post());

            return response()->json([
                'message'=>'Order Created Successfully!!'
            ]);
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while creating a order!'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return response()->json([
            'order'=>$order
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'name'=>'required',
            'address'=>'required',
            'city'=>'required',
            'phone'=>'required',
            'variety'=>'required',
            'quantity'=>'required',
            'to'=>'required',
            'status'=>'required'
        ]);

        try{

            $order->fill($request->post())->update();

            return response()->json([
                'message'=>'Order Updated Successfully!!'
            ]);

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while updating a order!!'
            ],500);
        }
    }

    public function waitingList()
{
    return Order::select('id', 'name', 'address', 'city', 'phone', 'variety', 'quantity', 'to', 'created_at', 'status')
        ->where('status', 'Belum Selesai')
        ->get();
}


    public function updateStatus($id)
{
    // Temukan entitas berdasarkan $id
    $order = Order::find($id);

    if (!$order) {
        return response()->json(['message' => 'Order tidak ditemukan'], 404);
    }

    // Ubah kolom status menjadi "Selesai"
    $order->status = 'Selesai';
    $order->save();

    return response()->json(['message' => 'Status berhasil diperbarui']);
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        try {

            if($order->image){
                $exists = Storage::disk('public')->exists("order/image/{$order->image}");
                if($exists){
                    Storage::disk('public')->delete("order/image/{$order->image}");
                }
            }

            $order->delete();

            return response()->json([
                'message'=>'Order Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a order!!'
            ]);
        }
    }

    
}