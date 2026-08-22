<?php

/**
 * cPanel fallback front controller.
 * If Apache document root is this folder (not /public), this file boots Laravel.
 * Prefer pointing the domain document root to the public/ directory when possible.
 */
require __DIR__.'/public/index.php';
