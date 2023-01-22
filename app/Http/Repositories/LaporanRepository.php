<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Dashboard\BukuKas\BuatBuku;
use App\Http\Resources\ListBukuKas;

class LaporanRepository
{
    public function getHarian($id_buku_kas, $user_id, $tipe)
    {
        $data = (object)[];
        $catatan = DB::table('tbl_catatan_buku')
            ->leftJoin('tbl_sub_kategori as tsk', 'tbl_catatan_buku.idx_sub_kategori', '=', 'tsk.idx_sub_kat')
            ->where('tbl_catatan_buku.idx_buku_kas', $id_buku_kas)
            ->where('tbl_catatan_buku.id_user', $user_id)
            ->where('tbl_catatan_buku.status', 'aktif');
        if ($tipe == 1) {
            $catatan = $catatan->whereDate('tbl_catatan_buku.catatan_tgl', date('Y-m-d'));
        } elseif ($tipe == 2) {
            $catatan = $catatan->whereMonth('tbl_catatan_buku.catatan_tgl', date('m'))->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
        } else {
            $catatan = $catatan->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
        }

        $allCatatanPemasukkan = $catatan->where('tbl_catatan_buku.idx_kategori', 1)->get();
        // $allCatatanPengeluaran = $catatan->where('tbl_catatan_buku.idx_kategori', 2)->get();
        $saldo_awal = DB::table('tbl_buku_kas')->where('idx_buku_kas', $id_buku_kas)->sum('buku_saldo_awal');
        $pemasukkan = DB::table('tbl_catatan_buku')
            ->leftJoin('tbl_sub_kategori as tsk', 'tbl_catatan_buku.idx_sub_kategori', '=', 'tsk.idx_sub_kat')
            ->where('tbl_catatan_buku.idx_buku_kas', $id_buku_kas)
            ->where('tbl_catatan_buku.id_user', $user_id)
            ->where('tbl_catatan_buku.status', 'aktif');
        if ($tipe == 1) {
            $pemasukkan = $pemasukkan->whereDate('tbl_catatan_buku.catatan_tgl', date('Y-m-d'));
        } elseif ($tipe == 2) {
            $pemasukkan = $pemasukkan->whereMonth('tbl_catatan_buku.catatan_tgl', date('m'))->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
        } else {
            $pemasukkan = $pemasukkan->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
        }
        $pemasukkan = $pemasukkan->where('tbl_catatan_buku.idx_kategori', 1)->sum('catatan_jumlah');

        $pengeluaran = DB::table('tbl_catatan_buku')
            ->leftJoin('tbl_sub_kategori as tsk', 'tbl_catatan_buku.idx_sub_kategori', '=', 'tsk.idx_sub_kat')
            ->where('tbl_catatan_buku.idx_buku_kas', $id_buku_kas)
            ->where('tbl_catatan_buku.id_user', $user_id)
            ->where('tbl_catatan_buku.status', 'aktif')
            ->where('tbl_catatan_buku.idx_kategori', 2);
        // dd($pengeluaran);
        if ($tipe == 1) {
            $pengeluaran = $pengeluaran->whereDate('tbl_catatan_buku.catatan_tgl', date('Y-m-d'));
        } elseif ($tipe == 2) {
            $pengeluaran = $pengeluaran->whereMonth('tbl_catatan_buku.catatan_tgl', date('m'))->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
        } else {
            $pengeluaran = $pengeluaran->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
        }
        $allCatatanPengeluaran = $pengeluaran->get();
        $pengeluaran = $pengeluaran->sum('catatan_jumlah');

        // dd($pengeluaran);

        $data->total = (object)[];
        $data->total->saldo_awal = $saldo_awal;
        $data->total->semua_pemasukkan = $pemasukkan;
        $data->total->semua_pengeluaran = $pengeluaran;
        $data->total->akumulasi = $pemasukkan - $pengeluaran;
        $data->total->saldo_akhir = $saldo_awal + $data->total->akumulasi;

        $data->grafik_batang = (object)[];
        $data->grafik_batang->pemasukkan = $pemasukkan;
        $data->grafik_batang->pengeluaran = $pengeluaran;

        // GRAFIK PEMASUKKAN
        $collectionMasuk = collect($allCatatanPemasukkan);
        $uniquePemasukkan = $collectionMasuk->unique('idx_sub_kategori');
        $uniquePemasukkan = $uniquePemasukkan->values()->all();
        $uniquePemasukkan = array_values($uniquePemasukkan);
        // dd($uniquePemasukkan);
        $sub_nama_masuk = [];
        $id_sub_kat = [];
        for ($i = 0; $i < count($uniquePemasukkan); $i++) {
            array_push($sub_nama_masuk, $uniquePemasukkan[$i]->sub_nama);
            array_push($id_sub_kat, $uniquePemasukkan[$i]->idx_sub_kategori);
        }
        $allJumlahMasuk = [];
        for ($i = 0; $i < count($id_sub_kat); $i++) {
            $jumlah = DB::table('tbl_catatan_buku')
                ->where('idx_buku_kas', $id_buku_kas)
                ->where('status', 'aktif')
                ->where('idx_kategori', 1)
                ->where('idx_sub_kategori', $id_sub_kat[$i]);
            if ($tipe == 1) {
                $jumlah = $jumlah->whereDate('tbl_catatan_buku.catatan_tgl', date('Y-m-d'));
            } elseif ($tipe == 2) {
                $jumlah = $jumlah->whereMonth('tbl_catatan_buku.catatan_tgl', date('m'))->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
            } else {
                $jumlah = $jumlah->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
            }
            $jumlah = $jumlah->sum('catatan_jumlah');

            array_push($allJumlahMasuk, $jumlah);
        }
        $data->pemasukkan = (object)[];
        $data->pemasukkan->sub_nama = $sub_nama_masuk;
        $data->pemasukkan->jumlah = $allJumlahMasuk;
        // END GRAFIK PEMASUKKAN

        // GRAFIK PENGELUARAN
        // dd($allCatatanPengeluaran);
        $collectionMasuk = collect($allCatatanPengeluaran);
        $uniquePengeluaran = $collectionMasuk->unique('idx_sub_kategori');
        $uniquePengeluaran = $uniquePengeluaran->values()->all();
        $uniquePengeluaran = array_values($uniquePengeluaran);

        $sub_nama_keluar = [];
        $id_sub_kat = [];
        for ($i = 0; $i < count($uniquePengeluaran); $i++) {
            array_push($sub_nama_keluar, $uniquePengeluaran[$i]->sub_nama);
            array_push($id_sub_kat, $uniquePengeluaran[$i]->idx_sub_kategori);
        }

        $allJumlahKeluar = [];
        for ($i = 0; $i < count($id_sub_kat); $i++) {
            $jumlah = DB::table('tbl_catatan_buku')
                ->where('idx_buku_kas', $id_buku_kas)
                ->where('status', 'aktif')
                ->where('idx_kategori', 2)
                ->where('idx_sub_kategori', $id_sub_kat[$i]);
            if ($tipe == 1) {
                $jumlah = $jumlah->whereDate('tbl_catatan_buku.catatan_tgl', date('Y-m-d'));
            } elseif ($tipe == 2) {
                $jumlah = $jumlah->whereMonth('tbl_catatan_buku.catatan_tgl', date('m'))->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
            } else {
                $jumlah = $jumlah->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
            }
            $jumlah = $jumlah->sum('catatan_jumlah');

            array_push($allJumlahKeluar, $jumlah);
        }

        $data->pengeluaran = (object)[];
        $data->pengeluaran->sub_nama = $sub_nama_keluar;
        $data->pengeluaran->jumlah = $allJumlahKeluar;
        // END GRAFIK PENGELUARAN

        // LINE GRAFIK TAHUNAN
        if ($tipe == 3) {
            $dataMasuk = [];
            $dataKeluar = [];
            $dataAkumulasi = [];
            for ($i = 1; $i < 13; $i++) {
                $pemasukkan = DB::table('tbl_catatan_buku')
                    ->where('idx_buku_kas', $id_buku_kas)
                    ->where('status', 'aktif')
                    ->where('idx_kategori', 1)
                    ->whereMonth('catatan_tgl', $i)
                    ->whereYear('catatan_tgl', date('Y'))
                    ->sum('catatan_jumlah');

                $pengeluaran = DB::table('tbl_catatan_buku')
                    ->where('idx_buku_kas', $id_buku_kas)
                    ->where('status', 'aktif')
                    ->where('idx_kategori', 2)
                    ->whereMonth('catatan_tgl', $i)
                    ->whereYear('catatan_tgl', date('Y'))
                    ->sum('catatan_jumlah');

                $akum = $pemasukkan - $pengeluaran;

                array_push($dataMasuk, $pemasukkan);
                array_push($dataKeluar, $pengeluaran);
                array_push($dataAkumulasi, $akum);
            }

            $data->line_grafik = (object)[];
            $data->line_grafik->masuk = $dataMasuk;
            $data->line_grafik->keluar = $dataKeluar;
            $data->line_grafik->akumilasi = $dataAkumulasi;
        }
        // END LINE GRAFIK TAHUNAN

        // DATA TABEL
        $tb = $catatan = DB::table('tbl_catatan_buku')
            ->leftJoin('tbl_sub_kategori as tsk', 'tbl_catatan_buku.idx_sub_kategori', '=', 'tsk.idx_sub_kat')
            ->where('tbl_catatan_buku.idx_buku_kas', $id_buku_kas)
            ->where('tbl_catatan_buku.id_user', $user_id)
            ->where('tbl_catatan_buku.status', 'aktif');
        if ($tipe == 1) {
            $tb = $tb->whereDate('tbl_catatan_buku.catatan_tgl', date('Y-m-d'));
        } elseif ($tipe == 2) {
            $tb = $tb->whereMonth('tbl_catatan_buku.catatan_tgl', date('m'))->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
        } else {
            $tb = $tb->whereYear('tbl_catatan_buku.catatan_tgl', date('Y'));
        }
        $tb = $tb->get(['tbl_catatan_buku.*', 'tsk.sub_nama', 'tsk.idx_sub_kat']);
        $data->data = $tb;
        // END DATA TABEL

        return $data;
    }

