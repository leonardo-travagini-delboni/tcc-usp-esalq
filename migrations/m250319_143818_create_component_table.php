<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%component}}`.
 */
class m250319_143818_create_component_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%component}}', [
            'id' => $this->primaryKey(),
            'component' => $this->string(255)->notNull(),
            'description' => $this->string(255),
        ]);

        $this->batchInsert('{{%component}}', ['component', 'description'], [
            ['Equipamento', 'Equipamento de Consumo Elétrico'],
            ['Painel', 'Painel ou Módulo Solar'],
            ['Bateria', 'Armazenamento de Energia'],
            ['Controlador-Inversor', 'Controlador-Inversor Integrado do tipo MPPT'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%component}}');
    }
}
