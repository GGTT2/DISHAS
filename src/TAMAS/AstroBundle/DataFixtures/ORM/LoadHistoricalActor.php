<?php
namespace TAMAS\AstroBundle\DataFixtures\ORM;

use TAMAS\AstroBundle\Entity\HistoricalActor;
use TAMAS\AstroBundle\Entity\OriginalText;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class LoadHistoricalActor implements FixtureInterface
{
    public $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    function pickRand($array, $num = 1)
    {
        $rand_key = array_rand($array, $num);
        return $array[$rand_key];
    }

    function generateOriginalText($manager)
    {
        $text = new OriginalText();
        /* $editedTexts = $this->listAll('TAMASAstroBundle:EditedText', $manager);
        $ET = $manager->getRepository('TAMASAstroBundle:EditedText')->find($this->pickRand($editedTexts));
        $text->addEditedText($ET);
        $text->setTableType($ET->getTableType());
        $works = $this->listAll('TAMASAstroBundle:Work', $manager);
        $text->setWork($manager->getRepository('TAMASAstroBundle:Work')->find($this->pickRand($works)));
        $actors = $this->listAll('TAMASAstroBundle:HistoricalActor', $manager);
        $text->setHistoricalActor($manager->getRepository('TAMASAstroBundle:HistoricalActor')->find($this->pickRand($actors)));
        $places = $this->listAll('TAMASAstroBundle:Place', $manager);
        $text->setPlace($manager->getRepository('TAMASAstroBundle:Place')->find($this->pickRand($places))); */
        $text->setTextType('tabular');
        /*$primarySources = $this->listAll('TAMASAstroBundle:PrimarySource', $manager);
        $text->setPrimarySource($manager->getRepository('TAMASAstroBundle:PrimarySource')->find($this->pickRand($primarySources)));
        $text->setOriginalTextTitle('Text n°' . substr(str_shuffle($this->permitted_chars), 0, 16));
        $text->setLanguage($manager->getRepository('TAMASAstroBundle:Language')->find(2));
        $text->setScript($manager->getRepository('TAMASAstroBundle:Script')->find(2));
        $text->setTpq(rand(1200, 1500));
        $text->setTaq($text->getTpq() + 20);
        $page = rand(1, 300);
        $text->setPageMin($page . 'r');
        $text->setPageMax($page + rand(1, 12) . 'v');
        $text->setIsFolio(true); */
        return $text;
    }


    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 2; $i++) {
            $text = $this->generateOriginalText($manager);
            $text->setPublic(true);
            $manager->persist($text);

            if ($i % 50 == 0) $manager->flush();
        }
        $manager->flush();
        die;
        /*
		for ($i = 0; $i < 5000; $i++) {
            $actor = new HistoricalActor();
            $actor->setActorName('ms' . $i);
            $actor->setActorNameOriginalChar('ms' . $i);
            $actor->setPlace($manager->getRepository('TAMASAstroBundle:Place')->find(5));
            $manager->persist($actor);
        }
        */





        for ($i = 0; $i < 1000; $i++) {
            $text = new OriginalText();
            $text->addEditedText($manager->getRepository('TAMASAstroBundle:EditedText')->find(31));
            $text->setPlace($manager->getRepository('TAMASAstroBundle:Place')->find(5));
            $text->setHistoricalActor($manager->getRepository('TAMASAstroBundle:HistoricalActor')->find(15));
            $text->setPrimarySource($manager->getRepository('TAMASAstroBundle:PrimarySource')->find(5));
            $text->setTextType('Tabular');
            $text->setPublic(true);
            $manager->persist($text);



            if ($i % 50 == 0) $manager->flush();
        }


        $manager->flush();
    }
}
