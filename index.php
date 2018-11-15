<?php
    require_once 'vendor/autoload.php';
    require_once 'Configuration.php';

    ob_start();

    //$dbConfig promenjen u $databaseConfiguration
    $databaseConfiguration = new App\Core\DatabaseConfiguration(
        Configuration::DATABASE_HOST,
        Configuration::DATABASE_USER,
        Configuration::DATABASE_PASS,
        Configuration::DATABASE_NAME
    );
    // $dbCon menjamo u $databaseConnection
    $databaseConnection = new App\Core\DatabaseConnection($databaseConfiguration);

    $url = strval(filter_input(INPUT_GET, 'url'));
    $httpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

    $router = new \App\Core\Router();
    $routes = require_once 'routes.php';
    foreach ($routes as $route) {
        $router->add($route);
    }

    $route = $router->find($httpMethod, $url);
    $arguments = $route->extractArguments($url);

    //$controllerName     = $route->getControllerName();
    // controllerFullname -> $fullControllerName
    $fullControllerName = '\\App\\Controllers\\' . $route->getControllerName() .  'Controller';
    $controller         = new $fullControllerName($databaseConnection);


    $fingerprintProviderFactoryClass = Configuration::FINGERPRINT_PROVIDER_FACTORY;
    $fingerprintProviderFactoryMethod = Configuration::FINGERPRINT_PROVIDER_METHOD;
    $fingerprintProviderFactoryArgs = Configuration::FINGERPRINT_PROVIDER_ARGS;
    $fingerprintProviderFactory = new $fingerprintProviderFactoryClass;
    $fingerprintProvider = $fingerprintProviderFactory->$fingerprintProviderFactoryMethod(...$fingerprintProviderFactoryArgs);

    $sessionStorageClassName = Configuration::SESSION_STORAGE;
    $sessionStorageConstructorArguments = Configuration::SESSION_STORAGE_DATA;
    $sessionStorage = new $sessionStorageClassName(...$sessionStorageConstructorArguments);

    // Pravimo sesiju
    $session = new \App\Core\Session\Session($sessionStorage, Configuration::SESSION_LIFETIME);
    // Govorimo sesiji koji fingerprint provider da koristi pomocu Setera
    $session->setFingerprintProvider($fingerprintProvider);


    // Inicijalizujemo sesiju za kontroler, 
    // a potom u ItemController mozemo da 
    // dohvatimo tu sesiju i dodelimo joj 
    // neku vrednost uz pomoc getSession metode 
    // i put metode
    $controller->setSession($session);
    // Dohvati sve ranije sesije ako postoje
    $controller->getSession()->reload();
    $controller->__pre();
    call_user_func_array([$controller, $route->getMethodName()], $arguments);
    // Osiguraj da ce se sesije sacuvati u sessions folder
    $controller->getSession()->save();


    $data = $controller->getData();


    if ($controller instanceof \App\Core\ApiController) {
		ob_clean();
		header('Content-type: application/json; charset=utf-8');
		header('Access-Control-Allow-Origin: *');
		echo json_encode($data);
		exit;
	}

    $loader = new Twig_Loader_Filesystem("./views");
    $twig = new Twig_Environment($loader, [
        "cache"       => "./twig-cache",
        "auto_reload" => true
    ]);

    $data['BASE'] = Configuration::BASE;

    echo $twig->render($route->getControllerName() . '/' .  $route->getMethodName() . '.html', $data);
