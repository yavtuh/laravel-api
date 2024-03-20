<?php

namespace App\Services\Lead;

use App\Models\Lead\Lead;
use App\Repositories\Lead\LeadRepository;
use App\Services\Lead\Contracts\LeadGenerateServiceContract;
use GuzzleHttp\Client;
use League\Csv\Reader;
use libphonenumber\PhoneNumberUtil;

class LeadGenerateService implements Contracts\LeadGenerateServiceContract
{
    protected LeadRepository $leadRepository;
    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function generateEmptyValue(string $field, Lead $lead)
    {
        switch ($field){
            case 'last_name':
                $lastName = $this->generateLastName($lead);
                $this->leadRepository->update(['last_name' => $lastName], $lead);
                return $lastName;
            case 'email':
                $email = $this->generateEmail($lead);
                $this->leadRepository->update(['email' => $email], $lead);
                return $email;
            case 'first_name':
                $firstName = $this->generateFirstName($lead);
                $this->leadRepository->update(['first_name' => $firstName], $lead);
                return $firstName;
            case 'user_agent':
                $userAgent = $this->generateUserAgent();
                $this->leadRepository->update(['user_agent' => $userAgent], $lead);
                return $userAgent;
            case 'country':
                $country = $this->generateCountry($lead);
                dd($country);
                $this->leadRepository->update(['country' => $country], $lead);
                return $country;
            case 'domain':
                $domain = $this->generateDomain();
                $this->leadRepository->update(['domain' => $domain], $lead);
                return $domain;
            case 'ip':
                $ip = $this->generateIp($this->generateCountry($lead));
                $this->leadRepository->update(['ip' => $ip], $lead);
                return $ip;
            default:

                return '';
        }
    }

    private function generateFirstName(Lead $lead): string
    {
        return $lead->last_name ? $lead->last_name : '';
    }

    private function generateLastName(Lead $lead): string
    {
        return $lead->first_name ? $lead->first_name : '';
    }

    private function generateEmail(Lead $lead): string
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'ts',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => 'y',  'ы' => 'y',   'ъ' => 'y',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'Ts',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => 'Y',  'Ы' => 'Y',   'Ъ' => 'Y',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        $randomNumber = rand(1, 999);
        if($lead->first_name){
            $translit = $lead->last_name ? strtr($lead->first_name . $lead->last_name, $converter) : strtr($lead->first_name, $converter);
            return preg_replace('/\s+/', '', strtolower($translit .$randomNumber. "@gmail.com"));
        }
        return preg_replace('/\s+/', '', strtolower($randomNumber . "@gmail.com"));
    }

    private function generateUserAgent(): string
    {
        return fake()->userAgent();
    }
    private function generateDomain(): string
    {
        return 'backcharghelp.online';
    }
    private function generateCountry(Lead $lead): string
    {
        if($lead->ip){
            return $this->getCountryByIp($lead->ip);
        }

        return $this->getCountryByPhone($lead->phone);
    }

    private function getCountryByIp($ip): string
    {
        $httpClient = new Client([
            'verify' => env('VERIFY_SSL', true)
        ]);
        $response = $httpClient->get("https://api.ipbase.com/v2/info?apikey=ipb_live_5qGmdQcSbx8P30uaBcKpKx58lOq3d9plukXlr7jh&ip={$ip}");
        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();
            $content = $body->getContents();
            $data = json_decode($content, true);
            if(!empty($data['data']['location'])){
                return $data['data']['location']['country']['alpha2'];
            }else{
                return 'RU';
            }
        }

        return 'RU';
    }

    private function getCountryByPhone($phone): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneUtil->parse($phone[0] !='+' ? '+' . $phone : $phone, null);
        $countryCode = $phoneNumber->getCountryCode();

        return $phoneUtil->getRegionCodeForCountryCode($countryCode);
    }

    private function generateIp($countryCode): string {
        $csv = Reader::createFromPath(storage_path('app/IP2LOCATION.csv'), 'r');

        foreach ($csv->getRecords() as $record) {
            if ($record[2] === $countryCode) {
                $startIp = long2ip($record[0]);
                $endIp = long2ip($record[1]);
                $start = ip2long($startIp);
                $end = ip2long($endIp);
                $randomLong = mt_rand($start, $end);

                return long2ip($randomLong);
            }
        }

        return '2.16.20.43';
    }


}
