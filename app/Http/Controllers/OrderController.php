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

            //Consolidated report
            $orderTableArr = array();
            foreach ($customerData as $cv){
                $orderTableArr[$cv['id']] = $this->consolidated($cv,$productData,$orderData);
            }
            dd($orderTableArr);
            $returnData = array();
            $returnData['code'] = 0;
            $returnData['count'] = count($orderTableArr);
            $returnData['msg'] = '正在进行分页查询';
            $returnData['data'] = $orderTableArr;
            unset($orderTableArr);
            return $returnData;
        }

        //table title
        $productTitle = "[[{type:'checkbox',fixed:'left'},{field:'cid', width:60, title: 'ID'},";
        foreach ($productData as $pv){
            $productTitle .= "{field:'requirement".$this->strseparator.$pv['id']."', title:'".$pv['name']."量', width:120, edit: 'text'},";
            $productTitle .= "{field:'price".$this->strseparator.$pv['id']."', title:'单价', width:60, edit: 'text'},";
        }
        $productTitle .= "{field:'totalmoney', title:'总价', width:120},{field:'username', title:'购买用户', width:100, fixed: 'right'}]]";
        unset($productData);
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
        $order = new Order();
        $data = json_decode($request->data,true);
        foreach ($data as $dv){
            $ndata = $this->searchexprrd($dv);
            $return = $order->insertorupdate($ndata,$dv['cid'],$request->time);
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

    private function consolidated($carr,$parr,$oarr){
        $oarr = $this->treeorder($oarr);
        foreach ($parr as $pv){
            $data['username'] = $carr['name'];
            $data['cid'] = $carr['id'];
            $data[$pv['id']] = $pv['id'];

            $data['price'.$this->strseparator.$pv['id']] = isset($oarr[$carr['id']][$pv['id']]['price']) ? $oarr[$carr['id']][$pv['id']]['price'] : 0;
            $data['requirement'.$this->strseparator.$pv['id']] = isset($oarr[$carr['id']][$pv['id']]['requirement']) ? $oarr[$carr['id']][$pv['id']]['requirement'] : 0;
        }
        return $data;
    }

    //用于拆分提交数据
    private function searchexprrd($data){
        unset($data['username']);
        unset($data['cid']);
        foreach ($data as $dk => $dv){
            if(is_numeric($dk)) continue;
            $arr = explode('--',$dk);
            $newarr[$arr[1]][$arr[0]] = $dv;
        }
        return $newarr;
    }

    //组合好订单数组
    private function treeorder($oarr){
        foreach ($oarr as $ov) {
            $new[$ov['cid']][$ov['pid']]['price'] = $ov['price'];
            $new[$ov['cid']][$ov['pid']]['requirement'] = $ov['requirement'];
        }
        return $new;
    }
}
