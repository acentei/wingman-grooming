<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Order;

use Auth;
use Mail;

class OrderController extends Controller
{
    /**
     *  Authenticate access
     */
    public function __construct()
    {          
        $this->middleware('auth');      
        
        if (Auth::check()){
            parent::__construct();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::where('active',1)
        			   ->where('deleted',0)
                       ->where('order_status','Pending')
        			   ->orderBy('created_date','DESC')
        			   ->paginate(10);

        return view('pages.order.index',['orders' => $orders]);
    }    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with('details','details.product.brand','infos')
        			  ->find($id);
               
        return view('pages.order.show',['order' => $order]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::find($id);

        return view('pages.order.edit',['order' => $order]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        $order->order_status = $request->status;
        $order->tracking_number = $request->tracking;

        //send email if status is not pending
        if($request->status == "On-Delivery")
        {
            if($order->is_tracking == 1)
            {
                //SEND E-RECEIPT TO EMAIL
                $data = array(
                                'orderCode' => $order->order_code,
                                'tracking' => $request->tracking,   
                                'email' => $order->customer_email,                                                
                        );
                
                //change mail sender
                \Config::set('mail.username','noreply@wingmangrooming.com');
                \Config::set('mail.password','123456789');
                
                Mail::send('pages.emails.tracking-email', $data, function($message) use ($data)
                {
                    $message->subject('Tracking Number');
                    $message->from('noreply@wingmangrooming.com', 'Wingman Grooming');
                    $message->to($data['email']);
                });
            }
        }
        
        $order->save();

        return redirect()->route('order.index');
    }
}
