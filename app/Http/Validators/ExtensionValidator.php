<?php
namespace App\Http\Validators;

use Illuminate\Validation\Validator;

class ExtensionValidator extends Validator
{

    /**
     * validateKatakana カタカナのバリデーション（ブランクを許容）
     *
     * @param string $value
     * @access public
     * @return bool
     */
    public function validateKatakana($attribute, $value, $parameters)
    {
        return (bool) preg_match('/^[ァ-ヾ 　〜ー−]+$/u', $value);
    }
}
