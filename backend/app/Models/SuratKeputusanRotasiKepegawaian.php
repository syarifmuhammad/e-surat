<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratKeputusanRotasiKepegawaian extends Model
{
    // use HasFactory;
    protected $perPage = 10;
    public const NAME = 'SURAT_KEPUTUSAN_ROTASI_KEPEGAWAIAN';
    protected $table = 'surat_keputusan_rotasi_kepegawaian';
    public function scopeWhereAssigned($query, $user)
    {
        if ($user->roles == 'admin_sekretariat') {
            return $query;
        }
        
        return $query->where('employee_id', $user->id)->orWhereHas('signers', function ($query) use ($user) {
            return $query->where('employee_id', $user->id);
        })->orWhereHas('approvals', function ($query) use ($user) {
            return $query->where('employee_id', $user->id);
        });
    }

    public function scopeWhereUser($query, $user)
    {
        if ($user->roles == 'pegawai') {
            return $this->scopeWhereAssigned($query, $user);
        } else if ($user->roles == 'admin_unit') {
            return $query->orWhere('created_by', $user->id);
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
            ->orWhereHas('employee', function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            });
    }

    public function scopeByUser($query)
    {
        $user = auth()->user();
        return $this->scopeWhereUser($query, $user);
    }

    public function scopeWhereNotSigned($query)
    {
        return $query->where('is_signed', false);
    }

    public function scopeWhereSigned($query)
    {
        return $query->where('is_signed', true);
    }

    public function masa_berlaku_parse()
    {
        return interval_to_array($this->masa_berlaku);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable')->where('is_signer', false);
    }

    public function signers()
    {
        return $this->morphMany(Approval::class, 'approvable')->where('is_signer', true);
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

    public function is_approved()
    {
        return $this->is_approved;
    }

    public function can_give_reference_number()
    {
        return !$this->have_reference_number() && auth()->user()->roles == 'admin_sekretariat';
    }

    public function can_approved()
    {
        $not_approved = $this->approvals->where('is_approved', false)->first();
        $can_approved = isset($not_approved->employee_id) && $not_approved->employee_id == auth()->id();
        return $can_approved && $this->have_reference_number();
    }

    public function can_signed()
    {
        $can_signed = $this->signers->where('employee_id', auth()->id())->first();
        return $can_signed && !$can_signed->is_approved && $this->have_reference_number()
            && $this->signature_type != 'manual' && $this->is_approved();
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
        if ($this->is_signed) {
            $signed = $this->signers->sortBy('updated_at')->last();
            $templateProcessor->setValue('tanggal_berlaku', Carbon::parse($signed->updated_at)->translatedFormat('d F Y'));
            $templateProcessor->setValue('bulan_tahun_terakhir', Carbon::parse($signed->updated_at)->subMonth()->translatedFormat('F Y'));
        } else {
            $templateProcessor->setValue('tanggal_berlaku', '___________');
            $templateProcessor->setValue('bulan_tahun_terakhir', '___________');
        }

        // Kebutuhan data pegawai
        $templateProcessor->setValue('nama', $this->employee->name);
        $templateProcessor->setValue('nip', $this->employee->nip);
        $templateProcessor->setValue('status_awal', $this->status_awal);
        $templateProcessor->setValue('jabatan_awal', $this->jabatan_awal);
        $templateProcessor->setValue('status_akhir', $this->status_akhir);
        $templateProcessor->setValue('jabatan_akhir', $this->jabatan_akhir);

        // Kebutuhan data yang terkait dengan pejabat yang menandatangan
        foreach ($this->signers as $key => $signer) {
            $templateProcessor->setValue('nama_penandatangan' . $key + 1, $signer->employee->name);
            $templateProcessor->setValue('jabatan_penandatangan' . $key + 1, $signer->position);
            if ($this->is_signed()) {
                if ($signer->signed_file && file_exists(storage_path('app/signed_files/' . $signer->signed_file))) {
                    $templateProcessor->setImageValue('tanda_tangan' . $key + 1, [
                        'path' => storage_path('app/signed_files/' . $signer->signed_file),
                        'ratio' => true,
                        'width' => 100,
                        'height' => 100,
                    ]);
                } else {
                    $templateProcessor->setValue('tanda_tangan' . $key + 1, "Telah ditandatangani oleh: " . $signer->employee->name . " pada tanggal " . Carbon::parse($signer->updated_at)->translatedFormat('d F Y') . "");
                }
            } else {
                $templateProcessor->setValue('tanda_tangan' . $key + 1, '');
            }
        }

        return $templateProcessor;
    }
}
