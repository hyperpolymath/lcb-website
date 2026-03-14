<?php
/**
 * SPDX-License-Identifier: GPL-2.0-or-later
 * SPDX-FileCopyrightText: 2026 Jonathan D.A. Jewell (hyperpolymath) <j.d.a.jewell@open.ac.uk>
 *
 * Plugin Name: PhpAegis Loader
 * Description: Loads the PhpAegis security library for use by themes and plugins.
 * Version: 1.0.0
 * Author: Jonathan D.A. Jewell
 *
 * Must-use plugin — automatically loaded by WordPress on every request.
 * The library itself lives in the active theme's vendor/ directory.
 */

declare(strict_types=1);

$aegis_autoload = get_template_directory() . '/vendor/php-aegis/autoload.php';

if ( file_exists( $aegis_autoload ) ) {
    require_once $aegis_autoload;
}
