<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 28/01/2016
 * Time: 00:25
 */

/*
 * This class manages Contexts created by users
 * @includes base.php
 */

include_once("base.php");
class Contexts extends base
{
    //This variables holds the array of context information after calculation

    /*
     * This function inserts contexts into their respective tables
     * @context string The set context of user
     * @return boolean Returns true when successful and false otherwise
     */
    public function insertContext( $context)
    {
        $sql = "insert into context_table set context = '$context'";


        return $this->query($sql);
    }

    /*
     * This function inserts keywords derived from subject into the mail_keywords table
     * @subject The subject of email object
     */
    public function insertIntoKeywords( $mailSubject){
        $keywordsId = array();
        $mailKeywords=stripslashes($mailSubject);
        // echo $mailKeywords;

        $keywords = preg_split("/[\s,]+/",$mailKeywords);

        //  print_r($keywords);

        foreach ( $keywords as $mailKeyword)
        {
            if ( strlen($mailKeyword)<3 )
            {
                unset($keywords[array_search($mailKeyword,$keywords)]);

            }
        }
        // print_r($keywords);
        $keywords = array_values($keywords);

        //  print_r($keywords);

        foreach ( $keywords as $insertMailKeyword)
        {
            $keywordSql = "insert into mail_keywords set keyword = '$insertMailKeyword'";

           if($this->query($keywordSql)){
               array_push($keywordsId,mysqli_insert_id($this->link));
           }
            else {
                //the keyword exists and thus, get the keyword id from its table
                array_push($keywordsId,$this->get_keyword_id($insertMailKeyword));
               // echo "trial in repeating the keyword" . $this->get_keyword_id($insertMailKeyword);
            }



        }

        return $keywordsId;
    }

    /*
     *This function inserts senders into their respective table
     * @mailSender string The sender of email object
     * @returns boolean Returns true when successful and false otherwise
     */

    public function insertIntoSender( $mailSender)
    {
        $sql = "insert into mail_sender set sender_address = '$mailSender'";

        $this->query($sql);

        return mysqli_insert_id($this->link);
    }

    public function test()
    {
        $sql = "insert into tester set test=189";

        return $this->query($sql);
    }
    /*
     * This function maps a keyword with its context and weight
     * @contextId int The context Id of context to be mapped with keyword
     * @keywordId int The keyword Id of keyword to be mapped with context
     * @returns boolean Returns true if successful and false otherwise
     */
    public function mapContextKeyword( $contextId,$keywordId)
    {
        $sql = "insert into keyword_context set cid = '$contextId', kid = '$keywordId', weight = 0.5";

        return $this->query($sql);
    }

    /*
     * This function maps a sender to context and respective weight is given
     * @mailSender int The mail sender Id to mapped with a context
     * @contextId int The context id to be mapped with mail sender
     * @returns boolean Returns true when successful and false otherwise
     */

    public function mapContextSender ( $mailSender, $contextId)
    {
      //  echo "I am coming right now";
      //  echo $mailSender.$contextId;
        $sql = "insert into sender_context set cid = '$contextId', sid = '$mailSender', weight = 0.5";

        return $this->query($sql);
    }

    /*
     * This function gets all the cid from context table
     * @returns boolean Returns true if successful and false otherwise
     */

    public function getAllContexts()
    {
        $sql = "select cid from context_table";
       // echo "just sent request for cids";
        return $this->query($sql);
    }

    /*
     * This function returns the context name given its id
     * @param $context_id The context id
     * @returns The context name if successful and false otherwise
     */

    public function get_context($context_id)
    {
        $sql = "select * from context_table where cid = '$context_id'";

        if($this->query($sql)) {
            $context_fetch = $this->fetch();
            $context_name = $context_fetch['context'];
            return $context_name;

        }

        return false;
    }

    public function getAllkeywords()
    {
        $sql = "select kid from mail_keywords";

        return $this->query($sql);
    }



