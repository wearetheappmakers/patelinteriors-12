<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\ContactMaster;
use App\LeadSource;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ContactImport implements ToModel,WithHeadingRow,WithValidation
{

    public function model(array $row)
    {
    	//categoryname
    	$contact_category = LeadSource::where('name',$row['contactcategory'])->first();
    	if(isset($contact_category))
    	{
    		$contact_category = $contact_category->id;
    	}else{
    		$cat = new LeadSource();
    		$cat->name = $row['contactcategory'];
    		$cat->save();

    		$contact_category = $cat->id;
    	}
    	// $product = Product::where('model_code',$row['modelcode'])->first();

    	// if(isset($product))
    	// {
     //        $product->name = $row['productname'];
    	// 	$product->star_rank = $row['star_rank'];
    	// 	$product->category_id = $category;
    	// 	$product->description = $row['description'];
    	// 	$product->maximum_price = $row['maximum_price'];
    	// 	$product->dealer_N = $row['dealer_n'];
    	// 	$product->inverter_pcb = $row['inverter_pcb'];
    	// 	$product->compers_warranty = $row['compers_warranty'];
    	// 	$product->insutallation_support = $row['installation_support'];
    	// 	$product->meter_pipe_kit = $row['meter_pipe_kit'];
     //        $product->save();

    	// }else{
    		$contact = new ContactMaster();
    		$contact->contact_id = $contact_category;
    		$contact->name = $row['name'];
    		$contact->company = $row['company'];
    		$contact->mobile_no = $row['mobileno'];
    		$contact->email = $row['email'];
    		$contact->address = $row['address'];
    		
    		
    		$contact->save();
    	}

    

     public function rules(): array
    {
        return [
            
           
           
           
             
        ];
    }   
}
