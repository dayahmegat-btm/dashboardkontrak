<?php

namespace App\Policies\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait HasDepartmentScoping
{
    /**
     * Determine if the user can access records from the given department.
     */
    protected function canAccessDepartment(User $user, ?int $jabatanId): bool
    {
        // Super-admin and admin can access all departments
        if ($user->hasAnyRole(['super-admin', 'admin', 'sk-exec', 'audit'])) {
            return true;
        }

        // Pengarah can access their entire department
        if ($user->hasRole('pengarah')) {
            return $jabatanId === $user->jabatan_id;
        }

        // Ketua-unit can access their unit only
        if ($user->hasRole('ketua-unit')) {
            return $jabatanId === $user->jabatan_id;
        }

        // PIC can access their unit only
        if ($user->hasRole('pic')) {
            return $jabatanId === $user->jabatan_id;
        }

        return false;
    }

    /**
     * Determine if the user can access records from the given unit.
     */
    protected function canAccessUnit(User $user, ?int $seksyenUnitId): bool
    {
        // Super-admin and admin can access all units
        if ($user->hasAnyRole(['super-admin', 'admin', 'sk-exec', 'audit'])) {
            return true;
        }

        // Pengarah can access all units in their department
        if ($user->hasRole('pengarah')) {
            // Check if the unit belongs to their department
            if ($seksyenUnitId && $user->jabatan_id) {
                $unit = \App\Models\SeksyenUnit::find($seksyenUnitId);
                return $unit && $unit->jabatan_id === $user->jabatan_id;
            }
            return true; // Can access null unit
        }

        // Ketua-unit can access their specific unit
        if ($user->hasRole('ketua-unit')) {
            return $seksyenUnitId === $user->seksyen_unit_id;
        }

        // PIC can access their specific unit
        if ($user->hasRole('pic')) {
            return $seksyenUnitId === $user->seksyen_unit_id;
        }

        return false;
    }

    /**
     * Determine if the user can access the given model based on department/unit.
     */
    protected function canAccessModel(User $user, Model $model): bool
    {
        // Super-admin and admin can access all records
        if ($user->hasAnyRole(['super-admin', 'admin', 'sk-exec', 'audit'])) {
            return true;
        }

        // Check if model has jabatan_id
        if (isset($model->jabatan_id)) {
            if (!$this->canAccessDepartment($user, $model->jabatan_id)) {
                return false;
            }
        }

        // Check if model has seksyen_unit_id
        if (isset($model->seksyen_unit_id)) {
            if (!$this->canAccessUnit($user, $model->seksyen_unit_id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply department scoping to view_any queries.
     */
    protected function applyScopingToViewAny(User $user): bool
    {
        // Super-admin, admin, sk-exec, and audit can view all
        if ($user->hasAnyRole(['super-admin', 'admin', 'sk-exec', 'audit'])) {
            return true;
        }

        // Pengarah can view all in their department
        if ($user->hasRole('pengarah') && $user->jabatan_id) {
            return true;
        }

        // Ketua-unit can view all in their unit
        if ($user->hasRole('ketua-unit') && $user->seksyen_unit_id) {
            return true;
        }

        // PIC can view all in their unit
        if ($user->hasRole('pic') && $user->seksyen_unit_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can access a model through its DaftarSst relationship.
     * For models like DaftarKontrak, BonPelaksanaan, etc. that don't have direct jabatan_id.
     */
    protected function canAccessModelViaDaftarSst(User $user, Model $model): bool
    {
        // Super-admin and admin can access all records
        if ($user->hasAnyRole(['super-admin', 'admin', 'sk-exec', 'audit'])) {
            return true;
        }

        // Get the DaftarSst record through relationships
        $daftarSst = null;

        // DaftarKontrak has direct relationship to DaftarSst
        if ($model instanceof \App\Models\DaftarKontrak) {
            $daftarSst = $model->daftarSst;
        }

        // Other models (BonPelaksanaan, PenilaianPrestasi, Aduan) go through DaftarKontrak
        if ($model instanceof \App\Models\BonPelaksanaan ||
            $model instanceof \App\Models\PenilaianPrestasi ||
            $model instanceof \App\Models\Aduan) {
            $daftarSst = $model->daftarKontrak?->daftarSst;
        }

        // If we can't find the DaftarSst, deny access
        if (!$daftarSst) {
            return false;
        }

        // Check department access
        if (!$this->canAccessDepartment($user, $daftarSst->jabatan_id)) {
            return false;
        }

        // Check unit access
        if (isset($daftarSst->seksyen_unit_id)) {
            if (!$this->canAccessUnit($user, $daftarSst->seksyen_unit_id)) {
                return false;
            }
        }

        return true;
    }
}
