<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DifferentIds implements Rule
{
    /**
     * @var array<int|string>
     */
    protected array $ids;

    /**
     * @param array<int|string> $ids
     */
    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     */
    public function passes($attribute, $value): bool
    {
        return $this->ids[0] !== $this->ids[1];
    }

    public function message(): string
    {
        return 'O item e o componente precisam ser diferentes.';
    }
}
