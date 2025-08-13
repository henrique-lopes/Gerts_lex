<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase as BaseTestCase;

class LawyerTest extends BaseTestCase
{
    use RefreshDatabase;

    public function test_lawyer_can_be_created()
    {
        $user = User::factory()->create();
        
        $lawyer = Lawyer::create([
            'user_id' => $user->id,
            'tenant_id' => 1, // Simple tenant ID for testing
            'oab_number' => '123456',
            'state' => 'SP',
            'specialties' => 'Direito Civil',
            'phone' => '11999999999',
        ]);

        $this->assertInstanceOf(Lawyer::class, $lawyer);
        $this->assertEquals('123456', $lawyer->oab_number);
        $this->assertEquals('SP', $lawyer->state);
        $this->assertEquals('Direito Civil', $lawyer->specialties);
        $this->assertEquals($user->id, $lawyer->user_id);
        $this->assertEquals(1, $lawyer->tenant_id);
    }

    public function test_lawyer_belongs_to_user()
    {
        $user = User::factory()->create();
        
        $lawyer = Lawyer::create([
            'user_id' => $user->id,
            'tenant_id' => 1,
            'oab_number' => '123456',
            'state' => 'SP',
            'specialties' => 'Direito Civil',
        ]);

        $this->assertInstanceOf(User::class, $lawyer->user);
        $this->assertEquals($user->id, $lawyer->user->id);
        $this->assertEquals($user->name, $lawyer->user->name);
    }

    public function test_lawyer_can_have_cases()
    {
        $user = User::factory()->create();
        
        $lawyer = Lawyer::create([
            'user_id' => $user->id,
            'tenant_id' => 1,
            'oab_number' => '123456',
            'state' => 'SP',
            'specialties' => 'Direito Civil',
        ]);

        // Test that the relationship exists
        $this->assertTrue(method_exists($lawyer, 'cases'));
    }

    public function test_lawyer_can_have_appointments()
    {
        $user = User::factory()->create();
        
        $lawyer = Lawyer::create([
            'user_id' => $user->id,
            'tenant_id' => 1,
            'oab_number' => '123456',
            'state' => 'SP',
            'specialties' => 'Direito Civil',
        ]);

        // Test that the relationship exists
        $this->assertTrue(method_exists($lawyer, 'appointments'));
    }

    public function test_lawyer_fillable_attributes()
    {
        $lawyer = new Lawyer();
        
        $fillable = $lawyer->getFillable();
        
        $this->assertContains('user_id', $fillable);
        $this->assertContains('tenant_id', $fillable);
        $this->assertContains('oab_number', $fillable);
        $this->assertContains('state', $fillable);
        $this->assertContains('specialties', $fillable);
        $this->assertContains('phone', $fillable);
    }
}
