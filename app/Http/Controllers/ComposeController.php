<?php
/**
 * Created by PhpStorm.
 * User: SKings
 * Date: 6/30/2016
 * Time: 9:13 AM
 */

namespace App\Http\Controllers;
use Auth;

class ComposeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the inbox for the authenticated user.
     *
     * @return Response
     */
    public function showCompose()
    {
        $user = Auth::user();
        //echo $user;
        return view('compose');
    }
}