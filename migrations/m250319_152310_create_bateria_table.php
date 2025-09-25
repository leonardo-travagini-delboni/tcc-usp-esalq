<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bateria}}`.
 */
class m250319_152310_create_bateria_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // create table `bateria`
        $this->createTable('{{%bateria}}', [
            'id' => $this->primaryKey(),
            'component_id' => $this->integer()->notNull(),
            'modelo' => $this->text()->notNull(),
            'fabricante' => $this->text()->notNull(),
            'h_m' => $this->decimal(10,4)->notNull(),
            'w_m' => $this->decimal(10,4)->notNull(),
            'd_m' => $this->decimal(10,4)->notNull(),
            'cbi_c20_bat_ah' => $this->decimal(10,4)->notNull(),
            'voc_bat_v' => $this->decimal(10,4)->notNull(),
        ]);

        // add foreign key for table `component`
        $this->addForeignKey(
            'fk-bateria-component_id',
            'bateria',
            'component_id',
            'component',
            'id',
            'CASCADE'
        );

        // index
        $this->createIndex(
            'idx-bateria-component_id',
            'bateria',
            'component_id'
        );

        // Inserting data
        $this->batchInsert('{{%bateria}}', ['component_id', 'modelo', 'fabricante', 'h_m', 'w_m', 'd_m', 'cbi_c20_bat_ah', 'voc_bat_v'], [
            [3, 'LifePO4 Powerbox Dyness 2 Módulos', 'Dyness', 0.928, 0.555, 0.21, 100, 48],
            [3, 'LifePO4 Powerbox Dyness 3 Módulos', 'Dyness', 0.928, 0.555, 0.21, 150, 48],
            [3, 'LifePO4 Powerbox Dyness 4 Módulos', 'Dyness', 0.928, 0.555, 0.21, 200, 48],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drop foreign key for table `component`
        $this->dropForeignKey(
            'fk-bateria-component_id',
            'bateria'
        );

        // drop index for column `component_id`
        $this->dropIndex(
            'idx-bateria-component_id',
            'bateria'
        );

        // drop table `bateria`
        $this->dropTable('{{%bateria}}');
    }
}
