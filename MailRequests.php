<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 27/01/2016
 * Time: 22:57
 */

/*
 * This class receives request from an email plugin and processes to the right
 * function for execution
 * @includes Mail.php which contains all the database transactions method.
 */
include_once("Mail.php");
class MailRequests extends Mail
{
    /*
     * This function receives data from a request and sends it to insertMail function of
     * Mail.php for insertion
     * @returns boolean It returns true if insertion request was successful and false otherwise
     */
    public function insertIntoMail()
    {
        if( isset($_REQUEST['mailSender']) && isset($_REQUEST['mailSender']) && isset($_REQUEST['mailBody']))
        {

            $mailSender = $_REQUEST['mailSender'];
            $mailSubject = $_REQUEST['mailSubject'];
            $mailBody = $_REQUEST['mailBody'];



            return $this->insertMail($mailSender, $mailSubject, $mailBody);
        }


        return false;
    }

    /*
     * This function receives a deletion data from a request and updates the
     * database according using the deleteMail function of Mail.php
     * @return It returns true if it was successful in deleting and false otherwise
     */

    public function deleteFromMail()
    {
        //There is no definite way to know which email to delete.
        //Therefore, I will have to come back to this when needed.
    }
}

$request = new MailRequests();

if ( isset($_REQUEST["cmd"])) {

    $cmd = $_REQUEST["cmd"];

    switch ($cmd) {

        case 1:
            $request->insertIntoMail();
            break;
        case 2:

            break;
        default:
            echo "No command was given for this request";
    }
}