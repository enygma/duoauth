<?php

namespace DuoAuth;

class Error
{
    private static $errors = array();

    /**
     * Add a new error message to the set
     * 
     * @param mixed $msg Message (or data to add to error list)
     * @param string $key Optional key to set the error on
     */
    public static function add($msg, $key = null)
    {
        if ($key !== null) {
            self::$errors[$key] = $msg;
        } else {
            self::$errors[] = $msg;
        }
    }

    /**
     * Remove a specific error entry from the set
     * 
     * @param string $key Key of record to remove
     * @return boolean True if found, false if not
     */
    public static function remove($key)
    {
        if (isset(self::$errors[$key])) {
            unset(self::$errors[$key]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all of the errors in the list or just a specific one
     * 
     * @param string $key Optional key of record to return
     * @return mixed Either the one record if found or the entire array set
     */
    public static function get($key = null)
    {
        return (isset(self::$errors[$key])) ? self::$errors[$key] : self::$errors;
    }

    /**
     * Clear out the current error list
     * 
     * @return boolean True on clear
     */
    public static function clear()
    {
        self::$errors = array();
        return true;
    }
}

?>