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
             // echo $this->get_sender_id($mailSender);
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