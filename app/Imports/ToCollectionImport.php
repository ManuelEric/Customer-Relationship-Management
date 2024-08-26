<?php


namespace App\Imports;


use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;

abstract class ToCollectionImport implements ToCollection
{
    public static $allFailures;
    /**
     * Set additional user resolve callback.

    abstract public function processImport(Collection $rows);

    abstract public function rules(): array;

    abstract public function getUser();

    abstract public static function getOrganization();

    /**
     * @param Collection $rows
     * @throws ValidationException|Throwable
     */
    public function collection(Collection $rows)
    {
        if ($this instanceof WithValidation) {
            $rows = $this->validate($rows);
        }

        try {
            $this->processImport($rows);
        } catch (Throwable $e) {
            $this->recordOrThrowErrors($e);
        }

        if ($this->failures()->count() > 0) {
            self::$allFailures = $this->failures();
        }

        if ($this->errors()->count() > 0) {
            Log::error($this->errors());
        }
    }

    /**
     * Validate given collection data.
     *
     * @param Collection $rows
     *
     * @return Collection
     * @throws ValidationException
     *
     */
    protected function validate(Collection $rows): Collection
    {
        $validator = Validator::make($rows->toArray(), $this->rules());

        if (!$validator->fails()) {
            return $rows;
        }

        if ($this instanceof SkipsOnFailure) {
            $this->onFailure(
                ...$this->collectErrors($validator, $rows)
            );

            $keysCausingFailure = collect($validator->errors()->keys())->map(function ($key) {
                return Str::before($key, '.');
            })->values()->toArray();

            return $rows->except($keysCausingFailure);
        }

        throw new ValidationException($validator);
    }

    /**
     * Get all validation errors.
     *
     * @param $validator
     * @param Collection $rows
     *
     * @return array
     */
    protected function collectErrors($validator, Collection $rows): array
    {
        $failures = [];

        foreach ($validator->errors() as $attribute => $messages) {
            $row = strtok($attribute, '.');
            $attributeName = strtok('');
            $attributeName = $attributes['*.' . $attributeName] ?? $attributeName;

            $failures[] = new Failure(
                $row,
                $attributeName,
                str_replace($attribute, $attributeName, $messages),
                $rows[$row] ?? []
            );
        }
        return $failures;
    }

    /**
     * Records an error or throws its exception.
     *
     * @param Throwable $error
     *
     * @return void
     * @throws Throwable
     */
    protected function recordOrThrowErrors(Throwable $error)
    {
        if ($this instanceof SkipsOnError) {
            return $this->onError($error);
        }

        throw $error;
    }
}