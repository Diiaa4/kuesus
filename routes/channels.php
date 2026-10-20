<?php

use Illuminate\Support\Facades\Broadcast;

// Percakapan milik satu customer. Boleh dibuka oleh customer itu sendiri dan admin.
Broadcast::channel('chat.{customerId}', function ($user, $customerId) {
    return $user->isAdmin() || (int) $user->id === (int) $customerId;
});
