<?php

const ROUTES = [
    "/" => [
        "CONTROLLER" => "HomeController",
        "METHOD" => "index",
        "HTTP_METHODS" => "GET",
    ],
    "/users/{id}" => [
        "CONTROLLER" => "HomeController",
        "METHOD" => "show",
        "HTTP_METHODS" => "GET",
    ],

    "/users" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "index",
        "HTTP_METHODS" => "GET",
    ],
    "/api/users" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "getApiUsers",
        "HTTP_METHODS" => "GET",
    ],
    "/api/delete/user/{userId}" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "deleteApiUser",
        "HTTP_METHODS" => "DELETE",
    ],
    "/api/add/user" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "addApiUser",
        "HTTP_METHODS" => "POST",
    ],
    "/api/edit/user/{userId}" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "editApiUser",
        "HTTP_METHODS" => "PATCH",
    ],
    "/api/user/{userId}" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "getApiUser",
        "HTTP_METHODS" => "GET",
    ],

];
