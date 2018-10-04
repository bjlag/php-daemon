<?php

/**
 * Class LoggerEcho
 */
class LoggerEcho
{
    public static function log( $message )
    {
        echo date( 'd.m.Y H:i:s' ) . " {$message}\n";
    }
}
