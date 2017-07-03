<?php

namespace AppBundle\Model\Email;

class ProposalMessage extends Message
{
    private $sendCopy;

    /**
     * @return mixed
     */
    public function getSendCopy()
    {
        return $this->sendCopy;
    }

    /**
     * @param mixed $sendCopy
     * @return ProposalMessage
     */
    public function setSendCopy($sendCopy)
    {
        $this->sendCopy = $sendCopy;
        return $this;
    }
}