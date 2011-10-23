<?php

namespace NineThousand\Bundle\NineThousandJobqueueBundle\Vendor\Doctrine\Adapter\Job;

/**
 * Symfony2DoctrineJobAdapter designates the use of doctrine ORM entities in Jobqueue within a symfony2 oriented project.
 *
 * @category  NineThousand
 * @package   Jobqueue
 * @author    Jesse Greathouse <jesse.greathouse@gmail.com>
 * @copyright 2011 NineThousand (https://github.com/organizations/NineThousand)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @link      https://github.com/NineThousand/ninethousand-jobqueue
 */

use NineThousand\Jobqueue\Adapter\Job\Exception\UnmappedAdapterTypeException;
use NineThousand\Jobqueue\Adapter\Job\JobAdapterInterface;
use NineThousand\Bundle\NineThousandJobqueueBundle\Entity\Job as JobEntity;
use NineThousand\Bundle\NineThousandJobqueueBundle\Entity\History;
use NineThousand\Bundle\NineThousandJobqueueBundle\Entity\Param;
use NineThousand\Bundle\NineThousandJobqueueBundle\Entity\Tag;
use NineThousand\Bundle\NineThousandJobqueueBundle\Entity\Arg;

use Doctrine\ORM\EntityManager;

class Symfony2DoctrineJobAdapter implements JobAdapterInterface
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var JobEntity
     */
    private $jobEntity;

    /**
     * @var string name of the jobcontrol adapter to use when running jobs
     */
    private $adapterClass;

    /**
     * @var array the options as defined by the service params
     */
    private $options = array();

    /**
     * @var object the logger used in the application
     */
    private $logger;


    /**
     * Constructs the object.
     *
     * @param array $options
     * @param JobEntity $jobEntity
     * @param EntityManager $em
     */
    public function __construct(array $options, JobEntity $jobEntity, EntityManager $em, $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->jobEntity = $jobEntity;
        $this->options = $options;

        try {
            $adapterClass = $this->options['jobcontrol']['type_mapping'][$this->getType()];
        } catch (UnmappedAdapterTypeException $e) {

        }

        $this->adapterClass = new $adapterClass();
        $this->adapterClass->setLogger($this->logger);
    }

    /**
     * Duplicates a job with similar properties to the original.
     *
     * @return self
     */
    public function spawn()
    {
        $entity = new JobEntity();
        $entity->setRetry($this->jobEntity->getRetry());
        $entity->setCooldown($this->jobEntity->getCooldown());
        $entity->setMaxRetries($this->jobEntity->getMaxRetries());
        $entity->setExecutable($this->jobEntity->getExecutable());
        $entity->setType($this->jobEntity->getType());
        $entity->setParent($this->jobEntity);

        $entity->setActive(1);
        $this->em->persist($entity);
        $this->em->flush();

        //instantiate duplicated job adapter and set params, args, tags
        $jobAdapter = new self($this->options, $entity, $this->em, $this->logger);

        if (count($params = $this->getParams())) {
            $jobAdapter->setParams($params);
        }
        if (count($args = $this->getArgs())) {
            $jobAdapter->setArgs($args);
        }
        if (count($tags = $this->getTags())) {
            $jobAdapter->setTags($tags);
        }

        return $jobAdapter;
    }

    /**
     * Creates a new instantiation of a DoctrineJobAdapter object.
     *
     * @return DoctrineJobAdapter
     */
    public static function factory($options, $entity, $em, $logger)
    {
        return new self($options, $entity, $em, $logger);
    }

    /**
     * Takes an array of command line, params and args and tranforms it into something that can be run.
     *
     * @param array $input
     * @return string
     */
    public function getExecLine(array $input)
    {
        return $this->adapterClass->getExecLine($input);
    }

    /**
     * Runs an arbitrary command line and returns a variable containing status, message, and severity.
     *
     * @param string $execLine
     * @return array
     */
    public function run($execLine)
    {
        $this->preRun();
        $output = $this->adapterClass->run($execLine);
        $this->postRun();

        return $output;
    }

    /**
     * method to run before run is called
     */
    public function preRun()
    {

    }

    /**
     * method to run after run is called
     */
    public function postRun()
    {

    }

    /**
     * Appends a new log message to the log.
     *
     * @param string $severity
     * @param string $message
     */
    public function log($severity, $message)
    {
        $this->adapterClass->log($severity, $message);
    }

    /**
     * Calls refresh on the current entity -- refreshes the persistence state
     *
     */
    public function refresh()
    {
        $this->em->refresh($this->jobEntity);
    }


    /**
     * @return int
     */
    public function getId()
    {
        $this->refresh();

        return $this->jobEntity->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        $this->refresh();

        return $this->jobEntity->getName();
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->jobEntity->setName($name);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return int
     */
    public function getRetry()
    {
        $this->refresh();

        return $this->jobEntity->getRetry();
    }

    /**
     * @param int $retry
     */
    public function setRetry($retry)
    {
        $this->jobEntity->setRetry($retry);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return int
     */
    public function getCooldown()
    {
        $this->refresh();

        return $this->jobEntity->getCooldown();
    }

    /**
     * @param int $cooldown
     */
    public function setCooldown($cooldown)
    {
        $this->jobEntity->setCooldown($cooldown);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return int
     */
    public function getMaxRetries()
    {
        $this->refresh();

        return $this->jobEntity->getMaxRetries();
    }

    /**
     * @param int $maxRetries
     */
    public function setMaxRetries($maxRetries)
    {
        $this->jobEntity->setMaxRetries($maxRetries);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return int
     */
    public function getAttempts()
    {
        $this->refresh();

        return $this->jobEntity->getAttempts();
    }

    /**
     * @param int $attempts
     */
    public function setAttempts($attempts)
    {
        $this->jobEntity->setAttempts($attempts);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getExecutable()
    {
        $this->refresh();

        return $this->jobEntity->getExecutable();
    }

    /**
     * @param string $executable
     */
    public function setExecutable($executable)
    {
        $this->jobEntity->setExecutable($executable);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $params = array();
        $this->refresh();

        foreach ($this->jobEntity->getParams() as $param) {
            $params[$param->get()] = $param->getValue();
        }

        return $params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        //deactivate current associations
        foreach($this->jobEntity->getParams() as $param) {
            $param->setActive(0);
        }
        $this->em->flush();

        //create new params
        foreach ($params as $key => $value) {
            $param = new Param();
            $param->setKey($key);
            $param->setValue($value);
            $param->setJob($this->jobEntity);
            $param->setActive(1);
            $this->em->persist($param);
        }
        $this->em->flush();
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        $args = array();
        $this->refresh();
        foreach ($this->jobEntity->getArgs() as $arg) {
            $args[] = $arg->getValue();
        }

        return $args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args)
    {
       //deactivate current associations
        foreach ($this->jobEntity->getArgs() as $arg) {
            $arg->setActive(0);
        }
        $this->em->flush();

        //create new params
        foreach ($args as $value) {
            $arg = new Arg();
            $arg->setValue($value);
            $arg->setJob($this->jobEntity);
            $arg->setActive(1);
            $this->em->persist($arg);
        }
        $this->em->flush();
    }

    /**
     * @return array
     */
    public function getTags()
    {
        $tags = array();
        $this->refresh();
        foreach ($this->jobEntity->getTags() as $tag) {
            $tags[] = $tag->getValue();
        }

        return $tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        //deactivate current associations
        foreach ($this->jobEntity->getTags() as $tag) {
            $tag->setActive(0);
        }
        $this->em->flush();

        //create new params
        foreach ($tags as $value) {
            $tag = new Tag();
            $tag->setValue($value);
            $tag->setJob($this->jobEntity);
            $tag->setActive(1);
            $this->em->persist($tag);
        }
        $this->em->flush();
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $this->refresh();
        $history = array();
        foreach ($this->jobEntity->getHistory() as $log) {
            $history[] = array(
                'timestamp' => $log->getTimestamp(),
                'severity'  => $log->getSeverity(),
                'message'   => $log->getMessage(),
                'status'    => $log->getStatus(),
                'job'       => $log->getJob(),
                'id'        => $log->getId(),
            );
        }

        return $history;
    }

    /**
     * @param array $result
     */
    public function addHistory(array $result)
    {
        //add a history entry
        $history = new History();
        $history->setJob($this->jobEntity);
        $history->setTimestamp($this->getLastrunDate());
        $history->setStatus($result['status']);
        $history->setSeverity($result['severity']);
        $history->setMessage($result['message']);
        $history->setActive(1);
        $this->em->persist($history);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $this->refresh();

        return $this->jobEntity->getStatus();
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->jobEntity->setStatus($status);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getType()
    {
        $this->refresh();

        return $this->jobEntity->getType();
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->jobEntity->setType($type);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate()
    {
        $this->refresh();

        return $this->jobEntity->getCreateDate();
    }

    /**
     * @param \DateTime $date
     */
    public function setCreateDate(\DateTime $date)
    {
        $this->jobEntity->setCreateDate($date);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return \DateTime
     */
    public function getLastrunDate()
    {
        $this->refresh();

        return $this->jobEntity->getLastrunDate();
    }

    /**
     * @param \DateTime $date
     */
    public function setLastRunDate(\DateTime $date)
    {
        $this->jobEntity->setLastRunDate($date);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return int
     */
    public function getActive()
    {
        $this->refresh();

        return $this->jobEntity->getActive();
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->jobEntity->setActive($active);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getSchedule()
    {
        $this->refresh();

        return $this->jobEntity->getSchedule();
    }

    /**
     * @param string $schedule
     */
    public function setSchedule($schedule)
    {
        $this->jobEntity->setSchedule($schedule);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }

    /**
     * @return int
     */
    public function getParent()
    {
        $this->refresh();

        return $this->jobEntity->getParent();
    }

    /**
     * @param int $parent
     */
    public function setParent($parent)
    {
        $this->jobEntity->setParent($parent);
        $this->em->persist($this->jobEntity);
        $this->em->flush();
    }
}
