<?php

chdir('web');
require_once 'autoload.php';

$kernel = \Drupal::service('kernel');
$kernel->boot();

try {
    $controller = \Drupal::service('controller_resolver')->getController(
        \Symfony\Component\HttpFoundation\Request::create('/api/info')
    );
    
    if ($controller) {
        echo "Controller found!\n";
        $response = call_user_func($controller);
        echo "Response Type: " . get_class($response) . "\n";
        echo "Content: " . $response->getContent() . "\n";
    } else {
        echo "Controller NOT found\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

