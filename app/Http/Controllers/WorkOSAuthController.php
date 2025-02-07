<?php

// TODO: is this the correct namespace?
namespace App\Http\Controllers;

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

	public function rediect(Request $request)
	{
		$organizationId = "org_test_idp";
		$authorizationUrl = $this->userManagement->getAuthorizationUrl(
			redirectUri: env('WORKOS_REDIRECT_URI'),
			organizationId: $organizationId
			// FIXME needs at least one of the following:
			// - $provider = null,
			// - $connectionId = null,
			// - $organizationId = null,
		);

		return redirect($authorizationUrl);
	}

	public function callback(Request $request)
	{
		$code = $request->input('code');
        $clientId = config('workos.client_id');
		$user = $this->userManagement->authenticateWithCode(code: $code, clientId: $clientId);
		var_dump($user);
		return redirect('/');
	}
}
