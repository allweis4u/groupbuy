<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Product;
use App\ProductType;
use App\Order;
use Exception;

class ReportController extends Controller
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
        $endDate = date('Y-m-d', time());
//        $startDate = date('Y-m-d', strtotime("-1 months", strtotime($endDate)));
        $startDate = date("Y-m-01", time());
        // 取得所有訂單
        $orderDatas = $this->getOrderDatas($startDate, $endDate);
        
        return view('admin.reports.index')->with([
            "orderDatas" => $orderDatas,
            "startDate" => $startDate,
            "endDate" => $endDate
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
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
    
    private function getOrderDatas($startDate, $endDate) {
        $nextDate = date('Y-m-d', strtotime($endDate .' +1 day'));
        // 取得所有訂單
        $orderDatas = Order::with(array("productType" => function($query){
            $query->with("product");
        }))->with("member")
            ->with("receiveReports")
            ->where("created_at", ">=", $startDate)
            ->where("created_at", "<", $nextDate)
                ->get();
        return $orderDatas;
    }
    
    /**
     * 搜尋
     * @param Request $request
     */
    public function search(Request $request) {
        $startDate = $request->input("start_date");
        $endDate = $request->input("end_date");
        
        // 取得所有訂單
        $orderDatas = $this->getOrderDatas($startDate, $endDate);

        return view('admin.reports.index')->with([
            "orderDatas" => $orderDatas,
            "startDate" => $startDate,
            "endDate" => $endDate
        ]);
    }
    
}
