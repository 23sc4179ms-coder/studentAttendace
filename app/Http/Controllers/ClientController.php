<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function displayGreetings() {
    $name ="Mark Lhemuel Arenas";
    $address = "San Carlos City";
    //    return view("greetings",['name'=>$name]);
         return view("portal_greetings",compact("name","address"));
    }
    public function displayProfile() {
     return view("portal_client_profile");
    }

    public function displayDashboard() {
     return view("portal_client_dashboard");
    }

    public function displayAboutUs() {
     return view("portal_client_about");
    }

    public function index()
    {
      $grade = 90;
    //   $client = [
    //     "name" => "Mark Lhemuel",
    //     "sex" => "Male",
    //     "address" => "San Carlos City"
    //   ];

    $clients = array(
        array("name"=>"Mark Lhemuel","sex"=>"male","address"=>"san carlos"),
        array("name"=>"Shin Jay","sex"=>"male","address"=>"san carlos"),
        array("name"=>"Jimboy","sex"=>"male","address"=>"san carlos")
        
    );
    //   $clients = array();
    return view("portal_client")->with("grade",$grade)->with("clients",$clients);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
