<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;


class ConsumePesanan extends Command
{
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Konsumsi pesan pesanan baru dari RabbitMQ';

    public function handle()
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST'), env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'), env('RABBITMQ_PASS'), env('RABBITMQ_VHOST')
        );
        $channel = $connection->channel();
        $channel->queue_declare('pesanan_baru', false, true, false, false);

        $this->info('Menunggu pesan pesanan baru...');

        $callback = function ($msg) {
            $data = json_decode($msg->body, true);
            $this->info('Menerima pesanan untuk pelanggan ID: ' . $data['pelanggan_id']);
            // Logika untuk memperbarui histori pesanan bisa ditambahkan di sini
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_consume('pesanan_baru', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
