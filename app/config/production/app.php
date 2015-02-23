<?php

return array(
  'debug'  => true,
  'domain' => isset($_ENV['domain']) ? $_ENV['domain'] : '',
  'key'    => isset($_ENV['key']) ? $_ENV['key'] : '',
  'cipher' => isset($_ENV['cipher']) ? $_ENV['cipher'] : '',
);
