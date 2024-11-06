<?php 


namespace app\models;



class Category extends \yii\db\ActiveRecord{
    public static function tableName()
    {
        return 'category'; // The table name in your database
    }
}
