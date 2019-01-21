<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    public function __construct(){
        $this->uid = Auth::id();
    }

    public function index($request){
        $where['uid'] = $this->uid;
        $request->time ? $where['created_at'] = $request->time : $where['created_at'] = strtotime(date("Y-m-d"));
        return $this->where($where)->orderBy('uid','desc')->get();
    }
}
