<?php

use theorx\Structure\S;

require __DIR__ . '/vendor/autoload.php';

$start = microtime(true);


$structure = [
    "name"     => S::string()->description("Test name")->add("test", "a"),
    "age"      => S::integer()->description("Test age"),
    "weight"   => S::float()->description("Person's weight"),
    "active"   => S::boolean()->required()->description("Status"),
    "numbers"  => S::array()->required(),
    "children" => S::list(
        [
            "name" => S::string(),
            "test" => S::string()->required(),
            "age"  => S::integer()->required()->description("Children's age"),
            "a"    => S::list(
                [
                    "name" => S::string()->required()
                ]
            )
        ]
    )->description("Person's children")
];


$object = S::create($structure);

$object->populate(
    [
        "name"     => "test",
        "children" => [
            ["name" => "first"],
            ["name" => "second"],
            [
                "age" => "second"
            ],
        ],


    ]
);


$end = microtime(true);

print_r($object->value());
print_r($object->validate());

echo "Time elapsed: " . ($end - $start) . PHP_EOL;