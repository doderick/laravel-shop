<?php

return [
    'alipay' => [
        'app_id'         => '2016092100564457',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3GE693VhXlWkM6ia28EzQJDRknDl0R7wh013LeBlLuWxo/vw+mxW6gVXnUF99/e5vZHncPoemABYdW54Ihte+KRVOdvjCVfVvGdhzhptcAdenyTQzy9yWX+wxk/BQ7OSZVRyQASMceEoFa+NeOokJ/R1jt2eMFDLRcVHm1R+06HK53i0tMWcP+tanOMozTnjKRxY5clZOrMPv0IUVn8RiBjS4dpJ1pR4Ou4POSJ1QzVsUhyS39RVvIu6r8ESPWLyFfQ3JXjiA+P2UK5BeKuMdzPTMuhRxtOucOdRXiN/xN+t+ckWWBy7Up8vFYAeiXTQeDN+H7pPg3Qdn8w6dnRkkQIDAQAB
        sentinel',
        'private_key'    => 'MIIEowIBAAKCAQEA9ucjkV2qePsxMP4iS5LLjEzOk6PlftZN3V8/dlZi0F4C4q+H6HpqyD5gy2xKtw+Ce+KDQuOTDY1aUPsfWhCvylcXAuMPFYTVqZlMIN9SO9dFr1YYtjD+N1SCWaYn37wlfHPAd1O6cO9ajO3oOI+DmyoR61kVb0S2iqZDctSyuPPoCNxQXosmXhL637rbUK3Qva7W/Nhv4ODTNn5tdsaorN/3AHNFJCDtyyxuS0Mn+2U6jNwrXiO8wslREn9VyHmMww6XbpxTWO9o3i950v1Nv84MKw+GGrQhfKwzZG2KJc62Co3bjgDPZGQT+dcjsQVDcly3Y89/tTALO+ovipWe/QIDAQABAoIBAAaarifgQoQT6//sfExbM9if4VKvqp7W6qRGPPNUYjZCnX8kxNTiGWMb0AkA74qv/oSDzZEHd1KAem76GJ+XHKyj4bixDCE+OVzc/d1PO+rf2jxTaDvPWa1vNEFjoWNpq/MFQZuvgKgurtRgNLr2k8WdvXb3Hgyo4N0aj2lN6S3rQtFq9BGU7/7t4b7lTiClsEADH2pELR9c6RCSPbjmDpDRD1moFw9z9rwkV8iC9yweCeoVui4zJ5IWPN7VO3vNaGQ1hRbmBjU4Vtyw1S3Sgv3cZwmgDjL8Xc95afxw0IuSL49hjiA0BZkgDuH58pMRcXEfRXKlcrXf9Z/l5rHf/CECgYEA/Qok+Pe6pMu1LYJM6WztNHR3H7z5USbOAV/DBw31dguYcmWJoE9ZFJC1w93G5wkfmabVuCah/iASx22sfZhVtdnn5AETnkKHjStX6z6jQHZM6RtBbs0AEYxXE173R3B8+nEj9QaRx2F37PQNoUE6RVeX8cTt9hHEF1q3akj7YekCgYEA+cqdbA98A2qppFyLu5JKoQC6yw2x8DV39bFyxxB/fwZj+HPbX61Y+vJ07Jpa25XyYjs3VUpL5CGlT82y0Qb5UJ05ybXODwJ3CKxQo6iyI24aDQGgQvZKp4v5M5x920JIXg3JespL3aPB8nHgy3O7uv3lQS24AvJUONRFOv5Ts/UCgYEAj/8o4SGg7k0akoZXHHMnXbV1YPNacgsCKpgDVU1lRUL+AwOzWS4uhFyBg6/+k9WGTRs6/ivF/ebkqObJxUNeazlRFFhkgKhdmUF0K+QeZP2tcO5YwyQYCORzXQuq7tWd2atvCe4uIWdfJtIPu5dyAKoDRsuJm4GqtJukSvCzVZkCgYBrhmd7Y7L7eYeq49eWl6OeaMT/sVZ+U1XfIlKJFDX9xQ91nNG6/tDFvWbNjqg7y/E5jcoE4eWHP2B82Mv3pKvfor6EcMlRtrrstdhEitKb48I9BQ0qpgB+3QcZy2x13LNwITkwrTI5J0vOhGzCx3/xP2fXRcnnIs9UYzOl8GKxnQKBgEqqKAEp9uZjlu3JbbnkAPJ74Np/a1boR7bWuTVEXSnfMsKxdIH9+GGXenR9y209hBx80syrgXh22GuzRL13zZbO7Avnk8ymGCnDh+Q+PnKERJzVOV0j1hopbgQAYCZNpfhlZo9NzENCKo3taznOTCcbJ5aP1C9X1HYEX7lnHN8h',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],
    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];