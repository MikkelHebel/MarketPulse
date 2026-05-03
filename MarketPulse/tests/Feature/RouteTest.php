<?php

use App\Models\User;

test('guest can view login page', function () {
    $this->get(route('login'))->assertOk();
});

test('guest can view register page', function () {
    $this->get(route('register'))->assertOk();
});

test('unauthenticated user is redirected to login when accessing thresholds', function () {
    $this->get(route('thresholds'))->assertRedirect(route('login'));
});

test('unauthenticated user is redirected to login when accessing the search page', function () {
    $this->get(route('search'))->assertRedirect(route('login'));
});

test('unauthenticated user is redirected to login when polling notifications', function () {
    $this->get('/notifications/poll')->assertRedirect(route('login'));
});

test('authenticated user can access the thresholds page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('thresholds'))->assertOk();
});

test('authenticated user can access the search page with a valid ticker', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('search', ['ticker' => 'AAPL']))->assertOk();
});
