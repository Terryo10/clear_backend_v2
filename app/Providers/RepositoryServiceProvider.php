<?php

namespace App\Providers;

use App\Interfaces\Chat\ChatRepo;
use App\Interfaces\Chat\ChatRepoInterface;
use App\Interfaces\Images\ImageRepo;
use App\Interfaces\Images\ImageRepoInterface;
use App\Interfaces\Notifications\NotificationRepo;
use App\Interfaces\Notifications\NotificationRepoInterface;
use App\Interfaces\Offers\OfferRepo;
use App\Interfaces\Offers\OfferRepoInterface;
use App\Interfaces\Project\ProjectRepo;
use App\Interfaces\Project\ProjectRepoInterface;
use App\Interfaces\Proposals\RequestProposalRepo;
use App\Interfaces\Proposals\RequestProposalRepoInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ImageRepoInterface::class, ImageRepo::class);
        $this->app->bind(ProjectRepoInterface::class, ProjectRepo::class);
        $this->app->bind(ChatRepoInterface::class, ChatRepo::class);
        $this->app->bind(NotificationRepoInterface::class, NotificationRepo::class);
        $this->app->bind(RequestProposalRepoInterface::class, RequestProposalRepo::class);
        $this->app->bind(OfferRepoInterface::class, OfferRepo::class);
    }


    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

}
