<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'position',
        'is_signer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public static function create_approvals_and_signers_to_letter($letter, $signers, $approvals)
    {
        $tmp_signers = [];
        foreach ($signers as $signer) {
            $tmp = new Approval();
            $tmp->employee_id = $signer['employee']['id'];
            $tmp->position = $signer['position'];
            $tmp->is_signer = true;
            $tmp_signers[] = $tmp->toArray();
        }

        $letter->approvals()->createMany($tmp_signers);

        $tmp_approvals = [];
        foreach ($approvals as $approval) {
            $tmp = new Approval();
            $tmp->employee_id = $approval['employee']['id'];
            $tmp->position = $approval['position'];
            $tmp_approvals[] = $tmp->toArray();
        }

        $letter->approvals()->createMany($tmp_approvals);
    }

    public static function update_approvals_and_signers_to_letter($letter, $signers, $approvals)
    {
        $letter->signers()->delete();
        $tmp_signers = [];
        foreach ($signers as $signer) {
            $tmp = new Approval();
            $tmp->employee_id = $signer['employee']['id'];
            $tmp->position = $signer['position'];
            $tmp->is_signer = true;
            $tmp_signers[] = $tmp->toArray();
        }

        $letter->approvals()->createMany($tmp_signers);

        $letter->approvals()->delete();
        $tmp_approvals = [];
        foreach ($approvals as $approval) {
            $tmp = new Approval();
            $tmp->employee_id = $approval['employee']['id'];
            $tmp->position = $approval['position'];
            $tmp_approvals[] = $tmp->toArray();
        }

        $letter->approvals()->createMany($tmp_approvals);
    }
}
