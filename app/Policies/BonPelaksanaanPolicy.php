<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BonPelaksanaan;
use App\Policies\Traits\HasDepartmentScoping;
use Illuminate\Auth\Access\HandlesAuthorization;

class BonPelaksanaanPolicy
{
    use HandlesAuthorization;
    use HasDepartmentScoping;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bon::pelaksanaan')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BonPelaksanaan $bonPelaksanaan): bool
    {
        return $user->can('view_bon::pelaksanaan')
            && $this->canAccessModelViaDaftarSst($user, $bonPelaksanaan);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // User must have permission and have a department assigned
        return $user->can('create_bon::pelaksanaan')
            && $user->jabatan_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BonPelaksanaan $bonPelaksanaan): bool
    {
        return $user->can('update_bon::pelaksanaan')
            && $this->canAccessModelViaDaftarSst($user, $bonPelaksanaan);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BonPelaksanaan $bonPelaksanaan): bool
    {
        return $user->can('delete_bon::pelaksanaan')
            && $this->canAccessModelViaDaftarSst($user, $bonPelaksanaan);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_bon::pelaksanaan')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, BonPelaksanaan $bonPelaksanaan): bool
    {
        return $user->can('force_delete_bon::pelaksanaan')
            && $this->canAccessModelViaDaftarSst($user, $bonPelaksanaan);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_bon::pelaksanaan')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, BonPelaksanaan $bonPelaksanaan): bool
    {
        return $user->can('restore_bon::pelaksanaan')
            && $this->canAccessModelViaDaftarSst($user, $bonPelaksanaan);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_bon::pelaksanaan')
            && $this->applyScopingToViewAny($user);
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, BonPelaksanaan $bonPelaksanaan): bool
    {
        return $user->can('replicate_bon::pelaksanaan')
            && $this->canAccessModelViaDaftarSst($user, $bonPelaksanaan);
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_bon::pelaksanaan')
            && $this->applyScopingToViewAny($user);
    }
}
