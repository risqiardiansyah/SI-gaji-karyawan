<?php

namespace App\Http\Repositories;

use App\Exports\PenilaianExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SKRepository
{
    var $delete_data;
    public function listSK()
    {
        $select = [
            'surat_keterangan.*',
            's.name as signer_name',
            's.position as signer_position',
            's.instansi as signer_instansi',
            's.address as signer_address',
            'k.nama as karyawan_nama',
            'k.asal_kampus',
            'k.alamat as karyawan_address'
        ];

        $list = DB::table('surat_keterangan')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'surat_keterangan.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'surat_keterangan.karyawan_code')
            ->orderBy('surat_keterangan.created_at', 'desc')
            ->get();

        return $list;
    }

    public function createSK($request)
    {
        try {
            $data = [
                'no_surat' => $this->generateNomorSurat($request->category),
                'unique_code' => $this->generateUniqueCode($request->category),
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'signer_code' => $request->signer_code,
                'karyawan_code' => $request->karyawan_code,
                'position' => $request->position,
                'office' => $request->office,
                'category' => $request->category,
                'kop_code' => $request->kop_code,
            ];

            if (isset($request->present)) {
                $data['present'] = $request->present;
            }

            DB::table('surat_keterangan')->insert($data);
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function generateNomorSurat($category)
    {
        $type = 'KAR';
        if ($category === 1) {
            $type = 'MAG';
        }

        $month = date('m');
        $no = 0;
        $jml = DB::table('surat_keterangan')->where('category', $category)->whereMonth('created_at', $month)->count();
        $total = $jml + 1;
        if (strlen((string)$total) == 1) {
            $no = '00' . $total;
        } elseif (strlen((string)$total) == 2) {
            $no = '0' . $total;
        } else {
            $no = $total;
        }
        $romawi = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $bulan = $romawi[date('n')];
        $tahun = date('Y');

        return $no . '/HRD/' . $type . '/AMI/' . $bulan . '/' . $tahun;
    }

    public function generateUniqueCode($category)
    {
        $type = 'KAR';
        if ($category === 1) {
            $type = 'MAG';
        }

        $month = date('m');
        $no = 0;
        $jml = DB::table('surat_keterangan')->where('category', $category)->whereMonth('created_at', $month)->count();
        $total = $jml + 1;
        if (strlen((string)$total) == 1) {
            $no = '00' . $total;
        } elseif (strlen((string)$total) == 2) {
            $no = '0' . $total;
        } else {
            $no = $total;
        }
        $romawi = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $bulan = $romawi[date('n')];
        $tahun = date('Y');

        return $no . '-HRD-' . $type . '-AMI-' . $bulan . '-' . $tahun;
    }

    public function getSKData($code)
    {
        $select = [
            'surat_keterangan.*',
            's.name as signer_name',
            's.position as signer_position',
            's.instansi as signer_instansi',
            's.address as signer_address',
            'k.nama as karyawan_nama',
            'k.email as karyawan_email',
            'k.asal_kampus',
            'k.alamat as karyawan_address',
            'k.jenis_kelamin as karyawan_jenis_kelamin',
        ];
        $quo = DB::table('surat_keterangan')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'surat_keterangan.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'surat_keterangan.karyawan_code')
            ->where('unique_code', $code)
            ->first();

        $quo->from_date = date('d F Y', strtotime($quo->from_date));
        $quo->to_date = date('d F Y', strtotime($quo->to_date));
        $quo->now = date('d F Y');

        return $quo;
    }

    public function detailSK($code)
    {
        $select = [
            'surat_keterangan.*',
            's.name as signer_name',
            's.position as signer_position',
            's.instansi as signer_instansi',
            's.address as signer_address',
            'k.nama as karyawan_nama',
            'k.email as karyawan_email',
            'k.asal_kampus',
            'k.alamat as karyawan_address'
        ];
        $quo = DB::table('surat_keterangan')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'surat_keterangan.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'surat_keterangan.karyawan_code')
            ->where('unique_code', $code)
            ->first();

        return $quo;
    }

    public function editSK($request)
    {
        try {
            $data = [
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'signer_code' => $request->signer_code,
                'karyawan_code' => $request->karyawan_code,
                'position' => $request->position,
                'office' => $request->office,
                'category' => $request->category,
                'kop_code' => $request->kop_code,
                'present' => $request->present,
            ];

            DB::table('surat_keterangan')->where('unique_code', $request->unique_code)->update($data);
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteSK($code)
    {
        try {
            DB::table('surat_keterangan')->where('unique_code', $code)->delete();
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function generateNomorSuratPelanggaran($sp_ke)
    {
        $month = date('m');
        $no = 0;
        $jml = DB::table('surat_peringatan')->whereMonth('created_at', $month)->count();
        $total = $jml + 1;
        if (strlen((string)$total) == 1) {
            $no = '00' . $total;
        } elseif (strlen((string)$total) == 2) {
            $no = '0' . $total;
        } else {
            $no = $total;
        }
        $romawi = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $bulan = $romawi[date('n')];
        $tahun = date('Y');

        return $no . '/HRD/SP' . $sp_ke . '/AMI/' . $bulan . '/' . $tahun;
    }

    public function dataSupel()
    {
        $select = [
            'surat_peringatan.*',
            's.name as signer_name',
            's.position as signer_position',
            's.instansi as signer_instansi',
            's.address as signer_address',
            'k.nama as karyawan_nama',
            'k.alamat as karyawan_address',
            'k.posisi as karyawan_position'
        ];

        $list = DB::table('surat_peringatan')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'surat_peringatan.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'surat_peringatan.karyawan_code')
            ->where('surat_peringatan.is_deleted', 0)
            ->orderBy('surat_peringatan.created_at', 'desc')
            ->get();

        return $list;
    }

    public function detailSupel($unique_code)
    {
        $select = [
            'surat_peringatan.*',
            's.name as signer_name',
            's.position as signer_position',
            's.instansi as signer_instansi',
            's.address as signer_address',
            'k.nama as karyawan_nama',
            'k.alamat as karyawan_address',
            'k.posisi as karyawan_position',
            'k.email as karyawan_email'
        ];

        $data = DB::table('surat_peringatan')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'surat_peringatan.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'surat_peringatan.karyawan_code')
            ->where('unique_code', $unique_code)
            ->first();

        return $data;
    }

    public function checkSupel($karyawan_code)
    {
        try {
            $ke = 0;
            $sp1 = DB::table('surat_peringatan')->where('karyawan_code', $karyawan_code)->where('sp_ke', 1)->where('is_deleted', 0)->exists();
            if (!$sp1) {
                $ke = 1;
                $ret = (object)[
                    'sp_ke' => $ke,
                ];

                return ['success' => true, 'data' => $ret];
            }

            $sp2 = DB::table('surat_peringatan')->where('karyawan_code', $karyawan_code)->where('sp_ke', 2)->where('is_deleted', 0)->exists();
            if (!$sp2) {
                $ke = 2;
                $ret = (object)[
                    'sp_ke' => $ke,
                ];

                return ['success' => true, 'data' => $ret];
            }

            $sp3 = DB::table('surat_peringatan')->where('karyawan_code', $karyawan_code)->where('sp_ke', 3)->where('is_deleted', 0)->exists();
            if (!$sp3) {
                $ke = 3;
                $ret = (object)[
                    'sp_ke' => $ke,
                ];

                return ['success' => true, 'data' => $ret];
            }

            $ret = (object)[
                'sp_ke' => 0,
            ];

            return ['success' => true, 'data' => $ret];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function createSupel($request)
    {
        try {
            if ($request->sp_ke == 1) {
                $data = [
                    'no_surat' => $this->generateNomorSuratPelanggaran($request->sp_ke),
                    'unique_code' => generateFiledCode('UC'),
                    'karyawan_code' => $request->karyawan_code,
                    'signer_code' => $request->signer_code,
                    'pelanggaran' => $request->pelanggaran,
                    'tgl_surat' => $request->tgl_surat,
                    'sp_ke' => $request->sp_ke,
                    'sp_bulan' => $request->sp_bulan,
                    'kop_code' => $request->kop_code,
                ];

                DB::table('surat_peringatan')->insert($data);
            } else if ($request->sp_ke == 2) {
                $sp1 = DB::table('surat_peringatan')->where('karyawan_code', $request->karyawan_code)->where('sp_ke', 1)->first();
                $data = [
                    'no_surat' => $this->generateNomorSuratPelanggaran($request->sp_ke),
                    'unique_code' => generateFiledCode('UC'),
                    'karyawan_code' => $request->karyawan_code,
                    'signer_code' => $request->signer_code,
                    'pelanggaran' => $sp1->pelanggaran,
                    'tgl_surat' => $request->tgl_surat,
                    'sp_ke' => $request->sp_ke,
                    'sp_bulan' => 0,
                    'kop_code' => $request->kop_code,
                ];

                DB::table('surat_peringatan')->insert($data);
            } else {
                $sp1 = DB::table('surat_peringatan')->where('karyawan_code', $request->karyawan_code)->where('sp_ke', 1)->first();
                $data = [
                    'no_surat' => $this->generateNomorSuratPelanggaran($request->sp_ke),
                    'unique_code' => generateFiledCode('UC'),
                    'karyawan_code' => $request->karyawan_code,
                    'signer_code' => $request->signer_code,
                    'pelanggaran' => $sp1->pelanggaran,
                    'tgl_surat' => $request->tgl_surat,
                    'sp_ke' => $request->sp_ke,
                    'sp_bulan' => $request->sp_bulan,
                    'kop_code' => $request->kop_code,
                ];

                DB::table('surat_peringatan')->insert($data);
            }
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editSupel($request)
    {
        try {
            if ($request->sp_ke == 1) {
                $data = [
                    'karyawan_code' => $request->karyawan_code,
                    'signer_code' => $request->signer_code,
                    'pelanggaran' => $request->pelanggaran,
                    'tgl_surat' => $request->tgl_surat,
                    'sp_ke' => $request->sp_ke,
                    'sp_bulan' => $request->sp_bulan,
                    'kop_code' => $request->kop_code,
                ];

                DB::table('surat_peringatan')->where('unique_code', $request->unique_code)->update($data);
            } else if ($request->sp_ke == 2) {
                $sp1 = DB::table('surat_peringatan')->where('karyawan_code', $request->karyawan_code)->where('sp_ke', 1)->first();
                $data = [
                    'karyawan_code' => $request->karyawan_code,
                    'signer_code' => $request->signer_code,
                    'pelanggaran' => $sp1->pelanggaran,
                    'tgl_surat' => $request->tgl_surat,
                    'sp_ke' => $request->sp_ke,
                    'sp_bulan' => 0,
                    'kop_code' => $request->kop_code,
                ];

                DB::table('surat_peringatan')->where('unique_code', $request->unique_code)->update($data);
            } else {
                $sp1 = DB::table('surat_peringatan')->where('karyawan_code', $request->karyawan_code)->where('sp_ke', 1)->first();
                $data = [
                    'karyawan_code' => $request->karyawan_code,
                    'signer_code' => $request->signer_code,
                    'pelanggaran' => $sp1->pelanggaran,
                    'tgl_surat' => $request->tgl_surat,
                    'sp_ke' => $request->sp_ke,
                    'sp_bulan' => $request->sp_bulan,
                    'kop_code' => $request->kop_code,
                ];

                DB::table('surat_peringatan')->where('unique_code', $request->unique_code)->update($data);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteSupel($code)
    {
        try {
            DB::table('surat_peringatan')->where('unique_code', $code)->update(['is_deleted' => 1]);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function kriteriaPenilaian($type)
    {
        if ($type == 'mingguan') {
            $data = DB::table('penilaian_evaluasi')->where('type', 'mingguan')->where('group_mingguan', '!=', '')->groupBy('group_mingguan')->get(['group_mingguan']);
            for ($i = 0; $i < count($data); $i++) {
                $detail = DB::table('penilaian_evaluasi')->where('type', 'mingguan')->where('group_mingguan', $data[$i]->group_mingguan)->get();
                $data[$i]->detail = $detail;
            }
        } else if ($type == 'bulanan') {
            $data = DB::table('penilaian_evaluasi')->where('type', 'bulanan')->get();
        } else if ($type == '6bulanan') {
            $data = DB::table('penilaian_evaluasi')->where('type', '6bulanan')->get();
        } else if ($type == 'all') {
            $data = DB::table('penilaian_evaluasi')->get();
        }

        return $data;
    }

    public function allKriteriaPenilaian()
    {
        $data = DB::table('penilaian_evaluasi')->get();

        return $data;
    }

    public function addKriteriaPenilaian($request)
    {
        try {
            $data = [
                'pe_code' => generateFiledCode('PE'),
                'pe_name_id' => $request->pe_name_id,
                'type' => $request->type,
                'pe_bobot' => $request->pe_bobot,
            ];
            if ($request->type == 'mingguan') {
                $data['group_mingguan'] = $request->group_mingguan;
            }
            if ($request->type == 'bulanan') {
                $data['pe_name_en'] = $request->pe_name_en;
            }
            $data = DB::table('penilaian_evaluasi')->insert($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editKriteriaPenilaian($request)
    {
        try {
            $data = [
                'pe_name_id' => $request->pe_name_id,
                'type' => $request->type,
                'pe_bobot' => $request->pe_bobot,
            ];
            if ($request->type == 'mingguan') {
                $data['group_mingguan'] = $request->group_mingguan;
            }
            if ($request->type == 'bulanan') {
                $data['pe_name_en'] = $request->pe_name_en;
            }
            $data = DB::table('penilaian_evaluasi')->where('pe_code', $request->pe_code)->update($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteKriteriaPenilaian($code)
    {
        try {
            DB::table('penilaian_evaluasi')->where('pe_code', $code)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function suratPenilaian()
    {
        $select = [
            'penilaian.*',
            // 's.name as signer_name',
            // 's.position as signer_position',
            // 's.instansi as signer_instansi',
            // 's.address as signer_address',
            'k.nama as karyawan_nama',
            'k.alamat as karyawan_address',
            'k.posisi as karyawan_position'
        ];

        $list = DB::table('penilaian')
            ->select($select)
            // ->leftJoin('signer as s', 's.signer_code', '=', 'penilaian.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'penilaian.karyawan_code');

        if (!str_contains(Auth::user()->role, '1') && !str_contains(Auth::user()->role, '7')) {
            $list = $list->where('penilaian.karyawan_code', Auth::user()->karyawan_code)
                ->orWhere('penilaian.signer_code', Auth::user()->karyawan_code);
        }

        $list = $list->orderBy('penilaian.tgl', 'desc')->get();

        for ($i = 0; $i < count($list); $i++) {
            $signer = DB::table('signer')->where('signer_code', $list[$i]->signer_code)->first();
            if (!empty($signer)) {
                $list[$i]->signer_name = $signer->name;
                $list[$i]->signer_position = $signer->position;
                $list[$i]->signer_instansi = $signer->instansi;
                $list[$i]->signer_address = $signer->address;
            } else {
                $karyawan = DB::table('karyawans')->where('karyawan_code', $list[$i]->signer_code)->first();
                if (!empty($karyawan)) {
                    $list[$i]->signer_name = $karyawan->nama;
                    $list[$i]->signer_position = $karyawan->posisi;
                    $list[$i]->signer_instansi = '-';
                    $list[$i]->signer_address = '-';
                } else {
                    $list[$i]->signer_name = '-';
                    $list[$i]->signer_position = '-';
                    $list[$i]->signer_instansi = '-';
                    $list[$i]->signer_address = '-';
                }
            }
        }

        return $list;
    }

    public function detailSuratPenilaian($unique_code)
    {
        $select = [
            'penilaian.*',
            's.name as mengetahui_name',
            's.position as mengetahui_position',
            's.instansi as mengetahui_instansi',
            's.address as mengetahui_address',
            'k.nama as karyawan_nama',
            'k.alamat as karyawan_address',
            'k.posisi as karyawan_position',
            'k.email as karyawan_email',
            'k.nik as karyawan_nik'
        ];

        $data = DB::table('penilaian')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'penilaian.mengetahui_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'penilaian.karyawan_code')
            ->where('penilaian.penilaian_code', $unique_code)
            ->first();

        if (!empty($data)) {
            if ($data->type == 'mingguan') {
                $group = DB::table('penilaian_evaluasi')
                    ->where('type', 'mingguan')
                    ->where('group_mingguan', '!=', '')
                    ->groupBy('group_mingguan')
                    ->get(['group_mingguan']);

                $detail = [];
                for ($i = 0; $i < count($group); $i++) {
                    $det = DB::table('penilaian_evaluasi')
                        ->where('type', 'mingguan')
                        ->where('group_mingguan', $group[$i]->group_mingguan)
                        ->get([
                            'pe_code',
                            'pe_name_id as pe_name',
                            'pe_name_en',
                            'pe_bobot',
                            'type',
                            'group_mingguan'
                        ]);
                    for ($x = 0; $x < count($det); $x++) {
                        $dat = DB::table('penilaian_detail')
                            ->where('penilaian_code', $data->penilaian_code)
                            ->where('pe_code', $det[$x]->pe_code)
                            ->first();
                        if (empty($dat)) {
                            $det[$x]->nilai = 0;
                            $det[$x]->total_nilai = 0;
                            $det[$x]->catatan = '';
                            $det[$x]->detail_code = '';
                        } else {
                            $det[$x]->nilai = $dat->nilai;
                            $det[$x]->total_nilai = $dat->total_nilai;
                            $det[$x]->catatan = $dat->catatan;
                            $det[$x]->detail_code = $dat->detail_code;
                        }
                    }

                    $gr = (object)[
                        'group_mingguan' => $group[$i]->group_mingguan,
                        'detail' => $det
                    ];
                    array_push($detail, $gr);
                }
            } else {
                $select = [
                    'penilaian_detail.detail_code',
                    'penilaian_detail.penilaian_code',
                    'penilaian_detail.pe_code',
                    'penilaian_detail.nilai',
                    'penilaian_detail.total_nilai',
                    'penilaian_detail.catatan',
                    'penilaian_detail.created_at',
                    'penilaian_evaluasi.pe_name_id as pe_name',
                    'penilaian_evaluasi.pe_name_en',
                    'penilaian_evaluasi.pe_bobot',
                    'penilaian_evaluasi.type',
                    'penilaian_evaluasi.group_mingguan',
                ];
                $detail = DB::table('penilaian_detail')
                    ->select($select)
                    ->leftJoin('penilaian_evaluasi', 'penilaian_evaluasi.pe_code', '=', 'penilaian_detail.pe_code')
                    ->where('penilaian_code', $data->penilaian_code)
                    ->get();
            }
            $data->performance = $detail;

            $signer = DB::table('signer')->where('signer_code', $data->signer_code)->first();
            if (!empty($signer)) {
                $data->signer_name = $signer->name;
                $data->signer_position = $signer->position;
                $data->signer_instansi = $signer->instansi;
                $data->signer_address = $signer->address;
            } else {
                $karyawan = DB::table('karyawans')->where('karyawan_code', $data->signer_code)->first();
                if (!empty($karyawan)) {
                    $data->signer_name = $karyawan->nama;
                    $data->signer_position = $karyawan->posisi;
                    $data->signer_instansi = '-';
                    $data->signer_address = '-';
                } else {
                    $data->signer_name = '-';
                    $data->signer_position = '-';
                    $data->signer_instansi = '-';
                    $data->signer_address = '-';
                }
            }
        }

        return $data;
    }

    public function createSuratPenilaian($request)
    {
        try {
            $periode = '';
            if ($request->type == 'mingguan') {
                $periode = 'Minggu ke-' . weekOfMonth(strtotime($request->tgl));
            } else if ($request->type == 'bulanan') {
                $periode = 'Bulan ' . Carbon::parse($request->tgl)->locale('id')->isoformat('MMMM');
            } else if ($request->type == '6bulanan') {
                $periode = 'Semester 1';
                $month = date('n', strtotime($request->tgl));
                if ($month > 6) {
                    $periode = 'Semester 2';
                }
            }

            if (Auth::user()->karyawan_code == $request->karyawan_code) {
                return ['success' => false, 'msg' => 'Action not permitted'];
            }

            $data = [
                'penilaian_code' => generateFiledCode('PC'),
                'type' => $request->type,
                'karyawan_code' => $request->karyawan_code,
                'signer_code' => $request->signer_code,
                'mengetahui_code' => $request->mengetahui_code,
                'tgl' => $request->tgl,
                'periode' => $periode,
                'total_nilai' => $request->nilai_keseluruhan,
            ];
            DB::table('penilaian')->insert($data);

            $performance = $request->performance;
            for ($i = 0; $i < count($performance); $i++) {
                if ($request->type == 'mingguan') {
                    for ($x = 0; $x < count($performance[$i]['detail']); $x++) {
                        $detail = [
                            'detail_code' => generateFiledCode('PRC'),
                            'penilaian_code' => $data['penilaian_code'],
                            'pe_code' => $performance[$i]['detail'][$x]['pe_code'],
                            'group_mingguan' => $performance[$i]['group_mingguan'],
                            'nilai' => $performance[$i]['detail'][$x]['nilai'],
                            'total_nilai' => $performance[$i]['detail'][$x]['total_nilai'],
                            'catatan' => $performance[$i]['detail'][$x]['catatan'],
                        ];
                        DB::table('penilaian_detail')->insert($detail);
                    }
                } else {
                    $detail = [
                        'detail_code' => generateFiledCode('PRC'),
                        'penilaian_code' => $data['penilaian_code'],
                        'pe_code' => $performance[$i]['pe_code'],
                        'group_mingguan' => '',
                        'nilai' => $performance[$i]['nilai'],
                        'total_nilai' => $performance[$i]['total_nilai'],
                        'catatan' => $performance[$i]['catatan'],
                    ];
                    DB::table('penilaian_detail')->insert($detail);
                }
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function exportPenilaian($request)
    {
        $data = Excel::store(new PenilaianExport($request->year, $request->month), 'public/export/Penilaian.xlsx');
        Storage::putFile('exports', storage_path('app/public/export/Penilaian.xlsx'));

        $file = asset('storage/export/Penilaian.xlsx');
        return ['success' => true, 'data' => (object)[$file]];
    }

    public function editSuratPenilaian($request)
    {
        try {
            $periode = '';
            if ($request->type == 'mingguan') {
                $periode = 'Minggu ke-' . weekOfMonth(strtotime($request->tgl));
            } else if ($request->type == 'bulanan') {
                $periode = 'Bulan ' . Carbon::parse($request->tgl)->locale('id')->isoformat('MMMM');
            } else if ($request->type == '6bulanan') {
                $periode = 'Semester 1';
                $month = date('n', strtotime($request->tgl));
                if ($month > 6) {
                    $periode = 'Semester 2';
                }
            }

            if (Auth::user()->karyawan_code == $request->karyawan_code) {
                return ['success' => false, 'msg' => 'Action not permitted'];
            }

            $data = [
                'type' => $request->type,
                'karyawan_code' => $request->karyawan_code,
                'signer_code' => $request->signer_code,
                'mengetahui_code' => $request->mengetahui_code,
                'tgl' => $request->tgl,
                'periode' => $periode,
                'total_nilai' => $request->nilai_keseluruhan,
            ];
            DB::table('penilaian')->where('penilaian_code', $request->penilaian_code)->update($data);

            DB::table('penilaian_detail')->where('penilaian_code', $request->penilaian_code)->delete();
            $performance = $request->performance;
            for ($i = 0; $i < count($performance); $i++) {
                if ($request->type == 'mingguan') {
                    for ($x = 0; $x < count($performance[$i]['detail']); $x++) {
                        $detail = [
                            'detail_code' => generateFiledCode('PRC'),
                            'penilaian_code' => $request->penilaian_code,
                            'pe_code' => $performance[$i]['detail'][$x]['pe_code'],
                            'group_mingguan' => $performance[$i]['group_mingguan'],
                            'nilai' => $performance[$i]['detail'][$x]['nilai'],
                            'total_nilai' => $performance[$i]['detail'][$x]['total_nilai'],
                            'catatan' => $performance[$i]['detail'][$x]['catatan'],
                        ];
                        DB::table('penilaian_detail')->insert($detail);
                    }
                } else {
                    $detail = [
                        'detail_code' => generateFiledCode('PRC'),
                        'penilaian_code' => $request->penilaian_code,
                        'pe_code' => $performance[$i]['pe_code'],
                        'group_mingguan' => '',
                        'nilai' => $performance[$i]['nilai'],
                        'total_nilai' => $performance[$i]['total_nilai'],
                        'catatan' => $performance[$i]['catatan'],
                    ];
                    DB::table('penilaian_detail')->insert($detail);
                }
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteSuratPenilaian($code)
    {
        try {
            $penilaian = DB::table('penilaian')->where('penilaian_code', $code)->first();
            if (!empty($penilaian)) {
                if ($penilaian->karyawan_code == Auth::user()->karyawan_code) {
                    return ['success' => false, 'msg' => 'action not permitted'];
                }
            }
            DB::table('penilaian')->where('penilaian_code', $code)->delete();
            DB::table('penilaian_detail')->where('penilaian_code', $code)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function statistikPenilaian($request)
    {
        // try {
        $type = $request->type;
        $karyawan_code = $request->karyawan_code;

        $result = [];
        if (empty($karyawan_code)) {
            $data = DB::table('penilaian')
                ->where('penilaian.type', $type)
                ->groupBy('periode')
                ->get(['periode']);

            for ($x = 0; $x < count($data); $x++) {
                $karyawan = DB::table('karyawans')->where('aktif_bekerja', 1)->get();
                for ($i = 0; $i < count($karyawan); $i++) {
                    $nilai = DB::table('penilaian')
                        ->where('penilaian.type', $type)
                        ->where('penilaian.karyawan_code', $karyawan[$i]->karyawan_code)
                        ->where('penilaian.periode', $data[$x]->periode)
                        ->first();
                    if (!empty($nilai)) {
                        $month = Carbon::parse($nilai->tgl)->locale('id')->isoformat('MMMM');

                        $result[$x][$karyawan[$i]->nama] = $nilai->total_nilai;
                        $result[$x]['name'] = $data[$x]->periode . ' (' . $month . ')';
                    }
                }
            }
        } else {
            $data = $select = [
                'penilaian.*',
                'karyawans.nama as karyawan_nama',
            ];
            $data = DB::table('penilaian')
                ->select($select)
                ->leftJoin('karyawans', 'karyawans.karyawan_code', '=', 'penilaian.karyawan_code')
                ->where('penilaian.type', $type)
                ->where('penilaian.karyawan_code', $karyawan_code)
                ->orderBy('penilaian.tgl', 'asc')
                ->get();

            for ($i = 0; $i < count($data); $i++) {
                $month = Carbon::parse($data[$i]->tgl)->locale('id')->isoformat('MMMM');
                $result[$i]['name'] = $data[$i]->periode . ' (' . $month . ')';
                $result[$i][$data[$i]->karyawan_nama] = $data[$i]->total_nilai;
            }
        }

        return ['success' => true, 'data' => $result];
        // } catch (\Exception $e) {
        //     return ['success' => false, 'msg' => $e->getMessage()];
        // }
    }

    public function MOMWithData()
    {
        $data = DB::table('mom_with')->get();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]->is_nested) {
                $mwn = DB::table('mom_with_nested')->where('mw_code', $data[$i]->mw_code)->get();
                $data[$i]->mwn_data = $mwn;
            }
        }

        return $data;
    }

    public function detailMOM($code)
    {
        $data = DB::table('mom')->where('mom_code', $code)->first();
        if (!empty($data)) {
            $mom_with = DB::table('mom_with_selected')
                ->leftJoin('mom_with', 'mom_with.mw_code', '=', 'mom_with_selected.mw_code')
                ->where('mom_code', $code)
                ->get(['mom_with.mw_code', 'mw_name', 'mom_with.is_nested', 'mom_with_selected.mw_nested', 'mwn_code', 'mom_code']);

            $data->mom_with = $mom_with;

            $mwn_code = '';
            for ($i = 0; $i < count($mom_with); $i++) {
                if ($mom_with[$i]->mw_nested) {
                    $mwn_code .= $i != 0 ? ',' . $mom_with[$i]->mwn_code : $mom_with[$i]->mwn_code;
                }
            }

            $mom_with_nested = DB::table('mom_with_nested')->whereIn('mwn_code', explode(',', $mwn_code))->get(['mw_code', 'mwn_code']);
            $data->mom_with_nested = $mom_with_nested;

            if ($data->type_discussion == 'table') {
                $table = DB::table('mom_table')->where('mom_code', $data->mom_code)->get();
                $data->mom_table = $table;
            }
        }

        return $data;
    }

    public function detailCetakMOM($code)
    {
        $data = DB::table('mom')->where('mom_code', $code)->first();
        if (!empty($data)) {
            $with = DB::table('mom_with')->get(['mw_code', 'mw_name', 'is_nested']);
            for ($i = 0; $i < count($with); $i++) {
                $check = DB::table('mom_with_selected')->where('mom_code', $data->mom_code)->where('mw_code', $with[$i]->mw_code)->exists();
                $with[$i]->checked = $check ? true : false;

                $mwn_data = [];
                if ($with[$i]->is_nested) {
                    $mwn = DB::table('mom_with_nested')->where('mw_code', $with[$i]->mw_code)->get(['mw_code', 'mwn_code', 'mwn_name']);
                    for ($x = 0; $x < count($mwn); $x++) {
                        $check = DB::table('mom_with_selected')->where('mom_code', $data->mom_code)->where('mw_code', $mwn[$x]->mw_code)->where('mwn_code', 'LIKE', '%' . $mwn[$x]->mwn_code . '%')->exists();
                        $mwn[$x]->checked = $check ? true : false;
                    }
                    $mwn_data = $mwn;
                }
                $with[$i]->mwn_data = $mwn_data;
            }
            $data->mom_with = $with;

            if ($data->type_discussion == 'table') {
                $table = DB::table('mom_table')->where('mom_code', $data->mom_code)->get();
                $data->mom_table = $table;
            }

            $user = DB::table('users')
                ->leftJoin('karyawans', 'karyawans.karyawan_code', '=', 'users.karyawan_code')
                ->where('users_code', $data->created_by)
                ->first(['users.users_code', 'users.name', 'karyawans.nama as karyawan_nama', 'karyawans.posisi']);
            $data->user = $user;

            $signer = DB::table('signer')->where('signer_code', $data->signer_code)->first();
            $data->signer = $signer;

            $data->no_surat_formated = str_replace('/', '-', $data->no_surat);
        }

        return $data;
    }

    public function MOMData()
    {
        $data = DB::table('mom')->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();
        for ($i = 0; $i < count($data); $i++) {
            $with = DB::table('mom_with_selected')
                ->leftJoin('mom_with', 'mom_with.mw_code', '=', 'mom_with_selected.mw_code')
                ->where('mom_code', $data[$i]->mom_code)
                ->get(['mom_with.mw_code', 'mw_name', 'mom_with.is_nested', 'mom_with_selected.mw_nested', 'mwn_code', 'mom_code']);

            $nama_perusahaan = '';
            for ($x = 0; $x < count($with); $x++) {
                $mwn_data = [];
                if ($with[$x]->mw_nested) {
                    $mwn = explode(', ', $with[$x]->mwn_code);
                    $mwn_data = DB::table('mom_with_nested')->whereIn('mwn_code', $mwn)->get();
                }

                $with[$x]->mwn_data = $mwn_data;
                $nama_perusahaan .= $x == 0 ? $with[$x]->mw_name : ', ' . $with[$x]->mw_name;
            }
            $data[$i]->nama_perusahaan = $nama_perusahaan;

            $data[$i]->mom_with = $with;

            $user = DB::table('users')->where('users_code', $data[$i]->created_by)->first();
            $data[$i]->created_by_name = $user->name;
        }

        return $data;
    }

    public function generateNomorMOM()
    {
        $month = date('m');
        $no = 0;
        $jml = DB::table('mom')->whereMonth('created_at', $month)->count();
        $total = $jml + 1;
        if (strlen((string)$total) == 1) {
            $no = '00' . $total;
        } elseif (strlen((string)$total) == 2) {
            $no = '0' . $total;
        } else {
            $no = $total;
        }
        $romawi = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $bulan = $romawi[date('n')];
        $tahun = date('Y');

        return $no . '/MOM/AMI/' . $bulan . '/' . $tahun;
    }

    public function createMOM($request)
    {
        try {
            $mom = [
                'mom_code' => generateFiledCode('MOM'),
                'no_surat' => $this->generateNomorMOM(),
                'tgl' => $request->tgl,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'tempat' => $request->tempat,
                'agenda' => $request->agenda,
                'is_other_company' => $request->is_other_company,
                'nama_perusahaan' => $request->nama_perusahaan,
                'type_discussion' => $request->type_discussion,
                'catatan' => $request->catatan,
                'pembahasan' => $request->pembahasan,
                'created_by' => Auth::user()->users_code,
                'signer_code' => $request->signer_code,
            ];
            DB::table('mom')->insert($mom);

            $mom_with = $request->mom_with;
            for ($i = 0; $i < count($mom_with); $i++) {
                $dmw = [
                    'mom_code' => $mom['mom_code'],
                    'mw_code' => $mom_with[$i]['mw_code'],
                    'mw_nested' => $mom_with[$i]['mw_nested'],
                    'mwn_code' => isset($mom_with[$i]['mwn_code']) ? $mom_with[$i]['mwn_code'] : null,
                ];
                DB::table('mom_with_selected')->insert($dmw);
            }

            if ($request->type_discussion == 'table') {
                $mom_table = $request->mom_table;
                for ($i = 0; $i < count($mom_table); $i++) {
                    $dtb = [
                        'mom_code' => $mom['mom_code'],
                        'pembahasan' => $mom_table[$i]['pembahasan'],
                        'tindak_lanjut' => $mom_table[$i]['tindak_lanjut'],
                        'pic' => $mom_table[$i]['pic'],
                        'target' => $mom_table[$i]['target'],
                        'status' => $mom_table[$i]['status'],
                    ];
                    DB::table('mom_table')->insert($dtb);
                }
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editMOM($request)
    {
        // try {
        $mom = [
            'tgl' => $request->tgl,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'tempat' => $request->tempat,
            'agenda' => $request->agenda,
            'is_other_company' => $request->is_other_company,
            'nama_perusahaan' => $request->nama_perusahaan,
            'type_discussion' => $request->type_discussion,
            'catatan' => $request->catatan,
            'pembahasan' => $request->pembahasan,
            'signer_code' => $request->signer_code,
        ];
        DB::table('mom')->where('mom_code', $request->mom_code)->update($mom);

        DB::table('mom_with_selected')->where('mom_code', $request->mom_code)->delete();
        $mom_with = $request->mom_with;
        for ($i = 0; $i < count($mom_with); $i++) {
            $dmw = [
                'mom_code' => $request->mom_code,
                'mw_code' => $mom_with[$i]['mw_code'],
                'mw_nested' => $mom_with[$i]['mw_nested'],
                'mwn_code' => isset($mom_with[$i]['mwn_code']) ? $mom_with[$i]['mwn_code'] : null,
            ];
            DB::table('mom_with_selected')->insert($dmw);
        }

        if ($request->type_discussion == 'table') {
            DB::table('mom_table')->where('mom_code', $request->mom_code)->delete();
            $mom_table = $request->mom_table;
            for ($i = 0; $i < count($mom_table); $i++) {
                $dtb = [
                    'mom_code' => $request->mom_code,
                    'pembahasan' => $mom_table[$i]['pembahasan'],
                    'tindak_lanjut' => $mom_table[$i]['tindak_lanjut'],
                    'pic' => $mom_table[$i]['pic'],
                    'target' => $mom_table[$i]['target'],
                    'status' => $mom_table[$i]['status'],
                ];
                DB::table('mom_table')->insert($dtb);
            }
        }

        return ['success' => true];
        // } catch (\Exception $e) {
        //     return ['success' => false, 'msg' => $e->getMessage()];
        // }
    }

    public function deleteMOM($mom_code)
    {
        try {
            DB::table('mom')->where('mom_code', $mom_code)->update(['is_deleted' => 1]);
            DB::table('mom_with_selected')->where('mom_code', $mom_code)->update(['is_deleted' => 1]);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }


    public function detailHandover($code)
    {
        $data = DB::table('handover')->where('ho_code', $code)->first();
        if (!empty($data)) {
            $karyawan = DB::table('karyawans')->where('karyawan_code', $data->karyawan_code)->first(['nama', 'posisi']);
            $penerima = DB::table('karyawans')->where('karyawan_code', $data->penerima_ho)->first(['nama', 'posisi']);
            $signer = DB::table('signer')->where('signer_code', $data->signer_code)->first(['signer_code', 'name', 'position']);
            $list = DB::table('handover_list')->where('ho_code', $data->ho_code)->get();

            $status = 'lain';
            if (str_contains($data->alasan_resign, 'kontrak')) {
                $status = 'sk';
            }

            $data->karyawan = $karyawan;
            $data->penerima = $penerima;
            $data->signer = $signer;
            $data->list = $list;
            $data->status = $status;
        }

        return $data;
    }

    public function handoverData()
    {
        $data = DB::table('handover')->orderBy('created_at', 'desc')->get();
        for ($i = 0; $i < count($data); $i++) {
            $karyawan = DB::table('karyawans')->where('karyawan_code', $data[$i]->karyawan_code)->first(['nama', 'posisi']);
            $penerima = DB::table('karyawans')->where('karyawan_code', $data[$i]->penerima_ho)->first(['nama', 'posisi']);
            $signer = DB::table('signer')->where('signer_code', $data[$i]->signer_code)->first(['signer_code', 'name']);
            $list = DB::table('handover_list')->where('ho_code', $data[$i]->ho_code)->get();

            $data[$i]->karyawan = $karyawan;
            $data[$i]->penerima = $penerima;
            $data[$i]->signer = $signer;
            $data[$i]->list = $list;
        }

        return $data;
    }

    public function createHandover($request)
    {
        // try {
        $users = detailUsersKaryawans(Auth::user()->users_code);
        $alasan = $request->alasan;
        if ($alasan == 'sk') {
            $type = $users->type == 1 ? 'karyawan' : 'magang';
            $alasan = 'Masa kontrak ' . $type . ' telah selesai';
        } else {
            $alasan = $request->alasantext;
        }
        $ho = [
            'ho_code' => generateFiledCode('HO'),
            'karyawan_code' => $users->karyawan_code,
            'penerima_ho' => $request->penerima_ho,
            'signer_code' => $request->signer_code,
            'tgl_masuk' => $request->tgl_masuk,
            'tgl_resign' => $request->tgl_resign,
            'alasan_resign' => $alasan
        ];
        DB::table('handover')->insert($ho);

        $ho_list = $request->ho_list;
        for ($i = 0; $i < count($ho_list); $i++) {
            $list = [
                'ho_code' => $ho['ho_code'],
                'klien' => $ho_list[$i]['klien'],
                'pic' => $ho_list[$i]['pic'],
                'serahterimakan' => $ho_list[$i]['serahterimakan'],
            ];
            DB::table('handover_list')->insert($list);
        }

        return ['success' => true];
        // } catch (\Exception $e) {
        //     return ['success' => false, 'msg' => $e->getMessage()];
        // }
    }

    public function editHandover($request)
    {
        try {
            $users = detailUsersKaryawans(Auth::user()->users_code);
            $alasan = $request->alasan;
            if ($alasan == 'selesai') {
                $type = $users->type == 1 ? 'karyawan' : 'magang';
                $alasan = 'Masa kontrak ' + $type + ' telah selesai';
            }
            $ho = [
                'karyawan_code' => $users->karyawan_code,
                'penerima_ho' => $request->penerima_ho,
                'signer_code' => $request->signer_code,
                'tgl_masuk' => $request->tgl_masuk,
                'tgl_resign' => $request->tgl_resign,
                'alasan_resign' => $alasan
            ];
            DB::table('handover')->where('ho_code', $request->ho_code)->update($ho);

            DB::table('handover_list')->where('ho_code', $request->ho_code)->delete();
            $ho_list = $request->ho_list;
            for ($i = 0; $i < count($ho_list); $i++) {
                $list = [
                    'ho_code' => $request->ho_code,
                    'klien' => $ho_list[$i]['klien'],
                    'pic' => $ho_list[$i]['pic'],
                    'serahterimakan' => $ho_list[$i]['serahterimakan'],
                ];
                DB::table('handover_list')->insert($list);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteHandover($ho_code)
    {
        try {
            DB::table('handover')->where('ho_code', $ho_code)->delete();
            DB::table('handover_list')->where('ho_code', $ho_code)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function garansiData()
    {
        $data = DB::table('surat_garansi')->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();
        for ($i = 0; $i < count($data); $i++) {
            $signer = DB::table('signer')->where('signer_code', $data[$i]->signer_code)->first();
            $data[$i]->signer = $signer;

            $pelanggan = DB::table('pelanggan')->where('pelanggan_code', $data[$i]->pelanggan_code)->first();
            $data[$i]->pelanggan = $pelanggan;
        }

        return $data;
    }

    public function detailGaransi($code)
    {
        $data = DB::table('surat_garansi')->where('unique_code', $code)->first();
        if (!empty($data)) {
            $signer = DB::table('signer')->where('signer_code', $data->signer_code)->first();
            $data->signer = $signer;

            $pelanggan = DB::table('pelanggan')->where('pelanggan_code', $data->pelanggan_code)->first();
            $data->pelanggan = $pelanggan;
        }

        return $data;
    }

    public function generateNomorSuratGaransi()
    {
        $month = date('m');
        $no = 0;
        $jml = DB::table('surat_garansi')->whereMonth('created_at', $month)->count();
        $total = $jml + 1;
        if (strlen((string)$total) == 1) {
            $no = '00' . $total;
        } elseif (strlen((string)$total) == 2) {
            $no = '0' . $total;
        } else {
            $no = $total;
        }
        $romawi = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $bulan = $romawi[date('n')];
        $tahun = date('Y');

        return $no . '/HRD/CLAIM/AMI/' . $bulan . '/' . $tahun;
    }

    public function createGaransi($request)
    {
        try {
            $data = [
                'no_surat' => $this->generateNomorSuratGaransi(),
                'unique_code' => generateFiledCode('GR'),
                'signer_code' => $request->signer_code,
                'pelanggan_code' => $request->pelanggan_code,
                'tgl_surat' => $request->tgl_surat,
                'lang' => $request->lang,
                'kop_code' => $request->kop_code,
            ];
            DB::table('surat_garansi')->insert($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editGaransi($request)
    {
        try {
            $data = [
                'signer_code' => $request->signer_code,
                'pelanggan_code' => $request->pelanggan_code,
                'tgl_surat' => $request->tgl_surat,
                'lang' => $request->lang,
                'kop_code' => $request->kop_code,
            ];
            DB::table('surat_garansi')->where('unique_code', $request->unique_code)->update($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteGaransi($unique_code)
    {
        try {
            DB::table('surat_garansi')->where('unique_code', $unique_code)->update(['is_deleted' => 1]);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }
}
