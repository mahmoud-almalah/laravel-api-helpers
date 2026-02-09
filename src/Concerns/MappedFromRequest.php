<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ReflectionClass;
use ReflectionProperty;

trait MappedFromRequest
{
    /**
     * @param  array<string, mixed>|Request  $data
     */
    public static function fromRequest(array|Request $data): static
    {
        if ($data instanceof Request) {
            if (method_exists($data, 'validated')) {
                /** @var array<string, mixed> $validated */
                $validated = $data->validated();
                $data = $validated;
            } else {
                $data = $data->toArray();
            }
        }

        if (method_exists(static::class, 'rules')) {
            $rules = static::rules();
            if (! empty($rules)) {
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                $data = $validator->validated();
            }
        }

        $reflection = new ReflectionClass(static::class);
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();

            if (array_key_exists($name, $data)) {
                $property->setValue($instance, $data[$name]);
            }
        }

        return $instance;
    }
}
