<?php
/**
 * Intercom is a customer relationship management and messaging tool for web app owners
 * 
 * This library provides connectivity with the Intercom API (http://doc.intercom.io/api/)
 * 
 * Basic usage:
 * 
 * 1. Configure Intercom with your access credentials
 * <code>
 * <?php
 * $intercom = new Intercom('YOUR_APP_ID', 'YOUR_API_KEY');
 * ?>
 * </code>
 * 
 * 2. Make requests to the API
 * <code>
 * <?php
 * $intercom = new Intercom('YOUR_APP_ID', 'YOUR_API_KEY');
 * $users = $intercom->getAllUsers();
 * var_dump($users);
 * ?>
 * </code>
 * 
 * @author    Bruno Pedro <bruno.pedro@getapp.com>
 * @copyright Copyright 2013-2014 Nubera eBusiness S.L. All rights reserved.
 * @link      http://www.nubera.com/
 * @license   http://opensource.org/licenses/MIT
 **/


/**
 * Intercom.io API 
 */
class Intercom
{
    /**
     * The Intercom API endpoint
     */
    private $apiEndpoint = 'https://api.intercom.io/v1/';

    /**
     * The Intercom application ID
     */
    private $appId = null;

    /**
     * The Intercom API key
     */
    private $apiKey = null;

    /**
     * Last HTTP error obtained from curl_errno() and curl_error()
     */
    private $lastError = null;

    /**
     * Whether we are in debug mode. This is set by the constructor
     */
    private $debug = false;

    /**
     * The constructor
     *
     * @param  string $appId  The Intercom application ID
     * @param  string $apiKey The Intercom API key
     * @param  string $debug  Optional debug flag
     * @return void
     **/
    public function __construct($appId, $apiKey, $debug = false)
    {
        $this->appId = $appId;
        $this->apiKey = $apiKey;
        $this->debug = $debug;
    }

    /**
     * Check if a given value is an e-mail address.
     *
     * @param  string $value
     * @return boolean
     **/
    protected function isEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Make an HTTP call using curl.
     * 
     * @param  string $url       The URL to call
     * @param  string $method    The HTTP method to use, by default GET
     * @param  string $post_data The data to send on an HTTP POST (optional)
     * @return object
     **/
    protected function httpCall($url, $method = 'GET', $post_data = null)
    {
        $headers = array('Content-Type: application/json');

        $ch = curl_init($url);

        if ($this->debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $headers[] = 'Content-Length: ' . strlen($post_data);
        } elseif ($method != 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 4096);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
        curl_setopt($ch, CURLOPT_USERPWD, $this->appId . ':' . $this->apiKey);

        $response = curl_exec($ch);

        // Set HTTP error, if any
        $this->lastError = array('code' => curl_errno($ch),
                                 'message' => curl_error($ch),
                                 'httpCode' => curl_getinfo($ch, CURLINFO_HTTP_CODE));


        return json_decode($response);
    }

    /**
     * Get all users from your Intercom account.
     * 
     * @param  integer $page    The results page number
     * @param  integer $perPage The number of results to return on each page
     * @return object
     **/
    public function getAllUsers($page = 1, $perPage = null)
    {
        $path = 'users/?page=' . $page;

        if (!empty($perPage)) {
            $path .= '&per_page=' . $perPage;
        }

        return $this->httpCall($this->apiEndpoint . $path);
    }

    /**
     * Get a specific user from your Intercom account.
     * 
     * @param  string $id The ID of the user to retrieve
     * @return object
     **/
    public function getUser($id)
    {
        $path = 'users/';
        if ($this->isEmail($id)) {
            $path .= '?email=';
        } else {
            $path .= '?user_id=';
        }
        $path .= urlencode($id);
        return $this->httpCall($this->apiEndpoint . $path);
    }
    
    /**
     * Get the message thread of a specific user from your Intercom account.
     * 
     * @param  string $id The ID of the user to retrieve thread for
     * @return object
     **/
    public function getThread($id)
    {
        $path = 'users/message_threads';
        if ($this->isEmail($id)) {
            $path .= '?email=';
        } else {
            $path .= '?user_id=';
        }
        $path .= urlencode($id);
        return $this->httpCall($this->apiEndpoint . $path);
    }

