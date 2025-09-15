<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'name' => $faker->sentence(2),
    'icon' => $faker->file(targetDirectory: __DIR__ . '/../../web/img'),
];