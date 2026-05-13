<?php

namespace App\Policies;

use App\Models\Lokasi;
use App\Models\User;

class LokasiPolicy
{
    public function update(User $user, Lokasi $lokasi): bool
    {
        return $user->id === $lokasi->user_id;
    }

    public function delete(User $user, Lokasi $lokasi): bool
    {
        return $user->id === $lokasi->user_id;
    }
}
