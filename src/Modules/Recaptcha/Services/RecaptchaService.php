<?php

namespace Modules\Recaptcha\Services;

use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

class RecaptchaService
{
    /**
     * Create an assessment to analyze the risk of a UI action.
     * @param string $recaptchaKey The reCAPTCHA key associated with the site/app
     * @param string $token The generated token obtained from the client.
     * @param string $project Your Google Cloud Project ID.
     * @param string $action Action name corresponding to the token.
     */
    public function createAssessment(
        string $recaptchaKey,
        string $token,
        string $project,
        string $action
    ) {
        // Create the reCAPTCHA client.
        $client = new RecaptchaEnterpriseServiceClient();
        $projectName = $client->projectName($project);

        // Set the properties of the event to be tracked.
        $event = (new Event())
            ->setSiteKey($recaptchaKey)
            ->setToken($token)
            ->setUserIpAddress($_SERVER['REMOTE_ADDR'])
            ->setUserAgent($_SERVER['HTTP_USER_AGENT']);

        // Build the assessment request.
        $assessment = (new Assessment())
            ->setEvent($event);

        try {
            $response = $client->createAssessment(
                $projectName,
                $assessment
            );

            if ($response->getTokenProperties()->getValid() == false) {
                return InvalidReason::name($response->getTokenProperties()->getInvalidReason());
            }

            return $response->getRiskAnalysis()->getScore();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