    /**
     * Create an new message thread associated with a user on your Intercom account
     * 
     * @param  string $userId     The ID of the user
     * @param  string $email      The email of the user (optional)
     * @param  string $body       The body of the message
     * @param  string $currentUrl The URL the user is visiting (optional)
     * @return object
     **/
    public function createThread($userId, $email = null, $body = null, $currentUrl = null)
    {
        $data = array();

        $data['user_id'] = $userId;

        if (!empty($email)) {
            $data['email'] = $email;
        }
      
        $data['body'] = $body;

        if (!empty($currentUrl)) {
            $data['current_url'] = $currentUrl;
        }
        $path = 'users/message_threads';

        return $this->httpCall($this->apiEndpoint . $path, 'POST', json_encode($data));
    }

    /**
     * Create a user on your Intercom account.
     *
     * @param  string $id                     The ID of the user to be created
     * @param  string $email                  The user's email address (optional)
     * @param  string $name                   The user's name (optional)
     * @param  array  $customData             Any custom data to be aggregate to the user's record (optional)
     * @param  long   $createdAt              UNIX timestamp describing the date and time when the user was created (optional)
     * @param  string $lastSeenIp             The last IP address where the user was last seen (optional)
     * @param  string $lastSeenUserAgent      The last user agent of the user's browser (optional)
     * @param  long   $lastRequestAt          UNIX timestamp of the user's last request (optional)
     * @param  bool   $unsubscribedFromEmails The user's email subscription status (optional)
     * @param  string $method                 HTTP method, to be used by updateUser()
     * @return object
     **/
    public function createUser($id,
                               $email = null,
                               $name = null,
                               $customData = array(),
                               $createdAt = null,
                               $lastSeenIp = null,
                               $lastSeenUserAgent = null,
                               $lastRequestAt = null,
                               $unsubscribedFromEmails = null,
                               $method = 'POST')
    {
        $data = array();

        $data['user_id'] = $id;

        if (!empty($email)) {
            $data['email'] = $email;
        }

        if (!empty($name)) {
            $data['name'] = $name;
        }

        if (!empty($createdAt)) {
            $data['created_at'] = $createdAt;
        }

        if (!empty($lastSeenIp)) {
            $data['last_seen_ip'] = $lastSeenIp;
        }

        if (!empty($lastSeenUserAgent)) {
            $data['last_seen_user_agent'] = $lastSeenUserAgent;
        }

        if (!empty($lastRequestAt)) {
            $data['last_request_at'] = $lastRequestAt;
        }

        if (!empty($customData)) {
            $data['custom_data'] = $customData;
        }

        if (is_bool($unsubscribedFromEmails)) {
            $data['unsubscribed_from_emails'] = $unsubscribedFromEmails;
        }

        $path = 'users';
        return $this->httpCall($this->apiEndpoint . $path, $method, json_encode($data));
    }

    /**
     * Update an existing user on your Intercom account.
     *
     * @param  string $id                     The ID of the user to be updated
     * @param  string $email                  The user's email address (optional)
     * @param  string $name                   The user's name (optional)
     * @param  array  $customData             Any custom data to be aggregate to the user's record (optional)
     * @param  long   $createdAt              UNIX timestamp describing the date and time when the user was created (optional)
     * @param  string $lastSeenIp             The last IP address where the user was last seen (optional)
     * @param  string $lastSeenUserAgent      The last user agent of the user's browser (optional)
     * @param  long   $lastRequestAt          UNIX timestamp of the user's last request (optional)
     * @param  bool   $unsubscribedFromEmails The user's email subscription status (optional)
     * @return object
     **/
    public function updateUser($id,
                               $email = null,
                               $name = null,
                               $customData = array(),
                               $createdAt = null,
                               $lastSeenIp = null,
                               $lastSeenUserAgent = null,
                               $lastRequestAt = null,
                               $unsubscribedFromEmails = null)
    {
        return $this->createUser($id, $email, $name, $customData, $createdAt, $lastSeenIp, $lastSeenUserAgent, $lastRequestAt, $unsubscribedFromEmails, 'PUT');
    }

    /**
     * Delete an existing user from your Intercom account
     * 
     * @param  string $id The ID of the user to be deleted
     * @return object
     **/
    public function deleteUser($id)
    {
        $path = 'users/';
        if ($this->isEmail($id)) {
            $path .= '?email=';
        } else {
            $path .= '?user_id=';
        }
        $path .= urlencode($id);
        return $this->httpCall($this->apiEndpoint . $path, 'DELETE');
    }

