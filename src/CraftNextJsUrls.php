<?php

namespace useralberto\craftnextjsurls;

use Craft;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\elements\Category;
use yii\base\Event;

use craft\base\Plugin as BasePlugin;
use useralberto\craftnextjsurls\services\RoutersService;


/**
 * NextJs Urls plugin
 *
 * @method static Plugin getInstance()
 * @author useralberto <lalonso.dev@gmail.com>
 * @copyright useralberto
 * @license MIT
 */

class CraftNextJsUrls extends BasePlugin
{
    public string $schemaVersion = '1.0.0';
    public static $plugin;

    public static function config(): array
    {
        return [
            'components' => [
                'routeService' => RoutersService::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        $this->attachEventHandlers();

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function () {
            Craft::info('RouteUpdater plugin cargado', __METHOD__);
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/5.x/extend/events.html to get started)

        Event::on(Entry::class, Entry::EVENT_AFTER_SAVE, function ($event) {
            $this->routeService->checkIfRouteChanged($event);
        });

        Event::on(Category::class, Category::EVENT_AFTER_SAVE, function ($event) {
            $this->routeService->checkIfRouteChanged($event);
        });


        Event::on(Entry::class, Entry::EVENT_AFTER_DELETE, function ($event) {
            // Asegúrate de que el evento sea del tipo correcto
            $this->routeService->handleRouteDeletion($event);
        });

        // Escuchar el evento de eliminación para las categorías
        Event::on(Category::class, Category::EVENT_AFTER_DELETE, function ($event) {
            // Asegúrate de que el evento sea del tipo correcto
            $this->routeService->handleRouteDeletion($event);
        });
    }
}
