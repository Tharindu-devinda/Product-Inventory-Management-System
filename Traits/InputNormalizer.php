<?php
namespace Traits;

// Trait for normalizing input data
trait InputNormalizer
{
    // Normalize a single input value, trim whitespace and handle nulls
    public function normalize($value)
    {
        return \trim($value ?? '');
    }

    // Normalize multiple inputs from a request, return array
    public function normalizeInputs($request, $fields = [])
    {
        $normalized = [];
        foreach ($fields as $field) {
            $normalized[$field] = $this->normalize($request->request->get($field));
        }
        return $normalized;
    }
}