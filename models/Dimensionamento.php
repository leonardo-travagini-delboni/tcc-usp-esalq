<?php

namespace app\models;

use app\models\User;
use app\models\Painel;
use app\models\Bateria;
use app\models\Mppt;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "dimensionamento".
 *
 * @property int $id
 * @property int $user_id
 * @property int $simulacao_no
 * @property int $created_at
 * @property int $updated_at
 * @property float $latitude
 * @property float $longitude
 * @property float $consumo_diario_cc_wh
 * @property float $consumo_diario_ca_wh
 * @property float $potencia_instalada_cc_w
 * @property float $potencia_instalada_ca_w
 * @property float|null $efic_bateria
 * @property float|null $efic_inversor
 * @property float|null $efic_gerador
 * @property float|null $efic_elet
 * @property int $painel_id
 * @property int $painel_qtde_total
 * @property int $painel_qtde_serie
 * @property int $mppt_id
 * @property float $fator_seguranca
 * @property int $bateria_id
 * @property float $profundidade_descarga
 * @property int $dias_autonomia
 * @property int $tensao_nominal_cc
 *
 * @property Bateria $bateria
 * @property Mppt $mppt
 * @property Painel $painel
 * @property User $user
 */
class Dimensionamento extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dimensionamento';
    }

    // TimestampBehavior
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function () {
                    return time();
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'simulacao_no', 'latitude', 'longitude', 'consumo_diario_cc_wh', 'consumo_diario_ca_wh', 'potencia_instalada_cc_w', 'potencia_instalada_ca_w', 'painel_id', 'painel_qtde_total', 'painel_qtde_serie', 'mppt_id', 'bateria_id', 'dias_autonomia'], 'required', 'message' => 'Campo obrigatório*'],
            [['user_id', 'simulacao_no', 'created_at', 'updated_at', 'painel_id', 'painel_qtde_total', 'painel_qtde_serie', 'mppt_id', 'bateria_id', 'dias_autonomia', 'tensao_nominal_cc'], 'integer'],
            [['latitude', 'longitude', 'consumo_diario_cc_wh', 'consumo_diario_ca_wh', 'potencia_instalada_cc_w', 'potencia_instalada_ca_w', 'efic_bateria', 'efic_inversor', 'efic_gerador', 'efic_elet', 'fator_seguranca', 'profundidade_descarga'], 'number'],
            [['bateria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bateria::class, 'targetAttribute' => ['bateria_id' => 'id']],
            [['mppt_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mppt::class, 'targetAttribute' => ['mppt_id' => 'id']],
            [['painel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Painel::class, 'targetAttribute' => ['painel_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['efic_bateria', 'efic_inversor', 'efic_gerador', 'efic_elet'], 'number', 'min' => 0.01, 'max' => 1.00, 'message' => 'O valor deve estar entre 0.01 e 1.00'],
            [['painel_qtde_total'], 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => 'Deve haver ao menos 1 painel*'],
            [['bateria_id'], 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => 'Deve haver ao menos 1 bateria*'],
            [['mppt_id'], 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => 'Deve haver ao menos 1 controlador-inversor*'],
            [['fator_seguranca'], 'compare', 'compareValue' => 1.00, 'operator' => '>=', 'message' => 'O fator de segurança deve ser no mínimo 1.00*'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id_table',
            'user_id' => 'user_id',
            'simulacao_no' => 'ID',
            'created_at' => 'Criação',
            'updated_at' => 'Atualização',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'consumo_diario_cc_wh' => 'Consumo Diário CC (Wh)',
            'consumo_diario_ca_wh' => 'Consumo Diário CA (Wh)',
            'potencia_instalada_cc_w' => 'Potência Instalada CC (W)',
            'potencia_instalada_ca_w' => 'Potência Instalada CA (W)',
            'efic_bateria' => 'Eficiência Bateria',
            'efic_inversor' => 'Eficiência Inversor',
            'efic_gerador' => 'Eficiência Gerador',
            'efic_elet' => 'Eficiência Elet',
            'painel_id' => 'Painel',
            'painel_qtde_total' => 'Qtde Painéis',
            'painel_qtde_serie' => 'Qtde Painéis em Série',
            'mppt_id' => 'Controlador-Inversor',
            'fator_seguranca' => 'Fator de Segurança',
            'bateria_id' => 'Bateria',
            'profundidade_descarga' => 'Profundidade de Descarga',
            'dias_autonomia' => 'Dias de Autonomia',
            'tensao_nominal_cc' => 'Tensão Nominal CC (V)',
        ];
    }

    /**
     * Gets query for [[Bateria]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\BateriaQuery
     */
    public function getBateria()
    {
        return $this->hasOne(Bateria::class, ['id' => 'bateria_id']);
    }

    /**
     * Gets query for [[Mppt]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\MpptQuery
     */
    public function getMppt()
    {
        return $this->hasOne(Mppt::class, ['id' => 'mppt_id']);
    }

    /**
     * Gets query for [[Painel]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\PainelQuery
     */
    public function getPainel()
    {
        return $this->hasOne(Painel::class, ['id' => 'painel_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\DimensionamentoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\DimensionamentoQuery(get_called_class());
    }

    /**
     * Obtém o último número de simulação do usuário
     * @return int
     */
    public function getUltimoSimulacaoNo()  
    {
        $ultimo = Dimensionamento::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['simulacao_no' => SORT_DESC])
            ->one();

        return $ultimo ? $ultimo->simulacao_no : 0;
    }

    /**
     * Obtém parâmetros prévios do dimensionamento
     * @return array
     */
    public function getCoordenadas()
    {
        // Checando a definição de coordenadas:
        $use_gps = Yii::$app->user->identity->use_gps ?? null;
        if($use_gps == null){
            Yii::info('Coordenadas não definidas. Voltar às etapas anteriores por favor.', 'dimensionamento');
            throw new \Exception('Coordenadas não definidas. Voltar às etapas anteriores por favor.');
        }

        // Definindo as coordenadas:
        if($use_gps == 1){
            $lat = Yii::$app->user->identity->gps_lat;
            $lng = Yii::$app->user->identity->gps_lng;
        } else {
            Yii::
            $lat = Yii::$app->user->identity->latitude;
            $lng = Yii::$app->user->identity->longitude;
        }

        // Retornando os parâmetros:
        $params = [
            'lat' => $lat,
            'lng' => $lng,
        ];

        return $params;

    }
}
