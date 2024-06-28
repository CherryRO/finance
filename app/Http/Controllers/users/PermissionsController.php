<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        $permissionsData = $permissions->map(function ($permission) {
            $permNameParts = explode(' ', $permission->name);
            $permBaseName = array_slice($permNameParts, 0, -1);
            $permType = end($permNameParts);

            return [
                'name' => implode(' ', $permBaseName),
                'read' => $permType === 'read' ? true : false,
                'write' => $permType === 'write' ? true : false,
                'create' => $permType === 'create' ? true : false,
            ];
        })->groupBy('name')->map(function ($permissions, $name) {
            return [
                'name' => $name,
                'read' => $permissions->where('read', true)->isNotEmpty(),
                'write' => $permissions->where('write', true)->isNotEmpty(),
                'create' => $permissions->where('create', true)->isNotEmpty(),
            ];
        })->values()->toArray();

        return response()->json($permissionsData);
    }
}
