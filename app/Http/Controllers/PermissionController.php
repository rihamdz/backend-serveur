<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;


class PermissionController extends Controller {

    public function canAddOffer($employeeId)
    {
        // Récupérer l'employé en fonction de son ID avec son rôle associé
        $employee = Employee::with('userRole')->find($employeeId);
    
        // Vérifier si l'employé a été trouvé et s'il a un rôle associé
        if ($employee && $employee->userRole) {
            // Vérifier si l'employé a le rôle de président ou de vice-président
            if ($employee->userRole->type_role === 'Président' || $employee->userRole->type_role === 'Vice-Président') {
                return response()->json(['canAddOffer' => true]); // L'employé a le rôle de président ou de vice-président
            }
        }
    
        return response()->json(['canAddOffer' => false]); // L'employé n'a pas le rôle de président ou de vice-président ou n'a pas de rôle associé
    }
    
    
}