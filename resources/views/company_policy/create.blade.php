<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">
    <div class="row">
        @if(Auth::guard('user')->check() && Auth::user()->role == 1)

			<div class="form-group col-sm-3">
				<label>Branch</label>
				<select class="form-control select2" id="branch_id" name="branch_id" required>
					<option value="">Select</option>
					@foreach($branch as $datas)
						<option value="{{ $datas->id }}">{{ $datas->name }}</option>
					@endforeach
				</select>
			</div>
		@endif

       
    </div>
    <div class="row">
        <div class="form-group col-sm-12">
            <label><strong>Policy Description</strong></label>

            <textarea class="form-control"
                name="policy_description"
                id="policy_description"></textarea>
        </div>
    </div>

</div>

<script src="{{ asset('assets/plugins/custom/ckeditor/ckeditor.js') }}"></script>

<script>
    CKEDITOR.replace('policy_description', {
        filebrowserUploadMethod: 'form'
    });
</script>