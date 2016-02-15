<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 27/01/2016
 * Time: 22:32
 */
/*
 * This class connects and implements all the transactions for the mail database.
 * @includes base.php which contains all the database connections and manipulation
 */

include_once("base.php");
class Mail extends base
{

    /*
     * This function inserts the mail item object in the database.
     * @mailSender Contains the sender of the mail object.
     * @mailSubject string Contains the subject of the mail object.
     * @mailBody string Contains the body os the mail object.
     * @return boolean Returns true if the insertion was successful and false it was not successful.
     */

    public function insertMail( $mailSender, $mailSubject, $mailBody)
    {
        $sql = "insert into mail_database set mail_sender = '$mailSender', mail_subject = '$mailSubject', mail_body = '$mailBody'";

        return $this->query($sql);
    }

    /*
     * This function updates a mail item in the database
     * @mailSender Contains the sender of the mail object.
     * @mailSubject string Contains the subject of the mail object.
     * @mailBody string Contains the body os the mail object.
     * @return boolean Returns true if the update was successful and false it was not successful.
     */

    public function updateMail( $mailSender, $mailSubject, $mailBody)
    {
        $sql = "update mail_database set mail_sender = '$mailSender', mail_subject = '$mailSubject', mail_body = '$mailBody'";

        return $this->query($sql);
    }
    /*
     * This function deletes and email object from the database
     * @mailId integer The unique Identifier of a recorded email
     * @return boolean It returns true if it was successfully deleted and false it not deleted
     */
    public function deleteMail($mailId)
    {
        $sql = "delete from mail_database where mail_id = '$mailId'";

        return $this->query($sql);
    }
}