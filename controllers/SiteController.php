<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Category;
use app\models\TodoItem;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
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
     * @return string
     */
    public function actionIndex()
    {

    
        
       //$data = Category::find()->all();
   // echo '<pre>';print_r($data);'</pre>';exit;
        // return $this->render('index');
        return $this->render('home');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    public function actionHome()
    {
        
      
       $data = Category::find()->all();

    
     //  Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
       $todoItems = TodoItem::find()
           ->alias('todo')
           ->select(['todo.id', 'todo.name as item', 'todo.timestamp', 'c.name as category_name']) // Corrected field names and aliases
           ->leftJoin('category c', 'todo.category_id = c.id') // Added an alias for the category table
           ->asArray() // Fetch results as an array
           ->all();
   
    //    return [
    //        'status' => 'success',
    //        'data' => $todoItems,
    //    ];
   
   
      
        return $this->render('home',['category'=>$data,'todoItems'=>$todoItems]);
    }

  


    public function actionCreate()
    {
        $model = new TodoItem(); // Create a new instance of TodoItem model
    
        // Check if the form is submitted
        if (Yii::$app->request->isPost) {
            // Set the attributes for the model
            $model->category_id = Yii::$app->request->post('category'); // Assuming 'category' refers to category_id
            $model->name = Yii::$app->request->post('item');
            $model->timestamp = date('Y-m-d H:i:s'); // Set current timestamp
    
            // Validate and save the model
            if ($model->validate() && $model->save()) {
                // Redirect to the home page or another page after successful insertion
                return $this->asJson(['success' => true, 'message' => 'Todo item added successfully.']);
            } else {
                // Handle validation errors
                return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
            }
        }
    
      
    }
    public function actionHome2()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
        $todoItems = TodoItem::find()
    ->alias('todo')  // Alias for the 'todo' table
    ->select(['todo.id', 'todo.name AS item', 'todo.timestamp', 'c.name AS category_name']) // Corrected field names and aliases
    ->leftJoin('category c', 'todo.category_id = c.id') // Join with the 'category' table with alias 'c'
    ->asArray() // Fetch results as an array
    ->all();  // Retrieve all records

    



        return $this->asJson([
            'status' => 'success',
            'data' => $todoItems,
        ]);
    }
    

    public function actionDelete($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
        // Find the TodoItem by ID
        $todoItem = TodoItem::findOne($id);
        
        if ($todoItem !== null) {
            // Attempt to delete the Todo item
            if ($todoItem->delete()) {
                return $this->asJson([
                    'status' => 'success',
                    'message' => 'Todo item deleted successfully.',
                    'id' => $id,
                ]);
            } else {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'Failed to delete the todo item.',
                ]);
            }
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Todo item not found.',
            ]);
        }
    }
    
    
}
