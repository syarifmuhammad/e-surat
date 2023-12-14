<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SuratPerjanjianKerjaDosenLuarBiasa extends Model
{
    // use HasFactory;
    public const NAME = 'SURAT_PERJANJIAN_KERJA_DOSEN_LUAR_BIASA';
    protected $table = 'surat_perjanjian_kerja_dosen_luar_biasa';
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
        return $query->where('signed_file', null)->where('signed_file_docx', null);
    }

    public function scopeWhereSigned($query) {
        return $query->where('signed_file', '!=', null)->orWhere('signed_file_docx', '!=', null);
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
        return $this->signed_file != null || $this->signed_file_docx != null;
    }

    public function can_give_reference_number() {
        return !$this->have_reference_number() && auth()->user()->roles == 'admin_sekretariat';
    }

    public function can_signed() {
        return !$this->is_signed() && $this->have_reference_number() && auth()->id() == $this->signer_id && !(($this->signature_type == 'manual' || $this->signature_type == 'digital'));
    }

    public function can_edit() {
        return (auth()->user()->roles == 'admin_sdm' || $this->created_by == auth()->user()->id);
    }

    public function can_upload_verified_file()
    {
        return !$this->is_signed() && $this->have_reference_number() && ($this->signature_type == 'manual' || $this->signature_type == 'digital') && (auth()->user()->roles == 'admin_sekretariat');
    }

    public function generate_docx()
    {
        $templateProcessor = new TemplateProcessor(storage_path("app/letter_templates/" . $this->letter_template->file));

        // Kebutuhan data yang terkait dengan pemohon atau pembuat surat
        // $templateProcessor->setValue('nik_pemohon', $letter->resident->nik);
        // $templateProcessor->setValue('nama_pemohon', $letter->resident->name);
        // $templateProcessor->setValue('alamat_pemohon', $letter->resident->address);

        // Kebutuhan data yang terkait dengan data surat
        $templateProcessor->setValue('nomor_surat', $this->get_reference_number());
        $templateProcessor->setValue('tanggal_surat', Carbon::parse($this->tanggal_surat)->translatedFormat('d F Y'));
        $templateProcessor->setValue('hari', Carbon::parse($this->tanggal_surat)->translatedFormat('l'));
        $templateProcessor->setValue('tanggal_terbilang', ucwords(terbilang(Carbon::parse($this->tanggal_surat)->translatedFormat('d'))));
        $templateProcessor->setValue('bulan', Carbon::parse($this->tanggal_surat)->translatedFormat('F'));
        $templateProcessor->setValue('tahun_terbilang', ucwords(terbilang(Carbon::parse($this->tanggal_surat)->translatedFormat('Y'))));

        // Kebutuhan data pegawai
        $templateProcessor->setValue('nama', $this->employee->name);
        $templateProcessor->setValue('nip', $this->employee->nip);
        $templateProcessor->setValue('nik', $this->employee->nik);
        $templateProcessor->setValue('alamat', $this->employee->alamat);
        $templateProcessor->setValue('tempat_lahir', $this->employee->tempat_lahir);
        $templateProcessor->setValue('tanggal_lahir', Carbon::parse($this->employee->tanggal_lahir)->translatedFormat('d F Y'));
        $templateProcessor->setValue('jabatan_fungsional', $this->jabatan_fungsional);
        $templateProcessor->setValue('nidn', $this->nidn);
        $templateProcessor->setValue('mata_kuliah', $this->mata_kuliah);
        $templateProcessor->setValue('tahun_ajaran', $this->tahun_ajaran);
        $templateProcessor->setValue('semester', $this->semester);
        $templateProcessor->setValue('upah', number_format($this->upah, 0, ',', '.'));
        $templateProcessor->setValue('transportasi', number_format($this->transportasi, 0, ',', '.'));
        $rekening = json_decode($this->rekening);
        $templateProcessor->setValue('nama_bank', $rekening->nama_bank);
        $templateProcessor->setValue('atas_nama', $rekening->atas_nama);
        $templateProcessor->setValue('nomor_rekening', $rekening->nomor_rekening);
        $templateProcessor->setValue('npwp', $this->employee->npwp);

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
