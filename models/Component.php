<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "component".
 *
 * @property int $id
 * @property string $component
 * @property string|null $description
 *
 * @property Bateria[] $baterias
 * @property Mppt[] $mppts
 * @property Painel[] $painels
 */
class Component extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'component';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['component'], 'required'],
            [['component', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'component' => 'Componente',
            'description' => 'Descrição',
        ];
    }

    /**
     * Gets query for [[Baterias]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\BateriaQuery
     */
    public function getBaterias()
    {
        return $this->hasMany(Bateria::class, ['component_id' => 'id']);
    }

    /**
     * Gets query for [[Mppts]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\MpptQuery
     */
    public function getMppts()
    {
        return $this->hasMany(Mppt::class, ['component_id' => 'id']);
    }

    /**
     * Gets query for [[Painels]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\PainelQuery
     */
    public function getPainels()
    {
        return $this->hasMany(Painel::class, ['component_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\ComponentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\ComponentQuery(get_called_class());
    }
}
