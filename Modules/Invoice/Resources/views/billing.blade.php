<div class="billingUpdateForm d-none">
    <form method="POST" action="{{ route('invoice.billing.update.student') }}">
        @csrf
        <input type="hidden" name="tracking_id" id="tracking_id" value="{{ $checkout->tracking }}">
        <input type="hidden" name="billing_detail_id" id="billing_detail_id" value="{{ $checkout->billing_detail_id }}">
        <div class="row billing_form ">
            <input type="hidden" name="previous_address_edit" value="0" id="previous_address_edit">
            <div class="col-lg-6">
                @php
                    $billing = $checkout->bill;
                @endphp
                <label class="primary_label2">{{ __('frontend.First Name') }} <span
                        class="required_mark">*</span></label>
                <input id="first_name" name="first_name" placeholder="{{ __('frontend.Enter First Name') }}"
                       class="primary_input3" value="{{ $billing->first_name }}" type="text"
                    {{ $errors->first('first_name') ? 'autofocus' : '' }}>
                <span class="text-danger">{{ $errors->first('first_name') }}</span>
            </div>
            <div class="col-lg-6">
                <label class="primary_label2">{{ __('frontend.Last Name') }} <span
                        class="required_mark">*</span></label>
                <input id="last_name" name="last_name" placeholder="{{ __('frontend.Enter Last Name') }}"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = '{{ __('frontend.Enter Last Name') }}'"
                       class="primary_input3" value="{{ $billing->last_name }}" type="text"
                    {{ $errors->first('last_name') ? 'autofocus' : '' }}>
                <span class="text-danger">{{ $errors->first('last_name') }}</span>
            </div>

            <div class="col-lg-12 mt_20">
                <label class="primary_label2">{{ __('frontend.Company Name') }} ({{ __('frontend.Optional') }}
                    )</label>
                <input id="company_name" name="company_name" placeholder="{{ __('frontend.Enter Company Name') }}"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = '{{ __('frontend.Enter Company Name') }}'"
                       class="primary_input3" type="text" value="{{ $billing->company_name }}">
            </div>
            <div class="col-lg-12 mt_20">
                <label class="primary_label2">{{ __('frontend.Country') }} <span class="required_mark">*</span> </label>
                <select id="country" name="country" class="select2 mb-3 wide w-100 "
                    {{ $errors->first('country') ? 'autofocus' : '' }}>
                    @if (isset($countries))
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                            @if (!empty($billing->county))
                                {{ $billing->county == $country->id ? 'selected' : '' }}
                                @else
                                {{ auth()->user()->country == $country->id ? 'selected' : '' }}
                                @endif>
                                {{ $country->name }}</option>
                        @endforeach
                    @endif
                </select>
                <span class="text-danger">{{ $errors->first('country') }}</span>
            </div>


            <div class="col-lg-12 mt_20">
                <label class="primary_label2"> {{ __('common.State') }} </label>

                <select class=" select2 wide stateList" name="state" id="state">
                    <option data-display=" {{ __('common.Select') }} {{ __('common.State') }}" value="">
                        {{ __('common.Select') }} {{ __('common.State') }}
                    </option>
                    @foreach ($states as $state)
                        <option value="{{ @$state->id }}" @if (@$user->state == $state->id) selected @endif>
                            {{ @$state->name }}</option>
                    @endforeach
                </select>


                <span class="text-danger">{{ $errors->first('state') }}</span>
            </div>

            <div class="col-lg-12 mt_20">
                <label class="primary_label2">{{ __('frontend.City / Town') }} </label>

                <select class=" select2 wide cityList" name="city" id="city">
                    <option data-display=" {{ __('common.Select') }} {{ __('common.City') }}" value="">
                        {{ __('common.Select') }} {{ __('common.City') }}
                    </option>
                    @foreach ($cities as $city)
                        <option value="{{ @$city->id }}" @if (@$user->city == $city->id) selected @endif>
                            {{ @$city->name }}</option>
                    @endforeach
                </select>


                <span class="text-danger">{{ $errors->first('city') }}</span>
            </div>

            <div class="col-lg-12 mt_20">
                <label class="primary_label2">{{ __('frontend.Street Address') }} <span
                        class="required_mark">*</span></label>
                <input id="address1" name="address1" placeholder="{{ __('frontend.House Number and street address') }}"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = '{{ __('frontend.House Number and street addres') }}s'"
                       class="primary_input3" type="text"
                       value="@if (!empty($billing)) {{ $billing->address1 }}@else{{ $billing->cityName }} @endif"
                    {{ $errors->first('address1') ? 'autofocus' : '' }}>
                <span class="text-danger">{{ $errors->first('address1') }}</span>
            </div>
            <div class="col-lg-12 mt-2">
                <input id="address2" name="address2"
                       placeholder="{{ __('frontend.Apartment, suite, unit etc (Optional)') }}"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = '{{ __('frontend.Apartment, suite, unit etc (Optional)') }}'"
                       class="primary_input3" type="text"
                       value="@if (!empty($billing)) {{ $billing->address2 }}@else{{ old('address2') }} @endif">
            </div>
            <div class="col-lg-12 mt_20 mb_35">
                <label class="primary_label2">{{ __('frontend.Postcode / ZIP') }} ({{ __('frontend.Optional') }}
                    )</label>
                <input id="zip_code" name="zip_code" placeholder="{{ __('frontend.Enter Company Name') }}"
                       onfocus="this.placeholder = ''" class="primary_input3" type="text"
                       value="@if (!empty($billing)) {{ $billing->zip_code }}@else{{ old('zip_code') }} @endif">
            </div>

            <div class="col-12">
                <h3 class="font_22 f_w_700 mb_23">{{ __('frontend.Additional Information') }}</h3>
            </div>
            <div class="col-lg-12">
                <label class="primary_label2">{{ __('frontend.Information details') }}</label>
                <textarea id="details" name="details" class="primary_textarea3"
                          placeholder="{{ __('frontend.Note about your order, e.g. special note for you delivery') }}"
                          onfocus="this.placeholder = ''"
                          onblur="this.placeholder = '{{ __('frontend.Note about your order, e.g. special note for you delivery') }}'">  </textarea>

            </div>
            <div class="col-lg-12 text-center">
                <button class="link_value theme_btn small_btn4" id="billingUpdateBtn" data-bs-toggle="tooltip">
                    <i class="ti-check"></i>
                    {{ __('common.Submit') }}
                </button>
            </div>
        </div>
    </form>

</div>
