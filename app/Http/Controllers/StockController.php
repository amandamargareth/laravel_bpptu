<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        return Stock::select('id','stock_name', 'stock_quantity')->get();
    }

    public function page(): View
    {
        return view('stock.index', [
            'stocks' => DB::table('stocks')->paginate(15)
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
            'stock_name'=>'required',
            'stock_quantity'=>'required'
        ]);

        try{
             Stock::create($request->post());

            return response()->json([
                'message'=>'Stock Created Successfully!!'
            ]);
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while creating a stock!'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        return response()->json([
            'stock'=>$stock
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'stock_name'=>'required',
            'stock_quantity'=>'required'
            
        ]);

        try{

            $stock->fill($request->post())->update();

            return response()->json([
                'message'=>'Stock Updated Successfully!!'
            ]);

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while updating a stock!!'
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        try {

            if($stock->image){
                $exists = Storage::disk('public')->exists("stock/image/{$stock->image}");
                if($exists){
                    Storage::disk('public')->delete("stock/image/{$stock->image}");
                }
            }

            $stock->delete();

            return response()->json([
                'message'=>'Stock Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a stock!!'
            ]);
        }
    }
}