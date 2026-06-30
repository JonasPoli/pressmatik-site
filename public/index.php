<?php

if (!function_exists('ignore_user_abort')) {
    function ignore_user_abort(?bool $enable = null): int {
        return 0;
    }
}

if (!function_exists('highlight_file')) {
    function highlight_file(string $filename, bool $return = false): string|bool {
        return $return ? '' : false;
    }
}

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return static function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

