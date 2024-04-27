<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    
    public function index()
    {
        return Employee::select('id','email','phoneNumber', 'avatar','name', 	'salary')->get();
    }

    public function getAllEmployeesWithRole()
    {
      
        $employeesWithRoles = Employee::employeesWithRoles();

        return response()->json([
            'employees' => $employeesWithRoles
        ]);
    }

    // Fonction pour supprimer un employé et son rôle associé
   // Fonction pour supprimer un employé et son rôle associé
 
 
    public function getComiteeEmployees()
    {
        // Récupérer les détails des employés qui ont un rôle dans la table des rôles
        $comiteeEmployees = Employee::comiteesWithRoles();
    
        return response()->json([
            'comitee_employees' => $comiteeEmployees
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'=>  'required',
            'name'	=>  'required',
            'phoneNumber'=>'required',
            'salary'=> 'required',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validation pour l'avatar
        ]);

        // Créer un nouvel employé
        $employee = new Employee();
        $employee->email = $request->input('email');
        $employee->name = $request->input('name');
        $employee->phoneNumber = $request->input('phoneNumber');
        $employee->salary = $request->input('salary');

        // Gestion de l'avatar
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time().'.'.$avatar->getClientOriginalExtension();
            $avatar->storeAs('avatars', $avatarName); // Stocker l'avatar dans le dossier 'avatars'
            $employee->avatar = $avatarName;
        } else {
            // Avatar par défaut
            $defaultAvatar = 'default-avatar.png';
            $employee->avatar = $defaultAvatar;
        }

        // Enregistrer l'employé
        $employee->save();

        return response()->json([
            'message'=>'Employee added successfully'
        ]);
    }

    // Autres méthodes existantes...


    /**
     * Display the specified resource.
     */
    public function show(Employee $employee )
    {
        return   response()->json([
            'employee'=>$employee
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'name' => 'required',
            'phoneNumber'=>'required',
            'salary' => 'required|numeric',
        ]);
        $employee->fill($request->post())->update();
        return response()->json([
            'message'=>'Item update succsufly'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        {
            try {
                
    
                // Vérification si l'employé a un rôle associé
                $userRole = UserRole::where('user_id', $employee->id)->first();
    
                // Suppression de l'employé et de son rôle associé s'il en a un
                if ($userRole) {
                    $userRole->delete();
                }
                
                // Suppression de l'employé
                $employee->delete();
    
                return response()->json([
                    'message' => 'Employee and associated role (if any) deleted successfully.'
                ]);
            } catch (\Exception $e) {
                // Gérer les erreurs
                return response()->json([
                    'error' => 'Failed to delete employee and associated role: ' . $e->getMessage()
                ], 500);
            }
    }}
}