    /*
     * This function inserts emails into a table with a specific context
     * @dbTable string The name of the table data is to be inserted into
     * @mailKeywords string The keywords of email object which falls in that context
     * @mailSender string The sender of the email object which falls in that context
     * @returns boolean It returns true if it successfully inserted into the table
     */
    public function insertIntoContext ($context,$mailKeywords, $mailSender)
    {
        $dbTableKeyword = $context.'_keywords';
        $dbTableSender = $context.'_sender';

       // $this->createContextTable($dbTableKeyword,$dbTableSender);

       // echo $mailKeywords;
       $mailKeywords=stripslashes($mailKeywords);
       // echo $mailKeywords;

        $keywords = preg_split("/[\s,]+/",$mailKeywords);

      //  print_r($keywords);

        foreach ( $keywords as $mailKeyword)
            {
                if ( strlen($mailKeyword)<3 )
                {
                    unset($keywords[array_search($mailKeyword,$keywords)]);

                }
            }
       // print_r($keywords);
        $keywords = array_values($keywords);

      //  print_r($keywords);

        foreach ( $keywords as $insertMailKeyword)
        {
            $keywordSql = "insert into ".$dbTableKeyword." set keyword = '$insertMailKeyword', weight = 0.5";

            $this->query($keywordSql);
        }

        $senderSql = "insert into ".$dbTableSender." set sender = '$mailSender', weight = 0.5";


        return $this->query($senderSql);
    }

    /*
     * This function checks for //keywords, sender and subject from the contectMatch table
     * @mailSender string The sender of email object
     * @mailSubject string The subject of email object
     * @mailBody string The body of email object
     * @
     */

    public function searchContext($mailSender, $mailSubject)
    {
        $databaseTables = $this->getDbTables();
        $senderWeight = null;
        $senderContext = null;
        $keyWordWeight = null;
        $keyWordContext = null;

        foreach ($databaseTables as $dbTable)
            {
               // echo $dbTable;
                //checking for table of senders and look for the sender in each of the table
                if(strpos($dbTable,"sender")!==false)
                {
                    $sqlSearch = "select * from $dbTable where sender='$mailSender'";

                    if($this->query($sqlSearch))
                    {
                        $row=$this->fetchArray();

                        while($row)
                        {
                           // echo $row[2].$dbTable;
                            $temp = $row[2]*0.4;
                            if($senderWeight!=null && $senderWeight<$temp)
                            {
                                $senderWeight = $temp;
                                $senderContext = $dbTable;
                            }
                            else{
                                $senderWeight = $temp;
                                $senderContext = $dbTable;
                            }

                            $row=$this->fetchArray();
                        }
                    }
                }
                //check all the tables of keywords for the given keywords
                else if(strpos($dbTable,"keywords")!==false)
                {
                    $keywords = preg_split("/[\s,]+/",$mailSubject);

                    foreach ( $keywords as $mailKeyword)
                    {
                        if ( strlen($mailKeyword)<3 )
                        {
                            unset($keywords[array_search($mailKeyword,$keywords)]);
                        }
                    }

                    $keywords = array_values($keywords);

                    foreach( $keywords as $keyWord)
                    {
                        $searchKeywordSql="select * from $dbTable where keyword = '$keyWord'";

                        if($this->query($searchKeywordSql))
                        {
                            $row=$this->fetchArray();

                            while($row)
                            {
                                $temp = $row[2]*0.6;
                                if($keyWordWeight!=null && $keyWordWeight<$temp)
                                {
                                    $keyWordWeight = $temp;
                                    $keyWordContext = $dbTable;
                                }
                                else if($keyWordWeight==null){
                                    $keyWordWeight = $temp;
                                    $keyWordContext = $dbTable;
                                }

                                $row=$this->fetchArray();
                            }
                        }
                    }
                }
            }
          //  echo $keyWordContext;
            $results = array($keyWordContext, $keyWordWeight, $senderContext, $senderWeight);
        //returns an array containing the context values and their weights
        return $results;
    }

