<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DetailTransaksiController extends Controller
{
    //
    public function index($id_transaksi)
    {
        try {
            // $get_layanan = Layanan::latest()->paginate(5);
            $get_detail_transaksi = DB::table('detail_transaksi')
                ->join('transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
                ->join('layanan', 'layanan.id_layanan', '=', 'detail_transaksi.id_layanan')
                ->select('*')
                ->where('detail_transaksi.id_transaksi', $id_transaksi)
                ->paginate(8);

            $results = [
                'results' => true,
                'data' => $get_detail_transaksi,
                'message' => 'Success get detail transaksi'
            ];

            return response()->json($results, 200);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error get data ' . $ex], 500);
        }
    }

    public function create(Request $request, $id_transaksi)
    {
        $validator = Validator::make($request->all(), [
            'id_layanan' => 'required',
            'jumlah' => 'required|numeric',
            'catatan_tambahan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $data = [
                'id_transaksi' => $id_transaksi,
                'id_layanan' => $request->id_layanan,
                'jumlah' => $request->jumlah,
                'catatan_tambahan' => $request->catatan_tambahan,
            ];

            $insert_detail_transaksi = DetailTransaksi::create($data);
            return response()->json([
                'results' => true,
                'data' => [
                    'transaksi' => $insert_detail_transaksi
                ],
                'message' => 'Detail Transaksi berhasil dilakukan',
                'status_code' => 201
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error create data ' . $ex], 500);
        }
    }
}
