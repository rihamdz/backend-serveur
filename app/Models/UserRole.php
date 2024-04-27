<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
     // Définition du nom de la clé primaire
     protected $primaryKey = 'user_id';

     // Indique si la clé primaire est un entier
     public $incrementing = false;
 
     // Indique si les timestamps sont gérés automatiquement
     public $timestamps = true;
 
     // Autres attributs du modèle
     protected $fillable = [
         'user_id',
         'type_role',
     ];}
