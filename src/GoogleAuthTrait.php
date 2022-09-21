<?php

namespace samuelelonghin\google\auth;

use Google\Client;
use Google\Exception;
use Yii;

trait GoogleAuthTrait
{
    private ?Client $_client = null;

    private function checkServiceAccountCredentialsFile()
    {
        // service account creds
        $application_creds = Yii::$app->params['google-service-account-credentials'];
        return file_exists($application_creds) ? $application_creds : false;
    }

    /**
     * @throws Exception|Exception
     */
    public function getClient(): ?Client
    {
        if (!$this->_client) {
            $this->_client = new Client();

            if ($credentials_file = $this->checkServiceAccountCredentialsFile()) {
                // set the location manually
                $this->_client->setAuthConfig($credentials_file);
            } elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
                // use the application default credentials
                $this->_client->useApplicationDefaultCredentials();
            } else {
                echo $this->missingServiceAccountDetailsWarning();
                return null;
            }
        }
        return $this->_client;
    }

    private function missingServiceAccountDetailsWarning(): string
    {
        return "
    <h3 class='warn'>
      Warning: You need download your Service Account Credentials JSON from the
      <a href='https://developers.google.com/console'>Google API console</a>.
    </h3>
    <p>
      Once downloaded, move them into the root directory of this repository and
      rename them 'service-account-credentials.json'.
    </p>
    <p>
      In your application, you should set the GOOGLE_APPLICATION_CREDENTIALS environment variable
      as the path to this file, but in the context of this example we will do this for you.
    </p>";
    }
}