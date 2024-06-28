<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserStatsController extends Controller
{
    public function getUserStats()
    {
        $totalUsers = User::count();
        $totalAdmins = User::role('admin', 'sanctum')->count();  // Specifică guard-ul 'sanctum'

        // Total utilizatori activi care nu sunt admini
        $totalActiveUsers = User::where('status', 'active')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->count();

        // Total utilizatori inactivi care nu sunt admini
        $totalInactiveUsers = User::where('status', 'inactive')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalActiveUsers' => $totalActiveUsers,
            'totalInactiveUsers' => $totalInactiveUsers
        ]);
    }
}
