<?php
namespace QuestBundle\Utils;

use Doctrine\ORM\EntityManager;
use QuestBundle\Entity\Chapter;
use QuestBundle\Entity\Question;

class ResponseFormatter
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var int
     */
    protected $userId;

    /**
     * ResponseFormatter constructor.
     * @param $entityManager
     * @param int $userId
     */
    public function __construct($entityManager, $userId)
    {
        $this->em = $entityManager;
        $this->userId = $userId;
    }

    /**
     * @param array $chapters
     * @return array
     */
    public function chapters($chapters = [])
    {
        return array_map(function (Chapter $chapter) {
            return $this->chapter($chapter);
        }, $chapters);
    }

    /**
     * @param Chapter $chapter
     * @return array
     */
    public function chapter(Chapter $chapter)
    {
        return [
            'id' => $chapter->getUid(),
            'title' => $chapter->getTitle(),
            'content' => $chapter->getContent(),
            'lockCount' => $chapter->getQuestions()->count(),
            'questions' => $this->composeQuestions($chapter)
        ];
    }

    /**
     * @param array $questions
     * @return array
     */
    public function questions($questions = [])
    {
        return array_map(function (Question $question) {
            return $this->question($question);
        }, $questions);
    }

    /**
     * @param Question $question
     * @return array
     */
    public function question(Question $question)
    {
        return [
            'id' => $question->getUid(),
            'title' => $question->getTitle(),
            'content' => $question->getContent(),
        ];
    }

    /**
     * @param $chapter
     * @return array
     */
    protected function composeQuestions($chapter)
    {
        $questions = $this->getAnsweredQuestions($chapter);

        if (($activeQuestion = $this->getActiveQuestion($chapter))) {
            $questions[] = $activeQuestion;
        }


        return  $this->questions($questions);
    }

    /**
     * @param Chapter $chapter
     * @return array
     */
    protected function getAnsweredQuestions(Chapter $chapter)
    {
        return $this->em
            ->getRepository(Question::class)
            ->getAnsweredByUserQuestions($chapter->getId(), $this->userId);
    }

    /**
     * @param Chapter $chapter
     * @return mixed
     */
    protected function getActiveQuestion(Chapter $chapter)
    {
        return $this->em
            ->getRepository(Question::class)
            ->getActiveQuestion($chapter->getId(), $this->userId);
    }
}
