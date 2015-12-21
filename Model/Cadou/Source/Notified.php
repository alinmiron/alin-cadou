<?php
namespace Alin\Cadou\Model\Cadou\Source;

class Notified implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Alin\Cadou\Model\Cadou
     */
    protected $cadou;

    /**
     * Constructor
     *
     * @param \Alin\Cadou\Model\Cadou $cadou
     */
    public function __construct(\Alin\Cadou\Model\Cadou $cadou)
    {
        $this->cadou = $cadou;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->cadou->getNotifiedOptions();
        foreach ($availableOptions as $key => $value)
        {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
