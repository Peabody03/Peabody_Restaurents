<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\Food;
use common\models\UploadedImage;
use common\services\ImageUploadService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class FoodController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $query = Food::find();
        $menuType = Yii::$app->request->get('menu_type');
        if ($menuType) {
            $query->andWhere(['menu_type' => $menuType]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['menu_type' => SORT_ASC, 'food_name' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'menuTypes' => Food::menuTypes(),
        ]);
    }

    public function actionCreate(): string|Response
    {
        $model = new Food();
        $model->is_available = true;

        if ($model->load(Yii::$app->request->post()) && $this->saveWithImage($model)) {
            Yii::$app->session->setFlash('success', 'Food item created successfully.');

            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model' => $model,
            'galleryImages' => $this->getGalleryImages(),
        ]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $this->saveWithImage($model)) {
            Yii::$app->session->setFlash('success', 'Food item updated successfully.');

            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model' => $model,
            'galleryImages' => $this->getGalleryImages(),
        ]);
    }

    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Food item deleted.');

        return $this->redirect(['index']);
    }

    private function saveWithImage(Food $model): bool
    {
        if (Yii::$app->request->post('removeImage') === '1') {
            $model->image = null;
        }

        $existingPath = trim((string) Yii::$app->request->post('existingImagePath', ''));
        if ($existingPath !== '' && $this->isValidUploadPath($existingPath)) {
            $model->image = $existingPath;
        }

        $file = UploadedFile::getInstanceByName('imageFile');
        if ($file !== null && $file->name !== '') {
            if ($model->menu_type === null || $model->menu_type === '') {
                $model->addError('menu_type', 'Select a menu category before uploading a photo.');

                return false;
            }

            $result = (new ImageUploadService())->upload($file, (int) Yii::$app->user->id, 'foods/' . $model->menu_type);
            if ($result['success']) {
                $model->image = $result['model']->path;
            } else {
                $model->addError('image', $result['error'] ?? 'Upload failed.');

                return false;
            }
        }

        return $model->save();
    }

    private function isValidUploadPath(string $path): bool
    {
        $path = ltrim($path, '/');
        if (str_contains($path, '..')) {
            return false;
        }

        $absolute = Yii::getAlias('@frontend/web/uploads/' . $path);

        return is_file($absolute);
    }

    /**
     * @return UploadedImage[]
     */
    private function getGalleryImages(): array
    {
        return UploadedImage::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(24)
            ->all();
    }

    private function findModel(int $id): Food
    {
        $model = Food::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Food not found.');
        }

        return $model;
    }
}
