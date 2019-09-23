<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Product;
use App\ProductType;
use App\Order;
use App\Member;
use App\StockReport;
use App\ReceiveReport;
use Exception;

class OrderController extends Controller
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
    public function index($productId)
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
            $query->select("id", "products_id", "name", "price");
        }))->where("id", $productId)->first();
        if (count($productData) == 0) {
            throw new Exception("無此產品編號");
        }
        $memberDatas = Member::select("id", "name")->get();
        
        return view('admin.orders.create')->with([
            'productData' => $productData,
            'memberDatas' => $memberDatas
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
            $productsId = $request->input('products_id');
            $memberNames = $request->input('member_names');
            $productTypesIds = $request->input('product_types_ids');
            $quantities = $request->input('quantities');
            $memos = $request->input('memos');
            
            // 檢查產品是否存在
            $product = Product::where("id", "=", $productsId)->first();
            if (count($product) == 0) {
                throw new Exception("無此產品編號");
            }
            
            // 檢查是否有值
            if (count($memberNames) == 0) {
                throw new Exception("請輸入姓名");
            }
            if (count($quantities) == 0) {
                throw new Exception("請輸入數量");
            }
            
            // 檢查輸入數值並存入陣列
            $arr = array();
            $numPattern = "/^[0-9]+$/"; // 數字pattern
            for($i = 0; $i < count($memberNames); $i ++) {
                $memberName = $memberNames[$i];
                $productTypesId = $productTypesIds[$i];
                $quantity = $quantities[$i];
                $memo = $memos[$i];
                
                $productType = ProductType::where("products_id", "=", $productsId)->where("id", "=", $productTypesId)->first();
                if (count($productType) == 0) {
                    throw new Exception("沒有此商品類別");
                }
                
                $memberName = trim($memberName); // 去除前後空白
                if ($memberName == "") {
                    throw new Exception("請輸入姓名");
                }
                if (!preg_match($numPattern, $quantity, $matches)) {
                    throw new Exception("數量需為數字");
                }
                
                // 轉integer
                $quantity = (int)$quantity;
                if ($quantity < 1) {
                    throw new Exception("數量需大於0");
                }
                
                array_push($arr, array($memberName, $productTypesId, $quantity, $memo, $productType->price));
            }
            
            // 新增訂單
            for ($i = 0; $i < count($arr); $i ++) {
                // 先檢查成員是否存在與找出member id
                $memberName = $arr[$i][0];
                $member = Member::where("name", "=", $memberName)->first();
                $memberId = 0;
                
                if (count($member) > 0) {
                    $memberId = $member->id;
                } else {
                    $memberObj = new Member();
                    $memberObj->name = $memberName;
                    $memberObj->save();
                    $memberId = $memberObj->id;
                }
                
                // 新增訂單資料
                $order = new Order();
                $order->product_types_id = $arr[$i][1];
                $order->users_id = Auth::id();
                $order->members_id = $memberId;
                $order->status = 0;
                $order->quantity = $arr[$i][2];
                $order->price = $arr[$i][4];
                $order->memo = $arr[$i][3];
                $order->save();
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
    public function show($productId)
    {
        $productData = Product::with(array('productTypes' => function($query) {
            $query->with("orders")
                    ->with("stockReports");
        }))->where("id", "=", $productId)->first();
        if (count($productData) == 0) {
            throw new Exception("無此產品編號");
        }
        
        return view('admin.orders.show')->with([
            'productData' => $productData
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        
        // 為了取得產品編號
        $orderObj = Order::with(array("productType" => function($query) {
                $query->with("product");
            }))->where("id", "=", $id)->first();

        // 檢查與儲存
        DB::transaction(function() use ($request, $id) {
            $productTypesId = $request->input('product_types_id');
            $quantity = $request->input('quantity');
            $memo = $request->input('memo');
            
            // 檢查類別是否存在
            $productType = ProductType::where("id", "=", $productTypesId)->first();
            if (count($productType) == 0) {
                throw new Exception("無此類別");
            }
            
            // 檢查輸入數值
            $numPattern = "/^[0-9]+$/"; // 數字pattern
            if (!preg_match($numPattern, $quantity, $matches)) {
                throw new Exception("數量需為數字");
            }

            // 轉integer
            $quantity = (int)$quantity;
            if ($quantity < 1) {
                throw new Exception("數量需大於0");
            }

            // 更新訂單資料
            $order = new Order();
            Order::where("id", "=", $id)
                ->update([
                    'product_types_id' => $productTypesId,
                    'quantity' => $quantity,
                    "users_id" => Auth::id(),
                    "price" => $productType->price,
                    "memo" => $memo
                ]);
            
            // 如果有領取紀錄就更新數量
            ReceiveReport::where("orders_id", "=", $id)
                ->update([
                    'quantity' => $quantity,
                    "users_id" => Auth::id()
                ]);
        });
        
        return redirect('orders/' . $orderObj->productType->product->id);
    }
    
    /**
     * 更新單筆領取狀態
     * @param Request $request
     * @param type $id
     */
    public function updateReceive(Request $request, $id)
    {
        // 為了取得產品編號
        $orderObj = Order::with(array("productType" => function($query) {
                $query->with("product");
            }))->where("id", "=", $id)->first();
        DB::transaction(function() use ($request, $orderObj, $id) {
                
            $stockReport = StockReport::where("product_types_id", "=", $orderObj->productType->id)->first();
            if (count($stockReport) == 0) {
                throw new Exception("沒有進貨");
            }
            
            // 新增領取紀錄
            $receiveReport = new ReceiveReport();
            $receiveReport->orders_id = $id;
            $receiveReport->stock_reports_id = $stockReport->id;
            $receiveReport->users_id = Auth::id();
            $receiveReport->quantity = $orderObj->quantity;
            $receiveReport->save();
                
            // 更新狀態
            Order::where("id", "=", $id)
                ->update([
                    'status' => 1,
                    'stock_reports_id' => $stockReport->id,
                    "users_id" => Auth::id()
                ]);
        });
        
        return redirect('orders/' . $orderObj->productType->product->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 為了取得產品編號
        $orderObj = Order::with(array("productType" => function($query) {
                $query->with("product");
            }))->where("id", "=", $id)->first();
        
        // 刪除資料
        $affectedRows = Order::where('id', '=', $id)
                ->delete();
        
        return redirect('orders/'. $orderObj->productType->product->id);
    }
    
    /**
     * 關鍵字搜尋成員
     * @param Request $request
     * @return type
     */
    public function keyword(Request $request) {
        $keyName = $request->input('key_name');
        $action = $request->input('action');

        $orderDatas = Order::with(array("productType" => function($query){
            $query->with("product");
        }))->with("member")
                ->join("members", "orders.members_id", "=", "members.id")
                ->where("members.name", "like", "%" . $keyName . "%")
                ->select("orders.*")
                ->get();
        
        $stats = $this->getStats($orderDatas);
        
        return view('admin.orders.keyword')->with([
            'action' => 'keyword',
            'keyName' => $keyName,
            'orderDatas' => $orderDatas,
            "stats" => $stats
        ]);
    }
    
    /**
     * 關鍵字搜尋成員
     * @param Request $request
     * @return type
     */
    public function exactSearch(Request $request) {
        $keyName = $request->input('key_name');

        $orderDatas = Order::with(array("productType" => function($query){
            $query->with("product");
        }))->with("member")
                ->join("members", "orders.members_id", "=", "members.id")
                ->where("members.name", "=", $keyName)
                ->select("orders.*")
                ->get();
        
        $stats = $this->getStats($orderDatas);
        
        return view('admin.orders.keyword')->with([
            'action' => 'exactSearch',
            'keyName' => $keyName,
            'orderDatas' => $orderDatas,
            "stats" => $stats
        ]);
    }
    
    /**
     * 從搜尋結果刪除
     * @param Request $request
     * @return type
     */
    public function deleteSearch(Request $request) {
        $ordersId = $request->input('orders_id');
        $action = $request->input('action');
        
        Order::where("id", "=", $ordersId)->delete();
        
        if ($action == "exactSearch") {
            return $this->exactSearch($request);
        } else {
            return $this->keyword($request);
        }
    }
    
    /**
     * 多筆領取
     * @param Request $request
     * @return type
     */
    public function multipleReceive(Request $request) {
        
        DB::transaction(function() use ($request) {
            $ordersIds = $request->input('orders_ids');
            
            for ($i = 0; $i < count($ordersIds); $i ++) {
                $ordersId = $ordersIds[$i];
                $orderData = Order::where("id", "=", $ordersId)->first();

                // 確認訂單存在
                if (count($orderData) == 0) {
                    continue;
                }

                // 確認領取狀態
                if ($orderData->status == 1) {
                    continue;
                }
                
                // 確認進貨
                $stockReport = StockReport::where("product_types_id", "=", $orderData->product_types_id)->first();
                if (count($stockReport) == 0) {
                    throw new Exception("沒有進貨資料");
                }
                
                // 領取總數量
                $receiveNum = ReceiveReport::where("orders_id", "=", $orderData->id)->sum('quantity');
                $stockNum = StockReport::where("product_types_id", "=", $orderData->product_types_id)->sum('quantity');

                if ($stockNum >= $receiveNum + $orderData->quantity) {
                    // 新增領取紀錄
                    $receiveReport = new ReceiveReport();
                    $receiveReport->orders_id = $ordersId;
                    $receiveReport->stock_reports_id = $stockReport->id;
                    $receiveReport->users_id = Auth::id();
                    $receiveReport->quantity = $orderData->quantity;
                    $receiveReport->save();

                    // 更新狀態
                    Order::where("id", "=", $ordersId)
                        ->update([
                            'status' => 1,
                            "users_id" => Auth::id()
                        ]);
                }
            }
        });
        
        $action = $request->input('action');
        if ($action == "exactSearch") {
            return $this->exactSearch($request);
        } else {
            return $this->keyword($request);
        }
    }
    
    /**
     * 取得統計資料
     * @param type $orderDatas
     */
    private function getStats($orderDatas) {
        $stats = array();
        // 算出數量
        foreach ($orderDatas as $orderData) {
            $productTypesId = $orderData->product_types_id;
            if (!isset( $stats[$productTypesId] )) {
                // 算出領取數量
                $stats[$productTypesId] = array();
                $stats[$productTypesId]["receive"] = Order::
                        where("product_types_id", "=", $productTypesId)
                        ->where("status", "=", 1)
                        ->sum("quantity");
                
                // 找出未完成訂單數量
                $stats[$productTypesId]["order"] = Order::
                        where("product_types_id", "=", $productTypesId)
                        ->where("status", "=", 0)
                        ->sum("quantity");
                
                // 找出庫存
                $stats[$productTypesId]["stock"] = StockReport::
                        where("product_types_id", "=", $productTypesId)
                        ->sum("quantity");
            }
        }
//        echo "<pre>"; print_r($stats); echo "</pre>";
        
        return $stats;
    }
    
}

