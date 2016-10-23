<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shift".
 *
 * @property integer $id
 * @property string $started_at
 * @property string $finished_at
 */
class Shift extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shift';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['administrator_id'], 'integer'],
            [['started_at', 'finished_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'started_at' => 'Начало смены',
            'finished_at' => 'Конец смены',
        ];
    }

    public function getAdministrator()
    {
        return $this->hasOne(Administrator::className(), ['id' => 'administrator_id']);
    }

    public function getMasters()
    {
        return $this->hasMany(Master::className(), ['id' => 'master_id'])
            ->viaTable(MasterToShift::tableName(), ['shift_id' => 'id'], function ($query) {
                $query->orderBy(['arrive_time' => SORT_DESC]);
            });
    }

    public function getHaircuts()
    {
        return $this->hasMany(Haircut::className(), ['shift_id' => 'id']);
    }

    public function getExpenses()
    {
        return $this->hasMany(Expense::className(), ['shift_id' => 'id']);
    }

    public function getIncomes()
    {
        return $this->hasMany(Income::className(), ['shift_id' => 'id']);
    }

    public function getSales()
    {
        return $this->hasMany(Sale::className(), ['shift_id' => 'id']);
    }

    public static function getCurrent()
    {
        $current = static::findOne(['finished_at' => null]);
        if (!$current) {
            $prev = Shift::find()->orderBy(['id' => SORT_DESC])->one();
            $shift = new Shift();
            $shift->started_at = time();
            $shift->cash = ( $prev ? $prev->final_cash : 0 );
            $shift->save();
            $current = $shift;
        }
        return $current;
    }

    public function getStartedAt()
    {
        return strftime('%H:%M %d %B %Y', $this->started_at);
    }

    public function getFinishedAt()
    {
        return strftime('%H:%M %d %B %Y', $this->finished_at);
    }

    public function getAdministratorArrive()
    {
        return strftime('%H:%M %d %B %Y', $this->administrator_arrive);
    }

    public function getAdministratorArriveShort()
    {
        return date('H:i', $this->administrator_arrive);
    }

    public function getTotal()
    {
        $table = $this->getTotalTable();
        $sum = 0;
        foreach (end($table) as $total) {
            $sum += $total;
        }
        return $sum;
    }

    public function getSalesAmount()
    {
        $sum = 0;
        foreach ($this->sales as $sale) {
            $sum += $sale->amount;
        }
        return $sum;
    }

    public function getIncomeAmount()
    {
        $sum = 0;
        foreach ($this->incomes as $income) {
            $sum += $income->amount;
        }
        return $sum;
    }

    public function getExpenseAmount()
    {
        $sum = 0;
        foreach ($this->expenses as $expense) {
            $sum += $expense->amount;
        }
        return $sum;
    }

    public function getClientCount()
    {
        return count($this->haircuts);
    }

    public function getFinalCash()
    {
        return $this->cash + $this->total + $this->salesAmount + $this->incomeAmount - $this->administratorPayment - $this->expenseAmount;
    }

    public function getAdministratorPayment()
    {
        $bonus = (($this->clientCount - 80) / 10) * 200;
        $bonus = ( $bonus < 0 ? 0 : $bonus);
        $sales = ( $this->total >= 9000 ? ($this->total + $this->salesAmount) * 0.05 : 0 );
        $payment = round(1200 + $sales + $bonus);
        $round = $payment % 100;
        if ($round <= 25) {
            $payment -= $round;
        } else if ($round <= 50) {
            $payment += 50 - $round;
        } else if ($round <= 75) {
            $payment -= $round - 50;
        } else {
            $payment += 100 - $round;
        }
        return $payment;
    }

    public function close()
    {
        MasterStack::clear();
        $time = time();
        foreach ($this->masters as $master) {
            if (!$master->shift->leave_time) {
                $this->removeMaster($master->id);
            }
        }
        $this->final_cash = $this->finalCash;
        $this->finished_at = $time;
        return $this->save();
    }

    public function addExpense($name, $amount)
    {
        $expense = new Expense();
        $expense->shift_id = $this->id;
        $expense->name = $name;
        $expense->amount = $amount;
        return $expense->save();
    }

    public function addIncome($name, $amount)
    {
        $income = new Income();
        $income->shift_id = $this->id;
        $income->name = $name;
        $income->amount = $amount;
        return $income->save();
    }

    public function addSale($name, $amount)
    {
        $sale = new Sale();
        $sale->shift_id = $this->id;
        $sale->name = $name;
        $sale->amount = $amount;
        return $sale->save();
    }

    public function addMaster($id)
    {
        $assignment = new MasterToShift();
        $assignment->shift_id = $this->id;
        $assignment->master_id = $id;
        $assignment->arrive_time = time();
        return $assignment->save();
    }

    public function removeMaster($id)
    {
        if ($assignment = MasterToShift::findOne(['shift_id' => $this->id, 'master_id' => $id])) {
            $assignment->leave_time = time();
            return $assignment->save();
        }
        return false;
    }

    public function updatePrepayment($id, $value)
    {
        if ($assignment = MasterToShift::findOne(['shift_id' => $this->id, 'master_id' => $id])) {
            $assignment->prepayment = $value;
            return $assignment->save();
        }
        return false;
    }

    public function updatePenalty($id, $value)
    {
        if ($assignment = MasterToShift::findOne(['shift_id' => $this->id, 'master_id' => $id])) {
            $assignment->penalty = $value;
            return $assignment->save();
        }
        return false;
    }

    public function getHaircutTable()
    {
        $table = [];
        $haircuts = [];
        $masters = $this->masters;
        foreach ($masters as $master) {
            $table["{$master->time}{$master->id}"] = [];
            $haircuts["{$master->time}{$master->id}"] = [];
            foreach ($master->haircuts as $haircut) {
            	$isBonus = $haircut->bonus_id ? ' hasBonus ' : '';
            	$isDiscount = $haircut->price!=$haircut->original_price ? ' hasDiscount ' : '';
                $table["{$master->time}{$master->id}"][] =
                    "<span class='editable $isBonus $isDiscount' data-haircut-id='{$haircut->id}' title='{$haircut->shortTime}' style='float: left' id='haircut-price-{$haircut->id}'>".$haircut->price ."</span>
                    <span class='btn btn-xs btn-default glyphicon glyphicon-pencil' style='float: right' data-toggle='modal' data-target='#haircut-modal'></span>";
                $haircuts["{$master->time}{$master->id}"][] = $haircut->price;
            }
        }
        foreach ($table as $id => $row) {
            $table[$id][] = "
                <button class='btn btn-xs btn-primary add-haircut' style='float: left'><span class='glyphicon glyphicon-scissors' title='Стрижка'></span></button>
                <button class='btn btn-xs btn-warning master-leave' style='float: right'><span class='glyphicon glyphicon-share' title='Расчет'></span></button>
            ";
        }
        if ($table) {
            ksort($table);
            $table = call_user_func_array(
                'array_map',
                [-1 => null] + $table
            );
            foreach ($table as $id => $row) {
                if (!is_array($row)) {
                    $table[$id] = [0 => $row];
                }
            }
        }
        return $table;
    }

    public function getTotalTable()
    {
        $table = [];
        $table[0]['sum'] = 'Сумма';
        foreach ($this->masters as $master) {
            $id = "{$master->time}{$master->id}";
            $sum = 0;
            foreach ($master->haircuts as $haircut) {
                $sum += $haircut->price;
            }
            $table[$id]['sum'] = $sum;
        }
        $table[0]['material'] = 'Материалы';
        foreach ($this->masters as $master) {
            $id = "{$master->time}{$master->id}";
            $sum = 0;
            foreach ($master->materials as $material) {
                $sum += $material->price;
            }
            $table[$id]['material'] = $sum;
        }
        $table[0]['half'] = '1/2';
        foreach ($this->masters as $master) {
            $id = "{$master->time}{$master->id}";
            $table[$id]['half'] = ($table[$id]['sum'] - $table[$id]['material']) / 2;
        }
        $table[0]['prepayment'] = 'Аванс';
        foreach ($this->masters as $master) {
            $id = "{$master->time}{$master->id}";
            $table[$id]['prepayment'] = "<span class='prepayment'>{$master->prepayment}</span>";
        }
        $table[0]['penalty'] = 'Вычеты';
        foreach ($this->masters as $master) {
            $id = "{$master->time}{$master->id}";
            $table[$id]['penalty'] = "<span class='penalty'>{$master->penalty}</span>";
        }
        $table[0]['salary'] = 'Мастеру';
        foreach ($this->masters as $master) {
            $id = "{$master->time}{$master->id}";
            $table[$id]['salary'] = $table[$id]['half'] - preg_replace('~\D+~', '', $table[$id]['penalty']) - preg_replace('~\D+~', '', $table[$id]['prepayment']);
        }
        $table[0]['cash'] = 'Касса';
        foreach ($this->masters as $master) {
            $id = "{$master->time}{$master->id}";
            $table[$id]['cash'] = $table[$id]['material'] + preg_replace('~\D+~', '', $table[$id]['penalty']);
        }
        $table[0]['total'] = 'Итого';
        foreach ($this->masters as $master) {
            $id = "{$master->time}{$master->id}";
            $table[$id]['total'] = $table[$id]['half'] + $table[$id]['cash'];
        }
        if ($table) {
            ksort($table);
            $table = call_user_func_array(
                'array_map',
                [-1 => null] + $table
            );
            foreach ($table as $id => $row) {
                if (!is_array($row)) {
                    $table[$id] = [0 => $row];
                }
            }
        }
        return $table;
    }
}
