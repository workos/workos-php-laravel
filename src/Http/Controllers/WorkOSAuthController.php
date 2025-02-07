<?php

namespace App\Http\Controllers\WorkOS;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use WorkOS\Exception\WorkOSException;
use WorkOS\UserManagement;

class WorkOSAuthController extends Controller
{
    protected UserManagement $userManagement;

    public function __construct()
    {
        $this->userManagement = new UserManagement();
    }

    public function redirect(Request $request)
    {
        try {
            $authorizationUrl = $this->userManagement->getAuthorizationUrl(
                redirectUri: config('workos.redirect_uri'),
                organizationId: $request->get('organization_id')
            );

            return redirect($authorizationUrl);
        } catch (WorkOSException $e) {
            return redirect()->route('login')->withErrors([
                'workos' => 'Authentication failed: ' . $e->getMessage()
            ]);
        }
    }

    public function callback(Request $request)
    {
        try {
            $auth = $this->userManagement->authenticateWithCode(
                code: $request->get('code'),
                clientId: config('workos.client_id')
            );

            // Find or create user
            $user = User::findByWorkOSId($auth->user->id);
            if (!$user) {
                $user = User::create([
                    'name' => trim($auth->user->firstName . ' ' . $auth->user->lastName),
                    'email' => $auth->user->email,
                    'workos_id' => $auth->user->id,
                    'organization_id' => $auth->organization?->id,
                    'sso_data' => $auth->user->toArray(),
                ]);
            }

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'workos' => 'Authentication failed: ' . $e->getMessage()
            ]);
        }
    }
}
