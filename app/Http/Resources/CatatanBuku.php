<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CatatanBuku extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'idx_buku_kas' => $this->idx_buku_kas,
            'id_user' => $this->id_user,
            'idx_kategori' => $this->idx_kategori,
            'idx_sub_kategori' => $this->idx_sub_kategori,
            'idx_piutang' => $this->idx_piutang,
            'idx_hutang' => $this->idx_hutang,
            'idx_catatan_buku' => $this->idx_catatan_buku,
            'sub_nama' => $this->sub_nama,
            'catatan_jumlah' => $this->catatan_jumlah,
            'catatan_jam' => $this->catatan_jam,
            'catatan_tgl' => $this->catatan_tgl,
            'catatan_keterangan' => $this->catatan_keterangan,
            'status' => $this->status,
            'catatan_img' => ($this->catatan_img == null ? '' : asset('storage/' . $this->catatan_img)),
            'origin_img' => $this->catatan_img
        ];
    }
}
