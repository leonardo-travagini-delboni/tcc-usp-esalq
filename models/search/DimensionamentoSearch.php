<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Dimensionamento;

/**
 * DimensionamentoSearch represents the model behind the search form of `app\models\Dimensionamento`.
 */
class DimensionamentoSearch extends Dimensionamento
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'simulacao_no', 'created_at', 'updated_at', 'painel_id', 'painel_qtde_total', 'painel_qtde_serie', 'mppt_id', 'bateria_id', 'dias_autonomia', 'tensao_nominal_cc'], 'integer'],
            [['latitude', 'longitude', 'consumo_diario_cc_wh', 'consumo_diario_ca_wh', 'potencia_instalada_cc_w', 'potencia_instalada_ca_w', 'efic_bateria', 'efic_inversor', 'efic_gerador', 'efic_elet', 'fator_seguranca', 'profundidade_descarga'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Dimensionamento::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'simulacao_no' => $this->simulacao_no,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'consumo_diario_cc_wh' => $this->consumo_diario_cc_wh,
            'consumo_diario_ca_wh' => $this->consumo_diario_ca_wh,
            'potencia_instalada_cc_w' => $this->potencia_instalada_cc_w,
            'potencia_instalada_ca_w' => $this->potencia_instalada_ca_w,
            'efic_bateria' => $this->efic_bateria,
            'efic_inversor' => $this->efic_inversor,
            'efic_gerador' => $this->efic_gerador,
            'efic_elet' => $this->efic_elet,
            'painel_id' => $this->painel_id,
            'painel_qtde_total' => $this->painel_qtde_total,
            'painel_qtde_serie' => $this->painel_qtde_serie,
            'mppt_id' => $this->mppt_id,
            'fator_seguranca' => $this->fator_seguranca,
            'bateria_id' => $this->bateria_id,
            'profundidade_descarga' => $this->profundidade_descarga,
            'dias_autonomia' => $this->dias_autonomia,
            'tensao_nominal_cc' => $this->tensao_nominal_cc,
        ]);

        return $dataProvider;
    }
}
