<?php

namespace Database\Factories\Lead;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sendStatusArray = ['processing', 'deferred', 'unknown', 'sent', null];
        $leadStatusArray = [null, 'success', 'error', 'duplicate'];
        return [
            'user_id'      => 2, // Подставьте существующие ID пользователей
            'crm_id'       => 3, // Подставьте существующие ID CRM
            'funnel_id'    => 1, // Подставьте существующие ID воронок
            'first_name'   => fake()->firstName,
            'last_name'    => fake()->lastName,
            'email'        => fake()->safeEmail,
            'phone'        => fake()->phoneNumber,
            'utm'          => fake()->word,
            'user_agent'   => fake()->userAgent,
            'ip'           => fake()->ipv4,
            'extra'        => fake()->text,
            'domain'       => fake()->domainName,
            'country'      => fake()->countryCode,
            'send_status'  => $sendStatusArray[rand(0, 4)],
            'lead_status'  => $leadStatusArray[rand(0, 3)],
            'response'     => json_encode(['status' => 'success']),
            'send_date'    => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
