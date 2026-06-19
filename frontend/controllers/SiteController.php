<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\LoginForm;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\PasswordResetVerifyForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\mail\MailerInterface;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    ['actions' => ['signup'], 'allow' => true, 'roles' => ['?']],
                    ['actions' => ['logout'], 'allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'resend-password-reset' => ['post'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return ['error' => ['class' => ErrorAction::class]];
    }

    public function actionIndex(): Response
    {
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            if ($user->isAdmin()) {
                return $this->redirect('/restaurants/backend/web/index.php');
            }

            return $this->redirect(['dashboard/index']);
        }

        return $this->redirect(['menu/index']);
    }

    public function actionLogin(): string|Response
    {
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            if ($user->isAdmin()) {
                return $this->redirect('/restaurants/backend/web/index.php');
            }

            return $this->redirect(['dashboard/index']);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'Welcome back!');

            return $this->redirect(['dashboard/index']);
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('success', 'You have been logged out.');

        return $this->goHome();
    }

    public function actionContact(): string|Response
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['adminEmail'],
                Yii::$app->params['senderEmail'],
                Yii::$app->params['senderName'],
            );

            Yii::$app->session->setFlash($sent ? 'success' : 'error', $sent
                ? 'Thank you for contacting us.'
                : 'There was an error sending your message.');

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionSignup(): string|Response
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['menu/index']);
        }

        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();

            if ($user !== null) {
                Yii::$app->user->login($user, 3600 * 24 * 30);
                Yii::$app->session->setFlash('success', 'Account created! You are now signed in.');

                return $this->redirect(['dashboard/index']);
            }
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionRequestPasswordReset(): string|Response
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->sendReset(
            $this->mailer,
            Yii::$app->params['supportEmail'],
            Yii::$app->name,
        )) {
            Yii::$app->session->setFlash(
                'success',
                'Reset instructions sent! Check your email for a reset link and code. If you used your phone number, an SMS was also sent.',
            );

            return $this->redirect(['site/verify-password-reset']);
        }

        return $this->render('requestPasswordResetToken', ['model' => $model]);
    }

    public function actionVerifyPasswordReset(): string|Response
    {
        $model = new PasswordResetVerifyForm();
        $model->identifier = (string) Yii::$app->session->get('passwordResetIdentifier', '');

        if ($model->load(Yii::$app->request->post()) && $model->verify()) {
            Yii::$app->session->setFlash('success', 'Code verified! Set your new password below.');

            return $this->redirect(['site/reset-password']);
        }

        return $this->render('verify-password-reset', ['model' => $model]);
    }

    public function actionResendPasswordReset(): Response
    {
        $identifier = Yii::$app->request->post('identifier')
            ?: (string) Yii::$app->session->get('passwordResetIdentifier', '');

        $model = new PasswordResetRequestForm(['identifier' => $identifier]);

        if ($model->sendReset($this->mailer, Yii::$app->params['supportEmail'], Yii::$app->name)) {
            Yii::$app->session->setFlash('success', 'A new reset link and code have been sent.');
        } else {
            Yii::$app->session->setFlash('error', 'Unable to resend. Please check your email or phone number.');
        }

        return $this->redirect(['site/verify-password-reset']);
    }

    public function actionResetPassword(string $token = ''): string|Response
    {
        try {
            $model = $token !== '' ? new ResetPasswordForm($token) : new ResetPasswordForm();
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if (!$model->isAuthorized()) {
            throw new BadRequestHttpException('Your reset session has expired. Please start again.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Password updated! You can now sign in with your new password.');

            return $this->redirect(['site/login']);
        }

        return $this->render('resetPassword', ['model' => $model]);
    }
}
