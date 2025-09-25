<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mppt}}`.
 */
class m250319_181613_create_mppt_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mppt}}', [
            'id' => $this->primaryKey(),
            'component_id' => $this->integer()->notNull(),
            'modelo' => $this->text()->notNull(),
            'fabricante' => $this->text()->notNull(),
            'vmpptmin_range_v' => $this->text()->notNull(),
            'vmppt_min_v' => $this->decimal(10,4)->notNull(),
            'vmpptmax_range_v' => $this->text()->notNull(),
            'vmppt_max_v' => $this->decimal(10,4)->notNull(),
            'ictl_a' => $this->decimal(10,4)->notNull(),
            'p_inv_max' => $this->decimal(10,4)->notNull(),
        ]);

        // add foreign key for table `component`
        $this->addForeignKey(
            'fk-mppt-component_id',
            'mppt',
            'component_id',
            'component',
            'id',
            'CASCADE'
        );

        // index
        $this->createIndex(
            'idx-mppt-component_id',
            'mppt',
            'component_id'
        );

        // Inserting data
        $this->batchInsert('{{%mppt}}', ['component_id', 'modelo', 'fabricante', 'vmpptmin_range_v', 'vmppt_min_v', 'vmpptmax_range_v', 'vmppt_max_v', 'ictl_a', 'p_inv_max'], [
            [4, 'MPPT Multiplus-II Victron Energy 48/3000/35-32', 'Victron Energy', 38 - 66, 47, 187 - 265, 226, 32, 2400],
            [4, 'MPPT Multiplus-II Victron Energy 48/5000/70-50', 'Victron Energy', 38 - 66, 47, 187 - 265, 226, 50, 4000],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mppt}}');
    }
}
