<?php

namespace NineThousand\Bundle\NineThousandJobqueueBundle\Entity;

/**
 * Job Entity for use with DoctrineJobAdapter in Jobqueue.
 *
 * @category  NineThousand
 * @package   Jobqueue
 * @author    Jesse Greathouse <jesse.greathouse@gmail.com>
 * @copyright 2011 NineThousand (https://github.com/organizations/NineThousand)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @link      https://github.com/NineThousand/ninethousand-jobqueue
 */

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

use DateTime;

/**
 * Job
 *
 * @ORM\Table(name="jobqueue_job")
 * @ORM\Entity(repositoryClass="NineThousand\Bundle\NineThousandJobqueueBundle\Repository\JobRepository")
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(nullable="true", type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $retry;

    /**
     * @ORM\Column(type="integer")
     */
    protected $cooldown;

    /**
     * @ORM\Column(name="max_retries", type="integer")
     */
    protected $maxRetries;

    /**
     * @ORM\Column(type="integer")
     */
    protected $attempts;

    /**
     * @ORM\Column(nullable="true", type="text")
     */
    protected $executable;

    /**
     * @ORM\Column(nullable="true", type="string")
     */
    protected $type;

    /**
     * @ORM\Column(nullable="true", type="string")
     */
    protected $status;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate;

    /**
     * @ORM\Column(name="last_run", nullable="true", type="datetime")
     */
    protected $lastrunDate;

    /**
     * @ORM\Column(type="integer")
     */
    protected $active;

    /**
     * @ORM\Column(nullable="true", type="string")
     */
    protected $schedule;

    /**
     * @ORM\ManyToOne(tagetEntity="Job")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable="true")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Param", mappedBy="job", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $params;

    /**
     * @ORM\OneToMany(targetEntity="Arg", mappedBy="job", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $args;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="jobqueue_job_tag",
     *      joinColumns={@ORM\JoinColumn(name="job_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="History", mappedBy="job")
     */
    protected $history;

    public function __construct()
    {
        $this->args = new ArrayCollection();
        $this->params = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->history = new ArrayCollection();

        $this->retry = 0;
        $this->cooldown = 0;
        $this->maxRetries = 0;
        $this->attempts = 0;
        $this->status = null;
        $this->createDate = new DateTime();
        $this->lastrunDate = null;
        $this->active = 0;
        $this->schedule = null;
        $this->parent = null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getRetry()
    {
        return $this->retry;
    }

    /**
     * @param int $retry
     */
    public function setRetry($retry)
    {
        $this->retry = $retry;
    }

    /**
     * @return int
     */
    public function getCooldown()
    {
        return $this->cooldown;
    }

    /**
     * @param int $cooldown
     */
    public function setCooldown($cooldown)
    {
        $this->cooldown = $cooldown;
    }

    /**
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * @param int $maxRetries
     */
    public function setMaxRetries($maxRetries)
    {
        $this->maxRetries = $maxRetries;
    }

    /**
     * @return int
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * @param int $attempts
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
    }

    /**
     * @return string
     */
    public function getExecutable()
    {
        return $this->executable;
    }

    /**
     * @param string $executable
     */
    public function setExecutable($executable)
    {
        $this->executable = $executable;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @param DateTime $createDate
     */
    public function setCreateDate(DateTime $createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * @return DateTime
     */
    public function getLastrunDate()
    {
        return $this->lastrunDate;
    }

    /**
     * @param DateTime|null $lastrunDate
     */
    public function setLastrunDate(DateTime $lastrunDate = null)
    {
        $this->lastrunDate = $lastrunDate;
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

    /**
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param string $schedule
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @return Job
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Job $parent
     */
    public function setParent(Job $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return ArrayCollection
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param ArrayCollection $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    public function addParam(Param $param)
    {
        $this->params[] = $param;
        $param->setJob($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param ArrayCollection $args
     */
    public function setArgs($args)
    {
        $this->args = $args;
    }

    public function addArg(Arg $arg)
    {
        $this->args[] = $arg;
        $arg->setJob($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return ArrayCollection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param ArrayCollection $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }
}
