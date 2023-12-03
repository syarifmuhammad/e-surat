<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;

class SuratKeputusanPemberhentian extends Model
{
    // use HasFactory;
    public const NAME = 'SURAT_KEPUTUSAN_PEMBERHENTIAN';
    protected $table = 'surat_keputusan_pemberhentian';
    public function scopeWhereUser($query, $user)
    {
        if ($user->roles == 'pegawai') {
            return $query->where('employee_nip', $user->nip)->orWhere('signer_nip', $user->nip);
        } else {
            return $query;
        }
    }

    public function scopeSearch($query, $search)
    {
        $query->where('reference_number', 'like', '%' . $search . '%')
            ->orWhere('employee_nip', 'like', '%' . $search . '%')
            ->orWhere('signer_nip', 'like', '%' . $search . '%')
            ->orWhereHas('employee', function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('signer', function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_nip', 'nip');
    }

    public function signer()
    {
        return $this->belongsTo(Employee::class, 'signer_nip', 'nip');
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
        return $this->signed_file != null;
    }

    public function can_give_reference_number()
    {
        return !$this->have_reference_number() && auth()->user()->roles == 'admin_sekretariat';
    }

    public function can_signed()
    {
        return $this->have_reference_number() && auth()->id() == $this->signer_nip && !(($this->signature_type == 'manual' || $this->signature_type == 'digital'));
    }

    public function can_edit()
    {
        return !$this->have_reference_number() && (auth()->user()->roles == 'admin_sdm' || $this->created_by == auth()->user()->nip);
    }

    public function can_upload_verified_file()
    {
        return $this->signed_file == null && ($this->signature_type == 'manual' || $this->signature_type == 'digital') && (auth()->user()->roles == 'admin_sekretariat');
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

        // Kebutuhan data pegawai
        $templateProcessor->setValue('nama', $this->employee->name);
        $templateProcessor->setValue('nip', $this->employee->nip);
        $templateProcessor->setValue('pemberhentian_dalam_jabatan', $this->pemberhentian_dalam_jabatan);
        $templateProcessor->setValue('uppercase_pemberhentian_dalam_jabatan', Str::upper($this->pemberhentian_dalam_jabatan));
        $templateProcessor->setValue('dikembalikan_ke_jabatan', $this->dikembalikan_ke_jabatan);
        $templateProcessor->setValue('keterangan_tambahan', $this->keterangan_tambahan);

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
