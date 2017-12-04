<?php

namespace theorx\Structure;
/**
 * Class DataObject
 *
 * @package theorx\Structure
 */
class DataObject {
    /**
     * @var Node[]
     */
    public $structure;
    /**
     * @var array
     */
    public $data;

    /**
     * DataObject constructor.
     *
     * @param array $structure
     */
    public function __construct(array $structure) {
        $this->structure = $structure;
    }

    /**
     * @return array
     */
    public function listProperties(): array {

        return array_keys($this->structure);
    }

    /**
     * @param string $property
     *
     * @return string
     * @throws \Exception
     *
     */
    public function getString(string $property): string {
        $this->checkType($property, Node::TYPE_STRING);

        if (!isset($this->data[$property])) {
            return "";
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     *
     * @return bool
     * @throws \Exception
     */
    public function getBoolean(string $property): bool {
        $this->checkType($property, Node::TYPE_BOOLEAN);

        if (!isset($this->data[$property])) {
            return false;
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     *
     * @return array
     * @throws \Exception
     */
    public function getArray(string $property): array {
        $this->checkType($property, Node::TYPE_ARRAY);

        if (!isset($this->data[$property])) {
            return [];
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     *
     * @return float
     * @throws \Exception
     */
    public function getFloat(string $property): float {
        $this->checkType($property, Node::TYPE_FLOAT);

        if (!isset($this->data[$property])) {
            return 0.0;
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     *
     * @return int
     * @throws \Exception
     */
    public function getInteger(string $property): int {
        $this->checkType($property, Node::TYPE_INTEGER);

        if (!isset($this->data[$property])) {
            return 0;
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     *
     * @return ListObject
     * @throws \Exception
     */
    public function getList(string $property): ?ListObject {
        $this->checkType($property, Node::TYPE_LIST);

        if (!isset($this->data[$property])) {
            $this->data[$property] = new ListObject($this->structure[$property]->getStructure());
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     *
     * @return null
     */
    public function get(string $property) {

        if (!$this->has($property) || !isset($this->data[$property]) || $this->data[$property] === null) {
            return null;
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    public function has(string $property): bool {

        return isset($this->structure[$property]);
    }

    /**
     * @param string $property
     * @param        $value
     *
     * @throws \Exception
     */
    public function set(string $property, $value) {

        if (!$this->has($property)) {
            throw new \Exception("Trying to set property (" . $property . ") that does not exist");
        }

        $type = $this->type($property);

        if ($type === Node::TYPE_STRING) {
            $this->data[$property] = (string)$value;
        }

        if ($type === Node::TYPE_INTEGER) {
            $this->data[$property] = (int)$value;
        }

        if ($type === Node::TYPE_FLOAT) {
            $this->data[$property] = (float)$value;
        }

        if ($type === Node::TYPE_BOOLEAN) {
            $this->data[$property] = false;
            if ($value === 1 || $value === "true" || $value === true) {
                $this->data[$property] = true;
            }
        }

        if ($type === Node::TYPE_ARRAY) {
            $this->data[$property] = (array)$value;
        }

        if ($type === Node::TYPE_LIST) {
            if (!isset($this->data[$property])) {
                $this->data[$property] = new ListObject($this->structure[$property]->structure);
            }
            /**
             * @var $list  ListObject
             * @var $value array
             */
            $list = $this->data[$property];

            $list->empty();
            if (!is_array($value)) {
                throw new \Exception("Value must be array when setting list (array of arrays)");
            }

            foreach ($value as $record) {
                if (!is_array($record)) {
                    throw new \Exception(
                        "Record in list is not an array, array of arrays must be provided to set a list"
                    );
                }
                $list->push($record);
            }

            $this->data[$property] = $list;
        }
    }

    /**
     * @param string $property
     *
     * @return string
     */
    public function type(string $property): ?string {

        if (!$this->has($property)) {
            return null;
        }

        return $this->structure[$property]->type();
    }

    /**
     * Returns value
     *
     * @return array
     */
    public function value(): array {
        $result = [];

        foreach ($this->structure as $property => $node) {

            $value = null;
            if ($node->type() === Node::TYPE_LIST || $node->type() === Node::TYPE_ARRAY) {
                $value = [];
            }

            if (isset($this->data[$property])) {
                $value = $this->data[$property];
                if ($node->type() === Node::TYPE_LIST) {
                    /**
                     * @var $list ListObject
                     */
                    $list = $this->data[$property];
                    $value = $list->value();
                }
            }

            $result[$property] = $value;
        }

        return $result;
    }

    /**
     * @param string $property
     * @param string $type
     *
     * @throws \Exception
     */
    private function checkType(string $property, string $type) {
        if (!$this->has($property)) {
            throw new \Exception("Property " . $property . " is not defined in the data structure");
        }

        if ($this->type($property) !== $type) {
            throw new \Exception("Cannot get a property of type " . $this->type($property) . " as (" . $type . ")");
        }
    }

    /**
     * If validation is successful and the object is valid, then empty array will be returned
     * Otherwise a list of validation messages
     *
     * @return array
     */
    public function validate(): array {
        $status = [];
        foreach ($this->structure as $property => $node) {

            if ($node->type() === Node::TYPE_LIST) {
                if (isset($this->data[$property])) {
                    /**
                     * @var ListObject $listObject
                     */
                    $listObject = $this->data[$property];
                    $status = array_merge($status, $listObject->validate());
                }
            } else {
                if ($node->required && $this->get($property) === null) {
                    $status[] = "Property (" . $property . ") is required but is not set";
                } elseif (
                    $node->validate(
                        isset($this->data[$property]) ? $this->data[$property] : null
                    ) === false
                ) {
                    $status[] = "Property (" . $property . ") " . $node->message;
                }
            }
        }

        return $status;
    }

    /**
     * Populate object
     *
     * @param array $data
     */
    public function populate(array $data) {
        foreach ($data as $key => $value) {
            if ($this->type($key) !== Node::TYPE_LIST) {
                $this->set($key, $value);
            } else {
                $this->getList($key)->populate($value);
            }
        }
    }
}
