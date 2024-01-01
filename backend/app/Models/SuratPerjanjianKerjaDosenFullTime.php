<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Carbon;

class SuratPerjanjianKerjaDosenFullTime extends Model
{
    // use HasFactory;
    protected $perPage = 10;
    public const NAME = 'SURAT_PERJANJIAN_KERJA_DOSEN_FULL_TIME';
    protected $table = 'surat_perjanjian_kerja_dosen_full_time';
    public function scopeWhereUser($query, $user)
    {
        if ($user->roles == 'pegawai') {
            return $query->where('employee_id', $user->id)->orWhere('signer_id', $user->id);
        } else {
            return $query;
        }
    }

    public function scopeSearch($query, $search)
    {
        $query->where('reference_number', 'like', '%' . $search . '%')
            ->orWhereHas('employee', function ($query) use ($search) {
                return $query->where('nip', 'like', '%' . $search . '%');
            })
            ->orWhereHas('signer', function ($query) use ($search) {
                return $query->where('nip', 'like', '%' . $search . '%');
            })
            ->orWhereHas('employee', function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('signer', function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            });
    }

    public function scopeByUser($query)
    {
        $id = auth()->id();
        $roles = auth()->user()->roles;
        if ($roles === 'pegawai') {
            return $query->where('employee_id', $id)->orWhere('signer_id', $id);
        } else {
            return $query;
        }
    }

    public function scopeWhereNotSigned($query) {
        return $query->where('is_signed', false);
    }

