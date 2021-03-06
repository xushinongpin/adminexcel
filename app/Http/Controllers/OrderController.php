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
        $this->moneyconversion = config('app.moneyconversion');
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
            $request->limit = 1000000;
            //select customer
            $customer = new Customer();
            $customerData = $customer->index($request);

            //select order
            $order = new Order();
            $orderData = $order->index($request);

            //Consolidated report
            $orderTableArr = array();
            foreach ($customerData as $cv){
                $orderTableArr[$cv['id']] = $this->consolidated($cv,$productData,$orderData);
            }
            $returnData = $this->onlyAjaxData($orderTableArr);
//            $returnData['code'] = 0;
//            $returnData['count'] = count($orderTableArr);
//            $returnData['msg'] = '正在进行分页查询';
//            $returnData['data'] = $orderTableArr;
//            unset($orderTableArr);
            return $returnData;
        }

        //table title
        $productTitle = "[[{type:'checkbox',fixed:'left'},{field:'cid', width:60, title: 'ID', totalRowText: '合计'},";
        $titleData = array();
        foreach ($productData as $pv){
            $productTitle .= "{field:'requirement".$this->strseparator.$pv['id']."', title:'".$pv['name']."量', width:120, edit: 'text', totalRow: true},";
            $productTitle .= "{field:'price".$this->strseparator.$pv['id']."', title:'单价', width:60, edit: 'text'},";
            //点击出现的导航需要
            $titleData['requirement'.$this->strseparator.$pv['id']] = $pv['name'];
        }
        $productTitle .= "{field:'totalmoney', title:'总价', width:120, totalRow: true},{field:'username', title:'购买用户', width:100, fixed: 'right'}]]";
        unset($productData);
        return view('order/index',['product'=>$productTitle,'strseparator'=>$this->strseparator,'titledata'=>json_encode($titleData)]);
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
        $order = new Order();
        $data = json_decode($request->data,true);
        foreach ($data as $dv){
            $ndata = $this->searchexprrd($dv);
            $order->insertorupdate($ndata,$dv['cid'],$request->time,$this->moneyconversion);
        }
        $succ['status'] = 1;
        $succ['msg'] = '即将刷新页面';
        return $succ;
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

    //结账单
    public function bill(Request $request){
        $order = new Order();
        $data = $order->joincustomerproduct($request,$this->moneyconversion);
        return view('order/bill',['datas'=>$data]);

    }

    //送货单
    public function delivery(Request $request){
        $order = new Order();
        $data = $order->joincustomerproduct($request,$this->moneyconversion);
        return view('order/delivery',['datas'=>$data]);
    }

    //三表转换
    private function consolidated($carr,$parr,$oarr){
        $order = new Order();
        $oarr = $order->treeorder($oarr,$this->moneyconversion);
        $data['totalmoney'] = 0;
        foreach ($parr as $pv){
            $price = 0;
            $data['username'] = $carr['name'];
            $data['cid'] = $carr['id'];
            $data['LAY_CHECKED'] = true;
//            $data[$pv['id']] = $pv['id'];
            if(isset($oarr[$carr['id']][$pv['id']]['price'])){
                $data['totalmoney'] += ($oarr[$carr['id']][$pv['id']]['price'] * $oarr[$carr['id']][$pv['id']]['requirement']);
            }
            if(!isset($oarr[$carr['id']][$pv['id']]['price'])){
                $price = $order->userproductprice($carr['id'],$pv['id'],$this->moneyconversion,$parr);
            }else{
                $price = $oarr[$carr['id']][$pv['id']]['price'];
            }
            $data['price'.$this->strseparator.$pv['id']] = $price;
            $data['requirement'.$this->strseparator.$pv['id']] = isset($oarr[$carr['id']][$pv['id']]['requirement']) ? $oarr[$carr['id']][$pv['id']]['requirement'] : 0;
        }
        return $data;
    }

    //用于拆分提交数据
    private function searchexprrd($data){
        unset($data['username']);
        unset($data['cid']);
        unset($data['totalmoney']);
        foreach ($data as $dk => $dv){
            if(is_numeric($dk)) continue;
            $arr = explode('--',$dk);
            $newarr[$arr[1]][$arr[0]] = $dv;
        }
        return $newarr;
    }

    //删除订单 显示
    public function deleteOrderShow(Request $request){
        if($request->ajax()){
            //select order
            $order = new Order();
            $orderData = $order->index($request);

            //select product
            $product = new Product();
            $productData = $product->index($request);
            //id作为key
            $productData = $product->idtokey($productData->items());
            foreach ($orderData as &$ov){
                $ov['pname'] = $productData[$ov['pid']]['name'];
            }
            return $this->onlyAjaxData($orderData);

        }
        $customer = new Customer();
        $customerData = $customer->index($request);
        return view('order/deleteorder',['customerdatas'=>$customerData]);
    }

    //删除订单 操作
    public function isdeleteOrder(Request $request){
        $order = new Order();
        return $order->deleteOrder($request);
    }
}
