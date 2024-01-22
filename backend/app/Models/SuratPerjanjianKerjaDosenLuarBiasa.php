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
    protected $perPage = 10;
    public const NAME = 'SURAT_PERJANJIAN_KERJA_DOSEN_LUAR_BIASA';
    protected $table = 'surat_perjanjian_kerja_dosen_luar_biasa';
    public function scopeWhereUser($query, $user)
    {
        if ($user->roles == 'pegawai') {
            return $query->where('employee_id', $user->id)->orWhereHas('signers', function ($query) use ($user) {
                return $query->where('employee_id', $user->id);
            })->orWhereHas('approvals', function ($query) use ($user) {
                return $query->where('employee_id', $user->id);
            });
        } else if($user->roles == 'admin_unit') {
            return $query->where('created_by', auth()->id());
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
