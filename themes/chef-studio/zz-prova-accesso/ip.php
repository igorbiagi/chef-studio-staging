<?php
// Prova (staging): mostra l'IP con cui il server vede la richiesta. Da cancellare dopo le prove.
header('Content-Type: text/plain; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
if (($_SERVER['HTTP_HOST'] ?? '') !== 'staging.chefstudio.it') { http_response_code(404); exit; }
echo 'REMOTE_ADDR=' . preg_replace('/[^0-9a-f:.]/i', '', $_SERVER['REMOTE_ADDR'] ?? '') . "\n";
echo 'CF=' . (isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? 'si' : 'no') . "\n";
