<?php

namespace App\Http\DTO;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserDTO
{
    public $id;
    public $fullName;
    public $email;
    public $role;
    public $phone;
    public $status;
    public $avatar;


    public function __construct(User $user)
    {
            $this ->id = $user->id;
            $this ->fullName = $user->name;

           // Preluăm rolul utilizatorului din tabelul roles
           $userRole = $user->roles()->first();
           $this->role = $userRole ? $userRole->name : "N/A";
            $this ->phone = $user->phone;
            $this ->email = $user->email;
            $this ->status = $user->status;
            $this ->avatar = "";
    }
}
