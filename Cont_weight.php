<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 10/02/2016
 * Time: 22:34
 */

/*
 * This class will hold the context of a variable and weight associated with it
 *
 */
class Cont_weight
{
    /*
     * The global variables of a context_weight object
     */
	private $contextId=0;
	private $weight=0;
    /*
     * This is the constructor of this class
     * @contextId The context Id
     * @weight The weight corresponding to that context
     */

    public function  _construct($contextId,$weight)
    {

        $this->set_context_id($contextId);
        $this->set_context_weight($weight);
    }
    /*
     * This function sets the context id of this object
     * @id This is the id of the context involved
     */
    public function set_context_id($id)
    {
        $this->contextId = $id;
    }

    /*
     *This function returns the context id of this object
     * @returns int The id of object context
     */
    public function get_context_id()
    {
        return $this->contextId;
    }

    /*
     * This sets the weight of this object
     * @weight The weight of this object
     */
    public function set_context_weight($weight)
    {
        $this->weight = $weight;
    }

    /*
     * This function returns the weight of this object
     *@returns double The weight of this object
     */
    public function get_context_weight()
    {
        return $this->weight;
    }



}