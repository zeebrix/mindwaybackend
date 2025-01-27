<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractProvider
{
    protected $request;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUrl;
    protected $scopes = [];
    protected $state;

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $scopes = [])
    {
        $this->request = $request;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        $this->scopes = $scopes;
    }

    /**
     * Redirect to the OAuth provider's authorization URL.
     */
    public function redirect(): RedirectResponse
    {
        $this->state = Crypt::encrypt($this->request->all());
        $url = $this->createAuthUrl();
        return new RedirectResponse($url);
    }

    /**
     * Retrieve user info using authorization code and store tokens.
     */
    public function getUser()
    {
        try {
            $code = $this->request->get('code');
            $credentials = $this->fetchAccessTokenWithAuthCode($code);
           // dd($credentials);
            DB::table('google_tokens')->updateOrInsert(
                ['counseller_id' => session('google_action_user_id')],
                [
                    'access_token' => Crypt::encrypt($credentials['access_token']),
                    'refresh_token' => isset($credentials['refresh_token']) ? Crypt::encrypt($credentials['refresh_token']) : null,
                    'expires_in' => $credentials['expires_in'] ?? 0,
                ]
            );

            return $this->toUser($this->getBasicProfile($credentials));
        } catch (\Exception $exception) {
            Log::error('Google OAuth error: ' . $exception->getMessage());
            throw new \RuntimeException('Failed to retrieve user information.');
        }
    }

    /**
     * Abstract method to create the authentication URL.
     */
    abstract protected function createAuthUrl(): string;

    /**
     * Abstract method to fetch the access token with authorization code.
     */
    abstract protected function fetchAccessTokenWithAuthCode(string $code): array;

    /**
     * Abstract method to get the basic user profile from credentials.
     */
    abstract protected function getBasicProfile(array $credentials): array;

    /**
     * Abstract method to map profile to a user object.
     */
    abstract protected function toUser(array $profile);
}
?>
