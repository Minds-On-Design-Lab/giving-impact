<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Account model
 *
 * @class Account_model
 * @extends  GI_Model
 */
class Account_model extends GI_Model {

    public $id                  = false;
    public $account_name        = false;
    public $permalink           = false;
    public $created_at          = false;
    public $updated_at          = false;
    public $logo_file_name      = false;
    public $logo_content_type   = false;
    public $logo_file_size      = false;
    public $logo_updated_at     = false;
    public $accepted            = 0;
    public $first_name          = false;
    public $last_name           = false;
    public $postal_code         = false;
    public $reply_to_email      = false;
    public $street_address      = null;
    public $street_address_2    = null;
    public $city                = null;
    public $state               = null;
    public $mailing_postal_code = null;
    public $mailing_country     = null;
    public $time_zone           = false;
    public $timezone            = false;
    public $currency            = false;

	protected $_temp_file = false;
	protected $_temp_file_type = 'jpg';

    private $_logo_url = false;
    private $_thumb_url = false;

	public function __construct() {
		parent::__construct();

		$this->load->library('image_lib');
	}

    /**
     * Set new, uploaded file
     * @param String $file file path
     * @param String $type MIME type
     */
	public function set_file($file, $type) {
		$this->_temp_file = $file;
		$this->_temp_file_type = $type;
	}

    /**
     * Overrides parent class save_entry method by automatically generating
     *  * permalink
     *  * updated_at
     *  * created_at
     *
     * Also, properly handles processing and storage of attached files
     *
     */
	public function save_entry() {

        if( !$this->permalink ) {
            $this->permalink = strtolower(url_title($this->account_name));
        }

		$this->updated_at = date('Y-m-d H:i:s');

		if( !$this->created_at ) {
			$this->created_at = date('Y-m-d H:i:s');
		}

		parent::save_entry();

		if( $this->_temp_file ) {
			$this->_process_file();
		}

		return $this;
	}

    /**
     * image_url computed property
     *
     * <pre>
     *  echo $account->image_url
     * </pre>
     *
     * @return String
     */
    public function __image_url() {
		if( $this->logo_file_name && $this->_logo_url ) {
		    return $this->_logo_url;
		}

		$this->_fetch_urls();
        return $this->_logo_url;
    }

    /**
     * thumb_url computed property
     *
     * <pre>
     *  echo $account->thumb_url
     * </pre>
     *
     * @return String
     */
    public function __thumb_url() {
		if( $this->logo_file_name && $this->_thumb_url ) {
		    return $this->_thumb_url;
		}

		$this->_fetch_urls();
        return $this->_thumb_url;
    }

    private function _fetch_urls() {
		if( $this->logo_file_name ) {
			// is opportunity, so we have to generate the image URL
			$ext = substr(
				$this->logo_file_name,
				strrpos($this->logo_file_name, '.')+1
			);

            if ($this->config->item('s3_bucket')) {
                $this->_logo_url = 'https://s3.amazonaws.com/'.$this->config->item('s3_bucket').'/logos-'.$this->id.'-_original.'.$ext;
                $this->_thumb_url = 'https://s3.amazonaws.com/'.$this->config->item('s3_bucket').'/logos-'.$this->id.'-_thumb.'.$ext;
            } else {
                $this->_logo_url = base_url('/../uploads/files/logos-'.$this->id.'-_original.'.$ext);
                $this->_thumb_url = base_url('/../uploads/files/logos-'.$this->id.'-_thumb.'.$ext);
            }
		}
    }

    private function _process_file() {

		if( !$this->_temp_file ) {
			return false;
		}

		$temp_file = $this->_temp_file;
		$type = $this->_temp_file_type;

		$this->_temp_file = false;

		switch($type) {
			case 'png':
				$ext = 'png';
				break;
			case 'gif':
				$ext = 'gif';
				break;
			case 'jpg':
			default:
				$ext = 'jpg';
				break;
		}

		$config = array(
			'source_image' => $temp_file,
			'new_image' => sys_get_temp_dir().'/'.$this->id.'_thumb.'.$ext,
			'maintain_ratio' => TRUE,
			'width' => 135,
			'height' => 90
		);

		$this->image_lib->initialize($config);
		if( !$this->image_lib->resize() ) {
			return false;
		}

		$this->image_lib->clear();

		$config = array(
			'source_image' => $temp_file,
			'new_image' => sys_get_temp_dir().'/'.$this->id.'_original.'.$ext,
			'maintain_ratio' => TRUE,
			'width' => 300,
			'height' => 200
		);

		$this->image_lib->initialize($config);
		$this->image_lib->resize();

        if ($this->config->item('s3_bucket')) {
            $s3Client = \Aws\S3\S3Client::factory(array(
                'credentials' => array(
                    'key'    => $this->config->item('s3_access_key'),
                    'secret' => $this->config->item('s3_secret_key'),
                )
            ));

            $s3Client->putObject(array(
                'Bucket' => $this->config->item('s3_bucket'),
                'Key'    => 'logos-'.$this->id.'-_thumb.'.$ext,
                'Body'   => file_get_contents(sys_get_temp_dir().'/'.$this->id.'_thumb.'.$ext),
                'ACL'    => 'public-read',
            ));

            $s3Client->putObject(array(
                'Bucket' => $this->config->item('s3_bucket'),
                'Key'    => 'logos-'.$this->id.'-_original.'.$ext,
                'Body'   => file_get_contents(sys_get_temp_dir().'/'.$this->id.'_original.'.$ext),
                'ACL'    => 'public-read',
            ));
        } else {
            $store_path = rtrim(FCPATH, '/').'/../uploads/files/';

            if (!file_exists($store_path)) {
                mkdir($store_path, 0755, true);
            }

            file_put_contents(
                $store_path.'logos-'.$this->id.'-_thumb.'.$ext,
                file_get_contents(sys_get_temp_dir().'/'.$this->id.'_thumb.'.$ext)
            );

            file_put_contents(
                $store_path.'logos-'.$this->id.'-_original.'.$ext,
                file_get_contents(sys_get_temp_dir().'/'.$this->id.'_original.'.$ext)
            );

        }

		$this->logo_file_name = $this->id.'.'.$ext;
		$this->logo_content_type = 'image/'.$ext;
		$this->logo_file_size = filesize($temp_file);
		$this->logo_updated_at = date('Y-m-d H-i-s');

		@unlink($temp_file);
		@unlink($this->id.'_thumb.'.$ext);
		@unlink($this->id.'_original.'.$ext);

		$this->save_entry();
    }

}
