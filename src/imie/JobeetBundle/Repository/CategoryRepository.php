<?php

namespace imie\JobeetBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
	/**
	 * Get the jobs that are not expired
	 */
	public function getWithJobs()
	{
		$query = $this->getEntityManager()->createQuery(
			'SELECT c FROM imieJobeetBundle:Category c LEFT JOIN c.jobs j WHERE j.expires_at > :date'
			)->setParameter('date', date('Y-m-d H:i:s', time()));

		return $query->getResult();
	}   
}
