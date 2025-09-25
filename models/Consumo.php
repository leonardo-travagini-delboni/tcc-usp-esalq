<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "consumo".
 *
 * @property int $id
 * @property int $user_id
 * @property string $nome
 * @property int $qtde
 * @property float $potencia_w
 * @property int $minutos_por_dia
 * @property int $dias_por_mes
 * @property float|null $tensao_v
 * @property float|null $corrente_a
 * @property string $tipo_corrente
 *
 * @property User $user
 */
class Consumo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consumo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'nome', 'qtde', 'potencia_w', 'minutos_por_dia', 'dias_por_mes'], 'required', 'message' => 'Campo obrigatório!'],
            [['user_id', 'qtde', 'minutos_por_dia', 'dias_por_mes'], 'integer', 'message' => 'Campo numérico inteiro!'],
            [['potencia_w', 'tensao_v', 'corrente_a'], 'number', 'message' => 'Campo numérico decimal!'],
            [['nome'], 'string'],
            [['nome'], 'string', 'max' => 255, 'min' => 3, 'tooShort' => 'Mínimo de 3 caracteres!', 'tooLong' => 'Máximo de 255 caracteres!'],
            [['tipo_corrente'], 'string', 'max' => 2],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id'], 'message' => 'Usuário não encontrado!'],
            // minutos por dia deve estar entre 1 e 1440
            ['minutos_por_dia', 'compare', 'compareValue' => 1440, 'operator' => '<=', 'message' => 'Máximo de 1440 minutos por dia!'],
            ['minutos_por_dia', 'compare', 'compareValue' => 1, 'operator' => '>=', 'message' => 'Mínimo de 1 minuto por dia!'],
            // dias por mês deve estar entre 1 e 31
            ['dias_por_mes', 'compare', 'compareValue' => 31, 'operator' => '<=', 'message' => 'Máximo de 31 dias por mês!'],
            ['dias_por_mes', 'compare', 'compareValue' => 1, 'operator' => '>=', 'message' => 'Mínimo de 1 dia por mês!'],
            // potência deve ser maior que 0
            ['potencia_w', 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => 'Potência deve ser maior que 0!'],
            // quantidade deve ser maior ou igual a 0
            ['qtde', 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => 'Quantidade deve ser maior ou igual a 0!'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'nome' => 'Equipamento',
            'qtde' => 'Qtde',
            'potencia_w' => 'Potência (W)',
            'minutos_por_dia' => 'Min/Dia',
            'dias_por_mes' => 'Dias/Mês',
            'tensao_v' => 'Tensão (V)',
            'corrente_a' => 'Corrente (A)',
            'tipo_corrente' => 'Corrente',
        ];
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
     * @return \app\models\query\ConsumoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\ConsumoQuery(get_called_class());
    }

    /**
     * Retorna os equipamentos cadastrados para o usuário logado, trazendo seus dados juntos
     * @return array
     */
    public function getEquipamentos()
    {
        // filtrando apenas registros com user_id igual ao id do usuário logado
        $consumo = Consumo::find()->where(['user_id' => Yii::$app->user->id])->all();
        $equipamentos = [];
        foreach ($consumo as $c) {
            $equipamentos[] = [
                'id' => $c->id,
                'nome' => $c->nome,
                'qtde' => $c->qtde,
                'potencia_w' => $c->potencia_w,
                'minutos_por_dia' => $c->minutos_por_dia,
                'dias_por_mes' => $c->dias_por_mes,
                'tensao_v' => $c->tensao_v,
                'corrente_a' => $c->corrente_a,
                'tipo_corrente' => $c->tipo_corrente,
            ];
        }
        return $equipamentos;
    }

    /**
     * Retorna as potências totais (CC, CA e Sistema) em Watts (W) para o usuário logado
     * @return array
     */
    public function getPotencias()
    {
        // Filtrando apenas registros com user_id igual ao id do usuário logado
        $consumo = Consumo::find()->where(['user_id' => Yii::$app->user->id])->all();
        $potencia_total_cc = 0;
        $potencia_total_ca = 0;
    
        foreach ($consumo as $c) {
            $potencia_pontual = $c->qtde * $c->potencia_w; // Multiplica a potência pela quantidade
            if ($c->tipo_corrente == 'CC') {
                $potencia_total_cc += $potencia_pontual; // Soma ao total de corrente contínua
            } else {
                $potencia_total_ca += $potencia_pontual; // Soma ao total de corrente alternada
            }
        }
    
        $potencia_total_sistema = $potencia_total_cc + $potencia_total_ca; // Soma as potências totais
    
        return [
            'CC' => $potencia_total_cc,
            'CA' => $potencia_total_ca,
            'SISTEMA' => $potencia_total_sistema,
        ];
    }

    /**
     * Retorna os consumos diários totais (CC, CA e Sistema) em Wh/dia para o usuário logado
     * @return array
     */
    public function getConsumosDiarios()
    {
        // filtrando apenas registros com user_id igual ao id do usuário logado
        $consumo = Consumo::find()->where(['user_id' => Yii::$app->user->id])->all();
        $consumo_total_cc = 0;
        $consumo_total_ca = 0;
        foreach ($consumo as $c) {
            // $consumo_pontual = $c->qtde * $c->potencia_w * ($c->minutos_por_dia / 60) * $c->dias_por_mes; // Calcula o consumo diário
            $potencia_pontual = $c->qtde * $c->potencia_w; // Multiplica a potência pela quantidade
            $consumo_pontual = $potencia_pontual * ($c->minutos_por_dia / 60); // Calcula o consumo diário
            if ($c->tipo_corrente == 'CC') {
                $consumo_total_cc += $consumo_pontual; // Soma ao total de corrente contínua
            } else {
                $consumo_total_ca += $consumo_pontual; // Soma ao total de corrente alternada
            }
        }
        $consumo_total_sistema = $consumo_total_cc + $consumo_total_ca; // Soma os consumos totais
        return [
            'CC' => $consumo_total_cc,
            'CA' => $consumo_total_ca,
            'SISTEMA' => $consumo_total_sistema,
        ];
    }
}
