<?php

//commands generate structure project
//php artisan make:auth
//php artisan migrate
//php artisan make:seeder <seeder name>
//php artisan migrate --seed
//php artisan make:model <model name>
//php artisan make:controller <controller name>
//php artisan make:request <form request name>
//php artisan make:provider <service provider name>

function debug($var)
{
  if(is_string($var)) {
    \Log::debug('<START DEBUG>');
   	\Log::debug($var);
   	\Log::debug('<END DEBUG>');
  } else {
    \Log::debug('<START DEBUG>');
    \Log::debug(var_export($var, true));
    \Log::debug('<END DEBUG>');
  }
}