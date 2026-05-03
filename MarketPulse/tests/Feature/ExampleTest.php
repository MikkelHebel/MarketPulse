<?php

test('the root URL redirects to the dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});
