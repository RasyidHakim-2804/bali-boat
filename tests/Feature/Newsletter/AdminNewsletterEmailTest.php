<?php

namespace Tests\Feature\Article;

use App\Models\Admin;
use App\Models\NewsletterEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminNewsletterEmailTest extends TestCase
{
    public function test_admin_get_list_newsletter_email()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/newsletter-emails');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_users_id_parameter_admin_delete_newsletter_email()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/newsletter-emails/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_newsletter_email()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $newsletterEmail = NewsletterEmail::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/newsletter-emails/'. $newsletterEmail->id);

        $response->assertStatus(201);
    }
}
