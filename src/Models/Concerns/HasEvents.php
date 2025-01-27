<?php

namespace Adldap\Models\Concerns;

use Adldap\Adldap;
use Adldap\Models\Events\Event;

trait HasEvents
{
    /**
     * Fires the specified model event.
     *
     * @param Event $event
     * @return array|null
     */
    protected function fireModelEvent(Event $event): ?array
    {
        return Adldap::getEventDispatcher()->fire($event);
    }
}
