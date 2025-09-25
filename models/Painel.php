<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "painel".
 *
 * @property int $id
 * @property int $component_id
 * @property string $modelo
 * @property string $fabricante
 * @property float $comprimento_m
 * @property float $espessura_mm
 * @property float $largura_m
 * @property float $pmax_w
 * @property float $imax_a
 * @property float $vmax_v
 * @property float $voc_v
 * @property float $icc_a
 * @property float $tmin_oper_celsius
 * @property float $tmax_oper_celsius
 * @property float $beta_1_sobre_celsius
 *
 * @property Component $component
 * @property Dimensionamento[] $dimensionamentos
 */
class Painel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'painel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['component_id', 'modelo', 'fabricante', 'comprimento_m', 'espessura_mm', 'largura_m', 'pmax_w', 'imax_a', 'vmax_v', 'voc_v', 'icc_a', 'tmin_oper_celsius', 'tmax_oper_celsius', 'beta_1_sobre_celsius'], 'required'],
            [['component_id'], 'integer'],
            [['modelo', 'fabricante'], 'string'],
            [['comprimento_m', 'espessura_mm', 'largura_m', 'pmax_w', 'imax_a', 'vmax_v', 'voc_v', 'icc_a', 'tmin_oper_celsius', 'tmax_oper_celsius', 'beta_1_sobre_celsius'], 'number'],
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
            'comprimento_m' => 'Comprimento M',
            'espessura_mm' => 'Espessura Mm',
            'largura_m' => 'Largura M',
            'pmax_w' => 'Pmax W',
            'imax_a' => 'Imax A',
            'vmax_v' => 'Vmax V',
            'voc_v' => 'Voc V',
            'icc_a' => 'Icc A',
            'tmin_oper_celsius' => 'Tmin Oper Celsius',
            'tmax_oper_celsius' => 'Tmax Oper Celsius',
            'beta_1_sobre_celsius' => 'Beta 1 Sobre Celsius',
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
        return $this->hasMany(Dimensionamento::class, ['painel_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\PainelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\PainelQuery(get_called_class());
    }
}
