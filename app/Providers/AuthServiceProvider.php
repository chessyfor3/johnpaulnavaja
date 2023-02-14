<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->app['auth']->viaRequest('api', function ($request) {
            $auth = null;
            // With the request
            if ($request->api_key) {
              $auth = User::where('api_key', $request->api_key)->first();
            }

            // With the header
            if ($request->header('api-key')) {
              $user = User::where('api_key', $request->header('api-key'))->first();
              if(!empty($user)){
                $request->request->add(['userid' => $user->id]);
              }
              $auth = $user;
            }
            // Authorization header
            if ($request->header('Authorization')) {
              $key = explode(' ', $request->header('Authorization'));
              $user = User::where('api_key', $key[1])->first();
              if(!empty($user)){
                $request->request->add(['user_id' => $user->id]);
              }
              $auth = $user;
            }
            return $auth;
        });
    }
}
