<?php

namespace theorx\Structure;
/**
 * Class Node
 *
 * @package theorx\Structure
 */
class Node {

    const TYPE_STRING  = "string";
    const TYPE_INTEGER = "integer";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_FLOAT   = "float";
    const TYPE_ARRAY   = "array";
    const TYPE_LIST    = "list";

    /**
     * @var string
     */
    public $type;

    /**
     * @var bool
     */
    public $required = false;

    /**
     * @var callable
     */
    private $validator = null;

    /**
     * @var string
     */
    public $description = "";

    /**
     * @var string
     */
    public $message = "";

    /**
     * @var array
     */
    private $descriptors = [];

    /**
     * Node constructor.
     *
     * @param string $type
     */
    public function __construct(string $type) {
        $this->type = $type;
    }

    /**
     * @return Node
     */
    public function required(): self {
        $this->required = true;

        return $this;
    }

    /**
     * @param callable $validator
     *
     * @return Node
     */
    public function validator(callable $validator): self {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return Node
     */
    public function description(string $description): self {
        $this->description = $description;

        return $this;
    }

    /**
     * Custom descriptors
     *
     * @param string $key
     * @param        $value
     *
     * @return $this
     */
    public function add(string $key, $value) {
        $this->descriptors[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getDescriptors(): array {
        return $this->descriptors;
    }

    /**
     * @return string
     */
    public function type(): string {
        return $this->type;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function validate($value): bool {
        if ($this->validator !== null) {
            $result = ($this->validator)($value);
            if ($result !== true) {
                $this->message = $result;

                return false;
            }
        }

        return true;
    }
}
