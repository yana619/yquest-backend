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
     * @param bool $displayAnswer
     * @return array
     */
    public function questions($questions = [], $displayAnswer = false)
    {
        return array_map(function (Question $question) use ($displayAnswer) {
            return $this->question($question, $displayAnswer);
        }, $questions);
    }

    /**
     * @param Question $question
     * @param bool $displayAnswer
     * @return array
     */
    public function question(Question $question, $displayAnswer = false)
    {
        return [
            'id' => $question->getUid(),
            'title' => $question->getTitle(),
            'content' => $question->getContent(),
            'position' => $question->getPosition(),
            'answer' => ($displayAnswer) ? $question->getAnswer() : null
        ];
    }

    /**
     * @param $chapter
     * @return array
     */
    protected function composeQuestions($chapter)
    {
        $questions = $this->questions(
            $this->getAnsweredQuestions($chapter),
            true
        );

        if (($activeQuestion = $this->getActiveQuestion($chapter))) {
            $questions[] = $this->question($activeQuestion, false);
        }

        return $questions;
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
