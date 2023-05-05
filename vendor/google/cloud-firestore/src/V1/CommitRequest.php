<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/firestore/v1/firestore.proto

namespace Google\Cloud\Firestore\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The request for [Firestore.Commit][google.firestore.v1.Firestore.Commit].
 *
 * Generated from protobuf message <code>google.firestore.v1.CommitRequest</code>
 */
class CommitRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Required. The database name. In the format:
     * `projects/{project_id}/databases/{database_id}`.
     *
     * Generated from protobuf field <code>string database = 1 [(.google.api.field_behavior) = REQUIRED];</code>
     */
    private $database = '';
    /**
     * The writes to apply.
     * Always executed atomically and in order.
     *
     * Generated from protobuf field <code>repeated .google.firestore.v1.Write writes = 2;</code>
     */
    private $writes;
    /**
     * If set, applies all writes in this transaction, and commits it.
     *
     * Generated from protobuf field <code>bytes transaction = 3;</code>
     */
    private $transaction = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $database
     *           Required. The database name. In the format:
     *           `projects/{project_id}/databases/{database_id}`.
     *     @type array<\Google\Cloud\Firestore\V1\Write>|\Google\Protobuf\Internal\RepeatedField $writes
     *           The writes to apply.
     *           Always executed atomically and in order.
     *     @type string $transaction
     *           If set, applies all writes in this transaction, and commits it.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Firestore\V1\Firestore::initOnce();
        parent::__construct($data);
    }

    /**
     * Required. The database name. In the format:
     * `projects/{project_id}/databases/{database_id}`.
     *
     * Generated from protobuf field <code>string database = 1 [(.google.api.field_behavior) = REQUIRED];</code>
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Required. The database name. In the format:
     * `projects/{project_id}/databases/{database_id}`.
     *
     * Generated from protobuf field <code>string database = 1 [(.google.api.field_behavior) = REQUIRED];</code>
     * @param string $var
     * @return $this
     */
    public function setDatabase($var)
    {
        GPBUtil::checkString($var, True);
        $this->database = $var;

        return $this;
    }

    /**
     * The writes to apply.
     * Always executed atomically and in order.
     *
     * Generated from protobuf field <code>repeated .google.firestore.v1.Write writes = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getWrites()
    {
        return $this->writes;
    }

    /**
     * The writes to apply.
     * Always executed atomically and in order.
     *
     * Generated from protobuf field <code>repeated .google.firestore.v1.Write writes = 2;</code>
     * @param array<\Google\Cloud\Firestore\V1\Write>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setWrites($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Cloud\Firestore\V1\Write::class);
        $this->writes = $arr;

        return $this;
    }

    /**
     * If set, applies all writes in this transaction, and commits it.
     *
     * Generated from protobuf field <code>bytes transaction = 3;</code>
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * If set, applies all writes in this transaction, and commits it.
     *
     * Generated from protobuf field <code>bytes transaction = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setTransaction($var)
    {
        GPBUtil::checkString($var, False);
        $this->transaction = $var;

        return $this;
    }

}

