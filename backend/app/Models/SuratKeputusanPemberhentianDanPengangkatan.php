<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;

class SuratKeputusanPemberhentianDanPengangkatan extends Model
{
    // use HasFactory;
    protected $perPage = 10;
    public const NAME = 'SURAT_KEPUTUSAN_PEMBERHENTIAN_DAN_PENGANGKATAN';
    protected $table = 'surat_keputusan_pemberhentian_dan_pengangkatan';
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

    public function scopeWhereNotSigned($query)
    {
        return $query->where('is_signed', false);
    }

    public function scopeWhereSigned($query)
    {
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
        return $this->is_signed;
    }

    public function can_give_reference_number()
    {
        return !$this->have_reference_number() && auth()->user()->roles == 'admin_sekretariat';
    }

    public function can_signed()
    {
        return !$this->is_signed() && $this->have_reference_number() && auth()->id() == $this->signer_id && !$this->signature_type == 'manual';
    }

    public function can_edit()
    {
        return (auth()->user()->roles == 'admin_unit' || $this->created_by == auth()->user()->id) && !$this->is_signed();
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
        $templateProcessor->setValue('nomor_berita_acara', $this->nomor_berita_acara);
        $templateProcessor->setValue('tanggal_berita_acara', Carbon::parse($this->tanggal_berita_acara)->translatedFormat('d F Y'));
        $templateProcessor->setValue('tanggal_surat', Carbon::parse($this->tanggal_surat)->translatedFormat('d F Y'));
        $templateProcessor->setValue('tanggal_berlaku', Carbon::parse($this->tanggal_berlaku)->translatedFormat('d F Y'));

        // Kebutuhan data pegawai
        $templateProcessor->setValue('nama', $this->employee->name);
        $templateProcessor->setValue('nip', $this->employee->nip);
        $templateProcessor->setValue('pemberhentian_dalam_jabatan', $this->pemberhentian_dalam_jabatan);
        $templateProcessor->setValue('uppercase_pemberhentian_dalam_jabatan', Str::upper($this->pemberhentian_dalam_jabatan));
        $templateProcessor->setValue('pengangkatan_dalam_jabatan', $this->pengangkatan_dalam_jabatan);
        // $templateProcessor->setValue('keterangan_tambahan', $this->keterangan_tambahan);

        // Kebutuhan data yang terkait dengan pejabat yang menandatangan
        $templateProcessor->setValue('nama_penandatangan', $this->signer->name);
        $templateProcessor->setValue('jabatan_penandatangan', $this->signer_position);

        if ($this->is_signed()) {
            if ($this->signature_type == "gambar tanda tangan" || $this->signature_type == "digital") {
                $templateProcessor->setImageValue('tanda_tangan', [
                    'path' => storage_path('app/signed_files/' . $this->signed_file),
                    'ratio' => true,
                ]);
            }
        } else {
            $templateProcessor->setValue('tanda_tangan', '');
        }

        return $templateProcessor;
    }
}
