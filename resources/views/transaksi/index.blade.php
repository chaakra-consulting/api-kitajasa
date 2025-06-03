@extends('layouts.main')

@section('content')
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel">Modal title
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" id="formDivisi">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_divisi">Nama Divisi</label>
                            <input type="text" name="nama_divisi" id="nama_divisi" class="form-control"
                                placeholder="Divisi ...." required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="mb-2">
            <button type="button" class="btn btn-primary tambahTransaksi" data-bs-toggle="modal"
                data-bs-target="#staticBackdrop">
                Tambah Divisi
            </button>
        </div>
        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">
                    Data Divisi
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable-basic" class="table table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Aksi</th>
                                <th>Nama Customer</th>
                                <th>Alamat</th>
                                <th>Vendor</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>Status Pembayaran</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksisRemap as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-warning editTransaksi" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop" 
                                            {{-- data-id="{{ $item->id }}"
                                            data-nama_divisi="{{ $item->nama_divisi }}" --}}
                                            >
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- <form action="/admin_sdm/divisi/delete/{{ $item->id }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Hapus Data?')"><i
                                                    class="fas fa-trash"></i></button>
                                        </form> --}}
                                    </td>
                                    <td>{{ $item->nama_customer }}</td>
                                    <td>{{ $item->detail_alamat }}</td>
                                    <td>{{ $item->nama_vendor }}</td>
                                    <td>{{ $item->nama_layanan }}</td>
                                    <td>{{ $item->total }}</td>
                                    <td>
                                        <span class="badge {{ $item->status_pembayaran_color }}">{{ $item->status_pembayaran }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->status_transaksi_color }}">{{ $item->status_transaksi }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $(".tambahTransaksi").click(function() {
                $(".modal-title").text('Tambah Divisi');
                $("#formDivisi").attr('action', '/admin_sdm/divisi/store');
            })

            $(".editTransaksi").click(function(e) {
                e.preventDefault();
                $(".modal-title").text('Edit Nama Divisi');
                $("#nama_divisi").val($(this).data('nama_divisi'));

                $("#formDivisi").append('<input type="hidden" name="_method" value="PUT">');
                $("#formDivisi").attr('action', '/admin_sdm/divisi/update/' + $(this)
                    .data('id'));
            })
        })
    </script>
@endsection
