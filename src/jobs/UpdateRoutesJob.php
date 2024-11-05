<?php

namespace useralberto\craftnextjsurls\jobs;

use Craft;
use craft\queue\BaseJob;
use craft\elements\Entry;
use craft\elements\Category;
use craft\helpers\App;


class UpdateRoutesJob extends BaseJob
{
    public function execute($queue): void
    {
        $filePath = (App::env("JSON_PATH") ?? Craft::getAlias('@webroot')) . 'routes.json';
        $existingRoutes = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
        $updatedRoutes = $existingRoutes;

        $defaultSite = Craft::$app->sites->getPrimarySite();
        $sites = Craft::$app->sites->getAllSites();

        $entries = Entry::find()->uri(':notempty:')->status('live')->with(['site'])->all();
        $categories = Category::find()->uri(':notempty:')->status('live')->with(['site'])->all();

        $this->processElements($entries, $updatedRoutes, $sites);
        $this->processElements($categories, $updatedRoutes, $sites);

        file_put_contents($filePath, json_encode($updatedRoutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        Craft::info('Rutas actualizadas con éxito', __METHOD__);
    }


    private function processElements($elements, &$updatedRoutes, $sites)
    {
        foreach ($elements as $element) {
            $defaultSlug = '/' . $element->uri;

            if ($defaultSlug === '/__home__') {
                continue;
            }

            foreach ($sites as $site) {
                $localizedElement = $element->getLocalized()->siteId($site->id)->one();

                if ($localizedElement && $localizedElement->uri) {
                    $siteHandle = $site->handle;
                    $localizedUri = '/' . $localizedElement->uri;

                    if (!isset($updatedRoutes[$defaultSlug])) {
                        $updatedRoutes[$defaultSlug] = [];
                    }
                    $updatedRoutes[$defaultSlug][$siteHandle] = $localizedUri;
                }
            }
        }
    }

    protected function defaultDescription(): string
    {
        return Craft::t('app', 'Generando rutas para Next.js');
    }
}
