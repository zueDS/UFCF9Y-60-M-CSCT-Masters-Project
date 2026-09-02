<?php
header('Content-Type: text/plain; charset=utf-8');
echo function_exists('curl_init') ? "curl enabled" : "curl NOT enabled";
