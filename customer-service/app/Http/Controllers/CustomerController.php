<?php

     namespace App\Http\Controllers;

     use App\Models\Pelanggan;
     use Illuminate\Http\Request;
     use Illuminate\Support\Facades\Http;
     use Illuminate\Support\Facades\Validator;


     class CustomerController extends Controller
     {
         public function index()
         {
             $pelanggan = Pelanggan::all();
             return response()->json([
                 'status' => 'sukses',
                 'pesan' => 'Daftar pelanggan berhasil diambil',
                 'data' => $pelanggan
             ], 200);
         }

         public function show($id)
         {
             $pelanggan = Pelanggan::find($id);
             if ($pelanggan) {
                 return response()->json([
                     'status' => 'sukses',
                     'pesan' => 'Data pelanggan berhasil diambil',
                     'data' => $pelanggan
                 ], 200);
             }
             return response()->json([
                 'status' => 'gagal',
                 'pesan' => 'Pelanggan tidak ditemukan'
             ], 404);
         }

         public function store(Request $request)
         {
            // Validasi input
             $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            ],
            [
            'nama.required' => 'Nama wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'telepon.required' => 'Telepon wajib diisi',
        ]);

    // Jika validasi gagal, kembalikan respons gagal
    if ($validator->fails()) {
        return response()->json([
            'status' => 'gagal',
            'pesan' => 'Data pelanggan tidak valid',
            'errors' => $validator->errors()
        ], 422);
    }

             $pelanggan = Pelanggan::create($request->only(['nama', 'alamat', 'telepon']));
             return response()->json([
                 'status' => 'sukses',
                 'pesan' => 'Pelanggan berhasil ditambahkan',
                 'data' => $pelanggan
             ], 201);
         }

         public function update(Request $request, $id)
         {
             $pelanggan = Pelanggan::find($id);
             if ($pelanggan) {
                 $pelanggan->update($request->only(['nama', 'alamat', 'telepon']));
                 return response()->json([
                     'status' => 'sukses',
                     'pesan' => 'Data pelanggan berhasil diperbarui',
                     'data' => $pelanggan
                 ], 200);
             }
             return response()->json([
                 'status' => 'gagal',
                 'pesan' => 'Pelanggan tidak ditemukan'
             ], 404);
         }

         public function destroy($id)
         {
             $pelanggan = Pelanggan::find($id);
             if ($pelanggan) {
                 $pelanggan->delete();
                 return response()->json([
                     'status' => 'sukses',
                     'pesan' => 'Pelanggan berhasil dihapus'
                 ], 200);
             }
             return response()->json([
                 'status' => 'gagal',
                 'pesan' => 'Pelanggan tidak ditemukan'
             ], 404);
         }

         public function getOrderHistory($id)
         {
             $response = Http::get("http://localhost:8001/api/pesanan");
             $pesanan = array_filter($response->json()['data'], fn($order) => $order['pelanggan_id'] == $id);
             return response()->json([
                 'status' => 'sukses',
                 'pesan' => 'Histori pesanan berhasil diambil',
                 'data' => array_values($pesanan)
             ], 200);
         }
     }
