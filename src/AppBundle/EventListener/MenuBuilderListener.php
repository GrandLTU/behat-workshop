<?php

/*
 * @copyright C UAB NFQ Technologies
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 */

declare(strict_types=1);

namespace AppBundle\EventListener;

use Platform\Bundle\AdminBundle\Menu\MainMenuBuilder;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MenuBuilderListener.
 */
class MenuBuilderListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MainMenuBuilder::EVENT_NAME => 'onBuild',
        ];
    }

    /**
     * @param MenuBuilderEvent $event
     */
    public function onBuild(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->getChild('configuration')
            ->addChild('tasks', ['route' => 'app_task_index'])
            ->setLabel('Tasks')
            ->setLabelAttribute('icon', 'task');
    }
}
