<?php

namespace App\Search;

class SearchArticle 
{
    protected $filtrerParCategorie;

    public function getFiltrerParCategorie()
    {
        return $this->filtrerParCategorie;
    }

    public function setFiltrerParCategorie($filtrerParCategorie)
    {
        $this->filtrerParCategorie = $filtrerParCategorie;
        return $this;
    }
}