<?php

namespace App\Actions;

class TypesOfValueTypesAreValid
{
    protected $acceptedTypesForBoth = [
        'text'
    ];

    public function check(array $propertyTypes):null|string
    {
        foreach ($propertyTypes as $propertyType){
            if($propertyType['show_to_loser'] && $propertyType['show_to_finder']){
                if(!in_array($propertyType['value_type'], $this->acceptedTypesForBoth)){
                    return 'ویژگی های بین گم کننده و پیدا کننده باید از نوع متن باشند';
                }
            }
        }
        return null;
    }
}
