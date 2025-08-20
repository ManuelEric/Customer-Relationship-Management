<?php

namespace App\Observers;

use App\Models\pivot\ClientMentor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClientMentorObserver
{
    /**
     * Handle the ClientMentor "created" event.
     */
    public function created(ClientMentor $clientMentor): void
    {
        // send request to essay editing to store new record on mentor_as_pic
        $response = Http::post(env('ESSAY_EDITING_BE_URI')."/api/mentor/{$clientMentor->user_id}/type/{$clientMentor->type}/program/{$clientMentor->clientprog_id}");
        Log::debug('Client mentor has been created', [
            'mentor' => $clientMentor,
            'response' => $response->json(),
        ]);
    }

    /**
     * Handle the ClientMentor "updated" event.
     */
    public function updated(ClientMentor $clientMentor): void
    {
        if ($clientMentor->wasChanged('status') == 0) {
            // send request to essay editing carrying clientprog_id, mentor type, and user_id
            // http://127.0.0.1:8080/api/mentor/94384506-ac33-46d3-bbdd-1bb60fe3035e/type/1/program/11001
            $response = Http::patch(env('ESSAY_EDITING_BE_URI')."/api/mentor/{$clientMentor->user_id}/type/{$clientMentor->type}/program/{$clientMentor->clientprog_id}");
            Log::debug('Client mentor has been updated to 0', [
                'mentor' => $clientMentor,
                'response' => $response->json(),
            ]);
        }
    }

    /**
     * Handle the ClientMentor "deleted" event.
     */
    public function deleted(ClientMentor $clientMentor): void
    {
        //
    }

    /**
     * Handle the ClientMentor "restored" event.
     */
    public function restored(ClientMentor $clientMentor): void
    {
        //
    }

    /**
     * Handle the ClientMentor "force deleted" event.
     */
    public function forceDeleted(ClientMentor $clientMentor): void
    {
        //
    }
}
