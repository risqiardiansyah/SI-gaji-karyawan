<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'Api\Auth\AuthController@login');
Route::post('not/auth', 'Api\Auth\AuthController@unauthorized')->name('unauthorized');

// Route::get('user/invoice_view/{idx_invoice}/{idx_termin}/cetak/{email}', 'Api\SuratMenyurat\invoiceController@view_invoice');
Route::get('test', 'Api\WebSettingController@test');
Route::group(['prefix' => 'user', 'middleware' => 'auth:api'], function () {
    //USER
    Route::post('logout', 'Api\Auth\AuthController@logout')->name('user.logout');
    Route::get('list', 'Api\dashboard\dashboardController@getListUser');
    Route::post('edit', 'Api\dashboard\dashboardController@editDataUser');
    Route::post('create', 'Api\dashboard\dashboardController@createDataUser');
    Route::post('delete/{id}', 'Api\dashboard\dashboardController@deleteDataUser');
    Route::post('change/password', 'Api\dashboard\dashboardController@changePasswordUser');
    //Auth token
    Route::post('auth/token', 'Api\Auth\AuthController@tokencek');

    // DAFTAR KARYAWAN & MAGANG
    Route::get('karyawan/list', 'Api\SuratMenyurat\KaryawanController@getKaryawanList');
    Route::get('karyawan/all', 'Api\SuratMenyurat\KaryawanController@getAllKaryawan');
    Route::get('karyawan/detail/{karyawan_code}', 'Api\SuratMenyurat\KaryawanController@getOneKaryawan');
    Route::post('karyawan/add', 'Api\SuratMenyurat\KaryawanController@addKaryawan');
    Route::post('karyawan/{karyawan_code}/edit', 'Api\SuratMenyurat\KaryawanController@updateKaryawan');
    Route::post('karyawan/delete', 'Api\SuratMenyurat\KaryawanController@deleteKaryawan');
    Route::post('karyawan/export', 'Api\SuratMenyurat\KaryawanController@exportKaryawan');

    // SLIP GAJI
    Route::get('slip', 'Api\SuratMenyurat\SlipController@getAllSlip');
    Route::get('slip/detail/{slip_code}', 'Api\SuratMenyurat\SlipController@detailSlip');
    Route::post('slip/create', 'Api\SuratMenyurat\SlipController@createSlip');
    Route::post('slip/edit', 'Api\SuratMenyurat\SlipController@editSlip');
    Route::delete('slip/delete/{slip_code}', 'Api\SuratMenyurat\SlipController@deleteSlip');
    Route::post('slip/{slip_code}/cetak', 'Api\SuratMenyurat\SlipController@cetak_slip');

    Route::post('slip/export', 'Api\SuratMenyurat\SlipController@exportSlip');

    //Profile
    Route::get('profile', 'Api\Profile\profileController@getProfileUser');
    Route::post('profile/edit', 'Api\Profile\profileController@updateProfileUser');

    // SURAT PENILAIAN
    Route::get('surat_penilaian/statistik', 'Api\SuratMenyurat\SKController@statistikPenilaian');

    Route::get('surat_penilaian/kriteria', 'Api\SuratMenyurat\SKController@allKriteriaPenilaian');
    Route::get('surat_penilaian/kriteria/{type}', 'Api\SuratMenyurat\SKController@kriteriaPenilaian');
    Route::get('surat_penilaian/kriteria/detail/{code}', 'Api\SuratMenyurat\SKController@detailKriteriaPenilaian');
    Route::post('surat_penilaian/kriteria/add', 'Api\SuratMenyurat\SKController@addKriteriaPenilaian');
    Route::post('surat_penilaian/kriteria/edit', 'Api\SuratMenyurat\SKController@editKriteriaPenilaian');
    Route::delete('surat_penilaian/kriteria/delete/{code}', 'Api\SuratMenyurat\SKController@deleteKriteriaPenilaian');

    Route::get('surat_penilaian', 'Api\SuratMenyurat\SKController@suratPenilaian');
    Route::get('surat_penilaian/check/{karyawan_code}', 'Api\SuratMenyurat\SKController@checkSuratPenilaian');
    Route::post('surat_penilaian/create', 'Api\SuratMenyurat\SKController@createSuratPenilaian');
    Route::post('surat_penilaian/edit', 'Api\SuratMenyurat\SKController@editSuratPenilaian');
    Route::delete('surat_penilaian/delete/{code}', 'Api\SuratMenyurat\SKController@deleteSuratPenilaian');
    Route::get('surat_penilaian/{unique_code}', 'Api\SuratMenyurat\SKController@detailSuratPenilaian');
    Route::post('surat_penilaian/{code}/{send_email}/cetak', 'Api\SuratMenyurat\SKController@cetak_surat_penilaian');

    Route::post('surat_penilaian/export', 'Api\SuratMenyurat\SKController@exportPenilaian');

    // ABSENSI
    Route::get('karyawan/absen', 'Api\hr\AbsenController@getAbsensi');
    Route::get('karyawan/absen/{code}', 'Api\hr\AbsenController@getDetailAbsensi');
    Route::post('karyawan/absen/add', 'Api\hr\AbsenController@setAbsensi');
    Route::post('karyawan/absen/update', 'Api\hr\AbsenController@editAbsensi');
    Route::delete('karyawan/absen/delete/{code}', 'Api\hr\AbsenController@deleteAbsensi');
    Route::post('karyawan/absen/export', 'Api\hr\AbsenController@exportAbsensi');

    Route::get('signer/list', 'Api\SuratMenyurat\DaftarSignerController@getSignerList'); // LIST LABEL SIGNER
    Route::get('office/list', 'Api\WebSettingController@getOfficeList');
    Route::get('kop', 'Api\WebSettingController@dataKopSurat');
});
