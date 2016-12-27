<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Safe Email Hook Class
 *
 * Post Controller Hook
 *
 * @package		CodeIgniter
 * @subpackage	Hooks
 * @author		Michal Sniatala <m.sniatala@gmail.com>
 * @link		https://github.com/michalsn/CodeIgniter-Safe-Email
 * @license		http://opensource.org/licenses/MIT	MIT License
 * @version		1.0
 */
class Safe_email
{
	/**
	 * CI object.
	 *
	 * @var object
	 */
	public $ci;

	/**
	 * Output string.
	 *
	 * @var string
	 */
	public $output;

	/**
	 * Class name for element.
	 *
	 * @var string
	 */
	public $class_name = 'ci-safe-email';

	//--------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->ci = get_instance();
	}

	//--------------------------------------------------------------------

	/**
	 * Initialize procedure of setting safe email
	 *
	 * @return bool
	 */
	public function initialize()
	{
		if ($this->ci->output->get_content_type() !== 'text/html')
		{
			return FALSE;
		}
		
		$this->output = $this->ci->output->get_output();

		$this->process_mailto()->process_text();

		$this->ci->output->set_output($this->output);
		$this->ci->output->_display();

		return TRUE;
	}

	//--------------------------------------------------------------------

	/**
	 * Process mailto anchors
	 *
	 * @return self
	 */
	protected function process_mailto()
	{
		// pattern for emails
		$pattern = '`\<a([^>]+)href\=\"mailto\:([^">]+)\"([^>]*)\>(.*?)\<\/a\>`ism';
		preg_match_all($pattern, $this->output, $matches, PREG_SET_ORDER);

		foreach ($matches as &$email)
		{
			// if we have empty text for anchor
			if (empty(trim($email[4])))
			{
				// delete unused tag - probably wysiwyg fault
				$this->output = str_replace($email[0], '', $this->output);
			}
			else
			{
				$attr         = trim($email[1].$email[3]);
				$safe_tag     = $this->transform_email($email[2], $email[4], $attr);
				$this->output = str_replace($email[0], $safe_tag, $this->output);
			}
		}

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Process text emails
	 *
	 * @return self
	 */
	protected function process_text()
	{
		// take code between body tags
		preg_match("/<body[^>]*>(.*?)<\/body>/is", $this->output, $body);

		if ( ! empty($body))
		{
			// pattern for text emails
			$pattern = "/[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,18}/";

			preg_match_all($pattern, $body[1], $matches);

			// make emails unique
			$emails = array_unique($matches[0]);

			foreach ($emails as &$email)
			{
				$safe_email   = $this->transform_email($email, $email, '', FALSE);
				$this->output = str_replace($email, $safe_email, $this->output);
			}
		}

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Transform emails to safe elements that scrapers can't read
	 *
	 * Based on code by Maurits van der Schee (http://www.maurits.vdschee.nl/php_hide_email/)
	 *
	 * @param string $email   Email address
	 * @param string $anchor  Tag content
	 * @param string $attr    Attributes
	 * @param bool   $is_link Is this link?
	 *
	 * @return string
	 */
	protected function transform_email($email, $content = '', $attr = '', $is_link = TRUE)
	{
		$characters  = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';

		$key         = str_replace('@', '#', str_shuffle($characters)); 
		$cipher      = ''; 
		$new_content = '';

		for ($i = 0; $i < strlen($email); $i++) 
		{
			$cipher .= $key[strpos($characters, $email[$i])];
		}

		if ($email !== $content)
		{
			// if content contains email address
			if (strpos($content, $email) !== FALSE)
			{
				$content = str_replace($email, $key, $content);
			}

			$new_content = ' data-content="'.htmlspecialchars($content).'"';
		}

		if ( ! empty($attr))
		{
			// if any attribute contains email address
			if (strpos($attr, $email) !== FALSE)
			{
				$attr = str_replace($email, $key, $attr);
			}
			$new_content .= ' data-attr="'.htmlspecialchars($attr).'"';
		}

		$link = ' data-link="false"';
		if ($is_link)
		{
			$link = ' data-link="true"';
		}

		return '<span data-key="'.$key.'" data-cipher="'.$cipher.'" data-class="'.$this->class_name.'"'.$link.$new_content.'></span>';
	}

	//--------------------------------------------------------------------

}
