<?php

return array(
  'debug'  => false,
  'domain' => isset($_ENV['domain']) ? $_ENV['domain'] : '',
  'key'    => isset($_ENV['key']) ? $_ENV['key'] : 'QPBAWpOWQndXrkMeYMDYL6plMk2oLaPJ',
  'cipher' => isset($_ENV['cipher']) ? $_ENV['cipher'] : MCRYPT_RIJNDAEL_128,
);
