<?php

namespace Modules\JsonRPC\Procedures;

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

class HeadMergeRequest
{
    public function process(string $ttsKey)
    {
        //$this->createJiraIssue();
        return $ttsKey;
    }

    protected function createJiraIssue()
    {
        try {
            $issueField = new IssueField();

            $issueField->setProjectKey("FIRS")
                ->setSummary("test create issue from mmpi jsonrpc api")
                ->setAssigneeName("yarnaudov")
                ->setIssueType("Internal")
                ->setDescription("Full description for issue");

            $issueService = new IssueService();

            $result = $issueService->create($issueField);

            var_dump($result);
        } catch (JiraException $e) {
            var_dump($e->getMessage());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
