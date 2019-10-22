<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
use App\Model\Company;
use App\Model\Job;
use Faker\Generator as Faker;

$factory->define(
    Job::class,
    function (Faker $faker) {
        return [
        'name' => $this->faker->catchPhrase,
        'category' => $this->faker->word,
        'detail' => $this->faker->text,
        'company_id' => function () {
            return factory(Company::class)->create()->id;
        },
        ];
    }
);
