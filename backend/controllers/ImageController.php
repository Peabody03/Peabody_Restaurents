<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\UploadedImage;
use common\services\ImageUploadService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ImageController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $search = trim((string) Yii::$app->request->get('q', ''));
        $query = UploadedImage::find();
        if ($search !== '') {
            $query->andWhere(['or', ['like', 'original_name', $search], ['like', 'filename', $search]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 24],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider, 'search' => $search]);
    }

    public function actionUpload(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $file = UploadedFile::getInstanceByName('image');
        if ($file === null) {
            return ['success' => false, 'error' => 'No file received.'];
        }

        $result = (new ImageUploadService())->upload($file, Yii::$app->user->id, 'gallery');
        if (!$result['success']) {
            return ['success' => false, 'error' => $result['error']];
        }

        $model = $result['model'];

        return [
            'success' => true,
            'id' => $model->id,
            'url' => $model->getPublicUrl(),
            'name' => $model->original_name,
            'size' => $model->getFormattedSize(),
        ];
    }

    public function actionDelete(int $id): Response
    {
        $model = UploadedImage::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Image not found.');
        }
        (new ImageUploadService())->delete($model);
        Yii::$app->session->setFlash('success', 'Image deleted successfully.');

        return $this->redirect(['index']);
    }
}
