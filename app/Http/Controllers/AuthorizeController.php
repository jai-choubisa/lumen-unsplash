<?php
declare(strict_types = 1);

namespace App\Http\Controllers;
session_start();

use Illuminate\Http\Request;
use \League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Token\AccessToken;

class AuthorizeController extends Controller
{
    /**
     * Get a single page from the list of all photos.
     *
     * @return JsonResponse
     */
    public function getAccessCode(Request $request)
    {

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => env('UNSPLASH_ACCESS_KEY'),    // The client ID assigned to you by the provider
            'clientSecret'            => env('UNSPLASH_SECRET_KEY'),   
            'redirectUri'             => "http://127.0.0.1:8000/get-token",
            'urlAuthorize'            => 'https://unsplash.com/oauth/authorize',
            'urlAccessToken'          => 'https://unsplash.com/oauth/token',
            'urlResourceOwnerDetails' => 'https://api.unsplash.com/me'
        ]);

        // If we don't have an authorization code then get one
        if (!$request->input('code')) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $provider->getAuthorizationUrl();

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $provider->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($request->input('state')) || (isset($_SESSION['oauth2state']) && $request->input('state') !== $_SESSION['oauth2state'])) {

            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            
            exit('Invalid state');

        } else {
            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $request->input('code')
                ]);

                $_SESSION['access_token'] = $accessToken->getToken();
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token or user details.
                exit($e->getMessage());
            }
        }

        if(isset($_SESSION['request_url'])) {
            $path = $_SESSION['request_url'];
            // unset($_SESSION['request_url']);
            return redirect($path);
        }
    }

}