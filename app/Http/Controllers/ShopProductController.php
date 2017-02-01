<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Product;

class ShopProductController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $product = Product::with('brand','brand.product','producttype','property')
                          ->where('slug',$slug)
                          ->first();     
        
        return view('pages.shop.productdetails2',['product' => $product]);
    }
    
}
