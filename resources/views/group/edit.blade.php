
<div class="kt-portlet__body">
	<div class="row">
         

		<div class="form-group col-lg-3">
			<label>Name </label>
			<input type="text" class="form-control" placeholder="Name" name="name" required value="{{$data->name}}">
		</div>
		
		<!--  <div class=" form-group col-lg-4">
            @include('layouts.status_checkbox',array('data' => $data->status))
        </div> -->
	</div>

	 <div class="col-sm-6" class="per_div" id="per_div">
                                    @php 
                                    $pc_checked = explode(',',$data->pc);
                                    $product_checked = explode(',',$data->product);
                                    $lead_checked = explode(',',$data->lead);
                                    $complain_checked = explode(',',$data->complain);
                                    $quot_checked = explode(',',$data->quot);
                                    $super_checked = explode(',',$data->super);
                                    $distri_checked = explode(',',$data->distri);
                                    $employee_checked = explode(',',$data->employee);
                                    $section_checked = explode(',',$data->section);
                                    $stage_checked = explode(',',$data->stage);
                                    $contact_checked = explode(',',$data->contact);
                                    $source_checked = explode(',',$data->source);
                                    $gst_checked = explode(',',$data->gst);
                                    $client_checked = explode(',',$data->client);
                                    $quotation_footer_checked = explode(',',$data->quotation_footer);
                                    $task_checked = explode(',',$data->task);
                                    $amc_checked = explode(',',$data->amc);
                                    $member_checked = explode(',',$data->member);
                                    $autoresponder_checked = explode(',',$data->autoresponder);
                                    $system_a_checked = explode(',',$data->system_a);
                                    $a_keyword_checked = explode(',',$data->a_keyword);
                                    $template_checked = explode(',',$data->template);
                                   
                                    @endphp

                                    <table class="table table-bordered" border="0" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th colspan="6">Permission List</th>
                                            </tr>
                                            <tr>
                                                <th style="width: 32%">Main Section</th>
                                                <th style="width: 32%">Path of Page</th>
                                              
                                                <th style="width: 1%"><i class="fa fa-eye"></i></th>
                                                <th style="width: 1%"><i class="fa fa-plus"></i></th>
                                                <th style="width: 1%"><i class="fa fa-edit"></i></th>
                                                <th style="width: 1%"><i class="fa fa-trash"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td rowspan="2">Product Master</td>
                                                <td>Product Category Master</td>
                                                
                                                <td><input type="checkbox" name="pc_view" value="1" @if($data->pc != '') @if($pc_checked[0] == 1) checked @endif @endif></td>
                                                <td><input type="checkbox" name="pc_add" value="1" @if($data->pc != '') @if($pc_checked[1] == 1) checked @endif @endif></td>
                                                <td><input type="checkbox" name="pc_edit" value="1" @if($data->pc != '') @if($pc_checked[2] == 1) checked @endif @endif></td>
                                                <td><input type="checkbox" name="pc_delete" value="1" @if($data->pc != '') @if($pc_checked[3] == 1) checked @endif @endif></td>
                                            </tr>
                                            <tr>
                                                <td>Product</td>
                                                
                                                <td><input type="checkbox" name="product_view" value="1" @if($data->product != '') @if($product_checked[0] == 1) checked @endif @endif></td>
                                                <td><input type="checkbox" name="product_add" value="1" @if($data->product != '') @if($product_checked[1] == 1) checked @endif @endif></td>
                                                <td><input type="checkbox" name="product_edit" value="1" @if($data->product != '') @if($product_checked[2] == 1) checked @endif @endif></td>
                                                <td><input type="checkbox" name="product_delete" value="1" @if($data->product != '') @if($product_checked[3] == 1) checked @endif @endif></td>
                                            </tr>
                                            <tr>
                                        	<td >Leads</td>
                                            <td></td>
                                           
                                            <td><input type="checkbox" name="lead_view" value="1" @if($data->lead != '') @if($lead_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="lead_add" value="1" @if($data->lead != '') @if($lead_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="lead_edit" value="1" @if($data->lead != '') @if($lead_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="lead_delete" value="1"  @if($data->lead != '') @if($lead_checked[3] == 1) checked @endif @endif></td>
                                           <!--  <td><input type="checkbox" name="suit_add" value="1"></td>
                                            <td><input type="checkbox" name="suit_edit" value="1"></td>
                                            <td><input type="checkbox" name="suit_delete" value="1"></td> -->
                                        </tr>
                                         <tr>
                                        	<td >Complain</td>
                                            <td></td>
                                           
                                            <td><input type="checkbox" name="complain_view" value="1"  @if($data->complain != '') @if($complain_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="complain_add" value="1" @if($data->complain != '') @if($complain_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="complain_edit" value="1" @if($data->complain != '') @if($complain_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="complain_delete" value="1" @if($data->complain != '') @if($complain_checked[3] == 1) checked @endif @endif></td>
                                           <!--  <td><input type="checkbox" name="suit_add" value="1"></td>
                                            <td><input type="checkbox" name="suit_edit" value="1"></td>
                                            <td><input type="checkbox" name="suit_delete" value="1"></td> -->
                                        </tr>
                                        <tr>
                                            <td >Member</td>
                                            <td></td>
                                            
                                            <td><input type="checkbox" name="member_view" value="1" @if($data->member != '') @if($member_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="member_add" value="1" @if($data->member != '') @if($member_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="member_edit" value="1" @if($data->member != '') @if($member_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="member_delete" value="1" @if($data->member != '') @if($member_checked[3] == 1) checked @endif @endif></td>
                                           <!--  <td><input type="checkbox" name="suit_add" value="1"></td>
                                            <td><input type="checkbox" name="suit_edit" value="1"></td>
                                            <td><input type="checkbox" name="suit_delete" value="1"></td> -->
                                        </tr>
                                        </tr>

                                         <tr>
                                        	<td >Quotation Master</td>
                                            <td>Quotation Master</td>
                                            
                                            <td><input type="checkbox" name="quot_view" value="1"  @if($data->quot != '') @if($quot_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="quot_add" value="1" @if($data->quot != '') @if($quot_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="quot_edit" value="1" @if($data->quot != '') @if($quot_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="quot_delete" value="1" @if($data->quot != '') @if($quot_checked[3] == 1) checked @endif @endif></td>
                                           <!--  <td><input type="checkbox" name="suit_add" value="1"></td>
                                            <td><input type="checkbox" name="suit_edit" value="1"></td>
                                            <td><input type="checkbox" name="suit_delete" value="1"></td> -->
                                        </tr>

                                         <tr>
                                            <td >AMC Maintenance</td>
                                            <td></td>
                                            
                                            <td><input type="checkbox" name="amc_view" value="1"  @if($data->amc != '') @if($amc_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="amc_add" value="1"  @if($data->amc != '') @if($amc_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="amc_edit" value="1"  @if($data->amc != '') @if($amc_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="amc_delete" value="1"  @if($data->amc != '') @if($amc_checked[3] == 1) checked @endif @endif></td>
                                           <!--  <td><input type="checkbox" name="suit_add" value="1"></td>
                                            <td><input type="checkbox" name="suit_edit" value="1"></td>
                                            <td><input type="checkbox" name="suit_delete" value="1"></td> -->
                                        </tr>
                                         <tr>
                                            <td rowspan="4">Communication</td>
                                            <td>Autoresponder</td>
                                            
                                            <td><input type="checkbox" name="autoresponder_view" value="1" @if($data->autoresponder != '') @if($autoresponder_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="autoresponder_add" value="1" @if($data->autoresponder != '') @if($autoresponder_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="autoresponder_edit" value="1" @if($data->autoresponder != '') @if($autoresponder_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="autoresponder_delete" value="1" @if($data->autoresponder != '') @if($autoresponder_checked[3] == 1) checked @endif @endif></td>
                                        </tr>
                                        <tr>
                                            <td>System Autoresponder</td>
                                            
                                            <td><input type="checkbox" name="system_a_view" value="1"  @if($data->system_a != '') @if($system_a_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="system_a_add" value="1" @if($data->system_a != '') @if($system_a_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="system_a_edit" value="1" @if($data->system_a != '') @if($system_a_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="system_a_delete" value="1" @if($data->system_a != '') @if($system_a_checked[3] == 1) checked @endif @endif></td>
                                        </tr>

                                         <tr>
                                            <td>Autoresponder Keyword</td>
                                            
                                            <td><input type="checkbox" name="a_keyword_view" value="1" @if($data->a_keyword != '') @if($a_keyword_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="a_keyword_add" value="1" @if($data->a_keyword != '') @if($a_keyword_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="a_keyword_edit" value="1" @if($data->a_keyword != '') @if($a_keyword_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="a_keyword_delete" value="1" @if($data->a_keyword != '') @if($a_keyword_checked[3] == 1) checked @endif @endif></td>
                                        </tr>
                                         <tr>
                                            <td>Templates</td>
                                            
                                            <td><input type="checkbox" name="template_view" value="1" @if($data->template != '') @if($template_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="template_add" value="1" @if($data->template != '') @if($template_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="template_edit" value="1" @if($data->template != '') @if($template_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="template_delete" value="1" @if($data->template != '') @if($template_checked[3] == 1) checked @endif @endif></td>
                                        </tr>
                                         <tr>
                                            <td rowspan="11">Master</td>
                                            <td>Supervisor Master</td>
                                            
                                            <td><input type="checkbox" name="super_view" value="1" @if($data->super != '') @if($super_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="super_add" value="1"  @if($data->super != '') @if($super_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="super_edit" value="1"  @if($data->super != '') @if($super_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="super_delete" value="1"  @if($data->super != '') @if($super_checked[3] == 1) checked @endif @endif></td>
                                        </tr>
                                         <tr>
                                            <td>Contact Master</td>
                                           
                                            <td><input type="checkbox" name="contact_view" value="1" @if($data->contact != '') @if($contact_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="contact_add" value="1" @if($data->contact != '') @if($contact_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="contact_edit" value="1" @if($data->contact != '') @if($contact_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="contact_delete" value="1" @if($data->contact != '') @if($contact_checked[3] == 1) checked @endif @endif></td>
                                        </tr>
                                        <tr>
                                            <td>Employee Master</td>
                                            
                                            <td><input type="checkbox" name="employee_view" value="1"  @if($data->employee != '') @if($employee_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="employee_add" value="1"  @if($data->employee != '') @if($employee_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="employee_edit" value="1"  @if($data->employee != '') @if($employee_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="employee_delete" value="1"  @if($data->employee != '') @if($employee_checked[3] == 1) checked @endif @endif></td>
                                        </tr>
                                         <tr>
                                            <td>Distributor </td>
                                            
                                            <td><input type="checkbox" name="distri_view" value="1"  @if($data->distri != '') @if($distri_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="distri_add" value="1" @if($data->distri != '') @if($distri_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="distri_edit" value="1" @if($data->distri != '') @if($distri_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="distri_delete" value="1" @if($data->distri != '') @if($distri_checked[3] == 1) checked @endif @endif></td>
                                        </tr>

                                         <tr>
                                            <td>Section </td>
                                            
                                            <td><input type="checkbox" name="section_view" value="1" @if($data->section != '') @if($section_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="section_add" value="1" @if($data->section != '') @if($section_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="section_edit" value="1" @if($data->section != '') @if($section_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="section_delete" value="1" @if($data->section != '') @if($section_checked[3] == 1) checked @endif @endif></td>
                                        </tr>

                                         <tr>
                                            <td>Lead Stage </td>
                                            
                                            <td><input type="checkbox" name="stage_view" value="1" @if($data->stage != '') @if($stage_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="stage_add" value="1"  @if($data->stage != '') @if($stage_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="stage_edit" value="1"  @if($data->stage != '') @if($stage_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="stage_delete" value="1"  @if($data->stage != '') @if($stage_checked[3] == 1) checked @endif @endif></td>
                                        </tr>

                                         <tr>
                                            <td>Lead Source </td>
                                            
                                            <td><input type="checkbox" name="source_view" value="1"  @if($data->source != '') @if($source_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="source_add" value="1" @if($data->source != '') @if($source_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="source_edit" value="1" @if($data->source != '') @if($source_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="source_delete" value="1" @if($data->source != '') @if($source_checked[3] == 1) checked @endif @endif></td>
                                        </tr>

                                         <tr>
                                            <td>GST </td>
                                            
                                            <td><input type="checkbox" name="gst_view" value="1" @if($data->gst != '') @if($gst_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="gst_add" value="1" @if($data->gst != '') @if($gst_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="gst_edit" value="1" @if($data->gst != '') @if($gst_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="gst_delete" value="1" @if($data->gst != '') @if($gst_checked[3] == 1) checked @endif @endif></td>
                                        </tr>

                                         <tr>
                                            <td>Client Master </td>
                                            
                                            <td><input type="checkbox" name="client_view" value="1" @if($data->client != '') @if($client_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="client_add" value="1" @if($data->client != '') @if($client_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="client_edit" value="1" @if($data->client != '') @if($client_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="client_delete" value="1" @if($data->client != '') @if($client_checked[3] == 1) checked @endif @endif></td>
                                        </tr>

                                         <tr>
                                           
                                            <td>Quotation Footer </td>
                                            
                                            <td><input type="checkbox" name="footer_view" value="1" @if($data->quotation_footer != '') @if($quotation_footer_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="footer_add" value="1" @if($data->quotation_footer != '') @if($quotation_footer_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="footer_edit" value="1" @if($data->quotation_footer != '') @if($quotation_footer_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="footer_delete" value="1" @if($data->quotation_footer != '') @if($quotation_footer_checked[3] == 1) checked @endif @endif></td>
                                        </tr>

                                        <tr>
                                            
                                            <td>Task Master </td>
                                            <td><input type="checkbox" name="task_view" value="1"  @if($data->task != '') @if($task_checked[0] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="task_add" value="1"  @if($data->task != '') @if($task_checked[1] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="task_edit" value="1"  @if($data->task != '') @if($task_checked[2] == 1) checked @endif @endif></td>
                                            <td><input type="checkbox" name="task_delete" value="1"  @if($data->task != '') @if($task_checked[3] == 1) checked @endif @endif></td>
                                        </tr>
                                            
                                           
                                        </tbody>
                                    </table>
                                </div>
</div>
<script type="text/javascript">
	
   
</script>