<?php 
namespace app\models;

use yii\db\ActiveRecord;

class TodoItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'todo'; // The table name in your database
    }

    // Define the attributes for this model
    // public function rules()
    // {
    //     return [
    //         [['category', 'item'], 'required'], // Corrected 'category' to 'category_id'
    //         ['category_id', 'integer'], // category_id must be an integer
    //         ['name', 'string', 'max' => 255], // item must be a string with a max length of 255
    //         ['timestamp', 'safe'], // timestamp can be set as safe for date inputs, if you set it manually
    //     ];
    // }

    // Optional: Define labels for the attributes if needed
    // public function attributeLabels()
    // {
    //     return [
    //         'id' => 'ID',
    //         'category_id' => 'Category ID',
    //         'item' => 'Item',
    //         'timestamp' => 'Timestamp',
    //     ];
    // }
}
