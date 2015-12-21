<?php
namespace Alin\Cadou\Controller\Adminhtml\Cadou;

use Alin\Cadou\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassEnable
 */
class MassEnable extends AbstractMassStatus
{
    /**
     * Field id
     */
    const ID_FIELD = 'cadou_id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Alin\Cadou\Model\Resource\Cadou\Collection';

    /**
     * Post model
     *
     * @var string
     */
    protected $model = 'Alin\Cadou\Model\Cadou';

    /**
     * Post enable status
     *
     * @var boolean
     */
    protected $status = true;
}