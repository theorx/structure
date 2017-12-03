<?php

namespace theorx\Structure;
/**
 * Class ListObject
 *
 * @package theorx\Structure
 */
class ListObject {

    /**
     * Structure definition
     *
     * @var Node[]|ListNode[]
     */
    public $structure = [];

    /**
     * stored data
     *
     * @var DataObject[]
     */
    public $data = [];

    /**
     * ListObject constructor.
     *
     * @param array $structure
     */
    public function __construct(array $structure) {
        $this->structure = $structure;
    }

    /**
     * @param int $index
     *
     * @return DataObject
     * @throws \Exception
     */
    public function get(int $index) {

        if (!isset($this->data[array_keys($this->data)[$index]])) {
            throw new \Exception(
                "Attempted to get item from a list at index " . $index .
                " while only having " . $this->length() . " items"
            );
        }

        return $this->data[$index];
    }

    /**
     * @param array $record
     *
     * @return int
     */
    public function push(array $record) {
        $object = new DataObject($this->structure);
        foreach ($record as $key => $value) {
            $object->set($key, $value);
        }
        $this->data[] = $object;

        return $this->length();
    }

    /**
     * @param int $index
     */
    public function remove(int $index) {
        if ($this->length() > 0) {
            array_splice($this->data, $index, 1);
        }
    }

    /**
     * empty the list
     */
    public function empty() {
        $this->data = [];
    }

    /**
     * @return int
     */
    public function length(): int {
        return count($this->data);
    }

    /**
     * @return array
     */
    public function value(): array {

        $result = [];
        foreach ($this->data as $record) {
            $entry = [];
            foreach ($this->structure as $property => $node) {
                $value = null;
                if ($record->has($property)) {
                    $value = $record->get($property);
                    if ($node->type() === Node::TYPE_LIST) {
                        /**
                         * @var $list ListObject
                         */
                        $list = $record->getList($property);
                        $value = $list->value();
                    }
                }
                $entry[$property] = $value;
            }
            $result[] = $entry;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function validate(): array {
        $status = [];

        foreach ($this->data as $record) {
            foreach ($this->structure as $property => $node) {
                if ($node->type() === Node::TYPE_LIST) {
                    if ($record->has($property)) {
                        /**
                         * @var ListObject $listObject
                         */
                        $listObject = $record->get($property);
                        array_merge($status, $listObject->validate());
                    }
                } else {
                    $value = $record->get($property);
                    if ($node->required && $value === null) {
                        $status[] = "Property (" . $property . ") is required but is not set";
                    } elseif ($node->validate($value) === false) {
                        $status[] = "Property (" . $property . ") " . $node->message;
                    }
                }
            }
        }

        return $status;
    }

    /**
     * @param array $data
     */
    public function populate(array $data) {
        foreach ($data as $record) {
            $this->push($record);
        }
    }
}
