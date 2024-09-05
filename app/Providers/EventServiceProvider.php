<?php

namespace App\Providers;

use App\Observers\AdminActionObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        \App\Models\Location::class             => [AdminActionObserver::class],
        \App\Models\Harbor::class               => [AdminActionObserver::class],

        \App\Models\Tag::class                  => [AdminActionObserver::class],
        \App\Models\PivotActivityTag::class     => [AdminActionObserver::class],
        \App\Models\PivotPrivateBoatTag::class  => [AdminActionObserver::class],

        \App\Models\Activity::class             => [AdminActionObserver::class],
        \App\Models\ActivityImage::class        => [AdminActionObserver::class],
        \App\Models\ActivityTranslation::class  => [AdminActionObserver::class],
        \App\Models\ActivityVariant::class      => [AdminActionObserver::class],

        \App\Models\PrivateBoat::class              => [AdminActionObserver::class],
        \App\Models\PrivateBoatImage::class         => [AdminActionObserver::class],
        \App\Models\PrivateBoatSpecification::class => [AdminActionObserver::class],
        \App\Models\PrivateBoatTranslation::class   => [AdminActionObserver::class],
        \App\Models\PrivateBoatVariant::class       => [AdminActionObserver::class],
        
        \App\Models\FastBoat::class                 => [AdminActionObserver::class],
        \App\Models\FastBoatSchedule::class         => [AdminActionObserver::class],
        \App\Models\FastBoatScheduleVariant::class  => [AdminActionObserver::class],

        \App\Models\ProductInclude::class           => [AdminActionObserver::class],
        \App\Models\PivotPrivateBoatInclude::class  => [AdminActionObserver::class],
        \App\Models\PivotActivityInclude::class     => [AdminActionObserver::class],

        \App\Models\ProductExclude::class           => [AdminActionObserver::class],
        \App\Models\PivotActivityExclude::class     => [AdminActionObserver::class],
        
        \App\Models\ProductItinerary::class         => [AdminActionObserver::class],
        \App\Models\PivotActivityItinerary::class   => [AdminActionObserver::class],

        \App\Models\Faq::class                      => [AdminActionObserver::class],
        \App\Models\FaqTranslation::class           => [AdminActionObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
