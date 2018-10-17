<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Hashes\Repositories\HashRepository;
use App\Modules\Hashes\Repositories\EloquentHashRepository;
use App\Modules\Hashes\Models\HashCommit;

use App\Modules\Users\Repositories\UserRepository;
use App\Modules\Users\Repositories\EloquentUserRepository;
use App\Models\User;

use App\Modules\Issues\Repositories\IssueRepository;
use App\Modules\Issues\Repositories\EloquentIssueRepository;
use App\Modules\Issues\Models\Issue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepository::class, function () {
            return new EloquentUserRepository(new User);
        });

         $this->app->bind(HashRepository::class, function () {
            return new EloquentHashRepository(new HashCommit);
         });

        $this->app->bind(IssueRepository::class, function () {
            return new EloquentIssueRepository(new Issue);
        });
    }
}
