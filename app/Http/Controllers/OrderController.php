<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Stock;
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
            'quantity'=>'required|integer|min:0',
            'to'=>'required'
        ]);

        try{
             // Tentukan stock_id berdasarkan variety yang dipilih
        $variety = $request->input('variety');
        $stockId = 1; // Default stock_id jika tidak ditemukan
        $quantity = $request->input('quantity');

        // Tambahkan logika untuk menentukan stock_id berdasarkan variety
        if ($variety === 'DOD Itik Rambon') {
            $stockId = 2;
        } // Tambahkan pernyataan lain untuk variety lainnya jika diperlukan

        $stock = Stock::where('stock_name', $variety)->first();

        // Buat array data pesanan dengan tambahan stock_id
        if ($stock) {
            // Jika stok ditemukan
            if ($stock->stock_quantity >= $quantity) {
                // Jika jumlah pesanan kurang dari atau sama dengan stok yang tersedia
                $stockId = $stock->id;

                $orderData = $request->all();
                $orderData['stock_id'] = $stockId;
                $orderData['status'] = 'Belum Selesai';

                Order::create($orderData);

                // Kurangi stok yang tersedia
                $stock->stock_quantity -= $quantity;
                $stock->save();

                return response()->json([
                    'message' => 'Pesanan berhasil disimpan!!'
                ]);
            } else {
                // Jika pesanan melebihi stok yang tersedia
                return response()->json([
                    'message' => 'Maaf stock tidak mencukupi.'
                ], 400);
            }
        } else {
            // Jika stok tidak ditemukan
            return response()->json([
                'message' => 'Stock for this variety is not available.'
            ], 404);
        }
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response()->json([
            'message' => 'Something goes wrong while creating an order!'
        ], 500);
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

            $variety = $request->input('variety');
        $stockId = 1; // Default stock_id jika tidak ditemukan

        // Tambahkan logika untuk menentukan stock_id berdasarkan variety
        if ($variety === 'DOD Itik Rambon') {
            $stockId = 2;
        } // Tambahkan pernyataan lain untuk variety lainnya jika diperlukan

        // Isi model pesanan dengan data yang diterima dari permintaan
        $order->fill($request->all());

        // Setel stock_id ke nilai yang dihitung
        $order->stock_id = $stockId;

        $order->save();

        return response()->json([
            'message' => 'Data pesanan berhasil dirubah!!'
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

public function taskCount()
{
    $notCompletedTasks = Order::where('status', 'Belum Selesai')->count();
    $completedTasks = Order::where('status', 'Selesai')->count();

    return response()->json([
        'notCompleted' => $notCompletedTasks,
        'completed' => $completedTasks,
    ]);
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