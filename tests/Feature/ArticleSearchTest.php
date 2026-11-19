<?php

namespace Tests\Feature;

use App\Models\AbUser;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ArticleSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_search_returns_paginated_article_list_and_name_search_filters(): void
    {
        $user = AbUser::create([
            'ab_name' => 'owner', 'ab_mail' => 'owner@example.test',
            'ab_password' => Hash::make('test-password'),
        ]);
        foreach (['Toyota Corolla', 'Winterreifen', 'Radio'] as $name) {
            Article::create([
                'ab_name' => $name,
                'ab_description' => 'Demo',
                'ab_price' => 100,
                'ab_creator_id' => $user->id,
                'ab_createdate' => now(),
            ]);
        }

        $this->getJson('/api/articles?search=&page=1')
            ->assertOk()->assertJsonPath('total', 3)
            ->assertJsonCount(3, 'data');
        $this->getJson('/api/articles?search=toyota&page=1')
            ->assertOk()->assertJsonPath('total', 1)
            ->assertJsonCount(1, 'data');
    }
}
