<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Model
{
    use HasFactory,HasApiTokens;
    protected $fillable=[
        'id'  ,'email', 'phoneNumber','active','name', 	'salary','avatar'
    ];


    /**
     * Get employee  who have role in the user_roles table.
     */
    public function userRole()
    {
        return $this->hasOne(UserRole::class, 'user_id', 'id');
    }
    public static function employeesWithRoles()
    {
        return static::with('userRole')->get();
    }
    public static function comiteesWithRoles()
    {
        // Récupérer les employés avec leurs rôles associés
        return static::whereHas('userRole')->with('userRole')->get();
    }

}

