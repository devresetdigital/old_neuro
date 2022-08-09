<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;
use Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\CreativeBlocked;
use App\Creative;
use App\Organization;
use App\Concept;
use App\Campaign;
use App\User;

class MailerHelper
{
    


static function creativeBlocked($id) 
{
    $creative = Creative::where('id', $id)->first();

    $concept = Concept::where('id',$creative->concept_id)->first();

    $emails = [];
    $system_email=explode(',' ,$_ENV['TMT_EMAIL_NOTIFICATION'] );
    foreach ($system_email as $key => $email) {
        $emails[$email] = $email;
    }

    if($concept != null){
        $campaigns = [];
        foreach($concept->Strategies as $strategy){
            $campaigns[$strategy->campaign_id] =$strategy->campaign_id;
        }
     
        if($campaigns != []){
            $campaigns = Campaign::whereIn('id',$campaigns)->get();
            /*array*/$users_id = $campaigns->pluck('user_id')->toArray();
            $users = User::whereIn('id',$users_id)->get();
      
            foreach ($users as $key => $user) {
                $emails[$user->email] = $user->email;
            }
      
            $organization_ids = $users->pluck('organization_id')->toArray();
    
            $organizations = Organization::whereIn('id',$organization_ids)->get();
      
            foreach ($organizations as $key => $organization) {
                if($organization->email){
                    $emails[$organization->email] = $organization->email;
                }
                if($organization->quality_contact_email){
                    $emails[$organization->quality_contact_email] = $organization->quality_contact_email;
                }
            }
        }
    }

    $data = [
        'url'=>$_ENV['APP_URL'].'/admin/creatives/'.$creative->id.'/edit',
        'creative_name'=>$creative->name,
        'description'=>'Possible fraud'
    ];

    return self::send_email($emails,$data,'App\Mail\CreativeBlocked');
 
}



static function send_email($to,$data,$template='CreativeBlocked')
{
    try {
        Mail::to($to)->send(new $template($data));
    } catch (\Throwable $th) {
        return false;
    }
    return true;
}

}
