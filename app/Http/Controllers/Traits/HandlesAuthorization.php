<?php

namespace App\Http\Controllers\Traits;

trait HandlesAuthorization
{
    /**
     * Check if current user can modify a resource
     * 
     * @param object $resource Resource with AdminID property
     * @param string $resourceName Name of resource for error messages
     * @return bool|Illuminate\Http\RedirectResponse|Illuminate\Http\JsonResponse
     */
    protected function authorizeResource($resource, $resourceName = 'resource')
    {
        if (!$resource) {
            return $this->notFoundResponse($resourceName);
        }

        $currentAdminId = session('user_id');
        
        // If resource has AdminID and it doesn't match current user, deny access
        if (isset($resource->AdminID) && $resource->AdminID && $resource->AdminID != $currentAdminId) {
            $message = "Unauthorized. You can only modify {$resourceName}s you created.";
            
            if (request()->is('api/*')) {
                return response()->json(['message' => $message], 403);
            }
            
            return redirect()->back()->with('error', $message);
        }

        return true;
    }

    /**
     * Return not found response
     * 
     * @param string $resourceName
     * @return Illuminate\Http\RedirectResponse|Illuminate\Http\JsonResponse
     */
    protected function notFoundResponse($resourceName = 'Resource')
    {
        $message = ucfirst($resourceName) . ' not found';
        
        if (request()->is('api/*')) {
            return response()->json(['message' => $message], 404);
        }
        
        return redirect()->back()->with('error', $message);
    }

    /**
     * Get current admin ID from session
     * 
     * @return int|null
     */
    protected function getCurrentAdminId()
    {
        return session('user_id');
    }
}

