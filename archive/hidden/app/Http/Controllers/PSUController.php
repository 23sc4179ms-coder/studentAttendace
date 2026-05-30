<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PSUController extends Controller
{
    public function welcome(){
        return "Emmanuel Garcia";
    }
    public function mission(){
        return "The Pangasinan State University, shall provide a human-centric, resilient , and sustainable academic environment to produce dynamic, responsive, and future-ready individuals capable of meeting the requirements of the local and global communities and industries.";
    }
    public function vision(){
        return "To become a leading industry-driven State University in the ASEAN region by 2030";
    }
    public function EOMSPolicy(){
        return "The Pangasinan State University shall be recognized as an ASEAN premier state university that provides quality education and satisfactory service delivery through instruction, research, extension and production.

We commit our expertise and resources to produce professionals who meet the expectations of the industry and other interested parties in the national and international community.

We shall continuously improve our operations through systems and process innovations guided by ethical, intellectual property and technology transfer standards in response to the changing educational, scientific and technological developments for social responsiveness and in support of the institution’s strategic direction.";
    }
    public function dynamic(){
        return "Febraruary 14, 2024 <br> Author: Emmanuel Garcia <br>  PSU website";
        
       
    }
    public function student($name,$course)
    {
        
        return "name: $name|course: $course";
    }

}
