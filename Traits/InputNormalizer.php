<?php
namespace Traits;

trait InputNormalizer
{
    public function normalize($value)
    {
        return \trim($value ?? '');
    }

    public function normalizeInputs($request, $fields = [])
    {
        $normalized = [];
        foreach ($fields as $field) {
            $normalized[$field] = $this->normalize($request->request->get($field));
        }
        return $normalized;
    }
}