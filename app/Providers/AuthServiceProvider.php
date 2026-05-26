<?php

namespace App\Providers;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\DirectConversation;
use App\Policies\ChatGroupPolicy;
use App\Policies\ChatMessagePolicy;
use App\Policies\DirectConversationPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ChatGroup::class => ChatGroupPolicy::class,
        ChatMessage::class => ChatMessagePolicy::class,
        DirectConversation::class => DirectConversationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
