<?php declare(strict_types=1);

namespace SchemaHelper;

class EmailField extends Field
{
    public function __construct(string $name, bool $required = false, bool $nullable = true)
    {
        parent::__construct($name, FieldType::EMAIL, $required, $nullable);
    }

    private function email_strip_comments($comment, $email, $replace=''){

        while (1){
            $new = preg_replace("!$comment!", $replace, $email);
            if (strlen($new) == strlen($email)){
                return $email;
            }
            $email = $new;
        }
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();
        else if (!is_string($value))
            return false;

        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
