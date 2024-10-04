<?php

namespace App\Util;

use App\Models\SearchEvent;

class SearchApiEvent
{
    public function searchEvent(SearchEvent $searchEvent):array
    {
        $url="https://public.opendatasoft.com/api/records/1.0/search/?dataset=evenements-publics-openagenda";
        $content=file_get_contents($url.'&location_city='.ucfirst($searchEvent->city)
        .'&refine.firstdate_begin='.$searchEvent->dateEvent->format('Y-m-d'));
        //$content=file_get_contents($url.'&location_city=rennes&refine.firstdate_begin=2024-03-02);
        //dd(json_decode($content,true));
        if($content===false){
            return[];
        }else{
            return json_decode($content,true);
        }
    }
}