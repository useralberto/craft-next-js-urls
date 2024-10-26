<?php

namespace useralberto\craftnextjsurls;

use Craft;
use craft\base\Plugin as BasePlugin;

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

    public static function config(): array
    {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        $this->attachEventHandlers();

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function () {
            // ...
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/5.x/extend/events.html to get started)
    }
}
