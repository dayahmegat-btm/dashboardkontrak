<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DaftarKontrak;
use App\Policies\Traits\HasDepartmentScoping;
use Illuminate\Auth\Access\HandlesAuthorization;

class DaftarKontrakPolicy
{
    use HandlesAuthorization;
    use HasDepartmentScoping;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_daftar::kontrak')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DaftarKontrak $daftarKontrak): bool
    {
        return $user->can('view_daftar::kontrak')
            && $this->canAccessModelViaDaftarSst($user, $daftarKontrak);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // User must have permission and have a department assigned
        return $user->can('create_daftar::kontrak')
            && $user->jabatan_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DaftarKontrak $daftarKontrak): bool
    {
        return $user->can('update_daftar::kontrak')
            && $this->canAccessModelViaDaftarSst($user, $daftarKontrak);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DaftarKontrak $daftarKontrak): bool
    {
        return $user->can('delete_daftar::kontrak')
            && $this->canAccessModelViaDaftarSst($user, $daftarKontrak);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_daftar::kontrak')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, DaftarKontrak $daftarKontrak): bool
    {
        return $user->can('force_delete_daftar::kontrak')
            && $this->canAccessModelViaDaftarSst($user, $daftarKontrak);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_daftar::kontrak')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, DaftarKontrak $daftarKontrak): bool
    {
        return $user->can('restore_daftar::kontrak')
            && $this->canAccessModelViaDaftarSst($user, $daftarKontrak);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_daftar::kontrak')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, DaftarKontrak $daftarKontrak): bool
    {
        return $user->can('replicate_daftar::kontrak')
            && $this->canAccessModelViaDaftarSst($user, $daftarKontrak);
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_daftar::kontrak')
            && $this->applyScopingToViewAny($user);
    }
}
