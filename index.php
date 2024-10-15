<?php
    $request = $_SERVER['REQUEST_URI'];

    $viewDir = '/views/';

    

    switch ($request) {
        case '':
        case '/':
            require __DIR__ . $viewDir . 'home.php';
            break;

        case '/views/users':
            require __DIR__ . $viewDir . 'users.php';
            break;

        case '/contact':
            require __DIR__ . $viewDir . 'contact.php';
            break;

        default:
            require __DIR__ . $viewDir . '404.php';
    }
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buni</title>
</head>

<body>
</body>

</html>