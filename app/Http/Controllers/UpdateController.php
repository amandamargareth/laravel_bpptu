<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\View\View;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        return Order::select('id','name', 'address', 'city', 'phone', 'variety', 'quantity', 'to', 'created_at', 'status')
        ->where('status', 'Belum Selesai')
        ->get();
    }

    public function page(): View
    {
        return view('order.index', [
            'updates' => DB::table('orders')->paginate(15)
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
             Update::create($request->post());

            return response()->json([
                'message'=>'Update Created Successfully!!'
            ]);
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while creating a update!'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Update  $update
     * @return \Illuminate\Http\Response
     */
    public function show(Update $update)
    {
        return response()->json([
            'update'=>$update
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Update  $update
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Update $update)
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

            $update->fill($request->post())->update('status', 'Selesai');

            return response()->json([
                'message'=>'Update Updated Successfully!!'
            ]);

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while updating a update!!'
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Update  $update
     * @return \Illuminate\Http\Response
     */
    public function destroy(Update $update)
    {
        try {

            if($update->image){
                $exists = Storage::disk('public')->exists("update/image/{$update->image}");
                if($exists){
                    Storage::disk('public')->delete("update/image/{$update->image}");
                }
            }

            $update->delete();

            return response()->json([
                'message'=>'Update Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a update!!'
            ]);
        }
    }
}