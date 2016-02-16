<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 15/02/2016
 * Time: 23:20
 */
class keywordId_weight
{
    private $kid;
    private $weight;

    public function set_kid($kid)
    {
        $this->kid = $kid;
    }

    public function get_kid()
    {
        return $this->kid;
    }

    public function set_weight($weight)
    {
        $this->weight = $weight;
    }

    public function get_weight()
    {
        return $this->weight;
    }
}