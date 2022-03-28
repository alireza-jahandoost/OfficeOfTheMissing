<?php

namespace App\Actions;

class ShowToLoserAndFinderAreValid
{
    public function check(array $propertyTypes):string|null{
        if(!$this->samePropertyBetweenLoserAndFinderExists($propertyTypes)){
            return "حداقل باید یک فیلد مشترک بین گم کننده و پیدا کننده وجود داشته باشد.";
        }
        if(!$this->thereMustNotBeAPropertyTypeInvisibleToBothSides($propertyTypes)){
            return "فیلدی که نه برای گم کننده است و نه برای پیدا کننده غیر مجاز است";
        }

        return null;
    }

    protected function thereMustNotBeAPropertyTypeInvisibleToBothSides(array $propertyTypes): bool{
        $check = true;

        foreach($propertyTypes as $propertyType){
            if(!$propertyType['show_to_loser'] && !$propertyType['show_to_finder']){
                $check = false;
                break;
            }
        }

        return $check;
    }

    protected function samePropertyBetweenLoserAndFinderExists(array $propertyTypes): bool{
         $check = false;

         foreach($propertyTypes as $propertyType){
             if($propertyType['show_to_loser'] && $propertyType['show_to_finder']){
                 $check = true;
                 break;
             }
         }

         return $check;
    }
}
