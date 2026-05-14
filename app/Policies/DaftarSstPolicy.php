<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DaftarSst;
use App\Policies\Traits\HasDepartmentScoping;
use Illuminate\Auth\Access\HandlesAuthorization;

class DaftarSstPolicy
{
    use HandlesAuthorization;
    use HasDepartmentScoping;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_daftar::sst')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DaftarSst $daftarSst): bool
    {
        return $user->can('view_daftar::sst')
            && $this->canAccessModel($user, $daftarSst);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // User must have permission and have a department assigned
        return $user->can('create_daftar::sst')
            && $user->jabatan_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DaftarSst $daftarSst): bool
    {
        return $user->can('update_daftar::sst')
            && $this->canAccessModel($user, $daftarSst);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DaftarSst $daftarSst): bool
    {
        return $user->can('delete_daftar::sst')
            && $this->canAccessModel($user, $daftarSst);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_daftar::sst')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, DaftarSst $daftarSst): bool
    {
        return $user->can('force_delete_daftar::sst')
            && $this->canAccessModel($user, $daftarSst);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_daftar::sst')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, DaftarSst $daftarSst): bool
    {
        return $user->can('restore_daftar::sst')
            && $this->canAccessModel($user, $daftarSst);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_daftar::sst')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, DaftarSst $daftarSst): bool
    {
        return $user->can('replicate_daftar::sst')
            && $this->canAccessModel($user, $daftarSst);
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_daftar::sst')
            && $this->applyScopingToViewAny($user);
    }
}
