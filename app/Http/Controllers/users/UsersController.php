<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\DTO\UserDTO;

class UsersController extends Controller
{
  public function index(Request $request, $searchQuery = null)
  {
    $name = $request->input('name');
    $role = $request->input('role');
    $plan = $request->input('plan');
    $status = $request->input('status');
    $sortBy = $request->input('sortBy');
    $itemsPerPage = $request->input('itemsPerPage', 10);
    $page = $request->input('page', 1);
    $orderBy = $request->input('orderBy');

    $query = User::query();

    if ($name) {
      $query->where(function ($q) use ($name) {
        $q->where('name', 'like', '%' . $name . '%')
          ->orWhere('email', 'like', '%' . $name . '%')
          ->orWhere('phone', 'like', '%' . $name . '%');
      });
    }

    if ($role) {
      $roleModel = Role::findByName($role);
      $query->whereHas('roles', function ($q) use ($roleModel) {
        $q->where('name', $roleModel->name);
      });
    }

    if ($status) {
      $query->where('status', $status);
    }

    if ($sortBy) {
      if ($sortBy === 'user') {
        $query->orderBy('name', $orderBy ?? 'asc');
      } elseif ($sortBy === 'email') {
        $query->orderBy('email', $orderBy ?? 'asc');
      } elseif ($sortBy === 'role') {
        $query->orderBy('role', $orderBy ?? 'asc');
      } elseif ($sortBy === 'plan') {
        $query->orderBy('currentPlan', $orderBy ?? 'asc');
      } elseif ($sortBy === 'status') {
        $query->orderBy('status', $orderBy ?? 'asc');
      }
    } else {
      $query->latest();
    }

    $totalUsers = $query->count();
    $totalPages = ceil($totalUsers / $itemsPerPage);
    $users = $query->forPage($page, $itemsPerPage)->get();

    $userDTOs = $users->map(function ($user) {
      return new UserDTO($user);
    });

    return response()->json([
      'users' => $userDTOs,
      'totalPages' => $totalPages,
      'totalUsers' => $totalUsers,
      'page' => $page > $totalPages ? 1 : $page,
    ]);
  }

  /**
   * Remove the specified user from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy($id)
  {
    $user = User::find($id);

    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    try {
      $user->delete();
      return response()->json(['message' => 'User successfully deleted']);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Failed to delete the user', 'error' => $e->getMessage()], 500);
    }
  }
  public function store(Request $request)
  {
    // Validarea datelor primite în cerere
    $validatedData = $request->validate([
      'fullName' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|string|min:8',
      'role' => 'required|string',
      'contact' => [
        'required',
        'string',
        'unique:users,phone',
        'regex:/^[0-9]{10}$/'
      ],
      'status' => 'required|string',
      'avatar' => 'nullable|string',
    ]);

    // Crearea unui nou utilizator
    $user = User::create([
      'name' => $validatedData['fullName'],
      'email' => $validatedData['email'],
      'password' => Hash::make($validatedData['password']),
      'phone' => $validatedData['contact'],
      'status' => $validatedData['status'],
      'avatar' => $validatedData['avatar'],
    ]);

    // Atribuirea rolului specificat utilizatorului
    $role = Role::findByName($validatedData['role'], 'sanctum');
    $user->assignRole($role);

    // Returnarea unui răspuns cu utilizatorul creat
    return response()->json([
      'message' => 'Successfully created user!',
      'user' => $user,
    ], 201);
  }
}
