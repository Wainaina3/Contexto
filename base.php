<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 13/11/2015
 * Time: 19:48
 */
// define("DB_HOST", 'localhost');
// define("DB_NAME", 'csashesi_david-wainaina');
// define("DB_PORT", 3306);
// define("DB_USER","csashesi_dw16");
// define("DB_PWORD","db!o^_r46");

define("DB_HOST", 'localhost');
define("DB_NAME", 'mail_database');
define("DB_PORT", 3306);
define("DB_USER","root");
define("DB_PWORD","");

define("LOG_LEVEL_SEC",0);
define("LOG_LEVEL_DB_FAIL",0);
define("PAGE_SIZE",10);
/*
 * This function should log errors to a file.
 * @param int $level Level of error to be logged
 * @param int $code the code to identify type of error
 * @param string $msg the message to be logged
 * @param string $mysql_msg the message from mysql
 */
function log_msg($level, $er_code, $msg, $mysql_msg){
    return 0;
}
/*
 * This class will be used to connect to the database.
 * The database connections are defined above.
 */
class base
{
    /**error description*/
    var $str_error;
    /*error code*/
    var $error;
    /*db connection link*/
    var $link;
    /* Every error log has a 4 digit code. The first two digits(prefix) tells you which class logged the error*/
    var $er_code_prefix;
    /* query result resource*/
    var $result;
    /*
     * Constructor to initialize the class
     * The er_code prefix is initialized to 100
     * The link is initialized to false
     * The result is initialized false
     */
    function adb()
    {
        $this->er_code_prefix=1000;
        $this->link=false;
        $this->result = false;
    }
    /**
     * logs error into database using functions defined in log.php
     * @param int $level Level of error to be logged
     * @param int $code the code to indetify type of error
     * @param string $msg the message to be logged
     * @param string $mysql_msg the message from mysql
     * @return int $log_id the id of the error after being logged
     */
    function log_error($level, $code, $msg, $mysql_msg )
    {
        $er_code = $this->er_code_prefix + $code;
        //call to a predefined function
        $log_id = log_msg($level, $er_code, $msg, $mysql_msg);
        //if log id is false return 0;
        if (!$log_id) {
            return 0;
        }
        //display this code to user
        $this->error="$er_code-$log_id";
        return $log_id;
    }
    /**
     * creates connection to database
     * @return boolean It returns tru when there was a successful connection and false when it fails to connect
     */
    function connect()
    {
        if($this->link)
        {
            return true;
        }
        //try to connect to db
        //@param string $DB_HOST the database server host
        //@param string $DB_USER the user name of database server
        //@param string $DB_PWORD the password of the database server
        $this->link = mysqli_connect(DB_HOST , DB_USER, DB_PWORD);
        if (!$this->link) {
            //if connection fail log error and set $str_error
            $this->log_error(LOG_LEVEL_DB_FAIL,1, "connection failed  in db:connect()", mysqli_error($this->link));
            return false;
        }
        //Tries to select a database
        if (!mysqli_select_db($this->link,DB_NAME)) {
            //if it cannot connect, an error is logged
            $log_id = $this->log_error(LOG_LEVEL_DB_FAIL,2, "select db failed   in db:connect()", mysqli_error($this->link));
            return false;
        }
        return true;
    }
    /**
     *This function gets data from the query results.
     * @return string It returns an associative array of one row of the result.
     */
    function fetch()
    {
        return mysqli_fetch_assoc($this->result);
    }
    /**
     * This function connects to the database by calling the $connect function
     * It then runs query on the database
     * The result is stored in $result variable
     * @param string $str_sql This is the sql query to be run on the database
     * @return boolean Returns true when the query succeeds (no error) or false when it fails.
     */
    function query($str_sql)
    {
        //tries to connect
        if (!$this->connect()) {
            return false;
        }
        //sets result by running mysqli_query on the given string
        $this->result = mysqli_query($this->link,$str_sql);
      // print_r($this->result);
        if (!$this->result) {
            $this->log_error(LOG_LEVEL_DB_FAIL, 4, "query failed", mysqli_error($this->link));
            echo mysqli_error($this->link);
          //  echo "result was not set!!!";
            return false;
        }
     //   mysqli_close($this->link);
        return true;
    }
    /**
     * This function returns the number of rows in the dataset from the database
     * @return integer It returns the number of rows.
     */
    function get_num_rows()
    {
        return mysqli_num_rows($this->result);
    }
    /**
     *gets the last auto-generated id
     * @return integer the last insertion id.
     */
    function get_insert_id()
    {
        return mysqli_insert_id($this->link);
    }
    /**
     *This function closes the connection of the database
     * @return void
     */
    function close()
    {
        mysqli_close($this->link);
    }

    /*
   * This function returns an array of tables in the database.
   * @database string This is the database containing the needed tables.
   */
    function getDbTables( )
    {
        $database=DB_NAME;
        $tables = array();
        $list_tables_sql = "SHOW TABLES FROM {$database};";
        //tries to connect
        if (!$this->connect()) {
            return false;
        }

        $result = mysqli_query( $this->link, $list_tables_sql);

        if($result)
            while($table = mysqli_fetch_row($result))
            {
                $tables[] = $table[0];
            }
        return $tables;
    }

    /*
     * This function returns an array of items in a database result set
     */

    function fetchArray()
    {
        return mysqli_fetch_array($this->result);
    }
}