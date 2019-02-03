<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //After the paging data is queried, the collated data is returned to the front end, need
    //code: 0,
    //msg: "",
    //count: 1000,
    //data:[{},{}]
    public function returnAjaxData($data){
        $returnData = array();
        $returnData['code'] = 0;
        $returnData['count'] = $data->total();
        $returnData['msg'] = '正在进行分页查询';
        $returnData['data'] = $data->items();
        return $returnData;
    }

    public function onlyAjaxData($data){
        $returnData = array();
        $returnData['code'] = 0;
        $returnData['count'] = count($data);
        $returnData['msg'] = '正在进行分页查询';
        $returnData['data'] = $data;
        return $returnData;
    }

    //Returns the judgment data required by ajax status 0/1
    public function returnAjaxStatus($data){
        return $data;
    }
}
