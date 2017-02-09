<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Mail;

class WholesaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                    
        return view('pages.contactus.wholesale');
    }

    public function store(Request $request)
    {                  
        $data = array(
        				'tradename' => $request->legaltitle,
        				'fullname' => $request->fullname,
        				'reference' => $request->reference,
        				'sender' => $request->email,        				
        				'busadd' => $request->busadd,
        			 );

        //change mail sender
        \Config::set('mail.username','cs@wingmangrooming.com');
        \Config::set('mail.password','123456789');

        Mail::send('pages.emails.wholesale-email', $data, function($message) use ($data)
        {
        	$message->subject('Wholesale Inquiry');
            $message->from('cs@wingmangrooming.com', $data['tradename']);
            $message->to('admin@wingmangrooming.com');
        });

        return redirect()->to('/wholesale-inquiry/sent');
    }
}
