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
            'actions' => DB::table('orders')->paginate(15)
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
             Action::create($request->post());

            return response()->json([
                'message'=>'Action Created Successfully!!'
            ]);
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while creating a action!'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function show(Action $action)
    {
        return response()->json([
            'action'=>$action
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Action $action)
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

            $action->fill($request->post())->update();

            return response()->json([
                'message'=>'Action Updated Successfully!!'
            ]);

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while updating a action!!'
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function destroy(Action $action)
    {
        try {

            if($action->image){
                $exists = Storage::disk('public')->exists("action/image/{$action->image}");
                if($exists){
                    Storage::disk('public')->delete("action/image/{$action->image}");
                }
            }

            $action->delete();

            return response()->json([
                'message'=>'Action Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a action!!'
            ]);
        }
    }
}