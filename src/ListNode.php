<?php

namespace theorx\Structure;
/**
 * Class ListNode
 *
 * @package theorx\Structure
 */
class ListNode extends Node {
    /**
     * List record definition
     *
     * @var array
     */
    public $structure = [];

    /**
     * ListNode constructor.
     *
     * @param array $structure
     */
    public function __construct(array $structure) {
        parent::__construct(self::TYPE_LIST);
        $this->structure = $structure;
    }

    /**
     * @return array
     */
    public function getStructure() {
        return $this->structure;
    }
}
