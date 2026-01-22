<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

/**
 * Point d'entrée de l'application
 * Toutes les requêtes HTTP passent par ce fichier
 */
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
