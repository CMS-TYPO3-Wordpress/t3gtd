<?php


namespace ThomasWoehlke\T3gtd\Service;

use ThomasWoehlke\T3gtd\Domain\Model\Project;

/***
 *
 * This file is part of the "Getting Things Done" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2016-2919 Thomas Woehlke <thomas.woehlke@rub.de>, Ruhr Universitaet Bochum
 *
 ***/

class ProjectService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * projectRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\ProjectRepository
     * @inject
     */
    protected $projectRepository = null;

    /**
     * taskRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\TaskRepository
     * @inject
     */
    protected $taskRepository = null;

    /**
     * contextService
     *
     * @var \ThomasWoehlke\T3gtd\Service\ContextService
     * @inject
     */
    protected $contextService = null;

    /**
     * userAccountRepository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $userAccountRepository = null;

    /**
     * createTestData
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject
     * @return void
     */
    public function createTestData(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject,
                                   \ThomasWoehlke\T3gtd\Domain\Model\Context $currentContext)
    {
        $testProject1 = new Project();
        $testProject1->setContext($currentContext);
        $testProject1->setUserAccount($userObject);
        $testProject1->setName("Project 1");
        $testProject1->setDescription("Description 1");
        $testProject1->setParent(null);

        $testProject2 = new Project();
        $testProject2->setContext($currentContext);
        $testProject2->setUserAccount($userObject);
        $testProject2->setName("Project 2");
        $testProject2->setDescription("Description 2");
        $testProject2->setParent(null);

        $testProject3 = new Project();
        $testProject3->setContext($currentContext);
        $testProject3->setUserAccount($userObject);
        $testProject3->setName("Project 3");
        $testProject3->setDescription("Description 3");
        $testProject3->setParent(null);

        $testProject1_1 = new Project();
        $testProject1_1->setContext($currentContext);
        $testProject1_1->setUserAccount($userObject);
        $testProject1_1->setName("Project 11");
        $testProject1_1->setDescription("Description 11");
        $testProject1_1->setParent($testProject1);

        $testProject1_2 = new Project();
        $testProject1_2->setContext($currentContext);
        $testProject1_2->setUserAccount($userObject);
        $testProject1_2->setName("Project 12");
        $testProject1_2->setDescription("Description 12");
        $testProject1_2->setParent($testProject1);

        $testProject1_3 = new Project();
        $testProject1_3->setContext($currentContext);
        $testProject1_3->setUserAccount($userObject);
        $testProject1_3->setName("Project 13");
        $testProject1_3->setDescription("Description 13");
        $testProject1_3->setParent($testProject1);

        $testProject1_3_1 = new Project();
        $testProject1_3_1->setContext($currentContext);
        $testProject1_3_1->setUserAccount($userObject);
        $testProject1_3_1->setName("Project 131");
        $testProject1_3_1->setDescription("Description 131");
        $testProject1_3_1->setParent($testProject1_3);

        $testProject1_3_2 = new Project();
        $testProject1_3_2->setContext($currentContext);
        $testProject1_3_2->setUserAccount($userObject);
        $testProject1_3_2->setName("Project 132");
        $testProject1_3_2->setDescription("Description 132");
        $testProject1_3_2->setParent($testProject1_3);


        $testProject1->addChild($testProject1_1);
        $testProject1->addChild($testProject1_2);
        $testProject1->addChild($testProject1_3);

        $testProject1_3->addChild($testProject1_3_1);
        $testProject1_3->addChild($testProject1_3_2);

        $this->projectRepository->add($testProject1);
        $this->projectRepository->add($testProject2);
        $this->projectRepository->add($testProject3);
        $this->projectRepository->add($testProject1_1);
        $this->projectRepository->add($testProject1_2);
        $this->projectRepository->add($testProject1_3);
        $this->projectRepository->add($testProject1_3_1);
        $this->projectRepository->add($testProject1_3_2);
    }

    /**
     * @param Project $srcProject
     * @param Project|null $targetProject
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $context
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function moveProjectToRootProject(
        \ThomasWoehlke\T3gtd\Domain\Model\Project $srcProject,
        \ThomasWoehlke\T3gtd\Domain\Model\Context $context)
    {
        $oldParent = $srcProject->getParent();
        if($oldParent != null){
            $oldParent->removeChild($srcProject);
            $this->projectRepository->update($oldParent);
        }
        $srcProject->setParent(null);
        $srcProject->setContext($context);
        $this->projectRepository->update($srcProject);
    }

    /**
     * @param Project $srcProject
     * @param Project|null $targetProject
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $context
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function moveProjectToProject(
        \ThomasWoehlke\T3gtd\Domain\Model\Project $srcProject,
        \ThomasWoehlke\T3gtd\Domain\Model\Project $targetProject=null,
        \ThomasWoehlke\T3gtd\Domain\Model\Context $context)
    {
        if($targetProject != null){
            $oldParent = $srcProject->getParent();
            if($oldParent != null){
                $oldParent->removeChild($srcProject);
                $this->projectRepository->update($oldParent);
            }
            $srcProject->setParent($targetProject);
            $srcProject->setContext($context);
            $this->projectRepository->update($srcProject);
            $targetProject->addChild($srcProject);
            $this->projectRepository->update($targetProject);
        }
    }
}