    public function scopeWhereSigned($query) {
        return $query->where('is_signed', true);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function signer()
    {
        return $this->belongsTo(Employee::class, 'signer_id', 'id');
    }

    public function letter_template() {
        return $this->belongsTo(LetterTemplate::class, 'letter_template_id', 'id');
    }

    public function pertelaan_perjanjian_kerja() {
        return $this->hasOne(PertelaanPerjanjianKerja::class, 'id', 'id');
    }

    public function get_reference_number()
    {
        $reference_number = $this->reference_number;
        if ($reference_number == null) {
            $reference_number = ReferenceNumberSetting::generate_dummy(self::NAME);
        }
        return $reference_number;
    }

    public function have_reference_number()
    {
        return $this->reference_number != null;
    }

    public function is_signed()
    {
        return $this->is_signed;
    }

    public function can_give_reference_number() {
        return !$this->have_reference_number() && auth()->user()->roles == 'admin_sekretariat';
    }

    public function can_signed() {
        return !$this->is_signed() && $this->have_reference_number() && auth()->id() == $this->signer_id && $this->signature_type != 'manual';
    }

    public function can_edit()
    {
        return (auth()->user()->roles == 'admin_sdm' || $this->created_by == auth()->user()->id) && !$this->is_signed();
    }

    public function can_upload_verified_file()
    {
        return !$this->is_signed() && $this->have_reference_number() && $this->signature_type == 'manual' && (auth()->user()->roles == 'admin_sekretariat');
    }

    public function generate_docx()
    {
        $templateProcessor = new TemplateProcessor(storage_path("app/letter_templates/" . $this->letter_template->file));

        // Kebutuhan data yang terkait dengan data surat
        $templateProcessor->setValue('nomor_surat', $this->get_reference_number());
        $templateProcessor->setValue('tanggal_surat', Carbon::parse($this->tanggal_surat)->translatedFormat('d F Y'));
        $templateProcessor->setValue('hari', Carbon::parse($this->tanggal_surat)->translatedFormat('l'));
        $templateProcessor->setValue('tanggal_terbilang', ucwords(terbilang(Carbon::parse($this->tanggal_surat)->translatedFormat('d'))));
        $templateProcessor->setValue('bulan', Carbon::parse($this->tanggal_surat)->translatedFormat('F'));
        $templateProcessor->setValue('tahun_terbilang', ucwords(terbilang(Carbon::parse($this->tanggal_surat)->translatedFormat('Y'))));
        $templateProcessor->setValue('tanggal_format_tgl_bln_thn', Carbon::parse($this->tanggal_surat)->format('d - m - Y'));


        // Kebutuhan data pegawai
        $templateProcessor->setValue('nama', $this->employee->name);
        $templateProcessor->setValue('nip', $this->employee->nip);
        $templateProcessor->setValue('nik', $this->employee->nik);
        $templateProcessor->setValue('alamat', $this->employee->alamat);
        $templateProcessor->setValue('tempat_lahir', $this->employee->tempat_lahir);
        $templateProcessor->setValue('tanggal_lahir', Carbon::parse($this->employee->tanggal_lahir)->translatedFormat('d F Y'));
        $templateProcessor->setValue('profesi', $this->profesi);
        $templateProcessor->setValue('jabatan_fungsional', $this->jabatan_fungsional);
        $prodi = json_decode($this->prodi);
        $templateProcessor->setValue('nama_prodi', $prodi->nama_prodi);
        $templateProcessor->setValue('nama_fakultas', $prodi->nama_fakultas);
        $templateProcessor->setValue('singkatan_fakultas', $prodi->singkatan_fakultas);
        $templateProcessor->setValue('mulai_berlaku', Carbon::parse($this->mulai_berlaku)->translatedFormat('d F Y'));
        $templateProcessor->setValue('akhir_berlaku', Carbon::parse($this->akhir_berlaku)->translatedFormat('d F Y'));
        $bulan = Carbon::parse($this->mulai_berlaku)->diffInMonths(Carbon::parse($this->akhir_berlaku));
        $tahun = Carbon::parse($this->mulai_berlaku)->diffInYears(Carbon::parse($this->akhir_berlaku));
        $masa_berlaku = "0 Bulan";
        if ($bulan % 12 == 0) {
            $masa_berlaku = $tahun . " (". trim(ucwords(terbilang($tahun))) . ") Tahun";
        } else if ($bulan > 0) {
            $masa_berlaku = $bulan . " (". trim(ucwords(terbilang($bulan))) . ") Bulan";
        }
        $templateProcessor->setValue('masa_berlaku', $masa_berlaku);
        $rekening = json_decode($this->rekening);
        $templateProcessor->setValue('nama_bank', $rekening->nama_bank);
        $templateProcessor->setValue('atas_nama', $rekening->atas_nama);
        $templateProcessor->setValue('nomor_rekening', $rekening->nomor_rekening);
        $templateProcessor->setValue('npwp', $this->employee->npwp);
        
        //pertelaan perjanjian kerja
        $pertelaan_perjanjian_kerja = $this->pertelaan_perjanjian_kerja;
        // $templateProcessor->setValue('jangka_waktu', $pertelaan_perjanjian_kerja->jangka_waktu);
        $templateProcessor->setValue('pendidikan', $pertelaan_perjanjian_kerja->pendidikan);
        $templateProcessor->setValue('tahun_satu', $pertelaan_perjanjian_kerja->tahun_satu);
        $templateProcessor->setValue('tunjangan_dasar_satu', number_format($pertelaan_perjanjian_kerja->tunjangan_dasar_satu, 0, ',', '.'));
        $templateProcessor->setValue('tunjangan_fungsional_satu', number_format($pertelaan_perjanjian_kerja->tunjangan_fungsional_satu, 0, ',', '.'));
        $templateProcessor->setValue('tunjangan_struktural_satu', number_format($pertelaan_perjanjian_kerja->tunjangan_struktural_satu, 0, ',', '.'));
        $templateProcessor->setValue('tunjangan_kemahalan_satu', number_format($pertelaan_perjanjian_kerja->tunjangan_kemahalan_satu, 0, ',', '.'));
        $templateProcessor->setValue('pendapatan_bulanan_satu', number_format($pertelaan_perjanjian_kerja->pendapatan_bulanan_satu, 0, ',', '.'));
        $templateProcessor->setValue('tahun_dua', number_format($pertelaan_perjanjian_kerja->tahun_dua, 0, ',', '.'));
        $templateProcessor->setValue('tunjangan_dasar_dua', number_format($pertelaan_perjanjian_kerja->tunjangan_dasar_dua, 0, ',', '.'));
        $templateProcessor->setValue('tunjangan_fungsional_dua', number_format($pertelaan_perjanjian_kerja->tunjangan_fungsional_dua, 0, ',', '.'));
        $templateProcessor->setValue('tunjangan_struktural_dua', number_format($pertelaan_perjanjian_kerja->tunjangan_struktural_dua, 0, ',', '.'));
        $templateProcessor->setValue('tunjangan_kemahalan_dua', number_format($pertelaan_perjanjian_kerja->tunjangan_kemahalan_dua, 0, ',', '.'));
        $templateProcessor->setValue('pendapatan_bulanan_dua', number_format($pertelaan_perjanjian_kerja->pendapatan_bulanan_dua, 0, ',', '.'));

        $fasilitas_lainnya = json_decode($pertelaan_perjanjian_kerja->fasilitas_lainnya);
        $fasilitas_lainnya_set = [];
        foreach ($fasilitas_lainnya as $value) {
            array_push($fasilitas_lainnya_set, [
                'fasilitas_lainnya' => $value,
            ]);
        }
        $templateProcessor->cloneBlock('block_fasilitas_lainnya', 0, true, false, $fasilitas_lainnya_set);

        // Kebutuhan data yang terkait dengan pejabat yang menandatangan
        $templateProcessor->setValue('nama_penandatangan', $this->signer->name);
        $templateProcessor->setValue('jabatan_penandatangan', $this->signer_position);
        
        // $templateProcessor->setImageValue('signature', [
        //     'path' => storage_path('app/signature/' . $letter->official->signature),
        //     'ratio' => true,
        // ]);

        return $templateProcessor;
    }
}
