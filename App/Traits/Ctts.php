<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;
use JiraRestApi\JiraException;

trait Ctts
{
    /**
     * Get issue data
     *
     * @param string $username
     * @param Collection $sources
     * @param string $tts_id
     *
     * @return IssueField
     *
     * @throws \Exception
     */
    protected function getIssue(string $username, Collection $sources, string $tts_id = '') : IssueField
    {
        $ttsId = $tts_id ? $tts_id : $sources->first()['tts_id'];

        $issueField = new IssueField();

        // Get sources list
        $sources = implode(
            PHP_EOL,
            $sources->map(function ($item) {
                $sourceFile = isset($item['source_file']) ? $item['source_file'] : $item['name'];
                $sourceRevision = isset($item['revision']) ? $item['revision'] : $item['version'];
                return "{$sourceFile} - {$sourceRevision}";
            })->all()
        );

        $issueField
            ->setProjectKey('CVSHEAD')
            ->setSummary("Commit on Head the changes done in {$ttsId}")
            ->setAssigneeName($username)
            ->setIssueType('Short Task')
            ->setPriorityName('Normal')
            ->setDescription("
                The test of task {$ttsId} is completed OK. Please merge your changes in the HEAD. 
                If not already done in another task, please do the merge on ALPHA/ALPHADC instances.
                *Sources:* 
                {$sources}
            ")
            ->addLabel('MMPI_AUTO');

        // Set specification
        $issueField->addCustomField('customfield_10140', 'n/a');

        // Set sub-project
        $issueField->addCustomField('customfield_10123', 'n/a');

        // Set Milestone
        $issueField->addCustomField('customfield_10530', ['value' => 'Installation']);

        // Set Codix status
        $issueField->addCustomField('customfield_10601', ['value' => 'Under Investigation']);

        // Set DDCA
        try {
            $today = new \DateTime();
            $issueField->addCustomField(
                'customfield_10606',
                $today->modify('+5 day')->format('Y-m-d')
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $issueField;
    }

    /**
     * Create issues - batch
     *
     * @param string $username
     * @param Collection $sources
     * @param string $tts_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function createIssue(string $username, Collection $sources, string $tts_id = '')
    {
        $issue = $this->getIssue($username, $sources, $tts_id);

        $issueService = new IssueService();
        $newIssue = $issueService->create($issue);

        // Update reporter so we have initial reporter in history
        $issue->setReporterName($username);
        $issueService->update($newIssue->key, $issue);

        return $newIssue;
    }

    /**
     * Get link data
     *
     * @param string|int $inwardIssue
     * @param string|int $outwardIssue
     * @return IssueLink
     */
    protected function getLink($inwardIssue, $outwardIssue) : IssueLink
    {
        $issueLink = new IssueLink();

        $issueLink->setInwardIssue($inwardIssue)
            ->setOutwardIssue($outwardIssue)
            ->setLinkTypeName("Relate")
            ->setComment("Automatically linked to task {$outwardIssue}");

        return $issueLink;
    }

    /**
     * Link issues
     *
     * @param string|int $inwardIssue
     * @param string|int $outwardIssue
     *
     * @throws JiraException
     */
    protected function linkIssue($inwardIssue, $outwardIssue)
    {
        $issueLink = $this->getLink($inwardIssue, $outwardIssue);

        $issueLinkService = new IssueLinkService();

        $issueLinkService->addIssueLink($issueLink);
    }
}
