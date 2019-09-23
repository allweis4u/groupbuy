<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Product;
use App\ProductType;
use App\Order;
use App\StockReport;
use Exception;

class StockReportController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth:api');
        $this->middleware('auth', []);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($productId)
    {
        $productData = Product::with(array("productTypes" => function($query) {
            $query->with(array("orders" => function($query2){
                $query2->with("member");
            }))->with("stockReports");
        }))->where("id", "=", $productId)->first();

        return view('admin.stockReports.create')->with([
            "product" => $productData
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
        // 檢查與儲存
        DB::transaction(function() use ($request) {
            $productTypesId = $request->input("product_types_id");
            $total = $request->input("total");

            $productTypeData = productType::where("id", "=", $productTypesId)->first();
            if (count($productTypeData) == 0) {
                throw new Exception("沒有此類別");
            }

            // 檢查數量
            $pricePattern = "/^[0-9]+$/"; // 數字pattern
            if (!preg_match($pricePattern, $total, $matches)) {
                throw new Exception("總數量需為數字");
            }

            if ($total < 1) {
                throw new Exception("總數量需大於0");
            }

            $stockReport = StockReport::where("product_types_id", "=", $productTypesId)->first();
            // 沒有資料就新增，否則更新
            if (count($stockReport) == 0) {
                // 新增進貨數量
                $stockReport = new StockReport();
                $stockReport->product_types_id = $productTypeData->id;
                $stockReport->users_id = Auth::id();
                $stockReport->quantity = $total;
                $stockReport->save();
            } else {
                // 更新進貨數量
                $quantity = $stockReport->quantity + $total;
                StockReport::
                        where("product_types_id", "=", $productTypeData->id)
                        ->update([
                    'users_id' => Auth::id(),
                    'quantity' => $quantity
                ]);
            }
            
        });
        
        return redirect('products');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
