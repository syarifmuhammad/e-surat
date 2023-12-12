<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratKeputusanRotasiKepegawaian extends Model
{
    // use HasFactory;
    public const NAME = 'SURAT_KEPUTUSAN_ROTASI_KEPEGAWAIAN';
    protected $table = 'surat_keputusan_rotasi_kepegawaian';
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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function signer()
    {
        return $this->belongsTo(Employee::class, 'signer_id', 'id');
    }

    public function letter_template()
    {
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

    public function can_give_reference_number()
    {
        return !$this->have_reference_number() && auth()->user()->roles == 'admin_sekretariat';
    }

    public function can_signed()
    {
        return !$this->is_signed() && $this->have_reference_number() && auth()->id() == $this->signer_id && !(($this->signature_type == 'manual' || $this->signature_type == 'digital'));
    }

    public function can_edit()
    {
        return !$this->have_reference_number() && (auth()->user()->roles == 'admin_sdm' || $this->created_by == auth()->user()->id);
    }

    public function can_upload_verified_file()
    {
        return !$this->is_signed() && ($this->signature_type == 'manual' || $this->signature_type == 'digital') && (auth()->user()->roles == 'admin_sekretariat');
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
        $templateProcessor->setValue('nomor_berita_acara', $this->nomor_berita_acara);
        $templateProcessor->setValue('tanggal_berita_acara', Carbon::parse($this->tanggal_berita_acara)->translatedFormat('d F Y'));
        $templateProcessor->setValue('tanggal_surat', Carbon::parse($this->created_at)->translatedFormat('d F Y'));
        $templateProcessor->setValue('tanggal_berlaku', Carbon::parse($this->tanggal_berlaku)->translatedFormat('d F Y'));
        $templateProcessor->setValue('bulan_tahun_terakhir', Carbon::parse($this->tanggal_berlaku)->subMonth()->translatedFormat('F Y'));

        // Kebutuhan data pegawai
        $templateProcessor->setValue('nama', $this->employee->name);
        $templateProcessor->setValue('nip', $this->employee->nip);
        $templateProcessor->setValue('status_awal', $this->status_awal);
        $templateProcessor->setValue('jabatan_awal', $this->jabatan_awal);
        $templateProcessor->setValue('status_akhir', $this->status_akhir);
        $templateProcessor->setValue('jabatan_akhir', $this->jabatan_akhir);

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
