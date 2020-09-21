<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $successStatus = 200;
    public function index()
    {
        
        $users = DB::table('users')->get();
        return response()->json(['data' => $users], $this->successStatus); 
        
        
    }
}
