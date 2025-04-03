<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class UpdateAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:google-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all Google access tokens using their refresh tokens';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Fetch all rows with refresh tokens from the google_tokens table
        $tokens = DB::table('google_tokens')->whereNotNull('refresh_token')->get();

        foreach ($tokens as $token) {
            $this->info("Updating token for User ID: {$token->counseller_id}");

            // Ensure refresh token exists before using it
            if (!empty($token->refresh_token)) {
                try {
                    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                        'client_id' => config('services.google.client_id'),
                        'client_secret' => config('services.google.client_secret'),
                        'refresh_token' => Crypt::decrypt($token->refresh_token),
                        'grant_type' => 'refresh_token',
                    ]);
                    
                    // Check if the request was successful
                    if ($response->successful()) {
                        $accessToken = $response->json('access_token');
                        $expiresIn = $response->json('expires_in'); // Expiration time in seconds
                        $refreshToken = $response->json('refresh_token');
                        $updateData = [
                            'access_token' => Crypt::encrypt($accessToken),
                            'expires_in' => $expiresIn,
                            'updated_at' => now(),
                        ];
                        if ($refreshToken) {
                            $updateData['refresh_token'] = Crypt::encrypt($refreshToken);
                        }
                        DB::table('google_tokens')
                            ->where('id', $token->id)
                            ->update($updateData);
                        
                        Log::info("✅ Access token updated successfully.", [
                                'User ID' => $token->counseller_id,
                                'Token Expires In' => $expiresIn,
                                'New Refresh Token' => $refreshToken ? 'Yes' : 'No', // Log whether a new refresh token was provided
                            ]);
                        $this->info("Access token for User ID: {$token->counseller_id} updated successfully.");
                    } else {
                        $errorResponse = $response->json();
                        if (isset($errorResponse['error']) && $errorResponse['error'] == 'invalid_grant') {
                            $this->error("Refresh token for User ID: {$token->counseller_id} is invalid or expired. Please reauthorize.");
                        } else {
                            $this->error("Failed to refresh token for User ID: {$token->counseller_id}. Error: {$response->body()}");
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("Error updating token for User ID: {$token->counseller_id}. Exception: {$e->getMessage()}");
                }
            } else {
                $this->error("No refresh token found for User ID: {$token->counseller_id}");
            }
        }

        return Command::SUCCESS;
    }
}
