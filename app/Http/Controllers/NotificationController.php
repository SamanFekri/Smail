<?php
/**
 * Created by PhpStorm.
 * User: SKings
 * Date: 6/30/2016
 * Time: 9:13 AM
 */

namespace App\Http\Controllers;
use Auth;

class NotificationController extends Controller
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
    public function showNotif()
    {
        $user = Auth::user();
        //echo $user;
        return view('notification');
    }
}