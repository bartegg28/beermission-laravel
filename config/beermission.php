<?php

declare(strict_types=1);

return [

    /**
     * Name of the table in which the grants will be stored.
     */
    'grants_table_name' => 'beermission_grants',

    /**
     * Maximum length of the bearer id (for the most cases it is the same
     * as the user id, assuming, the user id is the uuid)
     */
    'bearer_id_length' => 32,

    /**
     * Defines the strategy that should be used for the grant synchronization.
     * The default value is \Yxvt\BeermissionLaravel\GrantSyncStrategy\ReinsertSyncStrategy::class.
     */
    'grant_sync_strategy' => \Yxvt\BeermissionLaravel\GrantSyncStrategy\ReinsertSyncStrategy::class,
];
