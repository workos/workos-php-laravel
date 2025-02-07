<?php

// TODO: is this the correct namespace?
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
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
		$organizationId = "org_test_idp";
try {
		$authorizationUrl = $this->userManagement->getAuthorizationUrl(
			redirectUri: config('workos.redirect_uri'),
			organizationId: $request->input('organization_id'),
			state: $this->generateState(),
			// FIXME needs at least one of the following:
			// - $provider = null,
			// - $connectionId = null,
			// - $organizationId = null,
		);

			$session->put('wos-session', $request->input('state'));

		return redirect($authorizationUrl);
		} catch (WorkOSException $e) {
			return redirect()->route('login')->withErrors([
				'workos' => 'Unable to initiate WorkOS authentication. Please try again later. '.$e.getMessage()
			]);
		}
	}

	public function callback(Request $request)
	{
		$code = $request->input('code');
        $clientId = config('workos.client_id');
		$user = $this->userManagement->authenticateWithCode(code: $code, clientId: $clientId);
		var_dump($user);
		return redirect('/');
	}

	protected function createUser($auth)
	{
		return User::create([
			'email' => $auth->email,
			'name' => $auth->name,
			'workos_id' => $auth->id,
			'organization_id' => $auth->organization_id,
			'sso_data' => $auth->sso_data,
		])
	}

	protected function generateState()
	{
		// FIXME: I don't think this is necessary
		return bin2hex(random_bytes(32));
	}
}
