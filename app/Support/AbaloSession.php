<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * A verified Abalo user ID is written only after a successful password login.
 * Never trust a user ID sent from the browser for authorization.
 */
final class AbaloSession
{
    public static function userId(Request $request): ?int
    {
        $value = $request->session()->get('abalo_user_id');
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
    }
}
