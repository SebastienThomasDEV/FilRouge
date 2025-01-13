<?php
const ROUTES = [
    "/{id}" => [
        "CONTROLLER" => "HomeController",
        "METHOD" => "index",
        "HTTP_METHODS" => "GET",
    ],
    "/users/{id}" => [
        "CONTROLLER" => "HomeController",
        "METHOD" => "show",
        "HTTP_METHODS" => "GET",
    ],
];
