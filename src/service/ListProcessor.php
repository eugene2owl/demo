<?php

declare(strict_types = 1);

namespace Demo\Service;

class ListProcessor
{
    public function distributeLists(array $lists): array
    {
        $listOfLists = [];
        foreach ($lists as $listElement){
            $listOfLists[$listElement["name"]][] = $listElement;
        }
        return $listOfLists;
    }
}