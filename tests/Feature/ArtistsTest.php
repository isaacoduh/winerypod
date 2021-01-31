<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ArtistsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function it_returns_an_artist_as_a_resource_object()
    {
        $artist = Artist::factory()->create();
        $user = User::factory()->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/artists/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "artists",
                    "attributes" => [
                        'name' => $artist->name,
                        'created_at' => $artist->created_at->toJSON(),
                        'updated_at' => $artist->updated_at->toJSON(),
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */

    public function it_returns_all_artists_as_a_collection_of_resource_objects()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $artists = Artist::factory()->times(3)->create();
        $this->get('/api/v1/artists', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "artists",
                    "attributes" => [
                        'name' => $artists[0]->name,
                        'created_at' => $artists[0]->created_at->toJSON(),
                        'updated_at' => $artists[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "artists",
                    "attributes" => [
                        'name' => $artists[1]->name,
                        'created_at' => $artists[1]->created_at->toJSON(),
                        'updated_at' => $artists[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "artists",
                    "attributes" => [
                        'name' => $artists[2]->name,
                        'created_at' => $artists[2]->created_at->toJSON(),
                        'updated_at' => $artists[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_create_an_artist_from_a_resource_object()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/artists', [
            'data' => [
                'type' => 'artists',
                'attributes' => [
                    'name' => 'Jane Snow'
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "artists",
                    "attributes" => [
                        'name' => 'Jane Snow',
                        'created_at' => now()->setMilliseconds(0)->toJSON(),
                        'updated_at' => now()->setMilliseconds(0)->toJSON(),
                    ]
                ]
            ])->assertHeader('Location', url('api/v1/artists/1'));
        $this->assertDatabaseHas('artists', [
            'id' =>  1,
            'name' => 'Jane Snow'
        ]);
    }


    /**
     * @test
     * @watch
     */
    public function it_can_update_an_artist_from_a_resource_object()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $artist = Artist::factory()->create();
        $this->patchJson('/api/v1/artists/1', [
            'data' => [
                'id' => '1',
                'type' => 'artists',
                'attributes' => [
                    'name' => 'Mendez Snow',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'artists',
                'attributes' => [
                    'name' => 'Mendez Snow',
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON(),
                ],
            ]
        ]);

        $this->assertDatabaseHas('artists', [
            'id' => 1,
            'name' => 'Mendez Snow',
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_delete_an_artist_through_a_delete_request()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $artist = Artist::factory()->create();

        $this->delete('/api/v1/artists/1', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseMissing('artists', [
            'id' => 1,
            'name' => $artist->name,
        ]);
    }
}
