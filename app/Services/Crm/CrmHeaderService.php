<?php

namespace App\Services\Crm;

use App\Models\Crm\CrmHeader;
use App\Services\Crm\Contracts\CrmHeaderServiceContract;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class CrmHeaderService implements Contracts\CrmHeaderServiceContract
{

    public function headers(Collection $headers): array
    {
        return $headers->mapWithKeys(function ($header) {
            if (is_null($header['header_value'])) {
                return [$header['header_name'] => $this->headerAuthToken($header)];
            }

            return [$header['header_name'] => cast_value($header['header_value'], $header['header_type'])];
        })->toArray();
    }

    public function headerAuthToken(CrmHeader $header): string
    {
        $config = $header->auth;
        $data = $config->fields->mapWithKeys(function ($field) {
            return [$field['header_name'] => cast_value($field['header_value'], $field['header_type'])];
        })->toArray();

        $client = new Client();
        $response = $client->request($config['method'], $config['base_url'], [
            'verify' => env('VERIFY_SSL', true),
            $config['content_type'] => $data
        ]);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $content = json_decode($response->getBody()->getContents(), true);
            $token = get_value_by_path($content, $config['token_path']);
            return is_null($config['auth_type']) ? $token : $config['auth_type'] . ' ' . $token;
        }
        return '';
    }
}
