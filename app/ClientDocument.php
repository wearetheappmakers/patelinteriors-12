<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'client_document';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
   protected $appends = ['image_full_path','image_pan','image_cancel_cheque','image_rtgs','image_submission','image_delivered','image_gst'];

    public function getImageFullPathAttribute()
    {
        if(empty($this->aadhar_img)){
            return '';
        }
        return asset('aadharimage/'.$this->aadhar_img);
    }

    public function getImagePanAttribute()
    {
    	if(empty($this->pan_img)){
    		return '';
    	}
    	return asset('panimage/'.$this->pan_img);
    }
    public function getImageCancelChequeAttribute()
    {
    	if(empty($this->cancelledcheque_img)){
    		return '';
    	}
    	return asset('cancelledchequeimage/'.$this->cancelledcheque_img);
    }
    public function getImageRtgsAttribute()
    {
    	if(empty($this->rtgs_img)){
    		return '';
    	}
    	return asset('rtgsimage/'.$this->rtgs_img);
    }
    public function getImageSubmissionAttribute()
    {
    	if(empty($this->form_submission_img)){
    		return '';
    	}
    	return asset('formsubmissionimage/'.$this->form_submission_img);
    }

    public function getImageDeliveredAttribute()
    {
    	if(empty($this->machine_delivered_img)){
    		return '';
    	}
    	return asset('machinedeliveredimage/'.$this->machine_delivered_img);
    }
     public function getImageGstAttribute()
    {
    	if(empty($this->gst_img)){
    		return '';
    	}
    	return asset('gstimage/'.$this->gst_img);
    }
}
