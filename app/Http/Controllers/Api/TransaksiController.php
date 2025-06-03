<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Midtrans\Snap;
use Midtrans\Config;

class TransaksiController extends Controller
{
    //
    public function index()
    {
        try {
            // $get_layanan = Layanan::latest()->paginate(5);
            $get_transaksi = DB::table('transaksi')
                ->join('customer', 'customer.id_customer', '=', 'transaksi.id_customer')
                ->join('vendor', 'vendor.id_vendor', '=', 'transaksi.id_vendor')
                ->select('*')
                ->paginate(8);

            $results = [
                'results' => true,
                'data' => $get_transaksi,
                'message' => 'Success get data transaksi'
            ];

            return response()->json($results, 200);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error get data ' . $ex], 500);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_customer' => 'required',
            'id_vendor' => 'required',
            'total' => 'required|numeric',
            'kuantitas' => 'required|numeric',
            'status_transaksi' => 'required',
            // 'link_pembayaran' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = false;
            Config::$isSanitized = true;
            Config::$is3ds = true;
    
            $params = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . time(),
                    'gross_amount' => $request->total,
                ],
                'customer_details' => [
                    'first_name' => $request->name ?? 'Guest',
                    'email' => $request->email ?? 'guest@example.com',
                ],
            ];
    
            $snapToken = Snap::getSnapToken($params);

            $data = [
                'id_customer' => $request->id_customer,
                'id_vendor' => $request->id_vendor,
                'total' => $request->total,
                'kuantitas' => $request->kuantitas,
                'status_transaksi' => $request->status_transaksi,
                'link_pembayaran' => env('MIDTRANS_URL').$snapToken,
                'token_pembayaran' => $snapToken,
            ];

            $insert_transaksi = Transaksi::create($data);
            return response()->json([
                'results' => true,
                'data' => [
                    'transaksi' => $insert_transaksi
                ],
                'message' => 'Transaksi berhasil dilakukan',
                'status_code' => 201
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error register data ' . $ex], 500);
        }
    }

    public function getSnapToken(Request $request)
    {
        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }

    // public function reverseGeocode(Request $request)
    // {
    //     $lat = $request->lat;
    //     $lng = $request->lng;

    //     if (!$lat || !$lng) {
    //         return response()->json(['error' => 'Latitude and longitude are required.'], 400);
    //     }

    //     $key = env('GOOGLE_MAPS_API_KEY');
    //     $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$key}";

    //     $response = Http::get($url);

    //     return $response->json();
    // }
}
