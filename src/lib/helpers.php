<?php

use App\Models\Option;
function get_setting_value($name){
    if(get_options_count($name)){
        return Option::where('name',$name)->first()->value;
    }else{
        return "无";
    }
}