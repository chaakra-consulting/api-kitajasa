<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    //
    public function index()
    {
        //
        try {
            $get_customer = Customer::latest()->paginate(8);
            $results = [
                'results' => true,
                'data' => $get_customer,
                'message' => 'Success get data barang'
            ];

            return response()->json($results, 200);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error get data ' . $ex], 500);
        }
    }

    public function create(Request $request)
    {
        //
        $email = $request->email;
        $username = $request->username;
        $password = Hash::make($request->password);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
            'password' => 'required',
            'nama_lengkap' => 'required',
            'nomor_hp' => 'required|numeric',
            'jenis_kelamin' => 'required',
            'nomor_telepon' => 'required|numeric',
            'kota' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa_kel' => 'required',
            'detail_alamat' => 'required',
            'latlon' => 'required',
            'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('photo_profil')) {
            $image = $request->file('photo_profil');
            $image->storeAs('public/img/customer', $image->hashName());
            $imageName = $image->hashName();
        } else {
            // Mengatur default image jika tidak ada file yang diupload
            $imageName = 'default.jpg';
        }


        try {
            $data_user = [
                'name' => $request->name,
                'email' => $email,
                'username' => $username,
                'password' => $password,
                'role_id' => 3
            ];

            $insert_user =  User::create($data_user);

            $id_user = $insert_user->id;

            $data_customer = [
                'id_user' => $id_user,
                'nama_lengkap' => $request->nama_lengkap,
                'nomor_hp' => $request->nomor_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
                'email' => $request->email,
                'username' => $request->username,
                'password' => $request->password,
                'kota' => $request->kota,
                'kabupaten' => $request->kabupaten,
                'kecamatan' => $request->kecamatan,
                'desa_kel' => $request->desa_kel,
                'detail_alamat' => $request->detail_alamat,
                'latlon' => $request->latlon,
                'photo_profil' => $imageName
            ];

            $insert_customer = Customer::create($data_customer);
            return response()->json([
                'results' => true,
                'data' => [
                    'users' => $insert_user,
                    'customer' => $insert_customer
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
        try {
            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required',
                'nomor_hp' => 'required|numeric',
                'jenis_kelamin' => 'required',
                'nomor_telepon' => 'required|numeric',
                'kota' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'desa_kel' => 'required',
                'detail_alamat' => 'required',
                'latlon' => 'required',
                'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $get_customer = Customer::findOrFail($id);
            $data_customer = [
                'nama_lengkap' => $request->nama_lengkap,
                'nomor_hp' => $request->nomor_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
                'email' => $request->email,
                'username' => $request->username,
                'password' => $request->password,
                'kota' => $request->kota,
                'kabupaten' => $request->kabupaten,
                'kecamatan' => $request->kecamatan,
                'desa_kel' => $request->desa_kel,
                'detail_alamat' => $request->detail_alamat,
                'latlon' => $request->latlon
            ];
            if ($request->hasFile('photo_profile')) {
                $image = $request->file('photo_profil');
                $image->storeAs('public/img/customer', $image->hashName());
                $imageName = $image->hashName();
                if ($get_customer->photo_profil != 'default.jpg') {
                    $path = public_path('img/customer/' . $get_customer->photo_profil);
                    unlink($path);
                }

                $data_customer['photo_profil'] = $imageName;
            }

            $get_customer->update($data_customer);
            return response()->json([
                'results' => true,
                'data' => [
                    'customer' => $get_customer
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
        $get_customer = Customer::findOrFail($id);

        //Delete user
        DB::table('users')->where('id', $get_customer->id_user)->delete();
        if ($get_customer->photo_profil != 'default.jpg') {
            $path = public_path('img/customer/' . $get_customer->photo_profil);
            unlink($path);
        }
        $get_customer->delete();

        return response()->json([
            'message' => 'Data customer berhasil di hapus',
            'status_code' => 200
        ], 200);
    }
}
