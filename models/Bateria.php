<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bateria".
 *
 * @property int $id
 * @property int $component_id
 * @property string $modelo
 * @property string $fabricante
 * @property float $h_m
 * @property float $w_m
 * @property float $d_m
 * @property float $cbi_c20_bat_ah
 * @property float $voc_bat_v
 *
 * @property Component $component
 * @property Dimensionamento[] $dimensionamentos
 */
class Bateria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bateria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['component_id', 'modelo', 'fabricante', 'h_m', 'w_m', 'd_m', 'cbi_c20_bat_ah', 'voc_bat_v'], 'required'],
            [['component_id'], 'integer'],
            [['modelo', 'fabricante'], 'string'],
            [['h_m', 'w_m', 'd_m', 'cbi_c20_bat_ah', 'voc_bat_v'], 'number'],
            [['component_id'], 'exist', 'skipOnError' => true, 'targetClass' => Component::class, 'targetAttribute' => ['component_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'component_id' => 'Component ID',
            'modelo' => 'Modelo',
            'fabricante' => 'Fabricante',
            'h_m' => 'H M',
            'w_m' => 'W M',
            'd_m' => 'D M',
            'cbi_c20_bat_ah' => 'Cbi C20 Bat Ah',
            'voc_bat_v' => 'Voc Bat V',
        ];
    }

    /**
     * Gets query for [[Component]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\ComponentQuery
     */
    public function getComponent()
    {
        return $this->hasOne(Component::class, ['id' => 'component_id']);
    }

    /**
     * Gets query for [[Dimensionamentos]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\DimensionamentoQuery
     */
    public function getDimensionamentos()
    {
        return $this->hasMany(Dimensionamento::class, ['bateria_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\BateriaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\BateriaQuery(get_called_class());
    }
}
