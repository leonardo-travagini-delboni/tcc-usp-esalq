<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coordenada".
 *
 * @property int $id
 * @property float $lat
 * @property float $long
 * @property int $mmc
 */
class Coordenada extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coordenada';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lat', 'long', 'mmc'], 'required'],
            [['lat', 'long'], 'number'],
            [['mmc'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lat' => 'Latitude',
            'long' => 'Longitude',
            'mmc' => 'MMC',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\CoordenadaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\CoordenadaQuery(get_called_class());
    }

    /**
     * Verifica se as coordenadas estão dentro do Brasil e retorna o ponto mais próximo
     * 
     * @param float $lat
     * @param float $long
     * @return int|null ID do ponto mais próximo ou null se estiver fora dos limites
     */
    public static function getPontoProximo($lat, $long)
    {
        // Limites do Brasil
        $minLat = -33.7;
        $maxLat = 5.3;
        $minLong = -73.9;
        $maxLong = -32.4;
    
        // Verificar se está dentro do Brasil
        if ($lat < $minLat || $lat > $maxLat || $long < $minLong || $long > $maxLong) {
            return null;
        }
    
        // Corrigindo o uso de `long` como nome de coluna (escapado com backticks)
        $ponto = self::find()
            ->orderBy([
                new \yii\db\Expression("POW(lat - :lat, 2) + POW(`long` - :long, 2) ASC")
            ])
            ->params([':lat' => $lat, ':long' => $long])
            ->limit(1)
            ->one();
    
        return $ponto ? $ponto->id : null;
    }
    

    /**
     * Retorna o MMC do ponto mais próximo
     * 
     * @param float $lat
     * @param float $long
     * @return int|null MMC do ponto ou null se não encontrado
     */
    public static function getMmc($lat, $long)
    {
        $idPonto = self::getPontoProximo($lat, $long);
        if ($idPonto === null) {
            return null;
        }

        $ponto = self::findOne($idPonto);
        return $ponto ? $ponto->mmc : null;
    }

    /**
     * Retorna a distância entre dois pontos em km
     * 
     * @param float $lat1
     * @param float $long1
     * @param float $lat2
     * @param float $long2
     * @return float Distância em km
     */
    public static function getDistancia($lat1, $long1, $lat2, $long2)
    {
        $lat1 = deg2rad($lat1);
        $long1 = deg2rad($long1);
        $lat2 = deg2rad($lat2);
        $long2 = deg2rad($long2);

        $distancia = 6371 * acos(
            cos($lat1) * cos($lat2) * cos($long1 - $long2) +
            sin($lat1) * sin($lat2)
        );
        
        return $distancia;
    }


}
