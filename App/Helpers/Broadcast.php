<?php

namespace App\Helpers;

use Bschmitt\Amqp\Facades\Amqp;
use Bschmitt\Amqp\Message;

class Broadcast
{
    /**
     * Get message
     *
     * @param mixed $message
     * @return Message
     */
    protected static function getMessage($message)
    {
        $options = [
            'content_type'  => 'text/plain',
            'delivery_mode' => 2
        ];

        if (is_array($message)) {
            $message = json_encode($message);
            $options['content_type'] = 'application/json';
        }
        return new Message($message, $options);
    }

    /**
     * Send message in direct exchange
     *
     * @param string $queue
     * @param array $message
     * @param array $options
     */
    public static function direct(string $queue, array $message, array $options = [])
    {
        $options = array_merge(
            [
                'queue'    => $queue,
                'exchange' => 'amq.direct'
            ],
            $options
        );

        Amqp::publish(
            $queue,
            self::getMessage($message),
            $options
        );
    }

    /**
     * Send message in direct exchange
     *
     * @param string $queue
     * @param array $message
     * @param array $options
     */
    public static function topic(string $queue, array $message, array $options = [])
    {
        $options = array_merge(
            [
                'queue'    => $queue,
                'exchange' => 'amq.topic'
            ],
            $options
        );

        Amqp::publish(
            $queue,
            self::getMessage($message),
            $options
        );
    }
}
