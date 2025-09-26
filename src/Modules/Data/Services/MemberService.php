<?php

namespace Modules\Data\Services;

use App\Models\Member;
use App\Models\PppoeUser;
use App\Models\PppoeMember;
use App\Models\User;
use Illuminate\Support\Carbon;

final class MemberService
{
    public function generateMemberId(User $user): string
    {
        // Get the latest member ID or start from 0
        $lastMember = Member::orderBy('id_member', 'desc')
            ->where('group_id', $user->id_group)
            ->first();
        $lastNumber = $lastMember ? (int)$lastMember->id_member : 0;
        $nextNumber = $lastNumber + 1;

        // Format to 8 digits with leading zeros
        return str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    }
    public function first_id_member(User $user): string
    {
        // Format to 8 digits with leading zeros
        $pad_user = str_pad($user->id_group, 5, '0', STR_PAD_LEFT);
        return $pad_user;
    }
}
