
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__body">
	<div class="row">
		

		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-sm-3">
			<label>Branch</label>
			<select class="form-control select2" id="branch_id" onchange="getdata(this)" name="branch_id">
				<option value="">Select</option>
				@foreach($branch as $datas)
				<option value="{{$datas->id}}">{{$datas->name}}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group col-sm-3">
			<label>Employee</label>
			<select class="form-control select2" id="employee_id" name="employee_id">
				<option value="">Select</option>
			</select>
		</div>
		@endif
		@endif

		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 2)

		<div class="form-group col-sm-3">
			<label>Employee</label>
			<select class="form-control select2" id="employee_id" name="employee_id">
				<option value="">Select</option>
				@foreach($employee as $datas)
				<option value="{{$datas->id}}">{{$datas->name}}</option>
				@endforeach
			</select>
		</div>

		@endif
		@endif

		<div class="form-group col-lg-3">
			<label>PDF Name</label>
			<select class="form-control select2" name="pdf_name" id="pdf_name"  required>
				<option value="">Select</option>
				<!-- <option value="1">Patel Interior</option> -->
				<option value="2">Kuche</option>
				<option value="3">Elementra</option>
				<option value="4">Nolte</option>
				
			</select>
		</div>


	</div>
	<div class="row" style="display: none;"  id="kuche"  >
		<div class="form-group col-lg-3">
			<label>Customer Name: </label>
			<input type="text" name="customer_name" placeholder="Enter Customer Name" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Site Address: </label>
			<input type="text" name="site_address" placeholder="Enter Site Address" class="form-control">
		</div>
		<div class="form-group col-lg-3">
			<label>Email: </label>
			<input type="text" name="email" placeholder="Enter Email" class="form-control">
		</div>
		<div class="form-group col-lg-3">
			<label>Contact Number: </label>
			<input type="number" name="contact_no" placeholder="Enter Contact Number" class="form-control">
		</div>
		<div class="form-group col-lg-3" style="display: none;"  id="architect">
			<label>Architect: </label>
			<input type="text" name="architect" placeholder="Enter Architect" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Date: </label>
			<input type="date" name="date" placeholder="Enter Date" value="{{date('Y-m-d')}}" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Quotation No: </label>
			<input type="text" name="quotation_no" placeholder="Enter Quotation No" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Designer: </label>
			<input type="text" name="designer" placeholder="Enter Designer" class="form-control">
		</div>	
		<div class="form-group col-lg-3" style="display: none;"  id="sales_contact">
			<label>Sales Contact: </label>
			<input type="text" name="sales_contact" placeholder="Enter Sales Contact" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Model: </label>
			<input type="text" name="model" placeholder="Enter Model" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Base: </label>
			<input type="text" name="f_base" placeholder="Enter Base" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Wall: </label>
			<input type="text" name="f_wall" placeholder="Enter Wall" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Tall: </label>
			<input type="text" name="f_tall" placeholder="Enter Tall" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Carcase Combination: </label>
			<input type="text" name="carcase_combination" placeholder="Enter Carcase Combination" class="form-control">
		</div>	
		<div class="form-group col-lg-3" >
			<label style="display: none;" id="glass">Glass : Clear / Tinted / Satin / Ribbed: </label>
			<label style="display: none;" id="glass4">Glass :</label>
			<label  style="display: none;" id="glass3">Glass: Clear/Satin/Wire look/ tinted black</label>
			<input type="text" name="glass" placeholder="Enter Glass" class="form-control">
		</div>
		
		<div class="form-group col-lg-3" style="display: none;" id="fillers_panels">
			<label>Fillers & Panels </label>
			<input type="text" name="fillers_panels" placeholder="Fillers & Panels" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Drawers & Internals </label>
			<input type="text" name="drawers_internals" placeholder="Drawers & Internals" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Handles</label>
			<input type="text" name="handles" placeholder="Handles" class="form-control">
		</div>
		<div class="form-group col-lg-3">
			<label>Plinth Colour</label>
			<input type="text" name="plinth_colour" placeholder="Plinth Colour" class="form-control">
		</div>
		<div class="form-group col-lg-3" style="display: none;" id="cornish">
			<label>Cornish</label>
			<input type="text" name="cornish" placeholder="Cornish" class="form-control">
		</div>
		<div class="form-group col-lg-3">
			<label>Plinth Height </label>
			<input type="text" name="plinth_height" placeholder="Plinth Height" class="form-control">
		</div>
		<div class="form-group col-lg-3" style="display: none;" id="kitchen_appliances">
			<label>Kitchen Appliances</label>
			<input type="text" name="kitchen_appliances" placeholder="Kitchen Appliances" class="form-control">
		</div>	
		<div class="form-group col-lg-3" style="display: none;" id="sitchen_sink">
			<label>Sitchen Sink</label>
			<input type="text" name="sitchen_sink" placeholder="Sitchen Sink" class="form-control">
		</div>	
		<div class="form-group col-lg-3" style="display: none;" id="core_cutting" >
			<label>Core cutting, Ducting and Gas Piping</label>
			<input type="text" name="core_cutting" placeholder="Core cutting, Ducting and Gas Piping" class="form-control">
		</div>
		<div class="form-group col-lg-3" style="display: none;" id="worktop">
			<label>Worktop / Stone</label>
			<input type="text" name="worktop" placeholder="Worktop / Stone" class="form-control">
		</div>	
		<div class="form-group col-lg-3">
			<label>Kitchen Furniture and Trade goods</label>
			<input type="text" name="kitchen_furniture" placeholder="Kitchen Furniture and Trade goods" class="form-control">
		</div>	

	
		
		<div class="form-group col-lg-3" style="display: none;" id="billing_name_no">
			<label>Billing Name & Address: </label>
			<input type="text" name="billing_name_no" placeholder="Enter Billing Name & Address " class="form-control">
		</div>	
		

		<div class="form-group col-lg-3" style="display: none;" id="customer_pan_no">
			<label>Customer PAN NO: </label>
			<input type="text" name="customer_pan_no" placeholder="Enter Customer PAN NO" class="form-control">
		</div>	
		

		
		
		
		
		
		<div class="form-group col-lg-3" style="display: none;" id="worktop_height">
			<label>Worktop Height: </label>
			<input type="text" name="worktop_height" placeholder="Worktop Height" class="form-control">
		</div>
		
	
		
		
	

	
		
		
		<div class="form-group col-lg-3" style="display: none;" id="project">
			<label>Project: </label>
			<input type="text" name="project" placeholder="Enter Project" class="form-control">
		</div>	
		
		
		
		
		
		<div class="form-group col-lg-3" style="display: none;" id="appliances_sink_accessories">
			<label>Appliances and sink accessories</label>
			<input type="text" name="appliances_sink_accessories" placeholder="Appliances and sink accessories" class="form-control">
		</div>	
		<div class="form-group col-lg-3" style="display: none;" id="worktop_dado" >
			<label> Worktop & Dado</label>
			<input type="text" name="worktop_dado" placeholder="Worktop & Dado" class="form-control">
		</div>	
		<div class="form-group col-lg-3" style="display: none;" id="ducting_gas_piping" >
			<label>Ducting and gas piping  </label>
			<input type="text" name="ducting_gas_piping" placeholder="Ducting and gas piping" class="form-control">
		</div>
		<div class="form-group col-lg-3" style="display: none;" id="transportation">
			<label>Transportation & installation charges   </label>
			<input type="text" name="" placeholder="Transportation & installation charges" class="form-control">
		</div>
		
		
	</div>
