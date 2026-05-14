<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class DaftarSstRelationshipScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * This scope is for models that are related to DaftarSst through relationships.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // Don't apply scope if no user is authenticated
        if (!$user) {
            return;
        }

        // Don't apply scope for super-admin, admin, sk-exec, and audit
        if ($user->hasAnyRole(['super-admin', 'admin', 'sk-exec', 'audit'])) {
            return;
        }

        // Apply scope based on relationship path
        $relationshipPath = $this->getRelationshipPathToDaftarSst($model);

        if (!$relationshipPath) {
            return;
        }

        // Pengarah: Filter by department through relationship
        if ($user->hasRole('pengarah') && $user->jabatan_id) {
            $builder->whereHas($relationshipPath, function ($query) use ($user) {
                $query->where('jabatan_id', $user->jabatan_id);
            });
            return;
        }

        // Ketua-unit and PIC: Filter by unit through relationship
        if ($user->hasAnyRole(['ketua-unit', 'pic']) && $user->seksyen_unit_id) {
            $builder->whereHas($relationshipPath, function ($query) use ($user) {
                $query->where('seksyen_unit_id', $user->seksyen_unit_id);
            });
            return;
        }
    }

    /**
     * Get the relationship path from the model to DaftarSst.
     */
    protected function getRelationshipPathToDaftarSst(Model $model): ?string
    {
        $table = $model->getTable();

        // Direct relationship: DaftarKontrak -> DaftarSst
        if ($table === 'daftar_kontrak') {
            return 'daftarSst';
        }

        // Nested relationships: Model -> DaftarKontrak -> DaftarSst
        if (in_array($table, ['bon_pelaksanaan', 'penilaian_prestasi', 'aduan'])) {
            return 'daftarKontrak.daftarSst';
        }

        return null;
    }
}
