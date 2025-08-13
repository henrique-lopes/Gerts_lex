<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lawyer;
use App\Models\Tenant;
use Laravel\Sanctum\Sanctum;

class LawyerApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $tenant;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a tenant for testing
        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test',
        ]);
        
        $this->tenant->makeCurrent();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    public function test_can_get_lawyers_list()
    {
        // Create some lawyers
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Lawyer::create([
            'user_id' => $user1->id,
            'tenant_id' => $this->tenant->id,
            'oab_number' => '123456',
            'state' => 'SP',
            'specialties' => 'Direito Civil',
        ]);
        
        Lawyer::create([
            'user_id' => $user2->id,
            'tenant_id' => $this->tenant->id,
            'oab_number' => '654321',
            'state' => 'RJ',
            'specialties' => 'Direito Penal',
        ]);

        // Test public endpoint
        $response = $this->withHeaders([
            'Host' => 'test.localhost',
            'Accept' => 'application/json',
        ])->get('/api/test/lawyers');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'oab_number',
                            'state',
                            'specialties',
                            'user' => [
                                'id',
                                'name',
                                'email',
                            ]
                        ]
                    ],
                    'pagination' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_create_lawyer_with_authentication()
    {
        Sanctum::actingAs($this->user);

        $lawyerData = [
            'name' => 'Dr. João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'oab_number' => '123456',
            'state' => 'SP',
            'specialties' => 'Direito Civil',
            'phone' => '11999999999',
        ];

        $response = $this->withHeaders([
            'Host' => 'test.localhost',
            'Accept' => 'application/json',
        ])->postJson('/api/lawyers', $lawyerData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'user_id',
                        'oab_number',
                        'state',
                        'specialties',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ]
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('Lawyer created successfully', $response->json('message'));
        
        // Verify lawyer was created in database
        $this->assertDatabaseHas('lawyers', [
            'oab_number' => '123456',
            'state' => 'SP',
            'specialties' => 'Direito Civil',
        ]);
        
        // Verify user was created
        $this->assertDatabaseHas('users', [
            'name' => 'Dr. João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    public function test_cannot_create_lawyer_without_authentication()
    {
        $lawyerData = [
            'name' => 'Dr. João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'oab_number' => '123456',
            'state' => 'SP',
            'specialties' => 'Direito Civil',
        ];

        $response = $this->withHeaders([
            'Host' => 'test.localhost',
            'Accept' => 'application/json',
        ])->postJson('/api/lawyers', $lawyerData);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
    }

    public function test_validation_errors_when_creating_lawyer()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'name' => '', // Required field empty
            'email' => 'invalid-email', // Invalid email format
            'oab_number' => '', // Required field empty
            'state' => 'INVALID', // Invalid state format
        ];

        $response = $this->withHeaders([
            'Host' => 'test.localhost',
            'Accept' => 'application/json',
        ])->postJson('/api/lawyers', $invalidData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => [
                        'name',
                        'email',
                        'oab_number',
                        'state',
                        'password',
                    ]
                ]);

        $this->assertFalse($response->json('success'));
        $this->assertEquals('Validation failed', $response->json('message'));
    }
}
