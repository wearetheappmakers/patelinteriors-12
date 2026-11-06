<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">
<div class="row">

    @if(Auth::guard('user')->check() && Auth::user()->role == 1)

    <div class="form-group col-sm-3">
        <label>Branch</label>

        <select class="form-control select2" id="branch_id" name="branch_id" required>

        <option value="">Select</option>

        @foreach($branch as $datas)

        <option value="{{ $datas->id }}"
        @if(isset($data) && $data->branch_id == $datas->id) selected @endif>
        {{ $datas->name }}
        </option>

        @endforeach

        </select>

    </div>

    @endif

</div>
<div class="row">
    <div class="form-group col-sm-12">
    <label><strong>Policy Description</strong></label>

    <textarea name="policy_description" id="policy_description" class="form-control">
    {!! $data->policy_description ?? '' !!}
    </textarea>

    </div>

</div>
</div>

{{-- ✅ CKEDITOR LOAD --}}
<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>

<script>
    // Initialize CKEditor
    CKEDITOR.replace('policy_description', {
        height: 200
    });

    // IMPORTANT: update textarea before submit (for AJAX)
    $(".submit").click(function (event) {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    });
</script>