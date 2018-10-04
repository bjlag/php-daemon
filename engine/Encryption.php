<?php

/**
 * Class Encryption
 */
class Encryption
{
    /**
     * Кодируем сообщение.
     * @param string $string
     * @param string $key
     * @return string
     */
    public static function encode( $string, $key )
    {
        $string = self::xorString( $string, $key );
        $string = base64_encode( $string );

        return $string;
    }

    /**
     * Декодируем сообщение.
     * @param string $string
     * @param string $key
     * @return string
     */
    public static function decode( $string, $key )
    {
        $string = base64_decode( $string );
        $string = self::xorString( $string, $key );

        return $string;
    }

    /**
     * XOR-шифрование.
     * @param string $string
     * @param string $key
     * @return string
     */
    private static function xorString( $string, $key )
    {
        for ( $i = 0; $i < strlen( $string ); $i++ ) {
            $string[ $i ] = ( $string[ $i ] ^ $key[ $i % strlen( $key ) ] );
        }

        return $string;
    }
}
