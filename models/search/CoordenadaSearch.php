<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Coordenada;

/**
 * CoordenadaSearch represents the model behind the search form of `app\models\Coordenada`.
 */
class CoordenadaSearch extends Coordenada
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'mmc'], 'integer'],
            [['lat', 'long'], 'number'],
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
        $query = Coordenada::find();

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
            'lat' => $this->lat,
            'long' => $this->long,
            'mmc' => $this->mmc,
        ]);

        return $dataProvider;
    }
}
