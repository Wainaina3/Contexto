<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 15/02/2016
 * Time: 23:18
 */
class cont_keyword_weight
{
    private $contextId;
    private $kid_weight_array = array();
    private $average_weight = 0;

    public function set_contextId($contextId)
    {
        $this->contextId = $contextId;
    }

    public function get_context_id()
    {
        return $this->contextId;
    }

    public function add_kid_weight($kid_weight){
        echo "In the cont_kid class";
       // print_r($kid_weight);
        array_push($this->kid_weight_array,$kid_weight);
       // print_r($this->kid_weight_array);

    }

    public function get_kid_weight_array()
    {
        return $this->kid_weight_array;
    }

    public function set_kid_weight_average($avg_weight)
    {
        $this->average_weight = $avg_weight;
    }
    public function get_kid_weight_average()
    {
        return $this->average_weight;
    }

}