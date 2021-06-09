<?php


namespace Autocall\Pragma;
require_once __DIR__ . '/../../business_rules/settings/iPip.php';
require_once __DIR__ . '/PipsSchema.php';


class Pip implements iPip
{
    static PipsSchema $PipsSchema;

    public function __construct(private int $account_id)
    {
        self::$PipsSchema = new PipsSchema($this->account_id);
    }


    function savePipe(int $id): void
    {
        $this->create_account();
        $arr_pip = self::$PipsSchema->return_pip();
        $res = in_array($id, $arr_pip);
        if (!$res) {
            array_push($arr_pip, $id);
            self::$PipsSchema->save_new_arr($arr_pip);
            $Q = self::$PipsSchema->getQuantity();
            Factory::Log('$Q', $Q);
            Factory::Log('count($arr_pip)', count($arr_pip));
            if (count($arr_pip) <= $Q) {
                Factory::Log('save', 'save');

                self::$PipsSchema->save();
            }
        }
    }

    function deletePipe(int $id): void
    {
        if (self::$PipsSchema->search_key()) {
            $arr_pip = self::$PipsSchema->return_pip();
            $new_arr = [];
            foreach ($arr_pip as $key => $item) {
                if ($item != $id)
                    array_push($new_arr, $item);
            }
            self::$PipsSchema->save_new_arr($new_arr);
            self::$PipsSchema->save();
        }


    }

    function getPips(): array|null
    {
        return self::$PipsSchema->get();
    }

    private function create_account()
    {
        if (!self::$PipsSchema->search_key())
            self::$PipsSchema->add_account();
    }
}