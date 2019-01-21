<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    public function __construct(){
        $this->uid = Auth::id();
    }

    public function index($request){
        $uid = $this->uid;
        return $this->where('uid',$uid)->orderBy('status','asc')->paginate($request->limit);
    }

    //update one customer data
    public function updateOnlyOne($request){
        $res['status'] = 0;
        $res['msg'] = '添加修改失败';
        if(!is_numeric($request->status) && ($request->status != 1 || $request->status != 2)){
            $res['msg'] = '状态【1使用 2停用】'.$request->status;
            return $res;
        }
        if(!isset($request->uid)){
            $return = $this->createOne($request);
            $res['msg'] = '添加成功';
        }elseif(isset($request->del)){
            $return = $this->deleteOne($request);
            $res['msg'] = '删除成功';
        }else{
            if($request->uid != $this->uid){
                $res['msg'] = 'uid fail';
                return $res;
            }
            $save['name'] = $request->name;
            $save['status'] = $request->status;
            $save['updated_at'] = date("Y-m-d H:i:s");
            $where['id'] = $request->id;
            $where['uid'] = $this->uid;
            $return = $this->where($where)->update($save);
            $res['msg'] = '修改成功';
        }
        if($return){
            $res['status'] = 1;
        }
        return $res;
    }

    //add one customer data
    private function createOne($request){
        $customer = new Product;
        $customer->uid = $this->uid;
        $customer->updated_at = $customer->created_at = date("Y-m-d H:i:s");
        $customer->name = $request->name;
        $customer->status = $request->status;
        return $customer->save();
    }

    //delete one customer
    private function deleteOne($request){
        $where['id'] = $request->id;
        $where['uid'] = $this->uid;
        return $this->where($where)->delete();
    }

    //can sales the product
    public function canSales($status=1){
        $where['uid'] = $this->uid;
        $where['status'] = $status;
        return $this->where($where)->get();
    }
}
