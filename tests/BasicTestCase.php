<?php

namespace Tests;

use App\Services\User\UserService;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

abstract class BasicTestCase extends TestCase
{
    /**
     * token获取
     */
    public function getAuthHeader()
    {
        $login_data = [
            'username' => 'root',
            'password' => 'root'
        ];

        $response = $this->post('wx/auth/login', $login_data);
        $token    = $response->getOriginalContent()['data']['token'] ?? '';
        return ['Authorization' => "Bearer {$token}"];
    }

    /**
     * http
     */
    public function toHttp($uri, $method = 'get', $data = [], $token)
    {
        $http = new Client();
        if ($method == 'get') {
            if (!empty($data)) {
                $uri .= '?'.Arr::query($data);
            }
            $response = $http->get($uri,['Authorization' => "Bearer {$token}"]);
        } else {
            $response = $http->post($uri,
                [
                    'headers' => ['Authorization' => "Bearer {$token}"],
                    'json' => $data
                ]);
        }
        return $response->getBody()->getContents();
    }
}
