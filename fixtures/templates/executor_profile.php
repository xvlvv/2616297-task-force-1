<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$age = $faker->numberBetween(18, 65);
$ageString = Yii::t(
    'app',
    '{n, plural, one{# год} few{# года} many{# лет} other{# лет}}',
    ['n' => $age]
);
$bio = implode(', ', [
    $faker->country,
    $faker->city,
    $ageString
]);

return [
    'user_id' => $faker->numberBetween(1,10),
    'day_of_birth' => $faker->dateTimeInInterval(interval: '-18 years'),
    'description' => $faker->text(255),
    'bio' => $bio,
    'phone_number' => $faker->phoneNumber(),
    'telegram_username' => $faker->userName(),
    'restrict_contacts' => $faker->boolean(),
];