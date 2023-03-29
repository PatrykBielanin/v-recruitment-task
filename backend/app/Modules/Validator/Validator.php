<?php

namespace App\Modules\Validator;

use App\Modules\Validator\Conditioner\Factory\Pair;
use App\Modules\Support\Support;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Validator
{
    protected $fields = [];
    protected $source = [];

    protected $errors;
    protected $finalFields;

    protected $errorPrefix = '';

    protected $rulesCollector;

    protected $orPrefix = 'Or';

    protected $callbacks = [];

    protected $paramPrefix = ':';
    protected $iteratorPrefix = '-';

    protected $returnFieldsBehavior;

    protected $lastReplacedFieldIndex = 0;

    public function __construct()
    {
        $this->rulesCollector = new RulesCollector();
    }

    public function addSource($source)
    {
        $this->source = $source;

        $this->rulesCollector->setOrPrexix($this->orPrefix);
        $this->rulesCollector->setSource($this->source);

        return $this;
    }

    public function setFields($fields)
    {
        $this->fields = collect($fields);

        return $this;
    }

    public function setErrorPrefix($prefix)
    {
        $this->errorPrefix = $prefix;

        return $this;
    }

    public function addReturnFieldsBehavior(?array $schema)
    {
        if ($schema) {
            $this->returnFieldsBehavior = $schema;
        }

        return $this;
    }

    public function validate($fields = [], $errors = [])
    {
        foreach ($this->prepare() as $field => $current) {
            foreach ($this->getWrappedValue($current->value) as $i => $value) {
                $firstOrFieldPassed = false;
                foreach ($current->rules as $rule) {
                    $isOrField = Str::endsWith($rule->rule, $this->orPrefix);

                    if (! $this->rulesCollector->get($value, $rule->rule, $rule->param)) {
                        if ($firstOrFieldPassed || $isOrField) {
                            continue;
                        }

                        $errors = $this->setError($errors, $field, $current, $rule, $i);

                        continue;
                    }

                    $firstOrFieldPassed = $isOrField;
                }
            }

            $fields[] = $field;
        }

        if (count($this->callbacks)) {
            $this->runCallbacks();

            return $this->validate($fields, $errors);
        }

        $this->errors = $errors;
        $this->finalFields = $fields;

        return $this;
    }

    public function errors()
    {
        return collect($this->errors)->map(function ($error) {
            $hasInteratorPrefix = $this->hasIteratorPrefix($error[0]);

            if ($hasInteratorPrefix) {
                return $this->groupIteratorErrors($error);
            }

            return $this->errorPrefix.$error[0];
        });
    }

    public function passed()
    {
        return count($this->errors) === 0;
    }

    public function fields()
    {
        $fields = collect($this->finalFields)
            ->unique()
            ->values();

        if ($this->returnFieldsBehavior) {
            foreach ($this->returnFieldsBehavior as $field => $schemaFields) {
                $fields = $fields->filter(function ($item) use ($schemaFields) {
                    return ! Support::contains($this->replaceFieldIndexToAsterisk($item)->field, $schemaFields);
                });

                $fields->push($field);
            }
        }

        return $fields
            ->unique()
            ->values();
    }

    protected function replaceFieldIndexToAsterisk($field)
    {
        $field = Str::of($field)->explode('.')->map(function ($item) {
            if (filter_var($item, FILTER_VALIDATE_INT) !== false) {
                $this->lastReplacedFieldIndex = $item;

                return '*';
            }

            return $item;
        })->implode('.');

        return (object) [
            'field' => $field,
            'index' => $this->lastReplacedFieldIndex,
        ];
    }

    protected function groupIteratorErrors($errors)
    {
        $errors = collect($errors)->map(function ($value) {
            return explode($this->iteratorPrefix, $value);
        });

        $result = [];

        foreach ($errors as [$group, $index]) {
            if (! isset($result, $index)) {
                $result[$index] = [$group];

                continue;
            }

            $result[$index][] = $group;
        }

        return collect($result)->map(function ($value, $key) {
            return $this->errorPrefix.implode($this->iteratorPrefix, [$value[0], $key]);
        })->values()->all();
    }

    protected function hasIteratorPrefix($error)
    {
        if (strlen($error) == 0) {
            return false;
        }

        [$prefix, $integer] = str_split(substr($error, -2));

        return $prefix == $this->iteratorPrefix && $this->rulesCollector->get($integer, 'trueInteger', null);
    }

    protected function prepare()
    {
        return $this->fields->mapWithKeys(function ($rules, $field) {
            if ($conditioner = $this->isConditioner($rules)) {
                $field = $conditioner->getFirstPair()->getField();
            }

            return [
                $field => (object) [
                    'rules' => $this->extractRules($rules, $field),
                    'value' => $this->getValueFromSource($field),
                ],
            ];
        })->filter(function ($item) {
            return $item->rules->count();
        });
    }

    protected function getValueFromSource($field)
    {
        return data_get($this->source, $field);
    }

    protected function extractRules($rules, $field)
    {
        if ([$callback, $callbackRules] = $this->isRuleWithCallback($rules)) {
            $this->callbacks[] = (object) compact('field', 'callback');

            $rules = $callbackRules;
        }

        return collect(explode('|', $rules))->map(function ($rule) {
            [$rule, $param] = explode(':', $rule) + [1 => null];
            [$rule, $replaceErrorKey] = explode('-', $rule) + [$rule, null];

            return (object) [
                'rule' => $rule,
                'param' => $param,
                'replaceErrorKey' => $replaceErrorKey,
            ];
        });
    }

    protected function isRuleWithCallback($rules)
    {
        if (is_array($rules) && count($rules) && is_callable($callback = array_values($rules)[0])) {
            return [
                $callback,
                array_keys($rules)[0],
            ];
        }

        if ($conditioner = $this->isConditioner($rules)) {
            return [
                $conditioner,
                $conditioner->getFirstPair()->getRule(),
            ];
        }

        return false;
    }

    protected function setError($errors, $field, $current, $rule, $i)
    {
        $replacedField = $this->replaceFieldIndexToAsterisk($field);

        if ($replacedField->field !== $field) {
            if (Arr::get($errors, $replacedField->field)) {
                $errors[$replacedField->field][] = $this->getErrorValue($current, $rule, $replacedField->index, true);

                return $errors;
            }

            $errors[$replacedField->field][] = $this->getErrorValue($current, $rule, $replacedField->index, true);

            return $errors;
        }

        $errors[$field][] = $this->getErrorValue($current, $rule, $i);

        return $errors;
    }

    protected function getErrorValue($current, $rule, $i, $forceIndex = false)
    {
        $error = [$rule->replaceErrorKey ?? $rule->rule];

        if ($rule->param) {
            $error[] = implode('', [$this->paramPrefix, $rule->param]);
        }

        if (is_array($current->value) || $forceIndex) {
            $error[] = implode('', [$this->iteratorPrefix, $i]);
        }

        return implode('', $error);
    }

    protected function runCallbacks()
    {
        $newFields = [];

        foreach ($this->callbacks as $callback) {
            if (Arr::get($this->errors, $callback->field)) {
                continue;
            }

            if ($fields = ($callback->callback)($this->getValueFromSource($callback->field), (object) $this->source, $callback->field)) {
                $newFields = array_merge($newFields, $fields);
            }
        }

        $this->callbacks = [];
        $this->setFields($newFields);
    }

    protected function getWrappedValue($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return [$value];
    }

    protected function isConditioner($rules)
    {
        if ($rules instanceof Pair) {
            return $rules->getConditioner();
        }

        return false;
    }
}
