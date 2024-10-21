<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buni</title>
    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="assets/js/global.js"></script>
    <script src="https://kit.fontawesome.com/ec615181f1.js" crossorigin="anonymous"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($_SESSION['messageToToast'])){
        echo "<script>
        Toast.fire({
            icon: '{$_SESSION['messageToToast']['icon']}',
            title: '{$_SESSION['messageToToast']['text']}'
        });
        </script>
        ";

        $_SESSION['messageToToast'] = null;
    }

    use Buni\Views\View;

    $view = new View($_SERVER["REQUEST_URI"]);

    $root = __DIR__ . "/modules";
    $viewUrl = $view->getViewURL();

    require $root . $viewUrl;
    ?>
</body>

</html>