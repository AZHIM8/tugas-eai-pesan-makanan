<?php

     namespace App\Http\Controllers;

     use App\Models\Pesanan;
     use Illuminate\Http\Request;
     use Illuminate\Support\Facades\Http;
     use PhpAmqpLib\Connection\AMQPStreamConnection;
     use PhpAmqpLib\Message\AMQPMessage;
        use PhpAmqpLib\Channel\AMQPChannel;
        use PhpAmqpLib\Exception\AMQPConnectionClosedException;
        use PhpAmqpLib\Exception\AMQPProtocolChannelException;

     class OrderController extends Controller
     {
         public function index()
         {
             $pesanan = Pesanan::all();
             return response()->json([
                 'status' => 'sukses',
                 'pesan' => 'Daftar pesanan berhasil diambil',
                 'data' => $pesanan
             ], 200);
         }

         public function show($id)
         {
             $pesanan = Pesanan::find($id);
             if ($pesanan) {
                 return response()->json([
                     'status' => 'sukses',
                     'pesan' => 'Data pesanan berhasil diambil',
                     'data' => $pesanan
                 ], 200);
             }
             return response()->json([
                 'status' => 'gagal',
                 'pesan' => 'Pesanan tidak ditemukan'
             ], 404);
         }

         public function store(Request $request)
         {
             $pelangganId = $request->pelanggan_id;
             $response = Http::get("http://localhost:8000/api/pelanggan/{$pelangganId}");
             if ($response->failed()) {
                 return response()->json([
                     'status' => 'gagal',
                     'pesan' => 'Pelanggan tidak ditemukan'
                 ], 404);
             }

             $pesanan = Pesanan::create([
                 'pelanggan_id' => $pelangganId,
                 'menu' => $request->menu,
                 'jumlah' => $request->jumlah,
                 'total_harga' => $request->total_harga,
                 'status' => 'menunggu'
             ]);

           // Kirim pesan ke RabbitMQ
            try {
                $connection = new AMQPStreamConnection('rabbitmq', 15673, 'guest', 'guest');
                $channel = $connection->channel();
                $channel->queue_declare('pesanan_baru', false, false, false, false);

                $message = new AMQPMessage(json_encode([
                    'pelanggan_id' => $pelangganId,
                    'menu' => $request->menu,
                    'jumlah' => $request->jumlah,
                    'total_harga' => $request->total_harga,
                    'status' => 'menunggu'
                ]));
                $channel->basic_publish($message, '', 'pesanan_baru');

                $channel->close();
                $connection->close();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Gagal mengirim pesan ke RabbitMQ: ' . $e->getMessage()
                ], 500);
            }

             return response()->json([
                 'status' => 'sukses',
                 'pesan' => 'Pesanan berhasil ditambahkan',
                 'data' => $pesanan
             ], 201);
         }

         public function update(Request $request, $id)
         {
             $pesanan = Pesanan::find($id);
             if ($pesanan) {
                 $pesanan->update([
                     'menu' => $request->menu ?? $pesanan->menu,
                     'jumlah' => $request->jumlah ?? $pesanan->jumlah,
                     'total_harga' => $request->total_harga ?? $pesanan->total_harga,
                     'status' => $request->status ?? $pesanan->status
                 ]);
                 return response()->json([
                     'status' => 'sukses',
                     'pesan' => 'Data pesanan berhasil diperbarui',
                     'data' => $pesanan
                 ], 200);
             }
             return response()->json([
                 'status' => 'gagal',
                 'pesan' => 'Pesanan tidak ditemukan'
             ], 404);
         }

         public function destroy($id)
         {
             $pesanan = Pesanan::find($id);
             if ($pesanan) {
                 $pesanan->delete();
                 return response()->json([
                     'status' => 'sukses',
                     'pesan' => 'Pesanan berhasil dihapus'
                 ], 200);
             }
             return response()->json([
                 'status' => 'gagal',
                 'pesan' => 'Pesanan tidak ditemukan'
             ], 404);
         }
     }
