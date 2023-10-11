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
        ->get();
    }

    public function page(): View
    {
        return view('status.index', [
            'statuss' => DB::table('orders')->paginate(15)
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
                'message'=>'Something goes wrong while creating a status!'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $status
     * @return \Illuminate\Http\Response
     */
    public function show(Status $status)
    {
        return response()->json([
            'status'=>$status
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $status
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Status $status)
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

            $status->fill($request->post())->update();

            return response()->json([
                'message'=>'Status Updated Successfully!!'
            ]);

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while updating a status!!'
            ],500);
        }
    }

    // public function changeStatus(Request $request, Status $status){
    //     $status->status = 'selesai';
    //     $status->save();

    //     return response()->json(['message' => 'Status changed to "selesai"']);
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Http\Response
     */
    public function destroy(Status $status)
    {
        try {

            if($status->image){
                $exists = Storage::disk('public')->exists("status/image/{$status->image}");
                if($exists){
                    Storage::disk('public')->delete("status/image/{$status->image}");
                }
            }

            $status->delete();

            return response()->json([
                'message'=>'Status Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a status!!'
            ]);
        }
    }

    
}