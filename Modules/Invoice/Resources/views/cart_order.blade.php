<div class="order_wrapper">
    <h3 class="font_22 f_w_700 mb_30">{{ __('frontend.Your order') }}</h3>
    <div class="ordered_products">
        @php $totalSum=0; @endphp
       
        @if (isset($carts))
       
            @foreach ($carts as $cart)
           
                @php
                    if ($cart->course_id) {
                        if ($cart->course_id != 0) {
                            if ($cart->course->discount_price != null) {
                                $price = $cart->course->discount_price;
                            } else {
                                $price = $cart->course->price;
                            }
                        } else {
                            $price = $cart->bundle->price;
                        }
                    } elseif (isModuleActive('Appointment')) {
                        $price = $cart->instructor->hour_rate;
                    } else {
                        $price = 0;
                    }
                    if($type=="certificate") {
                        $price = $cart->price;
                    }
                    $totalSum = $totalSum + @$price;
                    
                @endphp
                <div class="single_ordered_product">
                    <div class="product_name d-flex align-items-center">
                        <div class="thumb">
                            <img src="{{ getCourseImage(@$cart->course->thumbnail) }}" alt="">
                        </div>
                        <span>{{ @$cart->course->title }} {{ $type == 'certificate' ? '['.__('certificate.Certificate').']' :'' }}</span>
                    </div>
                    <span class="order_prise f_w_500 font_16">
                        {{ getPriceFormat($price) }}
                    </span>
                </div>
            @endforeach
        @endif
    </div>
    <div class="ordered_products_lists">
        <div class="single_lists">
            <span class=" total_text">{{ __('frontend.Subtotal') }}</span>
            <span>{{ getPriceFormat($checkout->price) }}</span>
        </div>
        @if ($checkout->purchase_price > 0)
            <div class="single_lists">

                <span class="total_text">{{ __('payment.Discount Amount') }}</span>
                <span>{{ $checkout->discount == '' ? '0' : getPriceFormat($checkout->discount) }}</span>
            </div>
            @if (hasTax())
                <div class="single_lists">
                    <span class="total_text">{{ __('tax.TAX') }} </span>

                    <span class="totalTax">{{ getPriceFormat($checkout->tax) }}</span>
                </div>
            @endif
            <div class="single_lists">
                <span class="total_text">{{ __('frontend.Payable Amount') }} </span>
                <span class="totalBalance">{{ getPriceFormat($checkout->purchase_price) }}</span>
            </div>
        @endif
    </div>

</div>