</div>

<script type="text/javascript">
	$(function () {
		$("#pdf_name").change(function () {
			if ($(this).val() == "2") {
				$("#kuche").show();
				$("#worktop_height").hide();
				$("#customer_pan_no").hide();
				$("#billing_name_no").hide();
				$("#project").hide();
				$("#appliances_sink_accessories").hide();
				$("#worktop_dado").hide();
				$("#ducting_gas_piping").hide();
				$("#transportation").hide();
				$("#cornish").show();
				$("#fillers_panels").show();
				$("#kitchen_appliances").show();
				$("#sitchen_sink").show();
				$("#core_cutting").show();
				$("#worktop").show();
				$("#sales_contact").show();
				$("#glass4").hide();
				$("#architect").show();
				$("#glass3").hide();
				$("#glass").show();
				
				


			}
			else if ($(this).val() == "1") {
				$("#kuche").hide();
				$("#elementra").hide();
				$("#nolte").hide();
				$("#worktop_height").hide();
				$("#customer_pan_no").hide();
				$("#billing_name_no").hide();
				$("#project").hide();
				$("#appliances_sink_accessories").hide();
				$("#worktop_dado").hide();
				$("#cornish").hide();
				$("#ducting_gas_piping").hide();
				$("#fillers_panels").hide();
				$("#transportation").hide();
				$("#kitchen_appliances").hide();
				$("#sitchen_sink").hide();
				$("#core_cutting").hide();
				$("#worktop").hide();
				$("#sales_contact").hide();
				$("#glass4").hide();
				$("#architect").hide();
				$("#glass3").hide();
				$("#glass").hide();
				
			}
			else if($(this).val() == "3")
			{
				
				$("#worktop_height").show();
				$("#customer_pan_no").show();
				$("#billing_name_no").show();
				$("#fillers_panels").show();
				$("#kitchen_appliances").hide();
				$("#cornish").show();
				$("#kuche").show();
				$("#nolte").hide();
				$("#sitchen_sink").hide();
				$("#core_cutting").hide();
				$("#sales_contact").show();
				$("#worktop").hide();
				$("#glass4").hide();
				$("#architect").hide();
				$("#glass3").show();
				$("#glass").hide();
			}
			else if($(this).val() == "4")
			{

				$("#kuche").show();
				$("#project").show();
				$("#worktop_height").show();
				$("#customer_pan_no").show();
				$("#billing_name_no").show();
				$("#appliances_sink_accessories").show();
				$("#worktop_dado").show();
				$("#ducting_gas_piping").show();
				$("#fillers_panels").hide();
				$("#cornish").hide();
				$("#transportation").show();
				$("#kitchen_appliances").hide();
				$("#sitchen_sink").hide();
				$("#core_cutting").hide();
				$("#worktop").hide();
				$("#sales_contact").hide();
				$("#glass4").show();
				$("#architect").show();
				$("#glass3").hide();
				$("#glass").hide();


				
				
			}
			
			else {
				$("#kuche").hide();
				$("#elementra").hide();
				$("#nolte").hide();
				$("#worktop_height").hide();
				$("#customer_pan_no").hide();
				$("#billing_name_no").hide();
				$("#project").hide();
				$("#appliances_sink_accessories").hide();
				$("#worktop_dado").hide();
				$("#ducting_gas_piping").hide();
				$("#transportation").hide();
				$("#fillers_panels").hide();
				$("#cornish").hide();
				$("#kitchen_appliances").hide();
				$("#sitchen_sink").hide();
				$("#core_cutting").hide();
				$("#worktop").hide();
				$("#sales_contact").hide();
				$("#glass4").hide();
				$("#architect").hide();
				$("#glass3").hide();
				$("#glass").hide();
				

				
			}
		});
	});
</script>

<script type="text/javascript">
	
	function getdata($this){

		var branch_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.employee.name') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){
          // console.log(data.data);
          $('#employee_id').html(data);
          // console.log(data.data3);

          
      }
  });

	};

</script>