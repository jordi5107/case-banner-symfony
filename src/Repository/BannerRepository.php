<?php 


namespace App\Repository;
use App\Entity\Banner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banner::class);
    }

    public function findActiveBanners(string $localeCode, \DateTimeImmutable $now): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.translations', 't')
            ->join('t.locale', 'l')
            ->where('b.active = :active')
            ->andWhere('b.startDate <= :now')
            ->andWhere('b.endDate >= :now')
            ->andWhere('l.code = :locale')
            ->andWhere('l.active = :localeActive')
            ->setParameter('active', true)
            ->setParameter('now', $now)
            ->setParameter('locale', $localeCode)
            ->setParameter('localeActive', true)
            ->getQuery()
            ->getResult();
    }
}
