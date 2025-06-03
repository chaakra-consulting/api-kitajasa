<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;

class TransaksiController extends Controller
{
    public function index()
    {
        $title = 'Transaksi';
        $transaksis = Transaksi::all();
        $transaksisRemap = $transaksis->map(function ($transaksi) {
            $detailTransaksi = $transaksi->detailTransaksi ?? null;
            $namaLayanan = '-';

            if ($detailTransaksi && $detailTransaksi->count() > 0) {
                $namaList = $detailTransaksi->map(function ($detail) {
                    return optional($detail->layanan)->nama_layanan;
                })->filter()->values()->toArray();

                $jumlah = count($namaList);

                if ($jumlah === 1) $namaLayanan = $namaList[0];
                elseif ($jumlah === 2) $namaLayanan = $namaList[0] . ' dan ' . $namaList[1];
                elseif ($jumlah > 2) {
                    $last = array_pop($namaList); $namaLayanan = implode(', ', $namaList) . ', dan ' . $last;
                }
            }

            $customer = $transaksi->customer ? $transaksi->customer : null;
            $vendor = $transaksi->vendor ? $transaksi->vendor : null;
            return (object)[
                'id'                => $transaksi->id,
                'nama_customer'     => $customer ? $customer->nama_lengkap : '-',
                'nomor_hp'          => $customer ? $customer->nomor_hp : '-',
                'detail_alamat'     => $customer ? $customer->detail_alamat : '-',                
                'nama_vendor'       => $vendor ? $vendor->nama : '-',
                'nama_layanan'      => $namaLayanan ?? '-',
                'total'             => $transaksi->total,
                'status_pembayaran' => $transaksi->status_pembayaran,
                'status_pembayaran_color' => Functions::generateColorStatusPembayaran($transaksi->status_pembayaran),
                'status_transaksi'  => $transaksi->status_transaksi,
                'status_transaksi_color' => Functions::generateColorStatusTransaksi($transaksi->status_transaksi),
            ];
        });

        return view('transaksi.index', compact('title', 'transaksisRemap'));
    }

    public function showPaymentForm()
    {
        return view('transaksi');
    }

    public function processPayment(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . time(),
                'gross_amount' => $request->amount, // nominal dari form
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('transaksi', compact('snapToken'));
    }
}
