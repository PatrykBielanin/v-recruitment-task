<?php

namespace App\Modules\Validator\Conditioner;

use App\Modules\Validator\Conditioner\Factory\Pair;
use App\Modules\Validator\Conditioner\Factory\Parser;
use App\Modules\Validator\Conditioner\Traits\AccessTrait;

class Conditioner
{
    protected $pairs = [];

    use AccessTrait;

    public function __invoke($validatorValue, $validatorPayload, $validatorField)
    {
        if (count($this->getPairs()) !== 1) {
            return [];
        }

        return $this->run(...[
            ...func_get_args(),
            $this->getFirstPair()->getConditions(),
        ]);
    }

    protected function run($validatorValue, $validatorPayload, $validatorField, $conditions, $lastField = null)
    {
        $collector = [];

        $replaceField = function ($pairField, $field, $validatorField, $validatorValue, $lastField) {
            return Parser::replaceField($pairField, $field, Parser::isArray($validatorField, $validatorValue), $lastField);
        };

        foreach ($conditions as $condition) {
            if (! $condition->compare($validatorValue, $validatorField, $validatorPayload)->passed()) {
                continue;
            }

            foreach ($condition->getConditioner()->getPairs() as $pair) {
                foreach (Parser::fields($validatorField, $validatorValue, $condition) as $field) {
                    $replacedField = $replaceField($pair->getField(), $field, $validatorField, $validatorValue, $lastField);

                    if (! count($pair->getConditions())) {
                        $collector[$replacedField] = $pair->getRule();

                        continue;
                    }

                    $collector[$replacedField] = [
                        $pair->getRule() => function ($vV, $vP, $vF) use ($pair, $field, $validatorField, $validatorValue) {
                            return $this->run(
                                $vV,
                                $vP,
                                $vF,
                                $pair->getConditions(),
                                Parser::isArray($validatorField, $validatorValue) ? $field : null
                            );
                        },
                    ];
                }
            }
        }

        return $collector;
    }

    public function pair($field, $rule)
    {
        $pair = factory(Pair::class)->resolve($this)
            ->field($field)
            ->rule($rule);

        $this->pairs[] = $pair;

        return $pair;
    }

    public function getFirstPair()
    {
        $pairs = $this->getPairs();

        return reset($pairs);
    }
}
