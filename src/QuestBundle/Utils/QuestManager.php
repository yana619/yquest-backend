<?php
namespace QuestBundle\Utils;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use QuestBundle\Entity\Chapter;
use QuestBundle\Entity\Question;
use QuestBundle\Entity\UserQuestion;

class QuestManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Quest constructor.
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $userId
     * @return array
     */
    public function getStateData($userId)
    {
        $chapters = $this->em
            ->getRepository(Chapter::class)
            ->findByIsPublished(true);

        return [
            'chapters' => (new ResponseFormatter($this->em, $userId))->chapters($chapters)
        ];
    }

    /**
     * @param $chapterUid
     * @param $userId
     * @return array
     */
    public function getNewData($chapterUid, $userId)
    {
        /** @var Chapter $chapter */
        $chapter = $this->em
            ->getRepository(Chapter::class)
            ->findOneByUid($chapterUid);

        if ($chapter) {
            $activeQuestion = $this->getActiveQuestion($chapter->getId(), $userId);

            if ($activeQuestion) {
                return [
                    'question' => (new ResponseFormatter($this->em, $userId))->question($activeQuestion)
                ];
            }
        }

        return null;
    }

    /**
     * @param $userId
     * @param $chapterUid
     * @param $answer
     * @return bool
     */
    public function checkAnswer($userId, $chapterUid, $answer)
    {
        /** @var Chapter $chapter */
        $chapter = $this->em
            ->getRepository(Chapter::class)
            ->findOneByUid($chapterUid);

        /** @var Question $question */
        $question = $this->getActiveQuestion($chapter->getId(), $userId);

        if ($question && mb_strtolower($answer) == $question->getAnswer()) {
            $user = $this->em
                ->getRepository(User::class)
                ->find($userId);
            $userQuestion = new UserQuestion();
            $userQuestion->setUser($user);
            $userQuestion->setQuestion($question);
            $this->em->persist($userQuestion);
            $this->em->flush();

            return true;
        }

        return false;
    }

    /**
     * @param $userId
     * @param $chapterUid
     * @return bool|string
     */
    public function getHint($userId, $chapterUid)
    {
        /** @var Chapter $chapter */
        $chapter = $this->em
            ->getRepository(Chapter::class)
            ->findOneByUid($chapterUid);

        /** @var Question $question */
        $question = $this->getActiveQuestion($chapter->getId(), $userId);

        if ($question) {
            if (is_null($question->getAnswer())) {
                return 'You won. No more hints =)';
            }

            $hint = $question->getHint();

            return (is_null($hint))
                ? 'It is easy! You do not need any hints!'
                : $hint;
        } else {
            return false;
        }
    }

    /**
     * @param $chapterId
     * @param $userId
     * @return mixed
     */
    private function getActiveQuestion($chapterId, $userId)
    {
        return $activeQuestion = $this->em
            ->getRepository(Question::class)
            ->getActiveQuestion($chapterId, $userId);
    }
}
