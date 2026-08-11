<?php

namespace Tests\Feature;

use App\Mail\LeadSubmissionReceived;
use App\Models\LeadSubmission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Tests\TestCase;

class LeadSubmissionTest extends TestCase
{
    public function test_lead_form_and_captcha_are_available(): void
    {
        $this->get('/industry/digital-marketing-for-mobile-apps')
            ->assertOk()
            ->assertSee('action="'.route('lead-submissions.store').'"', false)
            ->assertSee('name="_token"', false);

        $this->getJson('/lead-form/captcha')
            ->assertOk()
            ->assertJsonStructure(['captcha', 'key']);

        $this->assertNotEmpty(session('lead_captchas'));
    }

    public function test_incorrect_captcha_is_rejected_before_database_storage(): void
    {
        $captchaKey = '9f4af790-3d3e-4f18-8494-f0e31efed789';
        session(['lead_captchas' => [$captchaKey => 'ABC123']]);

        $this->from('/industry/digital-marketing-for-mobile-apps')
            ->post('/lead-form', [
                'name' => 'Test User',
                'company_name' => 'Test Company',
                'city' => 'Kolkata',
                'phone' => '+91 9999999999',
                'email' => 'test@example.com',
                'captcha' => 'WRONG1',
                'captcha_key' => $captchaKey,
                'source_url' => url('/industry/digital-marketing-for-mobile-apps'),
                'website' => '',
            ])
            ->assertRedirect('/industry/digital-marketing-for-mobile-apps')
            ->assertSessionHasErrors('captcha');
    }

    public function test_footer_form_has_message_field_and_uses_the_same_endpoint(): void
    {
        $this->get('/partials/footer')
            ->assertOk()
            ->assertSee('data-lead-form', false)
            ->assertSee('name="message"', false)
            ->assertSee('action="'.route('lead-submissions.store').'"', false);
    }

    public function test_lead_email_is_synchronous_and_can_be_rendered(): void
    {
        $lead = new LeadSubmission([
            'name' => 'Test User',
            'company_name' => 'Test Company',
            'city' => 'Kolkata',
            'phone' => '+91 9999999999',
            'email' => 'test@example.com',
            'message' => 'Please contact me.',
            'source_url' => url('/about-us/digital-marketing'),
        ]);
        $lead->created_at = now();

        $mail = new LeadSubmissionReceived($lead);

        $this->assertNotInstanceOf(ShouldQueue::class, $mail);
        $this->assertStringContainsString('Please contact me.', $mail->render());
    }
}
