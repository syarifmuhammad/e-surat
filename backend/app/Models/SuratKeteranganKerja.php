<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratKeteranganKerja extends Model
{
    // use HasFactory;
    public const NAME = 'SURAT_KETERANGAN_KERJA';
    protected $table = 'surat_keterangan_kerja';
    public function scopeWhereUser($query, $user)
    {
        if ($user->roles == 'pegawai') {
            return $query->where('employee_nip', $user->nip)->orWhere('signer_nip', $user->nip);
        } else if ($user->roles == 'admin_sdm') {
            return $query->where('created_by', $user->nip);
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
        return $this->signed_file != null;
    }

    public function can_give_reference_number() {
        return !$this->have_reference_number() && auth()->user()->roles == 'admin_sekretariat';
    }

    public function can_signed() {
        return $this->have_reference_number() && auth()->id() == $this->signer_nip;
    }

    public function can_edit() {
        return !$this->have_reference_number() && (auth()->user()->roles == 'admin_sdm' || $this->created_by == auth()->user()->nip);
    }

    public function can_upload_verified_file() {
        return $this->signed_file == null && ($this->signed_type == 'basah' || $this->signed_type == 'digital') ;
    }

    public function generate_docx()
    {
        $templateProcessor = new TemplateProcessor(storage_path("app/letter_templates/" . $this->letter_template->file));

        // // Kebutuhan data dari table user dan instansi
        // $templateProcessor->setValue('nama_instansi', Str::upper($letter->user->parent->name));
        // $templateProcessor->setValue('email', $letter->user->parent->email);
        // $templateProcessor->setValue('provinsi', $letter->user->parent->province);
        // $templateProcessor->setValue('kabupaten_upper', Str::upper($letter->user->parent->district));
        // $templateProcessor->setValue('kecamatan_upper', Str::upper($letter->user->parent->sub_district));
        // $templateProcessor->setValue('kabupaten', $letter->user->parent->district);
        // $templateProcessor->setValue('kecamatan', $letter->user->parent->sub_district);
        // $templateProcessor->setValue('desa_kop', $letter->user->parent->kop_village());
        // $templateProcessor->setValue('desa', $letter->user->parent->village);
        // $templateProcessor->setValue('jalan', $letter->user->parent->street);
        // $templateProcessor->setValue('kode_pos', $letter->user->parent->zip_code);

        // // Kebutuhan data yang terkait dengan pemohon atau pembuat surat
        // $templateProcessor->setValue('nik_pemohon', $letter->resident->nik);
        // $templateProcessor->setValue('nama_pemohon', $letter->resident->name);
        // $templateProcessor->setValue('alamat_pemohon', $letter->resident->address);

        // // Kebutuhan data yang terkait dengan data surat
        // $templateProcessor->setValue('nomor_surat', $letter->get_reference_number());
        // $templateProcessor->setValue('tanggal_surat', Carbon::parse($letter->updated_at)->translatedFormat('j F Y'));
        // $templateProcessor->setValue('nama_perusahaan', $letter->company_name);
        // $templateProcessor->setValue('alamat_perusahaan', $letter->company_address);
        // $templateProcessor->setValue('jenis_usaha', htmlspecialchars($letter->business));
        // $templateProcessor->setValue('modal_usaha', $letter->get_capital_parse());
        // $templateProcessor->setValue('awal_berlaku', Carbon::parse($letter->updated_at)->translatedFormat('j F Y'));
        // $templateProcessor->setValue('akhir_berlaku', Carbon::parse($letter->updated_at)->addYears($letter->validity_period)->translatedFormat('j F Y'));
        // $templateProcessor->setValue('masa_berlaku', $letter->validity_period . " Tahun");

        // // Kebutuhan data yang terkait dengan pejabat yang menandatangan
        // $templateProcessor->setValue('nip_penandatangan', 'NIP. ' . $letter->official->nip);
        // $templateProcessor->setValue('nama_penandatangan', $letter->official->name);
        // $templateProcessor->setValue('jabatan_penandatangan', $letter->official->position);
        // $templateProcessor->setValue('pangkat_penandatangan', $letter->official->rank);
        // $templateProcessor->setImageValue('signature', [
        //     'path' => storage_path('app/signature/' . $letter->official->signature),
        //     'ratio' => true,
        // ]);

        return $templateProcessor;
    }
}