    public function getFilterDate($id_buku_kas, $user_id, $tanggal, $tahun, $bulan)
    {
        $data = (object)[];
        $catatan = DB::table('tbl_catatan_buku')
            ->leftJoin('tbl_sub_kategori as tsk', 'tbl_catatan_buku.idx_sub_kategori', '=', 'tsk.idx_sub_kat')
            ->where('tbl_catatan_buku.idx_buku_kas', $id_buku_kas)
            ->where('tbl_catatan_buku.id_user', $user_id)
            ->where('tbl_catatan_buku.status', 'aktif');
        if ($tanggal != '') {
            $catatan = $catatan->whereDay('tbl_catatan_buku.catatan_tgl', $tanggal);
        }
        if ($tahun != '') {
            $catatan = $catatan->whereYear('tbl_catatan_buku.catatan_tgl', $tahun);
        }
        if ($bulan != '') {
            $catatan = $catatan->whereMonth('tbl_catatan_buku.catatan_tgl', $bulan);
        }
        $allCatatanPemasukkan = $catatan->where('tbl_catatan_buku.idx_kategori', 1)->get();

        $saldo_awal = DB::table('tbl_buku_kas')->where('idx_buku_kas', $id_buku_kas)->sum('buku_saldo_awal');
        $pemasukkan = DB::table('tbl_catatan_buku')
            ->leftJoin('tbl_sub_kategori as tsk', 'tbl_catatan_buku.idx_sub_kategori', '=', 'tsk.idx_sub_kat')
            ->where('tbl_catatan_buku.idx_buku_kas', $id_buku_kas)
            ->where('tbl_catatan_buku.id_user', $user_id)
            ->where('tbl_catatan_buku.status', 'aktif');
        if ($tanggal != '') {
            $pemasukkan = $pemasukkan->whereDay('tbl_catatan_buku.catatan_tgl', $tanggal);
        }
        if ($tahun != '') {
            $pemasukkan = $pemasukkan->whereYear('tbl_catatan_buku.catatan_tgl', $tahun);
        }
        if ($bulan != '') {
            $pemasukkan = $pemasukkan->whereMonth('tbl_catatan_buku.catatan_tgl', $bulan);
        }
        $pemasukkan = $pemasukkan->where('tbl_catatan_buku.idx_kategori', 1)->sum('catatan_jumlah');

        $pengeluaran = DB::table('tbl_catatan_buku')
            ->leftJoin('tbl_sub_kategori as tsk', 'tbl_catatan_buku.idx_sub_kategori', '=', 'tsk.idx_sub_kat')
            ->where('tbl_catatan_buku.idx_buku_kas', $id_buku_kas)
            ->where('tbl_catatan_buku.id_user', $user_id)
            ->where('tbl_catatan_buku.status', 'aktif')
            ->where('tbl_catatan_buku.idx_kategori', 2);
        if ($tanggal != '') {
            $pengeluaran = $pengeluaran->whereDay('tbl_catatan_buku.catatan_tgl', $tanggal);
        }
        if ($tahun != '') {
            $pengeluaran = $pengeluaran->whereYear('tbl_catatan_buku.catatan_tgl', $tahun);
        }
        if ($bulan != '') {
            $pengeluaran = $pengeluaran->whereMonth('tbl_catatan_buku.catatan_tgl', $bulan);
        }
        $allCatatanPengeluaran = $pengeluaran->get();
        $pengeluaran = $pengeluaran->sum('catatan_jumlah');

        // dd($pengeluaran);

        $data->total = (object)[];
        $data->total->saldo_awal = $saldo_awal;
        $data->total->semua_pemasukkan = $pemasukkan;
        $data->total->semua_pengeluaran = $pengeluaran;
        $data->total->akumulasi = $pemasukkan - $pengeluaran;
        $data->total->saldo_akhir = $saldo_awal + $data->total->akumulasi;

        $data->grafik_batang = (object)[];
        $data->grafik_batang->pemasukkan = $pemasukkan;
        $data->grafik_batang->pengeluaran = $pengeluaran;

        // GRAFIK PEMASUKKAN
        $collectionMasuk = collect($allCatatanPemasukkan);
        $uniquePemasukkan = $collectionMasuk->unique('idx_sub_kategori');
        $uniquePemasukkan = $uniquePemasukkan->values()->all();
        $uniquePemasukkan = array_values($uniquePemasukkan);

        $sub_nama_masuk = [];
        $id_sub_kat = [];
        for ($i = 0; $i < count($uniquePemasukkan); $i++) {
            array_push($sub_nama_masuk, $uniquePemasukkan[$i]->sub_nama);
            array_push($id_sub_kat, $uniquePemasukkan[$i]->idx_sub_kategori);
        }
        $allJumlahMasuk = [];
        for ($i = 0; $i < count($id_sub_kat); $i++) {
            $jumlah = DB::table('tbl_catatan_buku')
                ->where('idx_buku_kas', $id_buku_kas)
                ->where('status', 'aktif')
                ->where('idx_kategori', 1)
                ->where('idx_sub_kategori', $id_sub_kat[$i]);
            if ($tanggal != '') {
                $jumlah = $jumlah->whereDay('tbl_catatan_buku.catatan_tgl', $tanggal);
            }
            if ($tahun != '') {
                $jumlah = $jumlah->whereYear('tbl_catatan_buku.catatan_tgl', $tahun);
            }
            if ($bulan != '') {
                $jumlah = $jumlah->whereMonth('tbl_catatan_buku.catatan_tgl', $bulan);
            }
            $jumlah = $jumlah->sum('catatan_jumlah');

            array_push($allJumlahMasuk, $jumlah);
        }
        $data->pemasukkan = (object)[];
        $data->pemasukkan->sub_nama = $sub_nama_masuk;
        $data->pemasukkan->jumlah = $allJumlahMasuk;
        // END GRAFIK PEMASUKKAN

        // GRAFIK PENGELUARAN
        $collectionMasuk = collect($allCatatanPengeluaran);
        $uniquePengeluaran = $collectionMasuk->unique('idx_sub_kategori');
        $uniquePengeluaran = $uniquePengeluaran->values()->all();
        $uniquePengeluaran = array_values($uniquePengeluaran);

        $sub_nama_keluar = [];
        $id_sub_kat = [];
        for ($i = 0; $i < count($uniquePengeluaran); $i++) {
            array_push($sub_nama_keluar, $uniquePengeluaran[$i]->sub_nama);
            array_push($id_sub_kat, $uniquePengeluaran[$i]->idx_sub_kategori);
        }

        $allJumlahKeluar = [];
        for ($i = 0; $i < count($id_sub_kat); $i++) {
            $jumlah = DB::table('tbl_catatan_buku')
                ->where('idx_buku_kas', $id_buku_kas)
                ->where('status', 'aktif')
                ->where('idx_kategori', 2)
                ->where('idx_sub_kategori', $id_sub_kat[$i]);
            if ($tanggal != '') {
                $jumlah = $jumlah->whereDay('tbl_catatan_buku.catatan_tgl', $tanggal);
            }
            if ($tahun != '') {
                $jumlah = $jumlah->whereYear('tbl_catatan_buku.catatan_tgl', $tahun);
            }
            if ($bulan != '') {
                $jumlah = $jumlah->whereMonth('tbl_catatan_buku.catatan_tgl', $bulan);
            }
            $jumlah = $jumlah->sum('catatan_jumlah');

            array_push($allJumlahKeluar, $jumlah);
        }

        $data->pengeluaran = (object)[];
        $data->pengeluaran->sub_nama = $sub_nama_keluar;
        $data->pengeluaran->jumlah = $allJumlahKeluar;
        // END GRAFIK PENGELUARAN

        // DATA TABEL
        $tb = $catatan = DB::table('tbl_catatan_buku')
            ->leftJoin('tbl_sub_kategori as tsk', 'tbl_catatan_buku.idx_sub_kategori', '=', 'tsk.idx_sub_kat')
            ->where('tbl_catatan_buku.idx_buku_kas', $id_buku_kas)
            ->where('tbl_catatan_buku.id_user', $user_id)
            ->where('tbl_catatan_buku.status', 'aktif');
        if ($tanggal != '') {
            $tb = $tb->whereDay('tbl_catatan_buku.catatan_tgl', $tanggal);
        }
        if ($tahun != '') {
            $tb = $tb->whereYear('tbl_catatan_buku.catatan_tgl', $tahun);
        }
        if ($bulan != '') {
            $tb = $tb->whereMonth('tbl_catatan_buku.catatan_tgl', $bulan);
        }
        $tb = $tb->get();

        $data->data = $tb;
        // END DATA TABEL

        return $data;
    }

    public function getDataLaporan($id_buku_kas, $type, $tanggal, $tahun, $bulan, $user_id)
    {
        if ($type == '') {
            $data = $this->getFilterDate($id_buku_kas, $user_id, $tanggal, $tahun, $bulan);

            return $data;
        }

        if ($tanggal == '' && $tahun == '' && $bulan == '') {
            switch ($type) {
                case 'harian':
                    $data = $this->getHarian($id_buku_kas, $user_id, 1);

                    return $data;
                    break;
                case 'bulanan':
                    $data = $this->getHarian($id_buku_kas, $user_id, 2);

                    return $data;
                    break;
                case 'tahunan':
                    $data = $this->getHarian($id_buku_kas, $user_id, 3);

                    return $data;
                    break;

                default:
                    return [];
                    break;
            }
        }
    }
}
