<?php
echo "OPcache enabled: " . (function_exists('opcache_get_status') && opcache_get_status() ? 'Yes' : 'No') . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
