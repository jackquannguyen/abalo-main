<?php

namespace Tests\Feature;

use App\Models\AbUser;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OfferAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function demoUser(string $name): AbUser
    {
        return AbUser::create([
            'ab_name' => $name,
            'ab_mail' => $name . '@example.test',
            'ab_password' => Hash::make('test-password'),
        ]);
    }

    private function demoArticle(int $ownerId): Article
    {
        return Article::create([
            'ab_name' => 'Testartikel',
            'ab_description' => 'Nur Testdaten',
            'ab_price' => 1000,
            'ab_creator_id' => $ownerId,
            'ab_createdate' => now(),
        ]);
    }

    public function test_guest_cannot_send_offer(): void
    {
        $seller = $this->demoUser('seller');
        $article = $this->demoArticle($seller->id);
        Http::fake();

        $this->postJson("/api/articles/{$article->id}/offer")->assertUnauthorized();
        Http::assertNothingSent();
    }

    public function test_non_owner_cannot_send_offer(): void
    {
        $seller = $this->demoUser('seller');
        $other = $this->demoUser('other');
        $article = $this->demoArticle($seller->id);
        Http::fake();

        $this->withSession(['abalo_user_id' => $other->id])
            ->postJson("/api/articles/{$article->id}/offer")->assertForbidden();
        Http::assertNothingSent();
    }

    public function test_owner_can_send_offer_to_local_bridge(): void
    {
        $seller = $this->demoUser('seller');
        $article = $this->demoArticle($seller->id);
        Http::fake([config('services.abalo_websocket.notify_url') => Http::response(['ok' => true], 200)]);

        $this->withSession(['abalo_user_id' => $seller->id])
            ->postJson("/api/articles/{$article->id}/offer")
            ->assertOk()->assertJsonPath('success', true);

        Http::assertSent(fn ($request) =>
            $request->url() === config('services.abalo_websocket.notify_url')
            && $request['type'] === 'offer'
            && (int) $request['ownerId'] === $seller->id
            && (int) $request['articleId'] === $article->id
        );
    }

    public function test_unavailable_notification_server_returns_503(): void
    {
        $seller = $this->demoUser('seller');
        $article = $this->demoArticle($seller->id);
        Http::fake([config('services.abalo_websocket.notify_url') => Http::response('unavailable', 503)]);

        $this->withSession(['abalo_user_id' => $seller->id])
            ->postJson("/api/articles/{$article->id}/offer")
            ->assertStatus(503)->assertJsonPath('success', false);
    }
}
