<?php

namespace Tests\Feature\Newsletter;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PublicNewsletterEmailTest extends TestCase
{
    public function test_can_store_newsletter()
    {
        $response = $this->post('/api/v1/newsletter-emails', [
            'email'         => 'emailbaru1223@gmail.com',
        ]);

        $response->assertStatus(201);
    }

    public function test_email_unique_at_store_newsletter()
    {
        $response = $this->post('/api/v1/newsletter-emails', [
            'email'         => 'emailbaru1223@gmail.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_is_email_at_store_newsletter()
    {
        $response = $this->post('/api/v1/newsletter-emails', [
            'email'         => 'test123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_is_valid_domain_at_store_newsletter()
    {
        $response = $this->post('/api/v1/newsletter-emails', [
            'email'         => 'test123@babdhabijdija.com',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
