<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    public function __construct(){
        $this->uid = Auth::id();
    }
    //select all for where
    public function index(){
        $uid = $this->uid;
        return $this->where('uid',$uid)->orderBy('id','dasc')->orderBy('status','desc')->paginate(10);
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
        }
        if($return){
            $res['status'] = 1;
            $res['msg'] = '添加修改成功';
        }
        return $res;
    }

    //add one customer data
    private function createOne($request){
        $add['uid'] = $this->uid;
        $add['updated_at'] = $add['created_at'] = date("Y-m-d H:i:s");
        $add['name'] = $request->name;
        $add['status'] = $request->status;
        return $this->create($add);
    }
}
