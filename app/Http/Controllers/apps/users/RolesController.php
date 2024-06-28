<?php

namespace App\Http\Controllers\apps\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;


class RolesController extends Controller
{

    public function index()
    {
        $roles = Role::all(['id', 'name']);
        return response()->json($roles);
    }

    //👉 - For roles page
    public function rolesPermission()
    {
        $roles = Role::with(['permissions', 'users'])->get();

        $rolesData = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'role' => $role->name,
                'users' => $role->users->pluck('avatar')->toArray(), // Extragem doar avatarurile utilizatorilor
                'details' => [
                    'name' => $role->name,
                    'permissions' => $role->permissions->map(function ($permission) {
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
                    })->values()->toArray(),
                ],
            ];
        });

        return response()->json($rolesData);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*.name' => 'required|string',
            'permissions.*.read' => 'boolean',
            'permissions.*.write' => 'boolean',
            'permissions.*.create' => 'boolean',
        ]);

        $role = Role::create(['name' => $validatedData['name']]);

        $permissionsData = $validatedData['permissions'];
        $permissionsToSync = [];

        foreach ($permissionsData as $permissionData) {
            $permissionName = $permissionData['name'];
            $read = $permissionData['read'] ?? false;
            $write = $permissionData['write'] ?? false;
            $create = $permissionData['create'] ?? false;

            $readPermission = Permission::firstOrCreate(['name' => $permissionName . ' read']);
            $writePermission = Permission::firstOrCreate(['name' => $permissionName . ' write']);
            $createPermission = Permission::firstOrCreate(['name' => $permissionName . ' create']);

            if ($read) {
                $permissionsToSync[] = $readPermission->id;
            }

            if ($write) {
                $permissionsToSync[] = $writePermission->id;
            }

            if ($create) {
                $permissionsToSync[] = $createPermission->id;
            }
        }

        $role->syncPermissions($permissionsToSync);

        return response()->json(['message' => 'Role created successfully'], 201);
    }

    // add a function to delete a role
    public function destroy($roleId)
    {
        $role = Role::findById($roleId);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully'], 200);
    }

    public function update(Request $request, $roleId)
    {
        $role = Role::findById($roleId);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'array',
            'permissions.*.name' => 'required|string',
            'permissions.*.read' => 'boolean',
            'permissions.*.write' => 'boolean',
            'permissions.*.create' => 'boolean',
        ]);

        $validator->after(function ($validator) use ($request) {
            $permissions = $request->input('permissions', []);
            $hasReadPermission = false;
            foreach ($permissions as $permission) {
                if (isset($permission['read']) && $permission['read'] === true) {
                    $hasReadPermission = true;
                    break;
                }
            }
            if (!$hasReadPermission) {
                $validator->errors()->add('permissions', 'Trebuie să existe cel puțin o permisiune cu câmpul read setat la true.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $role->name = $validatedData['name'];
        $role->save();

        $permissionsData = $validatedData['permissions'];
        $permissionsToSync = [];

        foreach ($permissionsData as $permissionData) {
            $permissionName = $permissionData['name'];
            $read = $permissionData['read'] ?? false;
            $write = $permissionData['write'] ?? false;
            $create = $permissionData['create'] ?? false;

            $readPermission = Permission::firstOrCreate(['name' => $permissionName . ' read']);
            $writePermission = Permission::firstOrCreate(['name' => $permissionName . ' write']);
            $createPermission = Permission::firstOrCreate(['name' => $permissionName . ' create']);

            if ($read) {
                $permissionsToSync[] = $readPermission->id;
            } else {
                $role->revokePermissionTo($readPermission);
            }

            if ($write) {
                $permissionsToSync[] = $writePermission->id;
            } else {
                $role->revokePermissionTo($writePermission);
            }

            if ($create) {
                $permissionsToSync[] = $createPermission->id;
            } else {
                $role->revokePermissionTo($createPermission);
            }
        }

        $role->syncPermissions($permissionsToSync);

        return response()->json(['message' => 'Role updated successfully'], 200);
    }
}
