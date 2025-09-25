<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%consumo}}`.
 */
class m250324_221857_create_consumo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // creates table `consumo`
        $this->createTable('{{%consumo}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'nome' => $this->text()->notNull(),
            'qtde' => $this->integer()->notNull(),
            'potencia_w' => $this->decimal(10, 2)->notNull(),
            'minutos_por_dia' => $this->integer()->notNull(),
            'dias_por_mes' => $this->integer()->notNull(),
            'tensao_v' => $this->decimal(10, 2),
            'corrente_a' => $this->decimal(10, 2),
            'tipo_corrente' => $this->string(2)->notNull()->defaultValue('CA'),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-consumo-user_id',
            'consumo',
            'user_id'
        );

        // creates index for column `user_id`
        $this->addForeignKey(
            'fk-consumo-user_id',
            'consumo',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // Inserting data
        $this->batchInsert('{{%consumo}}', ['user_id', 'nome', 'qtde', 'potencia_w', 'minutos_por_dia', 'dias_por_mes', 'tipo_corrente'], [
            [1, 'Lâmpadas de Iluminação para Reunião', 5, 5.00, 497, 30, 'CA'],
            [1, 'Notebooks para as Salas', 7, 30.00, 643, 30, 'CA'],
            [1, 'Iluminação para Salas', 22, 5.00, 686, 30, 'CA'],
            [1, 'Notebooks das Salas', 7, 30.00, 643, 30, 'CA'],
            [1, 'Modem para as Salas', 1, 20.00, 1440, 30, 'CA'],
            [1, 'Iluminação para o Banheiro', 1, 9.00, 283, 30, 'CA'],
            [1, 'Iluminação para o Vestiário', 2, 5.00, 283, 30, 'CA'],
            [1, 'Iluminação Externa', 6, 5.00, 283, 30, 'CA'],
            [2, 'Lâmpadas de Iluminação para Reunião', 5, 5.00, 497, 30, 'CA'],
            [2, 'Notebooks para as Salas', 7, 30.00, 643, 30, 'CA'],
            [2, 'Iluminação para Salas', 22, 5.00, 686, 30, 'CA'],
            [2, 'Notebooks das Salas', 7, 30.00, 643, 30, 'CA'],
            [2, 'Modem para as Salas', 1, 20.00, 1440, 30, 'CA'],
            [2, 'Iluminação para o Banheiro', 1, 9.00, 283, 30, 'CA'],
            [2, 'Iluminação para o Vestiário', 2, 5.00, 283, 30, 'CA'],
            [2, 'Iluminação Externa', 6, 5.00, 283, 30, 'CA'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-consumo-user_id',
            'consumo'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-consumo-user_id',
            'consumo'
        );

        // drops table `consumo`
        $this->dropTable('{{%consumo}}');
    }
}
