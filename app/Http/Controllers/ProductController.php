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

class ProductController extends Controller
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
        $productDatas = Product::with(array("productTypes" => function($query) {
            $query->with('orders')->select("id", "products_id", "name", "price")
                ->with("stockReports");
        }))->get();
        
        // 統計資料
        $stats = array();
        
        // 算出統計數量
        foreach ($productDatas as $product) {
            $productsId = $product->id;
            $stats[$productsId] = array();
            foreach ($product->productTypes as $productType) {
                $productTypesId = $productType->id;
                $stats[$productsId][$productTypesId] = array();
                $stats[$productsId][$productTypesId]["name"] = $productType->name;
                $stats[$productsId][$productTypesId]["receive"] = Order::
                        where("product_types_id", "=", $productTypesId)
                        ->where("status", "=", 1)
                        ->sum("quantity");
                $stats[$productsId][$productTypesId]["order"] = Order::
                        where("product_types_id", "=", $productTypesId)
                        ->where("status", "=", 0)
                        ->sum("quantity");
                $stats[$productsId][$productTypesId]["stock"] = StockReport::
                        where("product_types_id", "=", $productTypesId)
                        ->sum("quantity");

            }
        }
        
        return view('admin.products.index')->with([
            'productDatas' => $productDatas,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // use transaction to save data
        DB::transaction(function() use ($request) {
            $name = $request->input('name');
            $types = $request->input('types');
            $prices = $request->input('prices');
            
            // 檢查是否有值
            $name = trim($name);
            if ($name == "") {
                throw new Exception("請輸入品名");
            }
            if (count($types) == 0) {
                throw new Exception("請輸入種類");
            }
            if (count($prices) == 0) {
                throw new Exception("請輸入價錢");
            }
            
            // 檢查種類資料並存入陣列
            $arr = array();
            $typePattern = "/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u"; // 中英文pattern
            $pricePattern = "/^[0-9]+$/"; // 數字pattern
            for ($i = 0; $i < count($types); $i ++) {
                $type = $types[$i];
                $price = $prices[$i];
                if (!preg_match($typePattern, $type, $matches)) {
                    throw new Exception("種類需為中英文");
                }
                
                if (!preg_match($pricePattern, $price, $matches)) {
                    throw new Exception("價錢需為數字");
                }
                $price = (int)$price;
                
                array_push($arr, array($type, $price));
            }
            
            // 儲存產品
            $product = new Product();
            $product->name = $name;
            $product->users_id = Auth::id();
            $product->save();

            // 儲存產品類別
            for ($i = 0; $i < count($arr); $i ++) {
                $productType = new ProductType();
                $productType->products_id = $product->id;
                $productType->users_id = Auth::id();
                $productType->name = $arr[$i][0];
                $productType->price = $arr[$i][1];
                $productType->save();
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
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $productData = Product::with(array("productTypes" => function($query) {
            
        }))->where("id", "=", $id)->first();
        
        return view('admin.products.edit')->with(['productData' => $productData]);
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
        
        // use transaction to save data
        DB::transaction(function() use ($request, $id) {
            $name = $request->input('name');
            $productTypesId = $request->input('product_types_id');
            $types = $request->input('types');
            $prices = $request->input('prices');
            
            // 檢查是否有值
            $name = trim($name);
            if ($name == "") {
                throw new Exception("請輸入品名");
            }
            if (count($types) == 0) {
                throw new Exception("請輸入種類");
            }
            if (count($prices) == 0) {
                throw new Exception("請輸入價錢");
            }
            
            // 檢查種類資料並存入陣列
            $arr = array();
            $typePattern = "/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u"; // 中英文pattern
            $pricePattern = "/^[0-9]+$/"; // 數字pattern
            for ($i = 0; $i < count($types); $i ++) {
                $type = $types[$i];
                $price = $prices[$i];
                if (!preg_match($typePattern, $type, $matches)) {
                    throw new Exception("種類需為中英文");
                }
                
                if (!preg_match($pricePattern, $price, $matches)) {
                    throw new Exception("價錢需為數字");
                }
                $price = (int)$price;
                
                array_push($arr, array($type, $price));
            }
            
            // 儲存產品
            Product::where("id", "=", $id)->update([
                "name" => $name,
                "users_id" => Auth::id()
            ]);

            // 儲存產品類別
            for ($i = 0; $i < count($arr); $i ++) {
                
                if (isset($productTypesId[$i])) {
                    $productTypeId = $productTypesId[$i];
                    // 檢查ProductType是否存在Product內
                    
                    $productData = ProductType::where("id", "=", $productTypeId)
                            ->where("products_id", "=", $id)->first();
                    if (count($productData) == 0) {
                        throw new Exception("此產品編號不在本產品內");
                    }
                    
                    ProductType::where("id", "=", $productTypeId)
                            ->update([
                                'name' =>$arr[$i][0],
                                'price' => $arr[$i][1],
                                "users_id" => Auth::id()
                            ]);
                } else {
                    $productType = new ProductType();
                    $productType->products_id = $id;
                    $productType->users_id = Auth::id();
                    $productType->name = $arr[$i][0];
                    $productType->price = $arr[$i][1];
                    $productType->save();
                }
                
            }
        });
        
        return redirect('products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::where("id", "=", $id)->delete();
        return redirect('products');
    }
}
