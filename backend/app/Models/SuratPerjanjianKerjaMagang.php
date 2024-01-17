<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SuratPerjanjianKerjaMagang extends Model
{
    // use HasFactory;
    protected $perPage = 10;
    public const NAME = 'SURAT_PERJANJIAN_KERJA_MAGANG';
    protected $table = 'surat_perjanjian_kerja_magang';
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
        return $query->where('is_signed', false)->orWhere('is_signed2', false);
    }

    public function scopeWhereSigned($query)
    {
        return $query->where('is_signed', true)->where('is_signed2', true);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function signer()
    {
        return $this->belongsTo(Employee::class, 'signer_id', 'id');
    }

    public function signer2()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
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

    public function is_signed2()
    {
        return $this->is_signed2;
    }

    public function can_give_reference_number()
    {
        return !$this->have_reference_number() && auth()->user()->roles == 'admin_sekretariat';
    }

    public function can_signed()
    {
        return !$this->is_signed() && $this->have_reference_number() && auth()->id() == $this->signer_id && $this->signature_type != 'manual';
    }

    public function can_signed2()
    {
        return !$this->is_signed2() && $this->have_reference_number() && auth()->id() == $this->employee_id && $this->signature_type != 'manual';
    }

    public function can_edit()
    {
        return (auth()->user()->roles == 'admin_unit' || $this->created_by == auth()->user()->id) && !$this->is_signed() && !$this->is_signed2();
    }

    public function can_upload_verified_file()
    {
        return !$this->is_signed() && !$this->is_signed2() && $this->have_reference_number() && $this->signature_type == 'manual' && (auth()->user()->roles == 'admin_sekretariat');
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
        $templateProcessor->setValue('tempat_lahir', $this->employee->tempat_lahir);
        $templateProcessor->setValue('tanggal_lahir', Carbon::parse($this->employee->tanggal_lahir)->translatedFormat('d F Y'));
        $templateProcessor->setValue('alamat', $this->employee->alamat);


        $templateProcessor->setValue('mulai_berlaku', Carbon::parse($this->mulai_berlaku)->translatedFormat('d F Y'));
        $templateProcessor->setValue('akhir_berlaku', Carbon::parse($this->akhir_berlaku)->translatedFormat('d F Y'));
        $bulan = Carbon::parse($this->mulai_berlaku)->diffInMonths(Carbon::parse($this->akhir_berlaku));
        $tahun = Carbon::parse($this->mulai_berlaku)->diffInYears(Carbon::parse($this->akhir_berlaku));
        $masa_berlaku = "0 Bulan";
        if ($bulan % 12 == 0) {
            $masa_berlaku = $tahun . " (" . trim(ucwords(terbilang($tahun))) . ") Tahun";
        } else if ($bulan > 0) {
            $masa_berlaku = $bulan . " (" . trim(ucwords(terbilang($bulan))) . ") Bulan";
        }
        $templateProcessor->setValue('masa_berlaku', $masa_berlaku);

        $tugas = json_decode($this->tugas);
        $tugas_set = [];
        foreach ($tugas as $value) {
            array_push($tugas_set, [
                'tugas' => $value,
            ]);
        }

        if (count($tugas_set) < 1) {
            array_push($tugas_set, [
                'tugas' => '',
            ]);
        }

        $templateProcessor->cloneBlock('block_tugas', 0, true, false, $tugas_set);
        $templateProcessor->setValue('tempat_kerja', $this->tempat_kerja);
        $templateProcessor->setValue('upah', number_format($this->upah, 0, ',', '.'));

        $rekening = json_decode($this->rekening);
        $templateProcessor->setValue('nama_bank', $rekening->nama_bank);
        $templateProcessor->setValue('atas_nama', $rekening->atas_nama);
        $templateProcessor->setValue('nomor_rekening', $rekening->nomor_rekening);
        $templateProcessor->setValue('npwp', $this->employee->npwp);

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

        if ($this->is_signed2()) {
            if ($this->signature_type == "gambar tanda tangan" || $this->signature_type == "digital") {
                $templateProcessor->setImageValue('tanda_tangan_pihak_kedua', [
                    'path' => storage_path('app/signed_files/' . $this->signed_file2),
                    'ratio' => true,
                ]);
            }
        } else {
            $templateProcessor->setValue('tanda_tangan_pihak_kedua', '');
        }

        return $templateProcessor;
    }
}
