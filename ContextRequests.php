<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 30/01/2016
 * Time: 23:41
 */
/*
 * This class receives request from the client and transacts according to requests
 * @includes Context.php
 */
include_once("Contexts.php");

class ContextRequests extends Contexts
{
    /*
     * This function adds user context to its table in the database
     * @return boolean Returns true if successful and false otherwise
     */
    public function insertIntoContextRequest()
    {
        if (isset($_REQUEST['context'])) {
            $context = $_REQUEST['context'];

            $this->insertContext($context);

            return true;
        }

        return false;
    }
    /*
     * This function inserts mail subject and sender into respective database tables
     * It then maps them in the context table
     * @returns boolean It returns true if the insertion was successful and false otherwise
     */
    public function insertIntoDatabaseRequest()
    {

      if (isset($_REQUEST['mailSubject']) && isset($_REQUEST['mailSender'])) {
          $mailKeywords = $_REQUEST['mailSubject'];
          $mailSender = $_REQUEST['mailSender'];

          $mailSenderId = $this->insertIntoSender($mailSender);
          $contextIds = $this->getContextIds();
          //echo "senderId".$mailSenderId. "sender";
          if ($mailSenderId==0) {
           //  echo "The sender already exists";
               $this->get_Sender_Contexts($this->get_sender_id($mailSender));
          }
          else {
              foreach($contextIds as $cid) {
                  $this->mapContextSender($mailSenderId,$cid);
              }
          }

          $mailKeywordsIds = $this->insertIntoKeywords($mailKeywords);
          //if($mailKeywordsIds)

          foreach ( $mailKeywordsIds as $mailKeywordId) {
              foreach($contextIds as $cid) {
                 $this->mapContextKeyword($cid,$mailKeywordId);
              }
          }

        return true;
      }
        return false;
    }

    /*
     * This function returns the context of given keywords
     * @returns string context The context of given keyword and false otherwise
     */
    public function searchKeywordContextRequest()
    {
        if (isset($_REQUEST['mailSubject']) && isset($_REQUEST['mailSender'])) {

            $mailKeywords = $_REQUEST['mailSubject'];
            $mailSender = $_REQUEST['mailSender'];

            echo $this->getKeywordContext($mailSender,$mailKeywords);
        }

        return false;
    }

    /*
     * This function returns the context an email given the user
     * @returns string The context of email sender and false otherwise
     */
    public function searchSenderContextRequest()
    {
        if (isset($_REQUEST['mailSubject']) && isset($_REQUEST['mailSender'])) {

            $mailKeywords = $_REQUEST['mailSubject'];
            $mailSender = $_REQUEST['mailSender'];

            echo $this->getSenderContext($mailSender,$mailKeywords);
        }

        return false;
    }

    /*
     * This function gets all the contexts available for the user
     * @returns array It returns an array of context Ids for the user
     */
    public function getContextIds(){
        $contextArray= array();

       if ( $this->getAllContexts())
       {
           $contextIds  = $this->fetch();

           while ( $contextIds) {
                array_push($contextArray,$contextIds['cid']);

               $contextIds = $this->fetch();
           }
       }

        return $contextArray;
    }

    /*
     * This function will analyse the context of a given email
     * @param SenderId The sender id of email object
     * @param keywords[] The array of keywords in the email object subject
     * @returns cid It returns the context id of email object
     */
    public function get_email_context($senderId, $keywordsIds)
    {
        include_once("cont_keyword_weight.php");
        include_once("keywordId_weight.php");
        $cont_ids = $this->getContextIds();
        $cont_keyword_weight_array = array();

        foreach ($cont_ids as $contId) {
            $cont_keyword_weight_object = new cont_keyword_weight();
            //set the context id of that object
            $cont_keyword_weight_object->set_contextId($contId);
            //put the object in an array
            array_push($cont_keyword_weight_array,$cont_keyword_weight_object);
        }

        //this will return an array of cont_weight objects
        $sender_contexts = $this->get_Sender_Contexts($senderId); //work on it later after completing the keywords

        //for each keyword
        foreach ( $keywordsIds as $value) {
            //This will return an array of cont_weight for the keyword
           $cont_weight_keywords = $this->get_Keyword_Context($value);
            foreach ($cont_weight_keywords as $weight_keyword)
            {
                //Cont_weight holds cid and weight of a keyword
                if ( $weight_keyword instanceof Cont_weight) {
                    //get the context id of this keyword instance and its weight
                    $keyword_contextId = $weight_keyword->get_context_id();
                    $keyword_weight = $weight_keyword->get_context_weight();
                    //Search through the array of cont_keyword_weight objects for the index of that context id
                    $cont_id_index = array_search($keyword_contextId,$cont_keyword_weight_array);
                    //get the object at that index
                    $context_keyword_weight_obj = $cont_keyword_weight_array[$cont_id_index];

                    if ($context_keyword_weight_obj instanceof cont_keyword_weight) {
                        //set the context Id of that object
                       // $context_keyword_weight_obj->set_contextId($cont_id_index);
                        //create a keywordId_weight object
                        $kid_weight = new keywordId_weight();
                        //set its kid which is the id of this keyword
                        $kid_weight->set_kid($value);
                        //set the weight of this keyword
                        $kid_weight->set_weight($keyword_weight);
                        //add the keyword_weight object in the cont_keyword_weight kid_weight_array
                        $context_keyword_weight_obj->add_kid_weight($kid_weight);
                    }
                }
            }


        }
    }


    /*
     * The methods below are for testing this application
     */

    public function get_context_sender()
    {

        if(isset($_REQUEST['sid'])){

            $sender = $_REQUEST['sid'];
          //  echo $sender;
            $cont_infos = $this->get_Sender_Contexts($sender);

            foreach($cont_infos as $value){

                if($value instanceof Cont_weight){
                    echo $value->get_context_id();
                }


            }

        }
    }

    public function get_context_keyword()
    {
        if(isset($_REQUEST['kid'])){

            $kid = $_REQUEST['kid'];
            //  echo $sender;
            $cont_infos = $this->get_Keyword_Context($kid);

            foreach($cont_infos as $value){

                if($value instanceof Cont_weight){
                    echo $value->get_context_id();
                    echo $value->get_context_weight();
                }


            }

        }
    }


}


$request = new ContextRequests();

if (isset($_REQUEST['cmd'])) {
    $cmd = $_REQUEST['cmd'];

    switch($cmd)
    {
        case 1:
            $request->insertIntoContextRequest();
            break;

        case 2:
            $request->searchSenderContextRequest();
            break;
        case 3:
            $request->searchKeywordContextRequest();
            break;
        case 4:
            $request->insertIntoDatabaseRequest();
            break;
        case 6:
            $request->get_context_keyword();
            break;
        case 7:
            $request->get_context_sender();
            break;
        default:
            echo "No command was given";
    }
}