<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    public function __construct(){
        $this->uid = Auth::id();
    }

    public function index($request){
        $where['uid'] = $this->uid;
        $time = $request->time;
        empty($time) ? $time = strtotime("+1 day") : $time = strtotime($time);
        $time = date("Y-m-d 00:00:00",$time);
        $where['time'] = $time;
        return $this->where($where)->orderBy('uid','desc')->get();
    }

    public function insertorupdate($data,$cid,$time,$moneyconversion=10000){
        empty($time) ? $time = strtotime("+1 day") : $time = strtotime($time);
        $time = date("Y-m-d 00:00:00",$time);
        foreach ($data as $dk => $dv){
            if($dv['requirement'] == 0 && $dv['price'] == 0) continue;
            $dv['cid'] = $cid;
            $dv['time'] = $time;
            $dv['pid'] = $dk;
            $this->insertnewdata($dv,$moneyconversion);
        }
        return true;
    }

    public function joincustomerproduct($request,$moneyconversion=10000){
        $request->limit = 1000000;
        $orderData = $this->index($request);
        $product = new Product();
        $productData = $product->index($request);
        //id作为key
        $productKey = array_column($productData->items(),'id');
        $productData = array_combine($productKey,$productData->items());
        $customer = new Customer();
        $customerData = $customer->index($request);
        //id作为key
        $customerKey = array_column($customerData->items(),'id');
        $customerData = array_combine($customerKey,$customerData->items());
        //数组组合
        $newData = array();
        foreach ($orderData as &$ov){
            $ov['price'] =($ov['price']/$moneyconversion);
            $ov['requirement'] =($ov['requirement']/$moneyconversion);
            $newData[$ov['cid']]['cname'] = $customerData[$ov['cid']]['name'];
            $newData[$ov['cid']]['time'] = date("Y-m-d",strtotime($ov['time']));
            isset($newData[$ov['cid']]['total']) ? $newData[$ov['cid']]['total'] = $newData[$ov['cid']]['total'] + $ov['price']*$ov['requirement'] : $newData[$ov['cid']]['total'] = $ov['price']*$ov['requirement'];
            $ov['pname'] = $productData[$ov['pid']]['name'];
            $newData[$ov['cid']]['data'][] = $ov;
        }
        return $newData;
    }

    private function insertnewdata($data,$moneyconversion=10000){
        DB::beginTransaction();
        try{
            $order = new Order();
            $order->uid = $this->uid;
            $order->cid = $data['cid'];
            $order->pid = $data['pid'];
            $order->price = ($data['price'] * $moneyconversion);
            $order->requirement = ($data['requirement'] * $moneyconversion);
            $order->time = $data['time'];
            $order->save();
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollback();
            return $this->updatedata($data,$moneyconversion);
        }
    }

    private function updatedata($data,$moneyconversion=10000){
        DB::beginTransaction();
        try{
            $where['uid'] = $this->uid;
            $where['cid'] = $data['cid'];
            $where['pid'] = $data['pid'];
            $where['time'] = $data['time'];
            $update['price'] = ($data['price'] * $moneyconversion);
            $update['requirement'] = ($data['requirement'] * $moneyconversion);
            Order::where($where)->update($update);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }

    //组合好订单数组
    public static function treeorder($oarr,$conversion){
        $new = array();
        foreach ($oarr as $ov) {
            $new[$ov['cid']][$ov['pid']]['price'] = ($ov['price']/$conversion);
            $new[$ov['cid']][$ov['pid']]['requirement'] = ($ov['requirement']/$conversion);
        }
        return $new;
    }
}
