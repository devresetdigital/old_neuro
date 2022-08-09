<?php

namespace App\Http\Helpers;

use App\Creative;
use App\Concept;
use App\Strategy;
use App\Campaign;

use Illuminate\Support\Facades\Log;

class CloneHelper
{

    /**
     * Recives a creative to ve cloned, returns the new model
     */
    static public function cloneCreative(Creative $item){
        try {
            $clone = $item->replicate();
            $clone->status = 0;
            $clone->name = $item->name . " - (copy)";
            $clone->push();

            foreach($item->CreativeAttributes as $att)
            {
                $attribute = $att->replicate();
                $attribute->creative_id = $clone->id;
                $attribute->push();
            }
            foreach($item->CreativeDisplays as $att)
            {
                $attribute = $att->replicate();
                $attribute->creative_id = $clone->id;
                $attribute->push();
            }
            foreach($item->CreativeLanguages as $att)
            {
                $attribute = $att->replicate();
                $attribute->creative_id = $clone->id;
                $attribute->push();
            }
            foreach($item->CreativeVideos as $att)
            {
                $attribute = $att->replicate();
                $attribute->creative_id = $clone->id;
                $attribute->push();
            }

            return ['status'=>true,"message" =>"success", "data"=>$clone];
        
        }  catch (\Exception $e) {
            dd($e->getMessage());
            Log::info($e->getMessage());
            return ['status'=>FALSE,"message" =>"there was an error trying to clonning the creative", "log" =>$e->getMessage()];
        }
    }

    /**
     * Recives a concept to ve cloned, returns the new model
     */
    static public function cloneConcept(Concept $item){
        try {
            $clone = $item->replicate();
            $clone->name = $item->name . " - (copy)";
            $clone->push();


            foreach($item->Creatives as $att)
            {
               $result = self::cloneCreative($att);
               if($result['status']){
                  $creative = $result['data'];
                  $creative->concept_id =$clone->id;
                  $creative->save(); 
               }else{
                    Log::info('there was an error trying to clonning the creative for concept' . $item->id);
                    return ['status'=>FALSE,"message" =>"there was an error trying to clonning the creative"];
               }
            }

            return ['status'=>true,"message" =>"success", "data"=>$clone];
        
        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return ['status'=>FALSE,"message" =>"there was an error trying to clonning the concept", "log" =>$e->getMessage()];
        }
    }

    /**
     * Recives a strategy to ve cloned, returns the new model
     */
    static public function cloneStrategy(Strategy $item){
        try {
            $clone = $item->replicate();
            $clone->status = 0;
            $clone->name = $item->name . " - (copy)";
            $clone->push();

            foreach($item->StrategyConcept as $att)
            {
                $attribute = $att->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            
            foreach($item->StrategiesPmp as $att)
            {
                $attribute = $att->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }

            foreach($item->StrategiesSitelist as $att)
            {
                $attribute = $att->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }

            foreach($item->StrategiesIplist as $att)
            {
                $attribute = $att->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }

            foreach($item->StrategiesZiplist as $att)
            {
                $attribute = $att->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }

            foreach($item->StrategiesSsp as $att)
            {
                $attribute = $att->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }

            if($item->StrategiesLang != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesLang->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            
            if($item->StrategiesGeofencing!= null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesGeofencing->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            if($item->StrategiesLocationsCity != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesLocationsCity->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            
            if( $item->StrategiesLocationsCountry != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesLocationsCountry->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            if($item->StrategiesLocationsRegion != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesLocationsRegion->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }

            if($item->StrategiesTechnologiesBrowser != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesTechnologiesBrowser->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            if($item->StrategiesTechnologiesDevice != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesTechnologiesDevice->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }

            if($item->StrategiesTechnologiesIsp != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesTechnologiesIsp->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            
            if($item->StrategiesTechnologiesOs != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesTechnologiesOs->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            
            if($item->StrategiesDataPixel != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesDataPixel->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            if($item->StrategiesInventoryType != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesInventoryType->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            
            if($item->StrategiesSegment != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesSegment->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }
            
            if($item->StrategiesCustomData != null ){
                //HAS ONE RELATIONSHIP
                $attribute = $item->StrategiesCustomData->replicate();
                $attribute->strategy_id = $clone->id;
                $attribute->push();
            }

            return ['status'=>true,"message" =>"success", "data"=>$clone];
        
        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return ['status'=>FALSE,"message" =>"there was an error trying to clonning the strategy", "log" =>$e->getMessage()];
        }
    }



     /**
     * Recives a campaign to ve cloned, returns the new model
     */
    static public function cloneCampaign(Campaign $item){
        try {
            $clone = $item->replicate();
            $clone->status = 0;
            $clone->name = $item->name . " - (copy)";
            $clone->push();

            foreach($item->CampaignsBudgetFlights as $att)
            {
                $attribute = $att->replicate();
                $attribute->campaign_id = $clone->id;
                $attribute->push();
            }

            foreach($item->Strategies as $att)
            {
               $result = self::cloneStrategy($att);
               if($result['status']){
                  $strategy = $result['data'];
                  $strategy->campaign_id =$clone->id;
                  $strategy->save(); 
               }else{
                    Log::info('there was an error trying to clonning the strategy for campaign' . $item->id);
                    return ['status'=>FALSE,"message" =>"there was an error trying to clonning the campaign"];
               }
            }

            return ['status'=>true,"message" =>"success", "data"=>$clone];
        
        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return ['status'=>FALSE,"message" =>"there was an error trying to clonning the campaign", "log" =>$e->getMessage()];
        }
    }



}
