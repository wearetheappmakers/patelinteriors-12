<input type="hidden" name="delete_url" id="delete_url" value="{{ route('user.home.delete-multiple') }}" />

@if(isset($table_name))



<input type="hidden" name="table_name" id="table_name" value="{{ $table_name }}" />



@endif

@push('scripts')

<script type="text/javascript">

	

	$(document).ready(function() {



		$(document).on("click", ".delete-record", function() {



			if (confirm("Are you sure ?")) {



				var ids = [];

				ids.push($(this).data('id'));



				var table = $('#table_name').val();

				var newurl = '';



				newurl = 'table_name=' + table + '&id=' + ids;

				$.ajax({

					type: 'GET',

					url: $('#delete_url').val(),

					data: newurl,

					dataType: 'json',



					success: function(data) {

						if (data.status == 'Success') {

							toastr["success"](data.message, "Success");

							alert(data.message);

							location.reload();

						} else {

							toastr["error"](data.message, "Error");

							location.reload();

						}

					}

				});

			}

			return false;

		});

	});





	 $(document).on("change", ".change_status", function() {



        var parent = $(this);



        var ids = [];



        var idrow = this.id.split('-');



        ids.push(idrow[1]);



        var params = '';



        var rowparam = parent.attr('href').split('-');



        if (rowparam[1] == '1') {



            params = '0';



        } else {



            params = '1';



        }







        var field = rowparam[0];



        ChangeMultiple(ids, field, params);



        return false;



    });











    function ChangeMultiple(ids, field, params) {







        var table = $('#table_name').val();







        $(ids).each(function() {







            $('#' + field + '-' + this).addClass('disabled');







            $('#' + field + '-' + this).html('<i class="kt-switch kt-switch--info"></i>');







        });







        $.ajax({







            type: 'GET',







            url: "{{route('user.home.change-multiple-status')}}",







            data: 'id=' + ids + '&table_name=' + table + '&field=' + field + '&param=' + params,







            dataType: 'json',











            success: function(data) {



                if (data.status == 'Success') {



                    toastr["success"](data.message, "Success");



                    $(ids).each(function() {



                        if (params == '1') {



                            $('#' + field + '-' + this).attr('href', field + '-1');



                        } else {



                            $('#' + field + '-' + this).attr('href', field + '-0');



                        }



                        $('#' + field + '-' + this).removeClass('disabled');



                        $("#multiple-action").removeClass('disabled');







                    });



                    location.reload();



                } else if(data.status === 'Errors'){



                    toastr["warning"](data.message, "Error");

                    setTimeout(function(){

                        location.reload();

                    }, 5000);



                }else {



                    // location.reload();

                    toastr["error"](data.message, "Error");



                }



            }







        });









    }

</script>

@endpush