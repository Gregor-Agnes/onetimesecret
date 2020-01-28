<?php

namespace Zwo3\Onetimesecret\Domain\Model;

class Onetimesecret extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var string
     *
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $secret;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var \DateTime
     */
    protected $validUntil;

    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * @var int
     */
    protected $lastHit;

    /**
     * @var int
     */
    protected $hitNumber;


    /**
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return \DateTime
     */
    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    /**
     * @param \DateTime $validUntil
     */
    public function setValidUntil(\DateTime $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    /**
     * @return \DateTime
     */
    public function getCrdate(): ?\DateTime
    {
        return $this->crdate;
    }

    /**
     * @param \DateTime $crdate
     */
    public function setCrdate(\DateTime $crdate): void
    {
        $this->crdate = $crdate;
    }

    /**
     * @return \DateTime
     */
    public function getTstamp(): ?\DateTime
    {
        return $this->tstamp;
    }

    /**
     * @param \DateTime $tstamp
     */
    public function setTstamp(\DateTime $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    /**
     * @return int
     */
    public function getLastHit(): ?int
    {
        return $this->lastHit;
    }

    /**
     * @param int $lastHit
     */
    public function setLastHit(int $lastHit): void
    {
        $this->lastHit = $lastHit;
    }

    /**
     * @return int
     */
    public function getHitNumber(): ?int
    {
        return $this->hitNumber;
    }

    /**
     * @param int $hitNumber
     */
    public function setHitNumber(int $hitNumber): void
    {
        $this->hitNumber = $hitNumber;
    }


}