<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../repository/Contents.php";

use Demo\Repository\Contents as ContentsRepo;

class ListProcessor
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ContentsRepo();
    }

    public function clarifyLists(array $lists): array
    {
        foreach ($lists as $number => $list) {
            $lists[$number] = $this->clarifyList($list);
        }
        return $lists;
    }

    public function clarifyList(array $list): array
    {
        return [
            "list" => $list,
            "elements" => $this->repository->getSpouses($list["name"], "list", "element")
        ];
    }

    public function clarifyCodesLists(array $codes): array
    {
        foreach ($codes as $codeNumber => $code) {
            $codes[$codeNumber]["lists"] =  $this->clarifyLists($codes[$codeNumber]["lists"]);
        }
        return $codes;
    }
}