<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\AbaloSession;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    public function offer(Request $request, int $id)
    {
        $userId = AbaloSession::userId($request);
        if ($userId === null) {
            return response()->json(['success' => false, 'error' => 'Bitte zuerst anmelden.'], 401);
        }

        $article = Article::findOrFail($id);
        if ((int) $article->ab_creator_id !== $userId) {
            return response()->json(['success' => false, 'error' => 'Dieser Artikel gehört nicht Ihnen.'], 403);
        }

        try {
            $response = Http::timeout(3)->post(config('services.abalo_websocket.notify_url'), [
                'type' => 'offer',
                'articleId' => (int) $article->id,
                'ownerId' => $userId,
                'articleName' => $article->ab_name,
                'message' => "Der Artikel {$article->ab_name} ist jetzt im Angebot!",
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('Abalo WebSocket bridge unreachable', ['error' => $exception->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Benachrichtigungsdienst ist nicht erreichbar.'], 503);
        }

        if (!$response->successful()) {
            Log::warning('Abalo WebSocket bridge rejected notification', ['status' => $response->status()]);
            return response()->json(['success' => false, 'error' => 'Benachrichtigung konnte nicht gesendet werden.'], 503);
        }

        return response()->json(['success' => true, 'message' => 'Angebot erfolgreich gesendet.']);
    }
}
