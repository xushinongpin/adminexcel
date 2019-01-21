<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Order;
use App\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->strseparator = '--';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //select product
        $product = new Product();
        $productData = $product->canSales();
        if($request->ajax()){

            //select customer
            $customer = new Customer();
            $customerData = $customer->index($request);

            //select order
            $order = new Order();
            $orderData = $order->index($request);
//            dump($orderData);
//            array_column($orderData,'cid');
//            dd($orderData);

            //Consolidated report
            foreach ($customerData as $cv){
                $orderTableArr[$cv['id']] = $this->consolidated($cv,$productData,$orderData);
            }
            $returnData = array();
            $returnData['code'] = 0;
            $returnData['count'] = count($orderTableArr);
            $returnData['msg'] = '正在进行分页查询';
            $returnData['data'] = $orderTableArr;
            unset($orderTableArr);
            return $returnData;
        }

        //table title
        $productTitle = "[[{type:'checkbox',fixed:'left'},{field:'uid', width:60, title: 'ID'},";
        foreach ($productData as $pv){
            $productTitle .= "{field:'requirement".$this->strseparator.$pv['id']."', title:'".$pv['name']."量', width:120, edit: 'text'},";
            $productTitle .= "{field:'price".$this->strseparator.$pv['id']."', title:'单价', width:60, edit: 'text'},";
        }
        $productTitle .= "{field:'totalmoney', title:'总价', width:120},{field:'username', title:'购买用户', width:100, fixed: 'right'}]]";
        return view('order/index',['product'=>$productTitle]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->data,true);
        foreach ($data as $dv){
            dump($dv);
        }
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

    private function consolidated($carr,$parr,$oarr){
        foreach ($parr as $pv){
            $data['username'] = $carr['name'];
            $data['uid'] = $carr['id'];
            $data[$pv['id']] = $pv['id'];
            $data['price'.$this->strseparator.$pv['id']] = 0;
            $data['requirement'.$this->strseparator.$pv['id']] = 0;
        }
        return $data;
    }
}
