<?php
class Validation extends Singleton
{

    use FilterTrait;

    private array $data;
    private array $errors;

    private string $field_name;
    private string $field_value;



    public function setData(array $entryData): self
    {
        $this->data = $this->sanitizeData($entryData);
        return $this;
    }

    private function sanitizeData($data): array
    {
        foreach ($data as $field) {
            $field = htmlspecialchars(strip_tags(trim($field)));
        }

        return $data;
    }

    public function field(string $name): self
    {
        $this->field_name = $name;
        return $this;
    }

    private function getFieldValue()
    {
        if (isset($this->data[$this->field_name])) {
            return $this->data[$this->field_name];
        }
    }

    public function type(string $type): self
    {
        if (!method_exists($this, $type)) {
            throw new Exception("Type not exists");
        }

        $this->field_value = $this->getFieldValue() !== null ? $this->getFieldValue() : '';

        if (!$this->{$type}($this->field_value)) {
            $this->setError("Field type does not correspond");
        }

        return $this;
    }

    public function required(): self
    {
        if ($this->getFieldValue() === '') {
            $this->setError("Field is required");
        }
        return $this;
    }


    public function pattern(string $regexp): self
    {
        if (!preg_match($regexp, $this->field_value)) {
            $this->setError("Field is not valid");
        }

        return $this;
    }

    private function setError(string $message)
    {
        if (!isset($this->errors[$this->field_name]))
            $this->errors[] = [
                $this->field_name => $message
            ];
    }

    public function hasErrors(): array | false
    {
        if (!empty($this->errors)) {
            return $this->errors;
        }

        return false;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
