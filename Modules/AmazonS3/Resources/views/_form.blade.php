<div class="row">
    <div class="col-xl-6">
        <div class="primary_input mb-25">
            <label class="primary_input_label"
                   for="">{{__('common.Access Key Id')}} <span class="required_mark">*</span></label>
            <input class="primary_input_field"
                   placeholder="{{__('common.Access Key Id')}}" type="text"
                   id="" name="access_key_id"
                   value="{{saasEnv('AWS_ACCESS_KEY_ID')??''}}">
        </div>
    </div>


    <div class="col-xl-6">
        <div class="primary_input mb-25">
            <label class="primary_input_label"
                   for="">{{__('common.Secret Key')}} <span class="required_mark">*</span></label>
            <input class="primary_input_field"
                   placeholder="{{__('common.Secret Key')}}" type="text"
                   id="" name="secret_key"
                   value="{{saasEnv('AWS_SECRET_ACCESS_KEY')??''}}">
        </div>
    </div>


    <div class="col-xl-6">
        <div class="primary_input mb-25">
            <label class="primary_input_label"
                   for="">{{__('common.Default Region')}} <span class="required_mark">*</span></label>
            <input class="primary_input_field"
                   placeholder="{{__('common.Default Region')}}"
                   type="text" id="" name="default_region"
                   value="{{saasEnv('AWS_DEFAULT_REGION')??''}}">
        </div>
    </div>
    <div class="col-xl-6">
        <div class="primary_input mb-25">
            <label class="primary_input_label"
                   for="">{{__('common.AWS Bucket')}} <span class="required_mark">*</span></label>
            <input class="primary_input_field"
                   placeholder="{{__('common.AWS Bucket')}}"
                   type="text" id="" name="bucket"
                   value="{{saasEnv('AWS_BUCKET')??''}}">
        </div>
    </div>

</div>
