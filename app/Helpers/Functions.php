<?php

namespace App\Helpers;

class Functions
{
    public static function generateColorStatusPembayaran($status = null) {
    
        switch($status){
            case'expire':
                $color = "bg-dark";
                break;
            case'pending':
                $color = "bg-danger";
                break;
            case'success':
                $color = "bg-success";
                break;
            default:
                $color = "bg-dark";
                break;
        }
        return $color;
    }

    public static function generateColorStatusTransaksi($status = null) {
    
        switch($status){
            case'menunggu konfirmasi vendor':
                $color = "bg-teal";
                break;
            case'dikonfirmasi oleh vendor':
                $color = "bg-primary";
                break;
            case'pending':
                $color = "bg-danger";
                break;
            case'pekerjaan selesai':
                $color = "bg-success";
                break;
            default:
                $color = "bg-dark";
                break;
        }
        return $color;
    }
}