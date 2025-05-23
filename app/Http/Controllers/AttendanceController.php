<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jmrashed\Zkteco\Lib\ZKTeco; 

class AttendanceController extends Controller
{
    //
    public function conection(){

    $connect=new ZKTeco('103.121.25.3','4369',1122);

    dd($connect->connect());

    }
    
}
