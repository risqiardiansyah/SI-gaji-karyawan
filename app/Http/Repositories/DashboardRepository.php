<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Dashboard\BukuKas\CatatanBuku;
use App\Dashboard\BukuKas\BuatBuku;
use Illuminate\Support\Facades\Hash;

class DashboardRepository
{
    public function getallbukukas($idx_buku_kas, $user_id)
    {
        $nowMonth = date('m');
        $preMonth = $nowMonth - 1;
        $prevMonth = strlen((string)$preMonth) == 1 ? '0' . $preMonth : $preMonth;
        $data_kas = new DashboardRepository();
        $data_kas->total_saldo = Buatbuku::where('id', '=', $user_id)->where('idx_buku_kas', '=', $idx_buku_kas)->where('status', '=', 'aktif')->sum('buku_saldo');
        // $data_kas->saldo = Buatbuku::where('id', '=', $user_id)->where('idx_buku_kas', '=', $idx_buku_kas)->where('status', '=', 'aktif')->sum('buku_saldo');

        $hasilpemasukanNow = CatatanBuku::where('id_user', '=', $user_id)
            ->where('idx_buku_kas', '=', $idx_buku_kas)
            ->where('idx_kategori', '=', 1)
            ->where('status', '=', 'aktif')
            ->whereMonth('catatan_tgl', '=', $nowMonth)
            ->whereYear('catatan_tgl', '=', date('Y'))
            ->sum('catatan_jumlah');

        $hasilpemasukanPrev = CatatanBuku::where('id_user', '=', $user_id)
            ->where('idx_buku_kas', '=', $idx_buku_kas)
            ->where('idx_kategori', '=', 1)
            ->where('status', '=', 'aktif')
            ->whereMonth('catatan_tgl', '=', $prevMonth)
            ->whereYear('catatan_tgl', '=', date('Y'))
            ->sum('catatan_jumlah');

        $hasilpengeluaranNow = CatatanBuku::where('id_user', '=', $user_id)
            ->where('idx_buku_kas', '=', $idx_buku_kas)
            ->where('idx_kategori', '=', 2)
            ->where('status', '=', 'aktif')
            ->whereMonth('catatan_tgl', '=', $nowMonth)
            ->whereYear('catatan_tgl', '=', date('Y'))
            ->sum('catatan_jumlah');

        $hasilpengeluaranPrev = CatatanBuku::where('id_user', '=', $user_id)
            ->where('idx_buku_kas', '=', $idx_buku_kas)
            ->where('idx_kategori', '=', 2)
            ->where('status', '=', 'aktif')
            ->whereMonth('catatan_tgl', '=', $prevMonth)
            ->whereYear('catatan_tgl', '=', date('Y'))
            ->sum('catatan_jumlah');
        // print_r($hasilpemasukanNow);
        // dd($hasilpemasukanPrev);
        // return ['a' => $hasilpemasukanNow, 'b' => $hasilpemasukanPrev];
        $saldo_awal = BuatBuku::where('id', '=', $user_id)->where('idx_buku_kas', '=', $idx_buku_kas)->sum('buku_saldo_awal');
        $saldo_akhir = BuatBuku::where('id', '=', $user_id)->where('idx_buku_kas', '=', $idx_buku_kas)->sum('buku_saldo');
        // print_r($saldo_awal);
        // dd($saldo_akhir);
        //buku kas
        if ($saldo_awal == 0 && $saldo_akhir > 0) {
            $hasil = 1;
        } elseif ($saldo_awal > 0 && $saldo_akhir > 0) {
            $hasil = (($saldo_akhir - $saldo_awal) / $saldo_awal) * 100 / 100;
        } elseif ($saldo_awal == 0 && $saldo_akhir == 0) {
            $hasil = 0;
        } else {
            $hasil = 0;
        }
        $persen_saldo = number_format($hasil * 100, 1);

        //pemasukan
        $hasilpemasukan = 0;
        if ($hasilpemasukanPrev != 0 && $hasilpemasukanNow != 0) {
            $hasilpemasukan = (($hasilpemasukanNow - $hasilpemasukanPrev) / $hasilpemasukanPrev);
        } elseif ($hasilpemasukanPrev == 0 && $hasilpemasukanNow != 0) {
            $hasilpemasukan = 1;
        } elseif ($hasilpemasukanPrev != 0 && $hasilpemasukanNow == 0) {
            $hasilpemasukan = -1;
        }
        // return ['a' => $hasilpemasukanNow, 'b' => $hasilpemasukanPrev, 'c' => $hasilpemasukan, 'cek' => $ha];
        $persen_saldo_pemasukan = number_format($hasilpemasukan * 100, 1);

        // Pengeluaran
        $hasilpengeluaran = 0;
        if ($hasilpengeluaranPrev != 0 && $hasilpengeluaranNow != 0) {
            $hasilpengeluaran = (($hasilpengeluaranNow - $hasilpengeluaranPrev) / $hasilpengeluaranPrev);
        } elseif ($hasilpengeluaranPrev == 0 && $hasilpengeluaranNow != 0) {
            $hasilpengeluaran = 1;
        } elseif ($hasilpengeluaranPrev != 0 && $hasilpengeluaranNow == 0) {
            $hasilpengeluaran = 1;
        }
        $persen_saldo_pengeluaran = number_format($hasilpengeluaran * 100, 1);

        //persentase
        if ($saldo_awal > $saldo_akhir) {
            $data_persen = '' . $persen_saldo;
        } elseif ($saldo_awal < $saldo_akhir) {
            $data_persen = '' . $persen_saldo;
        } elseif ($saldo_awal == $saldo_akhir) {
            // $data_persen = '' . $persen_saldo;
            $data_persen = 0;
        } else {
            // $data_persen = '' . abs($persen_saldo);
            $data_persen = 0;
        }

        //persentase pemasukan
        if ($hasilpemasukanPrev < $hasilpemasukanNow) {
            $data_persen_pemasukan = '' . $persen_saldo_pemasukan;
        } elseif ($hasilpemasukanPrev > $hasilpemasukanNow) {
            $data_persen_pemasukan = '' . $persen_saldo_pemasukan;
        } elseif ($hasilpemasukanPrev == $hasilpemasukanNow) {
            // $data_persen_pemasukan = '' . $persen_saldo_pemasukan;
            $data_persen_pemasukan = 0;
        } else {
            $data_persen_pemasukan = 0;
        }

        //persentase pengeluaran
        if ($hasilpengeluaranPrev < $hasilpengeluaranNow) {
            $data_persen_pengeluaran = '' . $persen_saldo_pengeluaran;
        } elseif ($hasilpengeluaranPrev > $hasilpengeluaranNow) {
            $data_persen_pengeluaran = '' . $persen_saldo_pengeluaran;
        } elseif ($hasilpengeluaranPrev == $hasilpengeluaranNow) {
            $data_persen_pengeluaran = 0;
        } else {
            $data_persen_pengeluaran = 0;
        }

        //Pemasukan
        $data_kas->persen_seluruh_pemasukan = $data_persen_pemasukan;
        //Pengeluaran
        $data_kas->persen_seluruh_pengeluaran = $data_persen_pengeluaran;
        $data_kas->persentase = $data_persen;
        $data_kas->pemasukan =  CatatanBuku::where('id_user', '=', $user_id)->where('idx_buku_kas', '=', $idx_buku_kas)->where('idx_kategori', '1')->where('status', '=', 'aktif')->sum('catatan_jumlah');
        $data_kas->pengeluaran = CatatanBuku::where('id_user', '=', $user_id)->where('idx_buku_kas', '=', $idx_buku_kas)->where('idx_kategori', '2')->where('status', '=', 'aktif')->sum('catatan_jumlah');

        $grafik = (object)[];
        $pemasukkan = [];
        $pengeluaran = [];
        for ($i = 1; $i <= 12; $i++) {
            $data = DB::table('tbl_catatan_buku')
                ->where('status', 'aktif')
                ->where('idx_kategori', 1)
                ->where('idx_buku_kas', '=', $idx_buku_kas)
                ->whereMonth('catatan_tgl', '=', $i)
                ->whereYear('catatan_tgl', '=', date('Y'))
                ->sum('catatan_jumlah');
            array_push($pemasukkan, $data);

            $data = DB::table('tbl_catatan_buku')
                ->where('status', 'aktif')
                ->where('idx_kategori', 2)
                ->where('idx_buku_kas', '=', $idx_buku_kas)
                ->whereMonth('catatan_tgl', '=', $i)
                ->whereYear('catatan_tgl', '=', date('Y'))
                ->sum('catatan_jumlah');
            array_push($pengeluaran, $data);
        }
        $grafik->pemasukkan = $pemasukkan;
        $grafik->pengeluaran = $pengeluaran;
        $data_kas->grafik = $grafik;

        return $data_kas;
    }

