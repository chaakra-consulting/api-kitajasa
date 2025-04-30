<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //
    public function index()
    {
        try {
            $get_category = Category::latest()->paginate(8);
            $results = [
                'results' => true,
                'data' => $get_category,
                'message' => 'Success get data kategori'
            ];

            return response()->json($results, 200);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error get data ' . $ex], 500);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $image->storeAs('public/img/category', $image->hashName());
            $imageName = $image->hashName();
        } else {
            $imageName = 'default.jpg';
        }

        try {
            $data = [
                'category' => $request->category,
                'gambar' => $imageName,
                'status' => $request->status
            ];

            $insert_category = Category::create($data);
            return response()->json([
                'results' => true,
                'data' => [
                    'category' => $insert_category
                ],
                'message' => 'Data kategori berhasil di tambahkan',
                'status_code' => 201
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error register data ' . $ex], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $get_category = Category::findOrFail($id);
            $data = [
                'category' => $request->category,
                'status' => 'aktif'
            ];
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $image->storeAs('public/img/category', $image->hashName());
                $imageName = $image->hashName();

                if ($get_category->gambar != 'default.jpg') {
                    $path = public_path('img/category/' . $get_category->gambar);
                    unlink($path);
                }

                $data['gambar'] = $imageName;
            }

            $get_category->update($data);

            return response()->json([
                'results' => true,
                'data' => [
                    'category' => $get_category
                ],
                'message' => 'Data kategori berhasil di udpate',
                'status_code' => 201
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['results' => false, 'message' => 'Error register data ' . $ex], 500);
        }
    }

    public function delete($id)
    {
        $get_category = Category::findOrFail($id);

        if ($get_category->gambar != 'default.jpg') {
            $path = public_path('img/category/' . $get_category->gambar);
            unlink($path);
        }
        $get_category->delete();

        return response()->json([
            'message' => 'Data kategori berhasil di hapus',
            'status_code' => 200
        ], 200);
    }
}