    /*
     * Checks whether the subject email falls under any context in the database
     * @mailSubject The subject of mail object
     * @mailSender The Sender of mail object
     * @returns string Returns context of the mail object or null if not present
     */

    public function getKeywordContext ($mailSender, $mailSubject)
    {
        $contextResults = $this->searchContext($mailSender, $mailSubject);


        return $contextResults[0];
    }

    /*
    * Checks whether the sender email falls under any context in the database
    * @mailSubject The subject of mail object
    * @mailSender The Sender of mail object
    * @returns string Returns context of the mail object or null if not present
    */

    public function getSenderContext ($mailSender, $mailSubject)
    {
        $contextResults = $this->searchContext($mailSender, $mailSubject);

        return $contextResults[2];
    }

    /*
     * This function will get the contexts of sender from the database with associated weights
     * @mailSenderId The sender ID of the email object
     * @returns userContext object which contains context and weight
     *
     */
    /**
     * @param $mailSenderId
     * @return array
     */
    public function get_Sender_Contexts( $mailSenderId)
    {
        $context_weight_array=array();
        include_once("Cont_weight.php");

        $sql = "select * from sender_context where sid = '$mailSenderId'";

       if($this->query($sql)){
           $cont_weight=$this->fetch();
           while($cont_weight) {

               $object_cont_weight = new Cont_weight();

               $object_cont_weight->set_context_id($cont_weight['cid']);
               $object_cont_weight->set_context_weight($cont_weight['weight']);
			   array_push($context_weight_array,$object_cont_weight);

               $cont_weight=$this->fetch();
           }
       }
//        foreach($context_weight_array as $val){
//            if( $val instanceof Cont_weight){
//               // echo $val->get_context_id();
//                //echo $val->get_context_weight();
//            }
//
//        }
        return $context_weight_array;
    }

    /*
     * This function returns an array of contexts and corresponding weight of the keyword
     * @param keywordId This is the keyword Id in the database
     * @returns array Returns an array containing the contexts and weights of the keyword
     */

    public function get_Keyword_Context($keywordId)
    {
        include_once("Cont_weight.php");
        $keyword_array=array();

        $sql = "select * from keyword_context where kid = '$keywordId'";

        if ($this->query($sql)) {

            $keyword_info = $this->fetch();

            while ($keyword_info) {
                $keyword_cont_weight = new Cont_weight();

                $keyword_cont_weight->set_context_id($keyword_info['cid']);
               // echo $keyword_info['kid'];
                $keyword_cont_weight->set_context_weight($keyword_info['weight']);

                array_push($keyword_array,$keyword_cont_weight);

                $keyword_info = $this->fetch();
            }
        }

//        foreach($keyword_array as $val){
//            if( $val instanceof Cont_weight){
//                echo $val->get_context_id();
//                echo $val->get_context_weight();
//            }
//
//       }

        return $keyword_array;

    }


    /*
     * This function gets the sid of sender from the sender table given the sender name/address
     * @param sender The identity of email sender
     * @returns int sender id (sid) if successful and false otherwise
     */
    public function get_sender_id($sender)
    {
        $sql = "select * from mail_sender where sender_address = '$sender'";

        if ( $this->query($sql)) {

            $sid = $this->fetch();

            return $sid['sid'];
        }

    return false;
    }

    /*
     * This function gets the id of a keyword in the database given the keyword
     * @param keyword The keyword to get its kid from the database
     * @returns int Returns the kid of keyword from the database if successful and false otherwise
     */
    public function get_keyword_id($keyword)
    {
        $sql = "select * from mail_keywords where keyword = '$keyword'";

        if ( $this->query($sql)) {

            $kid = $this->fetch();

            return $kid['kid'];
        }

        return false;
    }

    public function get_context_weights(){

    }


}