    public function getbukukasId($idx_buku_kas, $user_id)
    {
        $id_buku_kas = BuatBuku::where('id', '=', $user_id)->where('idx_buku_kas', '=', $idx_buku_kas)->sum('buku_saldo');
        return $id_buku_kas;
    }

    public function getListUser($request, $user_id)
    {
        $filtered = $request->filtered;
        $select = [
            'name',
            'email',
            'role',
            'id',
            'users_code',
        ];

        $data = DB::table('users')->where('id', '!=', $user_id)->where('is_deleted', 0)->get($select);
        if ($filtered == 'yes') {
            $filter = [];
            for ($i = 0; $i < count($data); $i++) {
                $cek = DB::table('pelanggan')->where('created_by', $data[$i]->users_code)->count();
                if ($cek > 0) {
                    array_push($filter, $data[$i]);
                }
            }

            return $filter;
        } else {
            return $data;
        }
    }

    public function editDataUser($request)
    {
        $user = DB::table('users')->where('id', $request->id)->first();
        $cek = Hash::check($request->password, $user->password);
        if (!$cek && $request->password == '') {
            $data = [
                'email' => $request->email,
                'name' => $request->name,
                'role' => $request->role
            ];
        } else {
            $data = [
                'email' => $request->email,
                'name' => $request->name,
                'role' => $request->role,
                'password' => Hash::make($request->password)
            ];
        }

        return DB::table('users')->where('id', $request->id)->update($data);
    }

    public function createDataUser($request)
    {
        $data = [
            'email' => $request->email,
            'name' => $request->name,
            'role' => $request->role,
            'users_code' => 'USERS-' . rand(1000000000, 9999999999),
            'password' => Hash::make($request->password)
        ];

        return DB::table('users')->insert($data);
    }

    public function deleteDataUser($id)
    {
        return DB::table('users')->where('id', $id)->update(['is_deleted' => 1]);
    }

    public function changePasswordUser($request, $user_id)
    {
        $check = Hash::check($request->password, $request->old_password);

        if ($check) {
            return false;
        } else {
            $new = Hash::make($request->password);
            return DB::table('users')->where('id', $user_id)->update(['password' => $new]);
        }
    }
}
