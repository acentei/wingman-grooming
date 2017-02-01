@extends('layouts.master')

@section('title')
    {{$product->name}} | Wingman Grooming
@endsection

{{--META TAGS--}}
@section('meta-url')
	{{Request::url()}}
@endsection

@section('meta-title')
	{{$product->name}} | Wingman Grooming
@endsection

@section('meta-description')
    Store
@endsection

@section('meta-image')
	
@endsection

{{-- STYLES AND SCRIPTS--}}
@section('styles')

@endsection

@section('scripts')

@endsection

@section('content')
<div class="proddet-container">
    
    <div class="proddet-left">
        <div class="proddet-main-img">
            <img id="zoom-main" src="{{$product->photo}}" data-zoom-image="{{$product->photo}}">
        </div>

        <div class="proddet-more-container">
            <div id="divphoto2" class="proddet-more-img">
                <img id="photo2" src="{{$product->photo_2}}">
            </div>

            <div id="divphoto3" class="proddet-more-img">
                <img id="photo3" src="{{$product->photo_3}}">
            </div>

            <div id="divphoto4" class="proddet-more-img">
                <img id="photo4" src="{{$product->photo_4}}">
            </div>
        </div>
    </div>
    
    <div class="proddet-right">
    
        <div class="modal-shop-name">
            {{ $product['brand']->display_name }}
        </div>

        <div class="details-top">
            <div class="details-left">    

                <div class="modal-product-name">
                    {{ $product->name }}
                </div>    

                <div class="modal-product-price">                    
                    PHP {{ $product->price }}
                </div> 
            </div>

            <div class="details-right">

                @if($product->stocks == 0)
                    <div id="outstock" class="prod-outofstock" style="display:none;">
                            Out of Stock!
                        </div>
                @else
                    <div id="hasstock" class="prod-hover-details">
                        Quantity
                        <div class="prod-hover-add">
                            <input id="prodQty" class="prh-num" type="number" min="1" max="{{$product->stocks}}" value="1" placeholder="1">            

                            <input id="prodAddCart" type="button"  class="prh-btn addCart" value="ADD TO CART" data-href="{{ route('shop.index') }}" data-id="{{$product->product_id}}" data-productcode="{{$product->product_code}}" data-checkout="{{route('cart.index')}}" data-image="{{$product->photo}}" data-name="{{$product->name}}" data-stock="{{$product->stocks}}" data-price="{{$product->price}}" data-toggle = "modal" data-target = "#addCartSuccess">  
                        </div>

                    </div>
                @endif                
            </div>       
        </div>

            <div class="modal-product-detials-bot">           

            <hr style="border-color:#999; width: 100%; float: left;">


            <div class="proddet-description">
                <pre class="product-description" style="background-color:#fff;">{{$product->description}}</pre>                
            </div>
            <br>

            <div class="details-properties"> 

                @foreach($product['property'] as $prod)  
                    <div class="details-properties">                                                 
                        <b>{{$prod->name}} :</b> {{$prod->value}}<br>
                    </div>
                @endforeach

                <br><br>
                <div class="details-properties">      
                    <?php 
                        $trimmedTags = trim($product->tags, "{ }");

                        $splitTags = explode(',',$trimmedTags);
                    ?>           

                    <b>TAGS :</b>
                    @foreach($splitTags as $key => $tags)                                                        
                        <a href="http://localhost:8080/wingmangrooming/public/index.php/shop?search={{$tags}}">{{$tags}}</a>,
                    @endforeach
                </div>

            </div>

        </div>                        
    </div>
    
    <br>  
    <hr style="border-color: #999;width:92%;float:left;margin-left:50px;">

    <div class="proddet-related-items">
        <span>RELATED ITEMS</span>
        <div class="related-items">

            <?php $i=0; ?>

            @foreach($product['brand']['product'] as $related)  
                @if($related->product_id != $product->product_id)
                    @if($i < 3)

                        <?php $i++; ?>
                        <div id="related-product-1" class="shop-product-container">

                            <div class="shop-product-image">

                                <a target="_blank" href="{{ route('shop-product.show',$related->slug) }}">                                    
                                    <img src="{{$related->photo}}" width="150px" height="150px">
                                </a>

                            </div>

                            <div class="shop-product-details">
                                <div class="prod-detail-name">
                                    {{$related->name}}
                                </div>

                                <div class="prod-detail-price">
                                    {{$related->price}}
                                </div>
                            </div>

                        </div>                            
                    @endif
                @endif
            @endforeach

        </div>
    </div>

</div>

<script type="text/javascript">

    $('#prodAddCart').click(function()            
    {
        $('#showProductDetails').modal('hide');

        var id = $(this).attr("data-id");
        var code = $(this).attr("data-productcode");
        var name = $(this).attr("data-name");
        var image = $(this).attr("data-image");
        var price = $(this).attr("data-price");            
        var stock = $(this).attr("data-stock");            
                    
        var qty = $('#prodQty').val();

        $('#prodAddCart').attr('data-quantity', qty);

        $.ajax({
            type: "POST",
            url: 'webapi/cart/add-cart-details',                
            data: {
                "id" : id,
                "code" : code,
                "name" : name,
                "image" : image,
                "qty" : parseInt(qty),
                "price" : price,
                "stock" : stock,
            },  
            success: function(data) {
                console.log(data);                         
            },
            error: function(xhr, status, error) {
          
            // So we remove everything before the first '{
            var result = xhr.responseText.replace(/[^{]*/i,'');
            
            //We parse the json
            var data = JSON.parse(result);
            console.log(data);
            $('#errorhere').html("<div class='alert alert-danger'></div>");
            // And continue like no error ever happened
            $.each(data, function(i,item){
                $('.alert-danger').append(item + "<br>");
            });
        } 
        });
    });

    $('.proddet-more-img img').click(function() {
        var thmb = this;
        var src = this.src;

        $('#zoom-main').removeData('src');
        $('#zoom-main').removeData('data-zoom-image');

        $('#zoom-main').fadeOut(400,function(){
            thmb.src = this.src;            
            $(this).fadeIn(400)[0].src = src;
            $(this).attr('data-zoom-image',src);
            $('div.zoomWindowContainer div').css({'background-image': 'url('+src+')'});
        });

        //for zooming the main image
        $("#zoom-main").elevateZoom({        
            zoomWindowFadeIn: 500,
            zoomWindowFadeOut: 500,
            lensFadeIn: 500,
            lensFadeOut: 500
        });

    });

    //zoom main
    $(document).ready(function(){

        //for zooming the main image
        $("#zoom-main").elevateZoom({        
          zoomWindowFadeIn: 500,
          zoomWindowFadeOut: 500,
          lensFadeIn: 500,
          lensFadeOut: 500
        });

    });

</script>

@endsection

