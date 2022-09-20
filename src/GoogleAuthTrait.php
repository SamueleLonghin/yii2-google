<?php

namespace samuelelonghin\google\auth;

use Google\Client;

trait  GoogleAuthTrait
{

    private function checkServiceAccountCredentialsFile()
    {
        // service account creds
        $application_creds = Yii::$app->params['service-account-credentials'];

        return file_exists($application_creds) ? $application_creds : false;
    }

    private function missingServiceAccountDetailsWarning()
    {
        $ret = "
    <h3 class='warn'>
      Warning: You need download your Service Account Credentials JSON from the
      <a href='http://developers.google.com/console'>Google API console</a>.
    </h3>
    <p>
      Once downloaded, move them into the root directory of this repository and
      rename them 'service-account-credentials.json'.
    </p>
    <p>
      In your application, you should set the GOOGLE_APPLICATION_CREDENTIALS environment variable
      as the path to this file, but in the context of this example we will do this for you.
    </p>";

        return $ret;
    }

     /**
     * @throws Exception
     */
    public function getClient(): ?Client
    {
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

        $this->_client->addScope("https://www.googleapis.com/auth/drive");
        return $this->_client;
    }
}