<?php

/**
 * Через cURL необходимо обратиться сюда https://syn.su/testwork.php, со следующими
 * параметрами (method=get) методом POST.
 *
 * Необходимо каждый час отправлять сюда https://syn.su/testwork.php cURL`ом со следующими
 * параметрами (method=UPDATE&message=) методом POST
 *
 * message response зашифровать методом XOR, к результату шифрования применить base64_encode.
 *
 * В случае успешного запроса в качестве результата придет JSON
 * {
 *    "errorCode": null,
 *    "response": "Success"
 *  }
 *
 * Если произошла ошибка, необходимо остановить выполнения демона и отправить сообщение на почту
 * (почта для ошибок должна быть указана в качестве константы в демоне).
 */

// Отвязываемся от консоли
$child_pid = pcntl_fork();
if ( $child_pid ) {
    exit();
}

posix_setsid();

// Перенаправляем вывод в файлы
$baseDir = dirname( __FILE__ );
ini_set( 'error_log', $baseDir . '/error.log' );
fclose( STDIN );
fclose( STDOUT );
fclose( STDERR );
$STDIN = fopen( '/dev/null', 'r' );
$STDOUT = fopen( $baseDir . '/application.log', 'ab' );
$STDERR = fopen( $baseDir . '/daemon.log', 'ab' );

// Запускаем демон
require_once __DIR__ . '/engine/DaemonSynergy.php';
require_once __DIR__ . '/engine/Encryption.php';
require_once __DIR__ . '/engine/LoggerEcho.php';
require_once __DIR__ . '/engine/MailSender.php';

$obj = new DaemonSynergy();

if ( $obj->get() ) {
    $message = Encryption::encode( $obj->getMessage(), $obj->getKey() );

    while ( true ) {
        if ( $obj->update( $message ) && $obj->isSuccess() ) {
            LoggerEcho::log( 'Success' );
        } else {
            LoggerEcho::log( "Error #{$obj->getErrorCode()}, message {$obj->getErrorMessage()}" );

            $mail = new MailSender(
                DaemonSynergy::EMAIL_ERROR,
                'Демон остановлен',
                "При работе демона возникла ошибка {$obj->getErrorCode()}, сообщение {$obj->getErrorMessage()}."
            );
            $mail->send();

            LoggerEcho::log( 'Работа демона завершена' );
            exit();
        }

        sleep( 3600 );
    }
}
