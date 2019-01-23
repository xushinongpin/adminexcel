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
        empty($time) ? $time = time() : $time = strtotime($time);
        $time = date("Y-m-d 00:00:00",$time);
        $where['time'] = $time;
        return $this->where($where)->orderBy('uid','desc')->get();
    }

    public function insertorupdate($data,$cid,$time){
        empty($time) ? $time = time() : $time = strtotime($time);
        $time = date("Y-m-d 00:00:00",$time);
        foreach ($data as $dk => $dv){
            if($dv['requirement'] == 0 && $dv['price'] == 0) continue;
            $dv['cid'] = $cid;
            $dv['time'] = $time;
            $dv['pid'] = $dk;
            $insert = $this->insertnewdata($dv);
        }
        return $insert;
    }

    private function insertnewdata($data){
        DB::beginTransaction();
        try{
            $order = new Order();
            $order->uid = $this->uid;
            $order->cid = $data['cid'];
            $order->pid = $data['pid'];
            $order->price = $data['price'];
            $order->requirement = $data['requirement'];
            $order->time = $data['time'];
            $order->save();
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollback();
            return $this->updatedata($data);
        }
    }

    private function updatedata($data){
        DB::beginTransaction();
        try{
            $where['uid'] = $this->uid;
            $where['cid'] = $data['cid'];
            $where['pid'] = $data['pid'];
            $where['time'] = $data['time'];
            $update['price'] = $data['price'];
            $update['requirement'] = $data['requirement'];
            Order::where($where)->update($update);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }
}
