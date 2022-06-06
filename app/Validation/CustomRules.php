<?php


namespace App\Validation;


class CustomRules
{
    // Rule is to validate mobile number digits
    public function mobileValidation(string $data){

        /*Checking: Mobile number must be of 10 digits*/
        $bool = preg_match('/^[0-9]{11}+$/', $data);
        $start=substr($data,0,3);
        $starts=['010','011','012','015'];
        if(in_array($start,$starts) && $bool){
            return true;
        }else{
            return false;
        }
    }
}