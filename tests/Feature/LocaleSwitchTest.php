<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    public function test_locale_switch_route_updates_session_and_redirects_back(): void
    {
        $response = $this->get(route('locale.switch', [
            'locale' => 'en',
            'redirect' => route('home'),
        ]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('locale', 'en');
    }
}
