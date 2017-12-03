<?php

namespace theorx\Structure;

/**
 * Class S
 *
 * @package theorx\Structure
 */
class S {
    /**
     * @param array $structure
     *
     * @return DataObject
     */
    public static function create(array $structure = []) {

        return new DataObject($structure);
    }

    /**
     * @return Node
     */
    public static function string() {
        return new Node(Node::TYPE_STRING);
    }

    /**
     * @return Node
     */
    public static function integer() {
        return new Node(Node::TYPE_INTEGER);
    }

    /**
     * @return Node
     */
    public static function float() {
        return new Node(Node::TYPE_FLOAT);
    }

    /**
     * @return Node
     */
    public static function array() {
        return new Node(Node::TYPE_ARRAY);
    }

    /**
     * @return Node
     */
    public static function boolean() {
        return new Node(Node::TYPE_BOOLEAN);
    }

    /**
     * @param array $structure
     *
     * @return ListNode
     */
    public static function list(array $structure = []) {

        return new ListNode($structure);
    }
}
