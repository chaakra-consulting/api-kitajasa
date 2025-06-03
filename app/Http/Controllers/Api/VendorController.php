<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    //
    public function index()
    {
        try {
            $get_vendor = Vendor::latest()->paginate(8);
            $results = [
                'results' => true,
                'data' => $get_vendor,
                'message' => 'Success get data vendor'
            ];

            return response()->json($results, 200);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error get data ' . $ex], 500);
        }
    }

    public function getByCategory(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:category,id',
            'order_by' => 'nullable|in:terdekat,terlaris,rating',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        try {
            switch($request->order_by){
                case 'terlaris':
                    $vendors = Vendor::select('vendor.id_vendor','vendor.id_user','vendor.nama','vendor.deskripsi','vendor.alamat','vendor.kelurahan','vendor.kecamatan','vendor.kota','vendor.latlon','vendor.nik','vendor.ktp_vendor','vendor.logo_vendor','vendor.foto_vendor','vendor.poin','vendor.isverified','vendor.created_at', 'vendor.updated_at')
                    ->join('layanan', 'vendor.id_vendor', '=', 'layanan.id_vendor')
                    ->leftJoin('transaksi', function ($join) {
                        $join->on('vendor.id_vendor', '=', 'transaksi.id_vendor')
                             ->where('transaksi.status_transaksi', 'pekerjaan selesai');
                    })
                    ->where('layanan.id_category', $request->category_id)
                    ->selectRaw('COUNT(transaksi.id_transaksi) as total_transaksi')
                    ->groupBy('vendor.id_vendor','vendor.id_user','vendor.nama','vendor.deskripsi','vendor.alamat','vendor.kelurahan','vendor.kecamatan','vendor.kota','vendor.latlon','vendor.nik','vendor.ktp_vendor','vendor.logo_vendor','vendor.foto_vendor','vendor.poin','vendor.isverified','vendor.created_at', 'vendor.updated_at')
                    ->orderByDesc('total_transaksi')
                    ->get();
                                              
                    break;
                case 'rating':
                    $vendors = Vendor::select('vendor.id_vendor','vendor.id_user','vendor.nama','vendor.deskripsi','vendor.alamat','vendor.kelurahan','vendor.kecamatan','vendor.kota','vendor.latlon','vendor.nik','vendor.ktp_vendor','vendor.logo_vendor','vendor.foto_vendor','vendor.poin','vendor.isverified','vendor.created_at', 'vendor.updated_at')
                    ->join('layanan', 'vendor.id_vendor', '=', 'layanan.id_vendor')
                    ->leftJoin('transaksi', function ($join) {
                        $join->on('vendor.id_vendor', '=', 'transaksi.id_vendor')
                             ->where('transaksi.status_transaksi', 'pekerjaan selesai');
                    })
                    ->where('layanan.id_category', $request->category_id)
                    ->selectRaw('ROUND(AVG(transaksi.rating), 1) as average_rating')
                    ->groupBy('vendor.id_vendor','vendor.id_user','vendor.nama','vendor.deskripsi','vendor.alamat','vendor.kelurahan','vendor.kecamatan','vendor.kota','vendor.latlon','vendor.nik','vendor.ktp_vendor','vendor.logo_vendor','vendor.foto_vendor','vendor.poin','vendor.isverified','vendor.created_at', 'vendor.updated_at')
                    ->orderByDesc('average_rating')
                    ->get();
                    break;
                default:
                    $latlonCust = $user->customer && !in_array($user->customer->latlon, [null, '-']) 
                    ? explode(',', $user->customer->latlon) 
                    : null;                
                    $vendors = Vendor::select('vendor.*')
                        ->when($latlonCust, function ($query) use ($latlonCust) {
                            $lat = (float) $latlonCust[0];
                            $lon = (float) $latlonCust[1];
                    
                            $haversine = "(6371 * acos(
                                cos(radians($lat)) * cos(radians(SUBSTRING_INDEX(vendor.latlon, ',', 1))) *
                                cos(radians(SUBSTRING_INDEX(vendor.latlon, ',', -1)) - radians($lon)) +
                                sin(radians($lat)) * sin(radians(SUBSTRING_INDEX(vendor.latlon, ',', 1)))
                            ))";
                    
                            $query->selectRaw("$haversine AS distance")
                                ->orderBy('distance');
                        })
                        // ->groupBy('vendor.id')
                        ->get();
            
                    break;
                
            }
            $results = [
                'results' => true,
                'data' => $vendors,
                'message' => 'Success get data vendor'
            ];

            return response()->json($results, 200);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error get data ' . $ex], 500);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
            'password' => 'required',
            'nama' => 'required',
            'deskripsi' => 'required',
            'alamat' => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'kota' => 'required',
            'latlon' => 'required',
            'rating' => 'required|numeric',
            'nik' => 'required|numeric',
            'ktp_vendor' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'logo_vendor' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'foto_vendor' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'poin' => 'required',
            'isverified' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //img ktp vendot
        if ($request->hasFile('ktp_vendor')) {
            $image_ktp = $request->file('ktp_vendor');
            $image_ktp->storeAs('public/img/vendor/ktp', $image_ktp->hashName());
            $imageNameKtp = $image_ktp->hashName();
        } else {
            // Mengatur default image jika tidak ada file yang diupload
            $imageNameKtp = 'default.jpg';
        }

        //img logo vendor
        if ($request->hasFile('logo_vendor')) {
            $logo_vendor = $request->file('logo_vendor');
            $logo_vendor->storeAs('public/img/vendor/logo', $logo_vendor->hashName());
            $imgLogoVendor = $logo_vendor->hashName();
        } else {
            // Mengatur default image jika tidak ada file yang diupload
            $imgLogoVendor = 'default.jpg';
        }

        //img user vendor
        if ($request->hasFile('foto_vendor')) {
            $foto_vendor = $request->file('foto_vendor');
            $foto_vendor->storeAs('public/img/vendor/foto_vendor', $foto_vendor->hashName());
            $imgVendor = $foto_vendor->hashName();
        } else {
            // Mengatur default image jika tidak ada file yang diupload
            $imgVendor = 'default.jpg';
        }

        try {
            $data_user = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role_id' => 2
            ];

            $insert_user =  User::create($data_user);

            $id_user = $insert_user->id;

            $data_vendor = [
                'id_user' => $id_user,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'alamat' => $request->alamat,
                'kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'latlon' => $request->latlon,
                'rating' => $request->rating,
                'nik' => $request->nik,
                'ktp_vendor' => $imageNameKtp,
                'logo_vendor' => $imgLogoVendor,
                'foto_vendor' => $imgVendor,
                'poin' => $request->poin,
                'isverified' => $request->isverified
            ];

            $insert_vendor = Vendor::create($data_vendor);
            return response()->json([
                'results' => true,
                'data' => [
                    'users' => $insert_user,
                    'vendor' => $insert_vendor
                ],
                'message' => 'Data customer berhasil di tambahkan',
                'status_code' => 201
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error register data ' . $ex], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
            'alamat' => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'kota' => 'required',
            'latlon' => 'required',
            'rating' => 'required|numeric',
            'nik' => 'required|numeric',
            'ktp_vendor' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'logo_vendor' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'foto_vendor' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'poin' => 'required',
            'isverified' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $get_vendor = Vendor::findOrFail($id);
            $data_vendor = [
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'alamat' => $request->alamat,
                'kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'latlon' => $request->latlon,
                'rating' => $request->rating,
                'nik' => $request->nik,
                'poin' => $request->poin,
                'isverified' => $request->isverified
            ];
            //img ktp vendor
            if ($request->hasFile('ktp_vendor')) {
                $image_ktp = $request->file('ktp_vendor');
                $image_ktp->storeAs('public/img/vendor/ktp', $image_ktp->hashName());
                $imageNameKtp = $image_ktp->hashName();
                if ($get_vendor->ktp_vendor != 'default.jpg') {
                    $path_ktp = public_path('img/vendor/ktp/' . $get_vendor->ktp_vendor);
                    unlink($path_ktp);
                }

                $data_vendor['ktp_vendor'] = $imageNameKtp;
            }

            //img logo vendor
            if ($request->hasFile('logo_vendor')) {
                $logo_vendor = $request->file('logo_vendor');
                $logo_vendor->storeAs('public/img/vendor/logo', $logo_vendor->hashName());
                $imgLogoVendor = $logo_vendor->hashName();
                if ($get_vendor->logo_vendor != 'default.jpg') {
                    $path_logo = public_path('img/vendor/logo/' . $get_vendor->logo_vendor);
                    unlink($path_logo);
                }

                $data_vendor['logo_vendor'] = $imgLogoVendor;
            }

            //img user vendor
            if ($request->hasFile('foto_vendor')) {
                $foto_vendor = $request->file('foto_vendor');
                $foto_vendor->storeAs('public/img/vendor/foto_vendor', $foto_vendor->hashName());
                $imgVendor = $foto_vendor->hashName();
                if ($get_vendor->foto_vendor != 'default.jpg') {
                    $path_fotoVendor = public_path('img/vendor/foto_vendor/' . $get_vendor->foto_vendor);
                    unlink($path_fotoVendor);
                }

                $data_vendor['foto_vendor'] = $imgVendor;
            }

            $get_vendor->update($data_vendor);
            return response()->json([
                'results' => true,
                'data' => [
                    'vendor' => $get_vendor
                ],
                'message' => 'Data customer berhasil di update',
                'status_code' => 201
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error updating data ' . $ex], 500);
        }
    }

    public function delete($id)
    {
        $get_vendor = Vendor::findOrFail($id);

        //Delete user
        DB::table('users')->where('id', $get_vendor->id_user)->delete();
        if ($get_vendor->ktp_vendor != 'default.jpg') {
            $path_ktp = public_path('img/vendor/ktp/' . $get_vendor->ktp_vendor);
            unlink($path_ktp);
        }
        if ($get_vendor->logo_vendor != 'default.jpg') {
            $path_logo = public_path('img/vendor/logo/' . $get_vendor->logo_vendor);
            unlink($path_logo);
        }
        if ($get_vendor->foto_vendor != 'default.jpg') {
            $path_fotoVendor = public_path('img/vendor/foto_vendor/' . $get_vendor->foto_vendor);
            unlink($path_fotoVendor);
        }
        $get_vendor->delete();
        return response()->json([
            'message' => 'Data vendor berhasil di hapus',
            'status_code' => 200
        ], 200);
    }
}
