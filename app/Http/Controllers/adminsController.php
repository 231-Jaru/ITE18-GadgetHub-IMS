<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use Illuminate\Http\Request;

class AdminsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admins",
     *     summary="Get all admins",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of admins",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Admin"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $admins = Admins::all();
        
        if (request()->is('api/*')) {
            return response()->json($admins, 200);
        }
        
        return view('admins.index', compact('admins'));
    }

    /**
     * @OA\Get(
     *     path="/api/admins/{id}",
     *     summary="Get specific admin",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Admin ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin not found"
     *     )
     * )
     */
    public function show($id)
    {
        $admin = Admins::find($id);
        
        if (!$admin) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Admin not found'], 404);
            }
            return redirect('/admins')->with('error', 'Admin not found');
        }
        
        if (request()->is('api/*')) {
            return response()->json($admin, 200);
        }
        
        return view('admins.show', compact('admin'));
    }

    /**
     * @OA\Post(
     *     path="/api/admins",
     *     summary="Create new admin",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Username","PasswordHash"},
     *             @OA\Property(property="Username", type="string", example="newadmin"),
     *             @OA\Property(property="PasswordHash", type="string", example="password123"),
     *             @OA\Property(property="Role", type="string", example="Staff")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Admin created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Admin created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Username' => 'required|string|max:50|unique:admins,Username',
            'PasswordHash' => 'required|string|min:6',
            'Role' => 'nullable|string|max:20',
        ]);

        // Encrypt password
        $validated['PasswordHash'] = bcrypt($validated['PasswordHash']);

        $admin = Admins::create($validated);

        if (request()->is('api/*')) {
            return response()->json($admin, 201);
        }

        return redirect()->route('admins.index')->with('success', 'Admin created successfully!');
    }

    /**
     * @OA\Put(
     *     path="/api/admins/{id}",
     *     summary="Update admin",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Admin ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="Username", type="string", example="updatedadmin"),
     *             @OA\Property(property="PasswordHash", type="string", example="newpassword123"),
     *             @OA\Property(property="Role", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Admin updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $admin = Admins::find($id);
        
        if (!$admin) {
            if ($this->isApiRequest()) {
                return $this->notFoundResponse('Admin');
            }
            return redirect('/admins')->with('error', 'Admin not found');
        }

        try {
            $validated = $request->validate([
                'Username' => 'sometimes|string|max:50|unique:admins,Username,' . $id . ',AdminID',
                'PasswordHash' => 'sometimes|string|min:6',
                'Role' => 'sometimes|string|max:20',
            ]);

            if (isset($validated['PasswordHash'])) {
                $validated['PasswordHash'] = bcrypt($validated['PasswordHash']);
            }

            $admin->update($validated);

            if ($this->isApiRequest()) {
                return $this->successResponse($admin, 'Admin updated successfully');
            }

            return redirect('/admins')->with('success', 'Admin updated successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($this->isApiRequest()) {
                return $this->validationErrorResponse($e->errors());
            }
            throw $e;
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admins/{id}",
     *     summary="Delete admin",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Admin ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Admin deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $admin = Admins::find($id);
        
        if (!$admin) {
            if ($this->isApiRequest()) {
                return $this->notFoundResponse('Admin');
            }
            return redirect('/admins')->with('error', 'Admin not found');
        }

        $admin->delete();

        if ($this->isApiRequest()) {
            return $this->successResponse(null, 'Admin deleted successfully');
        }

        return redirect()->route('admins.index')->with('success', 'Admin deleted successfully!');
    }
}
