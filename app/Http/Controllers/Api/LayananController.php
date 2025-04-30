<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LayananController extends Controller
{
    //
    public function index()
    {
        try {
            // $get_layanan = Layanan::latest()->paginate(5);
            $get_layanan = DB::table('layanan')
                ->join('vendor', 'vendor.id_vendor', '=', 'layanan.id_vendor')
                ->join('category', 'category.id', '=', 'layanan.id_category')
                ->select('*')
                ->paginate(8);

            $results = [
                'results' => true,
                'data' => $get_layanan,
                'message' => 'Success get data layanan'
            ];

            return response()->json($results, 200);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error get data ' . $ex], 500);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_vendor' => 'required',
            'id_category' => 'required',
            'nama_layanan' => 'required',
            'deskripsi' => 'required',
            'harga_layanan' => 'required|numeric',
            'rating_layanan' => 'required|numeric',
            'foto_layanan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('foto_layanan')) {
            $image = $request->file('foto_layanan');
            $image->storeAs('public/img/layanan', $image->hashName());
            $imageName = $image->hashName();
        } else {
            $imageName = 'default.jpg';
        }

        try {
            $data = [
                'id_vendor' => $request->id_vendor,
                'id_category' => $request->id_category,
                'nama_layanan' => $request->nama_layanan,
                'deskripsi' => $request->deskripsi,
                'harga_layanan' => $request->harga_layanan,
                'rating_layanan' => $request->rating_layanan,
                'foto_layanan' => $imageName
            ];

            $insert_layanan = Layanan::create($data);
            return response()->json([
                'results' => true,
                'data' => [
                    'layanan' => $insert_layanan
                ],
                'message' => 'Data layanan berhasil di tambahkan',
                'status_code' => 201
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error register data ' . $ex], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_vendor' => 'required',
            'id_category' => 'required',
            'nama_layanan' => 'required',
            'deskripsi' => 'required',
            'harga_layanan' => 'required|numeric',
            'rating_layanan' => 'required|numeric',
            'foto_layanan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $get_layanan = Layanan::findOrFail($id);
            $data = [
                'id_vendor' => $request->id_vendor,
                'id_category' => $request->id_category,
                'nama_layanan' => $request->nama_layanan,
                'deskripsi' => $request->deskripsi,
                'harga_layanan' => $request->harga_layanan,
                'rating_layanan' => $request->rating_layanan,
            ];
            if ($request->hasFile('foto_layanan')) {
                $image = $request->file('foto_layanan');
                $image->storeAs('public/img/layanan', $image->hashName());
                $imageName = $image->hashName();

                if ($get_layanan->foto_layanan != 'default.jpg') {
                    $path = public_path('img/layanan/' . $get_layanan->foto_layanan);
                    unlink($path);
                }

                $data['foto_layanan'] = $imageName;
            }

            $get_layanan->update($data);
            return response()->json([
                'results' => true,
                'data' => [
                    'layanan' => $get_layanan
                ],
                'message' => 'Data layanan berhasil di udpate',
                'status_code' => 201
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error register data ' . $ex], 500);
        }
    }

    public function delete($id)
    {
        $get_layanan = Layanan::findOrFail($id);

        if ($get_layanan->foto_layanan != 'default.jpg') {
            $path = public_path('img/layanan/' . $get_layanan->foto_layanan);
            unlink($path);
        }
        $get_layanan->delete();

        return response()->json([
            'message' => 'Data layanan berhasil di hapus',
            'status_code' => 200
        ], 200);
    }
}
