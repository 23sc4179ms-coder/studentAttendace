<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalculateController extends Controller
{
    public function add() {
   $a=5;
   $b= 3;
   $sum = $a+$b;
   return "sum is".$sum;
    
}
public function minus() {
   $a=5;
   $b= 3;
   $diff = $a-$b;
   return "diff is".$diff;
    
}
public function quotient() {
   $a=5;
   $b= 3;
   $quo = $a/$b;
   return "quotient is ".$quo;
    
}
public function product() {
   $a=5;
   $b= 3;
   $prod = $a*$b;
   return "product is ".$prod;
    
}
public function mod() {
   $a=5;
   $b= 3;
   $mod = $a%$b;
   return "remainder is ".$mod;
    
}
}
