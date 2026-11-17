<?php

declare(strict_types=1);

/**
 * Central configuration for Wazzup → Bitrix24 lead routing.
 * Edit this file only — no .env required.
 */
return [

  'app' => [
    'debug' => false,
    'timezone' => 'Asia/Almaty',
  ],

  'wazzup' => [
    'api_key' => '94c320e61f7540929c2405e9d72e10fd',
    'webhook_secret' => 'bC7kM9pQ2xR5vN8wL4jT6hF',
    'public_webhook_url' => 'https://bitrix-mamyr-city.inv-x.kz/public/webhook.php',
    'allowed_channel_ids' => null,
    // Placeholders: {branch}, {phone}, {city}
    'branch_reply_template' => "Спасибо! Ваш филиал — {branch}.\n\nТелефон для связи:\n{phone}\n\nМы уже передали вашу заявку менеджеру.",
    'http' => [
      'connect_timeout' => 5.0,
      'timeout' => 20.0,
    ],
  ],

  'bitrix' => [
    'webhook' => 'https://mamyr.bitrix24.kz/rest/11/873dqimffg0b3khc/',

    'entity_type' => [
      'lead' => 1,
      'deal' => 2,
      'contact' => 3,
    ],

    'http' => [
      'connect_timeout' => 5.0,
      'timeout' => 20.0,
      'retry_attempts' => 3,
      'retry_delay_ms' => 500,
    ],
  ],

  /**
   * Lead statuses (crm.item stageId for leads = classic STATUS_ID).
   * Replace with real STATUS_ID from crm.status.list (ENTITY_ID=STATUS).
   */
  'lead' => [
    'city_field' => 'UF_CRM_1788293861695',

    'statuses' => [
      'unprocessed' => 'NEW',        // Не обработан
      'processed' => 'CONVERTED',    // Качественный лид
    ],
  ],

  /**
   * Branch routing by city text → deal funnel.
   * category_id / stage_id MUST belong to the same funnel.
   *
   * While lead is "Не обработан":
   * - message NOT in cities → ignore (keep waiting)
   * - message matches a branch → save city + create deal + mark processed
   */
  'branches' => [

    'almaty' => [
      'name' => 'Алматы',
      'phone' => '+7 775 964 7925',
      'cities' => [
        'алматы',
        'алмата',
        'алма-ата',
        'алма ата',
        'almaty',
        '1',
      ],
      'category_id' => 0,
      'stage_id' => 'NEW',
      'assigned_by_id' => null,
    ],

    'astana' => [
      'name' => 'Астана',
      'phone' => '+7 701 491 7433',
      'cities' => [
        'астана',
        'astana',
        'нур-султан',
        'нурсултан',
        'нур султан',
        '2',
      ],
      'category_id' => 1,
      'stage_id' => 'C1:NEW',
      'assigned_by_id' => null,
    ],

    'aktobe' => [
      'name' => 'Актобе',
      'phone' => '+7 701 950 0914',
      'cities' => [
        'актобе',
        'актюбинск',
        'aktobe',
        '3',
      ],
      'category_id' => 13,
      'stage_id' => 'C13:NEW',
      'assigned_by_id' => null,
    ],

    'aktau' => [
      'name' => 'Актау',
      'phone' => '+7 775 513 1131 (WhatsApp)',
      'cities' => [
        'актау',
        'aktau',
        '4',
      ],
      'category_id' => 19,
      'stage_id' => 'C19:NEW',
      'assigned_by_id' => null,
    ],

    'atyrau' => [
      'name' => 'Атырау',
      'phone' => '+7 701 980 3718',
      'cities' => [
        'атырау',
        'atyrau',
        '5',
      ],
      'category_id' => 21,
      'stage_id' => 'C21:NEW',
      'assigned_by_id' => null,
    ],

    'karaganda' => [
      'name' => 'Караганды',
      'phone' => '+7 701 950 0966',
      'cities' => [
        'караганды',
        'караганда',
        'karaganda',
        'karagandy',
        '6',
      ],
      'category_id' => 23,
      'stage_id' => 'C23:NEW',
      'assigned_by_id' => null,
    ],

    'kostanay' => [
      'name' => 'Костанай',
      'phone' => '+7 708 746 9711',
      'cities' => [
        'костанай',
        'kustanay',
        'kostanay',
        '7',
      ],
      'category_id' => 25,
      'stage_id' => 'C25:NEW',
      'assigned_by_id' => null,
    ],

    'shymkent' => [
      'name' => 'Шымкент',
      'phone' => '+7 707 113 0207',
      'cities' => [
        'шымкент',
        'чимкент',
        'shymkent',
        '8',
      ],
      'category_id' => 17,
      'stage_id' => 'C17:NEW',
      'assigned_by_id' => null,
    ],

    'uralsk' => [
      'name' => 'Уральск',
      'phone' => '+7 701 950 0965',
      'cities' => [
        'уральск',
        'орал',
        'uralsk',
        'oral',
        '9',
      ],
      'category_id' => 27,
      'stage_id' => 'C27:NEW',
      'assigned_by_id' => null,
    ],

    'ust_kamenogorsk' => [
      'name' => 'Усть-Каменогорск',
      'phone' => '+7 701 234 2531',
      'cities' => [
        'усть-каменогорск',
        'усть каменогорск',
        'устькаменогорск',
        'оскемен',
        'өскемен',
        'ust-kamenogorsk',
        'oskemen',
        '10',
      ],
      'category_id' => 29,
      'stage_id' => 'C29:NEW',
      'assigned_by_id' => null,
    ],

  ],

  'deal' => [
    // Placeholders: {branch}, {phone}, {city}, {lead_id}
    'title_template' => 'Заявка WhatsApp — {branch} — +{phone}',
  ],

  'security' => [
    'max_body_bytes' => 1048576,
    'allowed_methods' => ['POST'],
    'content_types' => [
      'application/json',
      'application/json; charset=utf-8',
      'application/json; charset-utf-8',
    ],
  ],

  'logging' => [
    'enabled' => true,
    'level' => 'info',
    'file' => __DIR__ . '/storage/logs/wazzup.log',
    'max_message_length' => 255,
  ],

];
