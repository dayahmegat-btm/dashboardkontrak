<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class DepartmentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
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

        // Pengarah: Filter by department
        if ($user->hasRole('pengarah') && $user->jabatan_id) {
            if ($this->modelHasColumn($model, 'jabatan_id')) {
                $builder->where('jabatan_id', $user->jabatan_id);
            }
            return;
        }

        // Ketua-unit and PIC: Filter by unit
        if ($user->hasAnyRole(['ketua-unit', 'pic']) && $user->seksyen_unit_id) {
            if ($this->modelHasColumn($model, 'seksyen_unit_id')) {
                $builder->where('seksyen_unit_id', $user->seksyen_unit_id);
            } elseif ($this->modelHasColumn($model, 'jabatan_id') && $user->jabatan_id) {
                // Fallback to department if no unit column
                $builder->where('jabatan_id', $user->jabatan_id);
            }
            return;
        }
    }

    /**
     * Check if the model has a specific column.
     */
    protected function modelHasColumn(Model $model, string $column): bool
    {
        return $model->getConnection()
            ->getSchemaBuilder()
            ->hasColumn($model->getTable(), $column);
    }
}
