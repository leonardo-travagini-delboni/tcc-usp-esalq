<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dimensionamento}}`.
 */
class m250326_134108_create_dimensionamento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // create table
        $this->createTable('{{%dimensionamento}}', [
            'id' => $this->primaryKey(),
            // main params
            'user_id' => $this->integer()->notNull(),
            'simulacao_no' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            // coordenadas
            'latitude' => $this->decimal(10, 4)->notNull(),
            'longitude' => $this->decimal(10, 4)->notNull(),
            // consumo
            'consumo_diario_cc_wh' => $this->decimal(10, 2)->notNull(),
            'consumo_diario_ca_wh' => $this->decimal(10, 2)->notNull(),
            'potencia_instalada_cc_w' => $this->decimal(10, 2)->notNull(),
            'potencia_instalada_ca_w' => $this->decimal(10, 2)->notNull(),
            // eficiencias
            'efic_bateria' => $this->decimal(10, 2)->defaultValue(0.86),
            'efic_inversor' => $this->decimal(10, 2)->defaultValue(0.90),
            'efic_gerador' => $this->decimal(10, 2)->defaultValue(0.75),
            'efic_elet' => $this->decimal(10, 2)->defaultValue(0.90),
            // painel
            'painel_id' => $this->integer()->notNull(),
            'painel_qtde_total' => $this->integer()->notNull(),
            'painel_qtde_serie' => $this->integer()->notNull(),
            // mppt (controlador-inversor)
            'mppt_id' => $this->integer()->notNull(),
            'fator_seguranca' => $this->decimal(10, 2)->notNull()->defaultValue(1.25),
            // bateria
            'bateria_id' => $this->integer()->notNull(),
            'profundidade_descarga' => $this->decimal(10, 2)->notNull()->defaultValue(0.80),
            'dias_autonomia' => $this->integer()->notNull(),
            'tensao_nominal_cc' => $this->integer()->notNull()->defaultValue(48),
        ]);

        // add foreign keys
        $this->addForeignKey('fk_dimensionamento_user_id', '{{%dimensionamento}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_dimensionamento_painel_id', '{{%dimensionamento}}', 'painel_id', '{{%painel}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_dimensionamento_mppt_id', '{{%dimensionamento}}', 'mppt_id', '{{%mppt}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_dimensionamento_bateria_id', '{{%dimensionamento}}', 'bateria_id', '{{%bateria}}', 'id', 'CASCADE', 'CASCADE');

        // create indexes
        $this->createIndex('idx_dimensionamento_user_id', '{{%dimensionamento}}', 'user_id');
        $this->createIndex('idx_dimensionamento_painel_id', '{{%dimensionamento}}', 'painel_id');
        $this->createIndex('idx_dimensionamento_mppt_id', '{{%dimensionamento}}', 'mppt_id');
        $this->createIndex('idx_dimensionamento_bateria_id', '{{%dimensionamento}}', 'bateria_id');
    
        // batch insert default data example
        $this->batchInsert('{{%dimensionamento}}', [
            'id', 'user_id', 'simulacao_no', 'created_at', 'updated_at',
            'latitude', 'longitude', 'consumo_diario_cc_wh', 'consumo_diario_ca_wh',
            'potencia_instalada_cc_w', 'potencia_instalada_ca_w', 'efic_bateria',
            'efic_inversor', 'efic_gerador', 'efic_elet', 'painel_id',
            'painel_qtde_total', 'painel_qtde_serie', 'mppt_id',
            'fator_seguranca', 'bateria_id', 'profundidade_descarga',
            'dias_autonomia', 'tensao_nominal_cc'
        ], [
            [1, 1, 1, time(), time(), -15.7723, -47.8700, 0, 6678.84, 0, 624.00, 0.86, 0.90, 0.75, 0.90, 5, 70, 10, 2, 1.25, 3, 0.8, 2, 48], 
            [2, 2, 1, time(), time(), -15.7723, -47.8700, 0, 6678.84, 0, 624.00, 0.86, 0.90, 0.75, 0.90, 5, 70, 10, 2, 1.25, 3, 0.8, 2, 48], 
        ]);
    
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drop foreign keys
        $this->dropForeignKey('fk_dimensionamento_user_id', '{{%dimensionamento}}');
        $this->dropForeignKey('fk_dimensionamento_painel_id', '{{%dimensionamento}}');
        $this->dropForeignKey('fk_dimensionamento_mppt_id', '{{%dimensionamento}}');
        $this->dropForeignKey('fk_dimensionamento_bateria_id', '{{%dimensionamento}}');

        // drop indexes
        $this->dropIndex('idx_dimensionamento_user_id', '{{%dimensionamento}}');
        $this->dropIndex('idx_dimensionamento_painel_id', '{{%dimensionamento}}');
        $this->dropIndex('idx_dimensionamento_mppt_id', '{{%dimensionamento}}');
        $this->dropIndex('idx_dimensionamento_bateria_id', '{{%dimensionamento}}');

        // drop table
        $this->dropTable('{{%dimensionamento}}');
    }
}
