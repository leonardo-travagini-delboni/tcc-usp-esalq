<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mppt".
 *
 * @property int $id
 * @property int $component_id
 * @property string $modelo
 * @property string $fabricante
 * @property string $vmpptmin_range_v
 * @property float $vmppt_min_v
 * @property string $vmpptmax_range_v
 * @property float $vmppt_max_v
 * @property float $ictl_a
 * @property float $p_inv_max
 *
 * @property Component $component
 * @property Dimensionamento[] $dimensionamentos
 */
class Mppt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mppt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['component_id', 'modelo', 'fabricante', 'vmpptmin_range_v', 'vmppt_min_v', 'vmpptmax_range_v', 'vmppt_max_v', 'ictl_a', 'p_inv_max'], 'required'],
            [['component_id'], 'integer'],
            [['modelo', 'fabricante', 'vmpptmin_range_v', 'vmpptmax_range_v'], 'string'],
            [['vmppt_min_v', 'vmppt_max_v', 'ictl_a', 'p_inv_max'], 'number'],
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
            'vmpptmin_range_v' => 'Vmpptmin Range V',
            'vmppt_min_v' => 'Vmppt Min V',
            'vmpptmax_range_v' => 'Vmpptmax Range V',
            'vmppt_max_v' => 'Vmppt Max V',
            'ictl_a' => 'Ictl A',
            'p_inv_max' => 'P Inv Max',
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
        return $this->hasMany(Dimensionamento::class, ['mppt_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\MpptQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\MpptQuery(get_called_class());
    }
}
