<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('api', function ($data, $status = true, $message = 'Requisição executado com sucesso.') use ($factory) {
            
            $customFormat = [
                'status' => $status,
                'data' => $data,
                'message' => $message
            ];
            return $factory->make($customFormat);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
