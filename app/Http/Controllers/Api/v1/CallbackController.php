<?php

namespace App\Http\Controllers\Api\v1;

use App\Actions\Meta\Handle as HandleMetaLeads;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CallbackController extends Controller
{

    // Your Facebook App Credentials
    protected $app_id;
    protected $app_secret;
    protected $access_token;
    protected HandleMetaLeads $handleMetaLeads;

    public function __construct(HandleMetaLeads $handleMetaLeads)
    {
        $this->app_id = env('META_APP_ID');
        $this->app_secret = env('META_APP_SECRET');
        $this->access_token = env('META_ACCESS_TOKEN');
        $this->handleMetaLeads = $handleMetaLeads;
    }

    public function verify(Request $request)
    {
        // Replace with your own verify token
        $verify_token = 'EDUALL04';

        // Retrieve the parameters sent by Facebook
        $mode = $request->query('hub_mode');
        $challenge = $request->query('hub_challenge');
        $verify_token_received = $request->query('hub_verify_token');

        // Verify the token matches what you expect
        if ($mode === 'subscribe' && $verify_token === $verify_token_received) {
            // Return the challenge code to complete the verification
            Log::info('[META API] Verification success : ' . $challenge);
            return response($challenge);
        } else {
            // If the verification fails
            Log::error('[META API] Verification failed.');
            return response('Verification failed.', 400);
        }
    }

    public function read_lead(Request $request)
    {
        // Log request data
        $log_data = [
            'raw_post_data' => $request->getContent(),
            'headers' => $request->headers->all()
        ];

        // Extract Leadgen ID from the POST data
        $input_data = json_decode($request->getContent(), true);
        $leadgen_id = $input_data['entry'][0]['changes'][0]['value']['leadgen_id'] ?? null;
        $form_id = $input_data['entry'][0]['changes'][0]['value']['form_id'] ?? null;

        if ($leadgen_id) {
            // If we have the Leadgen ID, proceed with further logic
            $this->processLeadgenData($leadgen_id, $form_id);
        }

        return response()->json(['message' => 'Webhook received successfully.'], 200);
    }

    public function processLeadgenData($leadgen_id, $form_id)
    {
        // Check if the access token is valid
        if (!$this->isAccessTokenValid($this->access_token)) {
            $this->access_token = $this->refreshAccessToken();
        }

        $form = Http::get("https://graph.facebook.com/v17.0/{$form_id}", [
            'access_token' => $this->access_token
        ]);

        // Call Facebook Graph API to fetch lead data
        $response = Http::get("https://graph.facebook.com/v17.0/{$leadgen_id}", [
            'access_token' => $this->access_token
        ]);

        if ($response->successful()) {

            $this->handleMetaLeads->execute($form->json(), $response->json()['field_data']);
            Log::notice('[META API] Lead received successfully', ['Form' => $form->json(), 'Lead' => $response->json()['field_data']]);

        } else {
            Log::error("Error fetching lead data: " . $response->body());
        }
    }

    public function isAccessTokenValid($access_token)
    {
        $url = "https://graph.facebook.com/v17.0/debug_token?input_token={$access_token}&access_token={$this->app_id}|{$this->app_secret}";

        // Make the cURL request using Laravel's HTTP client (for better readability and error handling)
        $response = Http::get($url);

        return $response->successful() && $response->json()['data']['is_valid'] ?? false;
    }

    public function refreshAccessToken()
    {
        $url = "https://graph.facebook.com/v23.0/oauth/access_token?grant_type=fb_exchange_token&client_id={$this->app_id}&client_secret={$this->app_secret}&fb_exchange_token={$this->access_token}";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        Log::error("Error refreshing access token: " . $response->body());
        return null;
    }
}
