<?php

namespace NineThousand\Bundle\NineThousandJobqueueBundle\Entity;

/**
 * Arg Entity for use with DoctrineJobAdapter in Jobqueue.
 *
 * @category  NineThousand
 * @package   Jobqueue
 * @author    Jesse Greathouse <jesse.greathouse@gmail.com>
 * @copyright 2011 NineThousand (https://github.com/organizations/NineThousand)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @link      https://github.com/NineThousand/ninethousand-jobqueue
 */

use Doctrine\ORM\Mapping as ORM;

use DateTime;

/**
 * History
 *
 * @ORM\Table(name="jobqueue_history")
 * @ORM\Entity
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Job")
     * @ORM\JoinColumn(name="job", referencedColumnName="id")
     */
    protected $job;

    /**
     * @ORM\Column(nullable="true", type="datetime")
     */
    protected $timestamp;

    /**
     * @ORM\Column(nullable="true", type="string")
     */
    protected $status;

    /**
     * @ORM\Column(nullable="true", type="text", nullable="true")
     */
    protected $message;

    /**
     * @ORM\Column(nullable="true", type="string")
     */
    protected $severity;

    /**
     * @ORM\Column(type="integer")
     */
    protected $active;

    /*
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param Job $job
     */
    public function setJob(Job $job)
    {
        $this->job = $job;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     */
    public function setTimestamp(DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
