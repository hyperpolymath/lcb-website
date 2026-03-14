<?php
/**
 * SPDX-License-Identifier: MPL-2.0
 * SPDX-FileCopyrightText: 2024-2026 Jonathan D.A. Jewell (hyperpolymath) <j.d.a.jewell@open.ac.uk>
 *
 * PSR-4 compatible autoloader for PhpAegis in WordPress context.
 *
 * @package PhpAegis
 */

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'PhpAegis\\';
    $base_dir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
