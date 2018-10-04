<?php

/**
 * Class DemonSynergy
 */
class DaemonSynergy
{
    const URL = 'https://syn.su/testwork.php';
    const EMAIL_ERROR = 'vlad.duplin@ya.ru';

    /** @var resource */
    private $ch = null;
    /** @var string */
    private $message = null;
    /** @var string */
    private $key = null;
    /** @var object */
    private $response = null;

    /**
     * DemonSynergy constructor.
     */
    public function __construct( )
    {
        $this->init();
    }

    /**
     * DemonSynergy destructor.
     */
    public function __destruct()
    {
        curl_close( $this->ch );
    }

    /**
     * Отправить запрос методом get.
     * @return bool - true успешно, false иначе
     */
    public function get()
    {
        $data = [ 'method' => 'get' ];
        $response = $this->send( $data );

        if ( isset( $response->response->message, $response->response->key ) ) {
            $this->message = $response->response->message;
            $this->key = $response->response->key;

            return true;
        }

        return false;
    }

    /**
     * Отправить запрос методом update.
     * @param string $message
     * @return mixed
     */
    public function update( $message )
    {
        $data = [
            'method' => 'update',
            'message' => $message
        ];
        $response = $this->send( $data );

        if ( $response !== null ) {
            $this->response = $response;
            return true;
        }

        return false;
    }

    /**
     * Получить признак, что запрос успешно обработан.
     * @return bool - true все хорошо, false иначе
     */
    public function isSuccess()
    {
        return isset( $this->response->response ) && $this->response->response === 'Success';
    }

    /**
     * Получить код ошибки
     * @return null|string
     */
    public function getErrorCode()
    {
        return ( isset( $this->response->errorCode ) ? $this->response->errorCode : null );
    }

    /**
     * Получить сообщение об ошибке.
     * @return null|string
     */
    public function getErrorMessage()
    {
        return ( isset( $this->response->errorMessage ) ? $this->response->errorMessage : null );
    }

    /**
     * Получить сообщение, которое пришло с сервера.
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Получить ключ, который пришел с сервера.
     * @return null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Инициализация сеанса cURL.
     */
    private function init()
    {
        $this->ch = curl_init( self::URL );
    }

    /**
     * Выполняет запрос cURL.
     * @param array $data
     * @return object|null
     */
    private function send( array $data )
    {
        curl_setopt( $this->ch, CURLOPT_POST, true );
        curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $data );

        $result = json_decode( curl_exec( $this->ch ) );
        if ( json_last_error() === JSON_ERROR_NONE ) {
            return $result;
        }

        return null;
    }
}
