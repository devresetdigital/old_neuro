<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class AdminPanelTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
     
        $user = User::find(1);
        
        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'admin123$'
        ]);

        $response->assertStatus(302);

        $response->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);

        $response = $this->actingAs($user)->get('/admin/reports');

        $response->assertStatus(200);
                      
    }
}
