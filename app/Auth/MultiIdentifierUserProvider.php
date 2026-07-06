<?php

namespace App\Auth;

use App\Models\Member;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Resolves a login account from a single identifier that may be an email,
 * a phone number, or a username / member code.
 *
 * The `users` table only stores `email`, so phone and member-code logins are
 * resolved through the linked `members` record (members.user_id → users.id).
 *
 * This plugs into the standard Auth::attempt() pipeline, so every Tyro Login
 * flow (lockout, remember me, email verification, 2FA, OTP) keeps working —
 * only the "find the user" step is customised. Password checking is untouched.
 */
class MultiIdentifierUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $identifier = $this->extractIdentifier($credentials);

        if ($identifier === null || $identifier === '') {
            return null;
        }

        // 1) Direct email match on the users table.
        $user = $this->newModelQuery()->where('email', $identifier)->first();

        if ($user) {
            return $user;
        }

        // 2) Resolve through the member record (phone / secondary mobile / member code).
        $member = Member::query()
            ->whereNotNull('user_id')
            ->where(function ($q) use ($identifier) {
                $q->where('phone', $identifier)
                    ->orWhere('secondary_mobile', $identifier)
                    ->orWhere('member_code', $identifier);
            })
            ->first();

        return $member?->user;
    }

    /**
     * Pull the single login value out of the credentials, ignoring the
     * password (and any remember flag). Works whether Tyro labelled it
     * `email`, `username`, or `login`.
     */
    private function extractIdentifier(array $credentials): ?string
    {
        foreach ($credentials as $key => $value) {
            if ($key === 'remember' || str_contains($key, 'password')) {
                continue;
            }

            if (is_string($value)) {
                return trim($value);
            }
        }

        return null;
    }
}
