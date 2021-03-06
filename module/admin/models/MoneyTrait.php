<?php
namespace app\module\admin\models;

trait MoneyTrait
{

    /**
     * конвертация из одной валюты в другую
     * @param Money $moneyFrom
     * @param Money $moneyTo
     * @return double
     */
    public function convert(Money $moneyFrom, Money $moneyTo )
    {
        $rubley = $moneyFrom->convertToRubles($moneyFrom->getSum(), $moneyFrom->getNominal(), $moneyFrom->getValue());
        $result = $moneyTo->convertFromRubles($rubley, $moneyTo->getNominal(), $moneyTo->getValue());
        return $result;
    }
    
    private function getPowerArray()
    {
        return [
            [0,NULL          ,NULL           ,NULL           ],  // 1
            [1,"тысяча "     ,"тысячи "      ,"тысяч "        ],  // 2
            [0,"миллион "    ,"миллиона "    ,"миллионов "    ],  // 3
            [0,"миллиард "   ,"миллиарда "   ,"миллиардов "   ],  // 4
            [0,"триллион "   ,"триллиона "   ,"триллионов "   ],  // 5
            [0,"квадриллион ","квадриллиона ","квадриллионов "],  // 6
            [0,"квинтиллион ","квинтиллиона ","квинтиллионов "]   // 7
        ];
    }
    
    private function getDigitArray()
    {
        return [
            [[""       ,""       ],"десять "      ,""            ,""          ],
            [["один"  ,"одна "  ],"одиннадцать" ,"десять"     ,"сто"      ],
            [["два "   ,"две "   ],"двенадцать "  ,"двадцать "   ,"двести "   ],
            [["три "   ,"три "   ],"тринадцать "  ,"тридцать "   ,"триста "   ],
            [["четыре ","четыре "],"четырнадцать ","сорок "      ,"четыреста "],
            [["пять "  ,"пять "  ],"пятнадцать "  ,"пятьдесят "  ,"пятьсот "  ],
            [["шесть " ,"шесть " ],"шестнадцать " ,"шестьдесят " ,"шестьсот " ],
            [["семь "  ,"семь "  ],"семнадцать "  ,"семьдесят "  ,"семьсот "  ],
            [["восемь ","восемь "],"восемнадцать ","восемьдесят ","восемьсот "],
            [["девять ","девять "],"девятнадцать ","девяносто "  ,"девятьсот "]
        ];
    }
    
    /**
     * Получить число прописью
     * @param integer $p_summa
     * @param integer $sex (0 - жен, 1 - муж)
     * @param string $one (наименование количества для 1)
     * @param string $four (наименование количества для меньшеб либо равно 4)
     * @param string $many (наименование количества для больше 4)
     * @return string
     */
    
    public function getTextForm($p_summa, $sex, $one, $four, $many)
    {
        $dg_power = 6;
        $result = 0;
        $a_power= $this->getPowerArray();
        $a_power[0][0]  = $sex;
        $a_power[0][1]  = $one;
        $a_power[0][2] = $four;
        $a_power[0][3] = $many;
        $digit = $this->getDigitArray();
        $result = $this->transformToText($p_summa, $dg_power, $a_power, $digit, $many);
        return $result;
    }
    
    /**
     * Получить число прописью
     * 
     * @param integer $p_summa            
     * @param integer $dg_power            
     * @param array $a_power            
     * @param array $digit            
     * @param string $many            
     * @return string
     */
    private function transformToText($p_summa, $dg_power, array $a_power, array $digit, $many)
    {
        $mny = 0;
        $str = "";
        $result = "";
        $divisor = 1;
        
        if ($p_summa == 0) {
            return "ноль ";
        }
        
        if ($p_summa < 0) {
            $result = "минус ";
            $p_summa = - $p_summa;
        }
        
        for ($i = 0, $divisor = 1; $i < $dg_power; $i ++) {
            $divisor *= 1000;
        }
        for ($i = $dg_power - 1; $i >= 0; $i --) {
            $divisor /= 1000;
            $mny = (int) ($p_summa / $divisor);
            $p_summa %= $divisor;
            
            $str = "";
            if ($mny == 0) {
                if ($i > 0)
                    continue;
                $str .= $many;
            } else {
                if ($mny >= 100) {
                    $str .= ' ' . $digit[$mny / 100][3];
                    $mny %= 100;
                }
                if ($mny >= 20) {
                    $str .= ' ' . $digit[$mny / 10][2];
                    $mny %= 10;
                }
                if ($mny >= 10) {
                    $str .= ' ' . $digit[$mny - 10][1];
                } else 
                    if ($mny >= 1) {
                        $str .= ' ' . $digit[$mny][0][$a_power[$i][0]];
                    }
                switch ($mny) {
                    case 1:
                        $str .= ' ' . $a_power[$i][1];
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $str .= ' ' . $a_power[$i][2];
                        break;
                    default:
                        $str .= ' ' . $a_power[$i][3];
                        break;
                }
                ;
            }
            $result .= $str;
        }
        return $result;
    }

    /**
     * Сохрание числа прописью в модели
     * 
     * @param unknown $model            
     * @param text $field            
     * @param text $value            
     */
    public function savePriceStr($model, $field, $value)
    {
        $model->$field = $value;
        return $model->save(array(
            $field
        ));
    }
}
