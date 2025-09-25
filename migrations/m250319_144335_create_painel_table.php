<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%painel}}`.
 */
class m250319_144335_create_painel_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%painel}}', [
            'id' => $this->primaryKey(),
            'component_id' => $this->integer()->notNull(),
            'modelo' => $this->text()->notNull(),
            'fabricante' => $this->text()->notNull(),
            'comprimento_m' => $this->decimal(10,4)->notNull(),
            'espessura_mm' => $this->decimal(10,4)->notNull(),
            'largura_m' => $this->decimal(10,4)->notNull(),
            'pmax_w' => $this->decimal(10,4)->notNull(),
            'imax_a' => $this->decimal(10,4)->notNull(),
            'vmax_v' => $this->decimal(10,4)->notNull(),
            'voc_v' => $this->decimal(10,4)->notNull(),
            'icc_a' => $this->decimal(10,4)->notNull(),
            'tmin_oper_celsius' => $this->decimal(10,4)->notNull(),
            'tmax_oper_celsius' => $this->decimal(10,4)->notNull(),
            'beta_1_sobre_celsius' => $this->decimal(10,4)->notNull(),
        ]);


        // add foreign key for table `component`
        $this->addForeignKey(
            'fk-painel-component_id',
            'painel',
            'component_id',
            'component',
            'id',
            'CASCADE'
        );

        // index
        $this->createIndex(
            'idx-painel-component_id',
            'painel',
            'component_id'
        );

        // Inserting data
        $this->batchInsert('{{%painel}}', ['component_id', 'modelo', 'fabricante', 'comprimento_m', 'espessura_mm', 'largura_m', 'pmax_w', 'imax_a', 'vmax_v', 'voc_v', 'icc_a', 'tmin_oper_celsius', 'tmax_oper_celsius', 'beta_1_sobre_celsius'], [
            [2, 'OPV Sunew Flex TM 4', 'Sunew', 0.54, 0.4, 0.53, 7.4, 0.4422, 16.7, 24.9, 0.6242, -40, 85, 0.0035],
            [2, 'OPV Sunew Flex TM 10', 'Sunew', 1.3, 0.4, 0.53, 18.5, 1.1056, 16.7, 24.9, 1.5605, -40, 85, 0.0035],
            [2, 'OPV Sunew Flex TM 12', 'Sunew', 1.55, 0.4, 0.53, 22.2, 1.3267, 16.7, 24.9, 1.8726, -40, 85, 0.0035],
            [2, 'OPV Sunew Flex TM 14', 'Sunew', 1.81, 0.4, 0.53, 25.9, 1.5478, 16.7, 24.9, 2.1847, -40, 85, 0.0035],
            [2, 'OPV Sunew Flex TM 20', 'Sunew', 2.57, 0.4, 0.53, 37, 2.2112, 16.7, 24.9, 3.121, -40, 85, 0.0035],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-painel-component_id',
            'painel'
        );

        $this->dropIndex(
            'idx-painel-component_id',
            'painel'
        );

        $this->dropTable('{{%painel}}');
    }
}
