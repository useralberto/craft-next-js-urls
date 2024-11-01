<?php

namespace useralberto\craftnextjsurls\services;

use Craft;
use craft\helpers\App;
use craft\base\Component;

use useralberto\craftnextjsurls\records\RoutersRecord;
use useralberto\craftnextjsurls\models\RoutersModel;
use useralberto\craftnextjsurls\jobs\UpdateRoutesJob;
use useralberto\craftnextjsurls\helpers\StringHelper;


class RoutersService extends Component
{

    public function saveData(RoutersModel $model)
    {
        if ($model->validate()) {
            $record = RoutersRecord::findOne([
                'elementId' => $model->id,
                'siteId' => $model->siteId,
            ]) ?? new RoutersRecord();

            $record->elementId = $model->id;
            $record->siteId = $model->siteId;
            $record->newUri = $model->newUri;

            if ($record->save()) {
                Craft::$app->getSession()->setFlash("Rutas actualizadas con éxito'");
                return true;
            } else {
                $model->addErrors($record->getErrors());

                $helper = new StringHelper();
                $StringErrors = $helper->flattenArrayValues($model->getErrors());
                Craft::$app->getSession()->setError($StringErrors);
            }
        }
        return false;
    }
    public function checkIfRouteChanged($event)
    {
        $element = $event->sender;
        $siteId = $element->siteId;
        $newUri = $element->uri;
        $id = $element->canonicalId;

        $routeRecord = RoutersRecord::findOne(['elementId' => $id, 'siteId' => $siteId]);
        if ($routeRecord) {
            if ($routeRecord->newUri !== $newUri) {
                $oldUrl = $routeRecord->newUri;
                $routeRecord->oldUri = $oldUrl;
                $routeRecord->newUri = $newUri;
                $routeRecord->save();
                $this->queueUpdateRoutes($element);
            }
        } else {
            $routeRecord = new RoutersRecord();
            $routeRecord->elementId = $id;
            $routeRecord->siteId = $siteId;
            $routeRecord->newUri = $newUri;
            $routeRecord->save();
            $this->queueUpdateRoutes($element);
        }
    }

    protected function queueUpdateRoutes($element)
    {
        Craft::$app->queue->push(new UpdateRoutesJob());
    }

    public function handleRouteDeletion($event)
    {
        $element = $event->sender;
        $slug = '/' . $element->uri;
        $id = $element->canonicalId;
        $this->removeRouteFromJson($slug, $id);
    }

    private function removeRouteFromJson($slug, $id)
    {
        $filePath = (App::env("JSON_PATH") ?? Craft::getAlias('@webroot')) . '/routes.json';

        if (file_exists($filePath)) {
            $existingRoutes = json_decode(file_get_contents($filePath), true);
            if (isset($existingRoutes[$slug])) {
                unset($existingRoutes[$slug]);
                file_put_contents($filePath, json_encode($existingRoutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                $deletedRows = RoutersRecord::deleteAll(['elementId' => $id]);
                if ($deletedRows > 0) {
                    Craft::info("Se eliminaron $deletedRows registros con elementId $id.", __METHOD__);
                } else {
                    Craft::info("No se encontraron registros con elementId $id para eliminar.", __METHOD__);
                }
            }
        }
    }
}
