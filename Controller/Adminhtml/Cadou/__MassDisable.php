<?php
namespace Alin\Cadou\Controller\Adminhtml\Post;

use Alin\Cadou\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassDisable
 */
class MassDisable extends AbstractMassStatus
{
    /**
     * Field id
     */
    const ID_FIELD = 'post_id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Alin\Cadou\Model\Resource\Cadou\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $model = 'Alin\Cadou\Model\Cadou';

    /**
     * Page disable status
     *
     * @var boolean
     */
    protected $status = false;
}