    /**
     * Create an impression associated with a user on your Intercom account
     * 
     * @param  string $userId     The ID of the user
     * @param  string $email      The email of the user (optional)
     * @param  string $userIp     The IP address of the user (optional)
     * @param  string $userAgent  The user agent of the user (optional)
     * @param  string $currentUrl The URL the user is visiting (optional)
     * @return object
     **/
    public function createImpression($userId, $email = null, $userIp = null, $userAgent = null, $currentUrl = null)
    {
        $data = array();

        $data['user_id'] = $userId;

        if (!empty($email)) {
            $data['email'] = $email;
        }

        if (!empty($userIp)) {
            $data['user_ip'] = $userIp;
        }

        if (!empty($userAgent)) {
            $data['user_agent'] = $userAgent;
        }

        if (!empty($currentUrl)) {
            $data['current_url'] = $currentUrl;
        }
        $path = 'users/impressions';

        return $this->httpCall($this->apiEndpoint . $path, 'POST', json_encode($data));
    }

    /**
     * Create an event associated with a user on your Intercom account
     * 
     * @param  string $userId     The ID of the user
     * @param  string $eventName  Tge name of the event
     * #param  array  $metadata   The metadata associated with the event (optional) 
     * @param  string $email      The email of the user (optional)
     * @param  string $created    The time at which the event occurred (optional)
     * @return object
     **/
    public function createEvent($userId, $eventName, $metadata = null, $email = null, $created = null)
    {
        $data = array();

        $data['user_id'] = $userId;

        if (!empty($eventName)) {
            $data['event_name'] = $eventName;
        }

        if (!empty($email)) {
            $data['email'] = $email;
        }

        if (!empty($metadata)) {
            $data['metadata'] = $metadata;
        }

        if (!empty($created)) {
            $data['created'] = $created;
        } else {
            $data['created'] = time();
        }

        $path = 'events/';

        return $this->httpCall(
            str_replace('/v1', '', $this->apiEndpoint) . $path,
            'POST',
            json_encode($data)
        );
    }

    /**
     * Get the last error from curl.
     * 
     * @return array Array with 'code', 'message' and 'httpCode' indexes
     */
    public function getLastError()
    {
        return $this->lastError;
    }


    /**
     * Get a specific tag from your Intercom account.
     * 
     * @param  string $name The Name of the tag to retrieve
     * @return object
     **/
    public function getTag($name)
    {
        $path = 'tags/';
        $path .= '?name=' . urlencode($name);
        return $this->httpCall($this->apiEndpoint . $path);
    }


    /**
     * Create a tag on your Intercom account.
     * 
     * @param  string $name         The tag's name (required)
     * @param  array  $emails       Array of users to tag (optional)
     * @param  array  $userIds      Array of user ids to tag (optional)
     * @param  string $color        The color of the tag (must be "green", "red", "teal", "gold", "blue", or "purple").
     * @param  string $action       required (if emails or userIds are not empty) — either "tag" or "untag"
     * @param  string $method       HTTP method, to be used by updateTag()
     * @return object
     **/
    public function createTag($name,
                               $emails = null,
                               $userIds = null,
                               $color = null,
                               $action = null,
                               $method = "POST")
    {
        $data = array();

        $data['name'] = $name;

        if (!empty($email)) {
            $data['email'] = $email;
        }

        if (!empty($action)) {
            $data['tag_or_untag'] = $action;
        }

        if (!empty($emails)) {      
            $data['emails'] = $emails;
        }

        if (!empty($userIds)) {     
            $data['user_ids'] = $userIds;
        }

        if (!empty($color)) {
            $data['color'] = $color;
        }

        $path = 'tags';
        return $this->httpCall($this->apiEndpoint . $path, $method, json_encode($data));
    }

    /**
     * Create a tag on your Intercom account.
     * 
     * @param  string $name         The tag's name (required)
     * @param  array  $emails       Array of users to tag (optional)
     * @param  array  $userIds      Array of user ids to tag (optional)
     * @param  string $color        The color of the tag (must be "green", "red", "teal", "gold", "blue", or "purple").
     * @param  string $action       required (if emails or userIds are not empty) — either "tag" or "untag"
     * @return object
     **/
    public function updateTag($name,
                               $emails = null,
                               $userIds = null,
                               $color = null,
                               $action = null)
    {
        return $this->createTag($name, $emails, $userIds, $color, $action, 'PUT');

    }
}
?>
