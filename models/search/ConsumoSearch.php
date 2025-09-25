<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Consumo;

/**
 * ConsumoSearch represents the model behind the search form of `app\models\Consumo`.
 */
class ConsumoSearch extends Consumo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'qtde', 'minutos_por_dia', 'dias_por_mes'], 'integer'],
            [['potencia_w', 'tensao_v', 'corrente_a'], 'number'],
            [['nome', 'tipo_corrente'], 'safe'],
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
        $query = Consumo::find();

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
            'qtde' => $this->qtde,
            'potencia_w' => $this->potencia_w,
            'minutos_por_dia' => $this->minutos_por_dia,
            'dias_por_mes' => $this->dias_por_mes,
            'tensao_v' => $this->tensao_v,
            'corrente_a' => $this->corrente_a,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'tipo_corrente', $this->tipo_corrente]);

        return $dataProvider;
    }
}
