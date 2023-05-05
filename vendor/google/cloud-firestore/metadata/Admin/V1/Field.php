<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/firestore/admin/v1/field.proto

namespace GPBMetadata\Google\Firestore\Admin\V1;

class Field
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Api\FieldBehavior::initOnce();
        \GPBMetadata\Google\Api\Resource::initOnce();
        \GPBMetadata\Google\Firestore\Admin\V1\Index::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
%google/firestore/admin/v1/field.protogoogle.firestore.admin.v1google/api/resource.proto%google/firestore/admin/v1/index.proto"�
Field
name (	B�AB
index_config (2,.google.firestore.admin.v1.Field.IndexConfig>

ttl_config (2*.google.firestore.admin.v1.Field.TtlConfig�
IndexConfig1
indexes (2 .google.firestore.admin.v1.Index
uses_ancestor_config (
ancestor_field (	
	reverting (�
	TtlConfigD
state (20.google.firestore.admin.v1.Field.TtlConfig.StateB�A"J
State
STATE_UNSPECIFIED 
CREATING

ACTIVE
NEEDS_REPAIR:y�Av
firestore.googleapis.com/FieldTprojects/{project}/databases/{database}/collectionGroups/{collection}/fields/{field}B�
com.google.firestore.admin.v1B
FieldProtoPZ>google.golang.org/genproto/googleapis/firestore/admin/v1;admin�GCFS�Google.Cloud.Firestore.Admin.V1�Google\\Cloud\\Firestore\\Admin\\V1�#Google::Cloud::Firestore::Admin::V1bproto3'
        , true);

        static::$is_initialized = true;
    }
}

