<?php

return [
    // Points required to convert into 1 unit of wallet currency.
    // If null/empty, fallback uses Settings('gamification_reward_point_conversion_rate').
    'points_to_money_rate' => env('WALLET_POINTS_TO_MONEY_RATE', null),
];

