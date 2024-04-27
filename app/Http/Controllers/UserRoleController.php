<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserRole::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'type_role' => 'required'
        ]);
    
        // Vérifier si un enregistrement avec le même user_id et type_role existe déjà
        $existingRole = UserRole::where('user_id', $request->input('user_id'))
                                 ->where('type_role', $request->input('type_role'))
                                 ->first();
    
        if ($existingRole) {
            // Si un enregistrement existe, vous pouvez choisir de ne rien faire ou de mettre à jour les données existantes
            // Par exemple, vous pouvez ajouter du code ici pour gérer les cas où vous voulez mettre à jour certaines données
            // $existingRole->update($request->all());
            return response()->json([
                'message' => 'Item already exists'
            ]);
        } else {
            // Sinon, créez un nouvel enregistrement
            UserRole::create($request->all());
            return response()->json([
                'message' => 'Item added successfully'
            ]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($user_id)
    {
        return UserRole::findOrFail($user_id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id)
    {
        $request->validate([
            'type_role' => 'required'
        ]);
        
        UserRole::where('user_id', $user_id)->update($request->all());

        return response()->json([
            'message' => 'Item updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        UserRole::findOrFail($user_id)->delete();
        
        return response()->json([
            'message' => 'Item deleted successfully'
        ]);
    }
}