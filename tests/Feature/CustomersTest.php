<?php

namespace Tests\Feature;

use App\User;
use App\Customer;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomersTest extends TestCase
{
use RefreshDatabase;


protected function setUp(): void 
    {
        parent::setUP();
        Event::fake();
    }



/** @test */
public function only_logged_in_users_can_see_customers_list()
    {
        $response = $this->get('/customers')->assertRedirect('/login');  
    }
/** @test */
public function authenticated_users_can_see_customers_list()
     {
         $this->actingAs(factory(User::class)->create());
         $response = $this->get('/customers')->assertOk();
     }
/** @test */
public function customer_can_be_added_via_form()
    { 
        $this->actingAsAdmin();
        $response = $this->post('/customers', $this->data());
        $this->assertCount(1, Customer::all());
    }

/** @test */
public function a_name_is_required()
    {
        $this->actingAsAdmin();
        $response = $this->post('/customers', array_merge($this->data(), ['name' =>'']));
        $response->assertSessionHasErrors('name');
        $this->assertCount(0, Customer::all());
    }

/** @test */
public function a_name_is_at_least_three_characters()
    {
        $this->actingAsAdmin();
        $response = $this->post('/customers', array_merge($this->data(), ['name' =>'a']));
        $response->assertSessionHasErrors('name');
        $this->assertCount(0, Customer::all());
    }

/** @test */
public function an_email_is_required()
    {
        $this->actingAsAdmin();
        $response = $this->post('/customers', array_merge($this->data(), ['email' =>'']));
        $response->assertSessionHasErrors('email');
        $this->assertCount(0, Customer::all());
    }

/** @test */
public function a_valid_email_is_required()
    {
        $this->actingAsAdmin();
        $response = $this->post('/customers', array_merge($this->data(), ['email' =>'invalidEmal']));
        $response->assertSessionHasErrors('email');
        $this->assertCount(0, Customer::all());
    }

private function data()
    {
        return [
            'name'=>'Test User',
            'email'=>'test@test.com',
            'active'=>1,
            'company_id' => 1,
        ];
    }

private function actingAsAdmin()
    {
        $this->actingAs(factory(User::class)->create([
        'email'=>'admin@admin.com',
        ]));
    }
}
