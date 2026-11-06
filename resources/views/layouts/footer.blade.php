</div>
<div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-footer__copyright">
            2022&nbsp;&copy;&nbsp;<a target="_blank" class="kt-link">Applified</a>
        </div>
    </div>
</div>
<!-- end:: Footer -->
</div>
</div>
</div>
<!-- end:: Page -->
<!-- begin::Scrolltop -->
<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>
<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
 $(document).ready(function() {
    $('.select2').select2();
});
 @if(Session::has('message'))
 var type = "{{ Session::get('alert-type', 'info') }}";
 switch(type){
 case 'info':
    toastr.info("{{ Session::get('message') }}");
    break;
case 'warning':
    toastr.warning("{{ Session::get('message') }}");
    break;
case 'success':
    toastr.success("{{ Session::get('message') }}");
    break;
case 'error':
    toastr.error("{{ Session::get('message') }}");
    break;
}
@endif
</script>
<script>
    function getItemallSelect2($element) {
        $element.select2({
            ajax: {
                url: '{{ route("user.ajax.products.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        company_id: $('#company_id').val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            placeholder: 'Search Product',
            minimumInputLength: 1,
            allowClear: true,
            width: '100%'
        });
    }

    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#22b9ff",
                "light": "#ffffff",
                "dark": "#282a3c",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };
</script>
<!-- end::Global Config -->
<!--begin::Global Theme Bundle(used by all pages) -->
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
<!--end::Page Vendors -->
<!--begin::Page Scripts(used by this page) -->
<!-- <script src="{{ asset('assets/js/pages/crud/datatables/extensions/rowreorder.js') }}" type="text/javascript"></script> -->
<!--end::Global Theme Bundle -->
<!--begin::Page Vendors(used by this page) -->
<script src="{{ asset('/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}" type="text/javascript"></script>
<script src="//maps.google.com/maps/api/js?key=AIzaSyBTGnKT7dt597vo9QgeQ7BFhvSRP4eiMSM" type="text/javascript"></script>
<script src="{{ asset('/assets/plugins/custom/gmaps/gmaps.js') }}" type="text/javascript"></script>
<!--end::Page Vendors -->
<!-- <script src="{{ asset('/assets/js/pages/my-script.js') }}" type="text/javascript"></script> -->
<!--begin::Page Scripts(used by this page) -->
<script src="{{ asset('/assets/js/pages/dashboard.js') }}" type="text/javascript"></script>
@stack('scripts')
<!--end::Page Scripts -->
</body>
<!-- end::Body -->
</html>