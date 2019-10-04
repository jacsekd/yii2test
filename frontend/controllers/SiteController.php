<?php
namespace frontend\controllers;

use common\models\Comment;
use common\models\User;
use frontend\models\CreateTicketForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\EditProfileForm;
use frontend\models\ViewTicket;
use common\models\ViewProfile;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup','edit-profile', 'assign-admin', 'view-profile'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'edit-profile', 'assign-admin', 'view-profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays ticket creation page and creates a ticket
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionCreateTicket()
    {
        $model = new CreateTicketForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            if ($model->createTicket()) {
                Yii::$app->session->setFlash('success', 'Ticket successfully created.');
            } else {
                Yii::error("Can\'t create ticket-", __METHOD__);
                Yii::$app->session->setFlash('error', 'There was an error creating your ticket.');
            }
            return $this->goHome();

        } else {
            return $this->render('createTicket', [
                'model' => $model
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * Displays edit profile page and changes the data if possible
     *
     * @return string|\yii\web\Response
     */
    public function actionEditProfile()
    {
        $model = new EditProfileForm();
        $model->getData(Yii::$app->user->id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->edit()) {
                Yii::$app->session->setFlash('success', 'Your profile has successfully changed.');
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to edit your profile.');
            }

            return $this->refresh();
        }
        return $this->render('editProfile', [
            'model' => $model
        ]);
    }

    /**
     * Sends a password reset email to the current logged in user
     *
     * @return \yii\web\Response
     */
    public function actionSendPasswordEmail()
    {
        $model = new PasswordResetRequestForm();
        $model->email = User::findIdentity(Yii::$app->user->getId())->email;
        if ($model->sendEmail()) {
            Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
        } else {
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email.');
        }
        return $this->goHome();
    }

    /**
     * Creates a new comment
     *
     * @return \yii\web\Response
     */
    public function actionNewComment()
    {
        $model = new ViewTicket();
        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post()) && isset($_POST['id']) && isset($_POST['sid'])) {

            if ($model->addComment($_POST['sid'], $_POST['id'], Yii::$app->user->getId())) {
                Yii::$app->session->setFlash('success', 'Comment added.');
                return $this->redirect("view-ticket?id=".$_POST['id']);
            } else {
                Yii::error("Failed to add comment- ". $_POST['id'], __METHOD__);
                Yii::$app->session->setFlash('error', 'Can\'t add comment.');
            }

            return $this->goHome();
        }
    }

    /**
     * Displays a given ticket's page
     *
     * @param int $i
     * @return string|\yii\web\Response
     */
    public function actionViewTicket($i = 0)
    {
        $model = new ViewTicket();
        if (Yii::$app->request->get() && isset($_GET['id'])) {
            $i = $_GET['id'];
        }
        if ($model->setData($i)) {
            return $this->render('viewTicket', [
                'model' => $model
            ]);
        }
        Yii::$app->session->setFlash('error', 'There\'s no ticket with the given id.');
        return $this->goHome();

    }

    /**
     * Displays the current user's or the user with the given username's profile if authorized
     *
     * @return string|\yii\web\Response
     */
    public function actionViewProfile()
    {
        $model = new ViewProfile();
            if (!Yii::$app->user->isGuest && Yii::$app->request->get() && isset($_GET['uname'])) {
                if ($model->getData($_GET['uname'])) {
                    if ($_GET['uname'] == User::findOne(Yii::$app->user->id)->username || User::findOne(Yii::$app->user->id)->admin) {
                        return $this->render('viewProfile', [
                            'model' => $model
                        ]);
                    } else {
                        Yii::$app->session->setFlash('error', 'You are not authorized to view this.');
                        return $this->goHome();
                    }
                } else if (User::findOne(Yii::$app->user->id)->admin) {
                    Yii::$app->session->setFlash('error', 'No user with ' . $_GET['uname'] . ' username.');
                    return $this->goHome();
                } else {
                    Yii::$app->session->setFlash('error', 'You are not authorized to view this.');
                    return $this->goHome();
                }
            }
        Yii::$app->session->setFlash('error', 'You are not authorized to view this.');
        return $this->goHome();
    }

    /**
     * Assign the current logged in admin to the ticket
     *
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionAssignAdmin()
    {
        if (Yii::$app->request->get() && isset($_GET['id'])) {
            if (!Yii::$app->user->isGuest && User::findOne(Yii::$app->user->id)->admin) {
                if (ViewTicket::setAdmin($_GET['id'], Yii::$app->user->id)) {
                    Yii::$app->session->setFlash('success', 'You have successfully assigned yourself to this ticket.');
                    return $this->actionViewTicket($_GET['id']);
                } else {
                    Yii::$app->session->setFlash('error', 'There was a problem assigning yourself to this ticket.');
                    Yii::error("Failed to assign admin to ticket-".$_GET['id'], __METHOD__);
                }
            } else {
                return $this->goHome();
            }
        }
        return $this->goHome();
    }

    /**
     * Change the ticket's status
     *
     * @return \yii\web\Response
     */
    public function actionChangeTicket()
    {
        if (Yii::$app->request->get() && isset($_GET['tid'])) {
            if (isset ($_GET['st']) && ViewTicket::changeTicket($_GET['tid'], $_GET['st'])) {
                $status = $_GET['st'] == 1 ? 'open' : 'closed';
                Yii::$app->session->setFlash('success', 'You have successfully '.$status.' this ticket.');
            } else {
                Yii::$app->session->setFlash('error', 'There was a problem changing the ticket status.');
                Yii::error("Failed to change ticket-".$_GET['tid'], __METHOD__);
            }
            return $this->redirect("view-ticket?id=".$_GET['tid']);
        }
        return $this->goHome();
    }

    /**
     * Deletes a comment
     *
     * @return \yii\web\Response
     */
    public function actionDeleteComment()
    {
        if (isset($_GET['cid']) && !Yii::$app->user->isGuest) {
            $comment = Comment::findOne($_GET['cid']);
            if ($comment != null) {
                if (Yii::$app->user->id == $comment->author_id || User::findOne(Yii::$app->user->id)->admin) {
                    if ($comment->delete()) {
                        Yii::$app->session->setFlash('success', 'You have successfully deleted the comment.');
                    } else {
                        Yii::error("Failed to delete comment- ". $comment->id, __METHOD__);
                        Yii::$app->session->setFlash('error', 'There was a problem deleting the comment.');
                    }
                    return $this->redirect("view-ticket?id=".$_GET['tid']);
                }
            }
        }
        return $this->goHome();
    }

}
