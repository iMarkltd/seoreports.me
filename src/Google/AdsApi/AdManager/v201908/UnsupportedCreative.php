<?php

namespace Google\AdsApi\AdManager\v201908;


/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class UnsupportedCreative extends \Google\AdsApi\AdManager\v201908\Creative
{

    /**
     * @var string $unsupportedCreativeType
     */
    protected $unsupportedCreativeType = null;

    /**
     * @param int $advertiserId
     * @param int $id
     * @param string $name
     * @param \Google\AdsApi\AdManager\v201908\Size $size
     * @param string $previewUrl
     * @param string[] $policyViolations
     * @param string[] $policyLabels
     * @param \Google\AdsApi\AdManager\v201908\AppliedLabel[] $appliedLabels
     * @param \Google\AdsApi\AdManager\v201908\DateTime $lastModifiedDateTime
     * @param \Google\AdsApi\AdManager\v201908\BaseCustomFieldValue[] $customFieldValues
     * @param string $unsupportedCreativeType
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $policyLabels = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $unsupportedCreativeType = null)
    {
      parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $policyLabels, $appliedLabels, $lastModifiedDateTime, $customFieldValues);
      $this->unsupportedCreativeType = $unsupportedCreativeType;
    }

    /**
     * @return string
     */
    public function getUnsupportedCreativeType()
    {
      return $this->unsupportedCreativeType;
    }

    /**
     * @param string $unsupportedCreativeType
     * @return \Google\AdsApi\AdManager\v201908\UnsupportedCreative
     */
    public function setUnsupportedCreativeType($unsupportedCreativeType)
    {
      $this->unsupportedCreativeType = $unsupportedCreativeType;
      return $this;
    }

}
