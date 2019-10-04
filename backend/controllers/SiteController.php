<?php
namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use backend\models\EditProfile;
use common\models\Ticket;
use common\models\TicketSearch;
use common\models\UserSearch;

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
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'users', 'delete','view-profile', 'tickets', 'view-tickets', 'edit-profile', 'view-ticket'],
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
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
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
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Deletes the user with the given id. (Sets user.status to 0)
     *
     * @return \yii\web\Response
     */
    public function actionDelete()
    {
        if(!isset($_GET['u']) && Yii::$app->request->get()){
            $_GET['u'] = $_GET;
        }
        if (USER::findOne(Yii::$app->user->id)->admin) {
            if (User::deleteUser($_GET['u'])) {
                Yii::$app->session->setFlash('success', 'User successfully deleted.');
                return $this->redirect("/site/users");
            } else {
                Yii::$app->session->setFlash('error', 'There was a problem deleting the user.');
                Yii::error("Failed to delete user-".$_GET['u'], __METHOD__);
            }
        }
        return $this->goHome();
    }

    /**
     *
     */
    public function actionViewTickets(){
        if (USER::findIdentity(Yii::$app->user->getId())->admin && Yii::$app->request->get()) {
            $name = User::findOne($_GET['id']);
            if ($name != null) {
                $name = $name->username;
                if(isset($_GET['exact'])){
                    $name = $name."&exact=true";
                }
                $this->redirect("http://y2aa-backend.test/site/tickets?TicketSearch%5Bauthor.username%5D=" . $name);
            } else {
                Yii::$app->session->setFlash('error', 'There\'s no user with the given id!');
                $this->redirect("/site/tickets");
            }
        }
    }

    /**
     *
     */
    public function actionViewProfile(){
        if (USER::findIdentity(Yii::$app->user->getId())->admin && Yii::$app->request->get()) {
            $name = User::findOne($_GET);
            if ($name != null) {
                $name = $name->username;
                $this->redirect("http://y2aa-frontend.test/site/view-profile?uname=" . $name);
            } else {
                Yii::$app->session->setFlash('error', 'There\'s no user with the given id!');
                $this->redirect("/site/users");
            }
        }
    }

    /**
     *
     */
    public function actionEditProfile(){


        if (USER::findIdentity(Yii::$app->user->getId())->admin && Yii::$app->request->get()) {
        $name = User::findOne($_GET);
        if ($name != null) {
            $model = new EditProfile();
            $model->getData($name);
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->admin = Yii::$app->request->post('admin');
                if (!$model->saveProfile()) {
                    Yii::$app->session->setFlash('error', 'Can\'t edit the profile');
                } else {
                    Yii::$app->session->setFlash('success', 'Successfully edited the profile');
                }
            }
            return $this->render('editProfile', ['model' => $model]);
        } else {
            Yii::$app->session->setFlash('error', 'There\'s no user with the given id!');
            $this->redirect("/site/users");
        }
    }
    }



    /**
     * Displays all active and inactive users.
     *
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionUsers()
    {
        if (USER::findOne(Yii::$app->user->id)->admin) {

            $searchModel = new UserSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('users', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);

        } else {
            return $this->goHome();
        }
    }

    /**
     * Displays all tickets author, title, status, last comment time, assigned admin
     *
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionTickets()
    {
        if (USER::findIdentity(Yii::$app->user->getId())->admin) {
            $searchModel = new TicketSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('tickets', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
        }
        return $this->goHome();
    }

    public function actionViewTicket(){
        if (USER::findIdentity(Yii::$app->user->getId())->admin  && Yii::$app->request->get()) {

            $ticket = Ticket::findOne($_GET['id']);
            if ($ticket != null) {
                $this->redirect("http://y2aa-frontend.test/site/view-ticket?id=" . $ticket->id);
            } else {
                Yii::$app->session->setFlash('error', 'There\'s no user with the given id!');
                $this->redirect("/site/users");
            }

        }
    }
}
