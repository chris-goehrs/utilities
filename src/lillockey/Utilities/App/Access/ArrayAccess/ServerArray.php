<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 11/6/2015
 * Time: 1:31 PM
 */

namespace lillockey\Utilities\App\Access\ArrayAccess;


use lillockey\Utilities\Exceptions\NotAnArrayException;

/**
 * <h1>Class ServerArray</h1>
 * <p>
 * <em>Descriptions taken from <a href="http://php.net/manual/en/reserved.variables.server.php">here</a></em>
 * </p>
 *
 * @package lillockey\Utilities\App\Access\ArrayAccess
 */
class ServerArray extends AccessibleArray
{
    public function __construct()
    {
        if(!isset($_SERVER)) throw new NotAnArrayException('$_SERVER is not a valid array');
        if(!is_array($_SERVER)) throw new NotAnArrayException('$_SERVER is not a valid array');
        parent::__construct($_SERVER);
    }

	///////////////////////////////////////////////////////////////////////
	// Helpers
	///////////////////////////////////////////////////////////////////////

    /**
     * Special check for the host.  This checks against multiple values including:<ul>
     * <li>HTTP_HOST</li>
     * <li>SERVER_NAME</li>
     * <li>SERVER_ADDR</li></ul>
     * @return null|string
     */
    public function getHost()
    {
        $raw_host = $this->string(array('HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR'));
        $split_host = explode(':', $raw_host);
        return $split_host[0];
    }

	/**
	 * @return bool
	 */
	public function requestIsGet()
	{
		return $this->getRequestMethod() == 'GET';
	}

	/**
	 * @return bool
	 */
	public function requestIsPost()
	{
		return $this->getRequestMethod() == 'POST';
	}

	///////////////////////////////////////////////////////////////////////
	// Raw getters
	///////////////////////////////////////////////////////////////////////

	/**
	 * Array of arguments passed to the script. When the script is run on the command line, this gives C-style access to the command line parameters. When called via the GET method, this will contain the query string.
	 * @return array|null
	 */
	public function getArgv()
	{
		return $this->v_array('argv');
	}

	/**
	 * Contains the number of command line parameters passed to the script (if run on the command line).
	 * @return int|null
	 */
	public function getArgc()
	{
		return $this->int('argc');
	}

	/**
	 * What revision of the CGI specification the server is using; i.e. 'CGI/1.1'.
	 * @return null|string
	 */
	public function getGatewayInterface()
	{
		return $this->string('GATEWAY_INTERFACE');
	}

	/**
	 * The IP address of the server under which the current script is executing.
	 * @return null|string
	 */
	public function getServerAddress()
	{
		return $this->string('SERVER_ADDR');
	}

	/**
	 * The name of the server host under which the current script is executing. If the script is running on a virtual host, this will be the value defined for that virtual host.
	 * @return null|string
	 */
	public function getServerName()
	{
		return $this->string('SERVER_NAME');
	}

	/**
	 * Server identification string, given in the headers when responding to requests.
	 * @return null|string
	 */
	public function getServerSoftware()
	{
		return $this->string('SERVER_SOFTWARE');
	}

	/**
	 * Name and revision of the information protocol via which the page was requested; i.e. 'HTTP/1.0';
	 * @return null|string
	 */
	public function getServerProtocol()
	{
		return $this->string('SERVER_PROTOCOL');
	}

	/**
	 * Which request method was used to access the page; i.e. 'GET', 'HEAD', 'POST', 'PUT'.
	 * @return null|string
	 */
	public function getRequestMethod()
	{
		return $this->string('REQUEST_METHOD');
	}

	/**
	 * The timestamp of the start of the request. Available since PHP 5.1.0.
	 * @return int|null
	 */
	public function getRequestTime()
	{
		return $this->int('REQUEST_TIME');
	}

	/**
	 * The timestamp of the start of the request, with microsecond precision. Available since PHP 5.4.0.
	 * @return float|null
	 */
	public function getRequestTimeFloat()
	{
		return $this->float('REQUEST_TIME_FLOAT');
	}

	/**
	 * The query string, if any, via which the page was accessed.
	 * @return null|string
	 */
	public function getQueryString()
	{
		return $this->string('QUERY_STRING');
	}

	/**
	 * The document root directory under which the current script is executing, as defined in the server's configuration file.
	 * @return null|string
	 */
	public function getDocumentRoot()
	{
		return $this->string('DOCUMENT_ROOT');
	}

	/**
	 * Contents of the Accept: header from the current request, if there is one.
	 * @return null|string
	 */
	public function getHttpAccept()
	{
		return $this->string('HTTP_ACCEPT');
	}

	/**
	 * Contents of the Accept-Charset: header from the current request, if there is one. Example: 'iso-8859-1,*,utf-8'.
	 * @return null|string
	 */
	public function getHttpAcceptCharset()
	{
		return $this->string('HTTP_ACCEPT_CHARSET');
	}

	/**
	 * Contents of the Accept-Encoding: header from the current request, if there is one. Example: 'gzip'.
	 * @return null|string
	 */
	public function getHttpAcceptEncoding()
	{
		return $this->string('HTTP_ACCEPT_ENCODING');
	}

	/**
	 * Contents of the Accept-Language: header from the current request, if there is one. Example: 'en'.
	 * @return null|string
	 */
	public function getHttpAcceptLanguage()
	{
		return $this->string('HTTP_ACCEPT_LANGUAGE');
	}

	/**
	 * Contents of the Connection: header from the current request, if there is one. Example: 'Keep-Alive'.
	 * @return null|string
	 */
	public function getHttpConnection()
	{
		return $this->string('HTTP_CONNECTION');
	}

	/**
	 * Contents of the Host: header from the current request, if there is one.
	 * @return null|string
	 */
	public function getHttpHost()
	{
		return $this->string('HTTP_HOST');
	}

	/**
	 * The address of the page (if any) which referred the user agent to the current page. This is set by the user agent. Not all user agents will set this, and some provide the ability to modify HTTP_REFERER as a feature. In short, it cannot really be trusted.
	 * @return null|string
	 */
	public function getHttpReferer()
	{
		return $this->string('HTTP_REFERER');
	}

	/**
	 * Contents of the User-Agent: header from the current request, if there is one. This is a string denoting the user agent being which is accessing the page. A typical example is: Mozilla/4.5 [en] (X11; U; Linux 2.2.9 i586). Among other things, you can use this value with get_browser() to tailor your page's output to the capabilities of the user agent.
	 * @return null|string
	 */
	public function getHttpUserAgent()
	{
		return $this->string('HTTP_USER_AGENT');
	}

	/**
	 * Set to a non-empty value if the script was queried through the HTTPS protocol.
	 * @return bool
	 */
	public function getHttps()
	{
		return $this->boolean('HTTPS');
	}

	/**
	 * The IP address from which the user is viewing the current page.
	 * @return null|string
	 */
	public function getRemoteAddress()
	{
		return $this->string('REMOTE_ADDR');
	}

	/**
	 * The Host name from which the user is viewing the current page. The reverse dns lookup is based off the REMOTE_ADDR of the user.
	 * @return null|string
	 */
	public function getRemoteHost()
	{
		return $this->string('REMOTE_HOST');
	}

	/**
	 * The port being used on the user's machine to communicate with the web server.
	 * @return int|null
	 */
	public function getRemotePort()
	{
		return $this->int('REMOTE_PORT');
	}

	/**
	 * The authenticated user.
	 * @return null|string
	 */
	public function getRemoteUser()
	{
		return $this->string('REMOTE_USER');
	}

	/**
	 * The authenticated user if the request is internally redirected.
	 * @return null|string
	 */
	public function getRedirectRemoteUser()
	{
		return $this->string('REDIRECT_REMOTE_USER');
	}

	/**
	 * The absolute pathname of the currently executing script.
	 * @return null|string
	 */
	public function getScriptFilename()
	{
		return $this->string('SCRIPT_FILENAME');
	}

	/**
	 * The value given to the SERVER_ADMIN (for Apache) directive in the web server configuration file. If the script is running on a virtual host, this will be the value defined for that virtual host.
	 * @return null|string
	 */
	public function getServerAdmin()
	{
		return $this->string('SERVER_ADMIN');
	}

	/**
	 * The port on the server machine being used by the web server for communication. For default setups, this will be '80'; using SSL, for instance, will change this to whatever your defined secure HTTP port is.
	 * <p>
	 * <strong>Note</strong>: Under the Apache 2, you must set UseCanonicalName = On, as well as UseCanonicalPhysicalPort = On in order to get the physical (real) port, otherwise, this value can be spoofed and it may or may not return the physical port value. It is not safe to rely on this value in security-dependent contexts.
	 * </p>
	 * @return int|null
	 */
	public function getServerPort()
	{
		return $this->int('SERVER_PORT');
	}

	/**
	 * String containing the server version and virtual host name which are added to server-generated pages, if enabled.
	 * @return null|string
	 */
	public function getServerSignature()
	{
		return $this->string('SERVER_SIGNATURE');
	}

	/**
	 * Filesystem- (not document root-) based path to the current script, after the server has done any virtual-to-real mapping.
	 * <p>
	 * <strong>Note</strong>: As of PHP 4.3.2, PATH_TRANSLATED is no longer set implicitly under the Apache 2 SAPI in contrast to the situation in Apache 1, where it's set to the same value as the SCRIPT_FILENAME server variable when it's not populated by Apache. This change was made to comply with the CGI specification that PATH_TRANSLATED should only exist if PATH_INFO is defined. Apache 2 users may use AcceptPathInfo = On inside httpd.conf to define PATH_INFO.
	 * </p>
	 * @return null|string
	 */
	public function getPathTranslated()
	{
		return $this->string('PATH_TRANSLATED');
	}

	/**
	 * Contains the current script's path. This is useful for pages which need to point to themselves. The __FILE__ constant contains the full path and filename of the current (i.e. included) file.
	 * @return null|string
	 */
	public function getScriptName()
	{
		return $this->string('SCRIPT_NAME');
	}

	/**
	 * The URI which was given in order to access this page; for instance, '/index.html'.
	 * @return null|string
	 */
	public function getRequestUri()
	{
		return $this->string('REQUEST_URI');
	}

	/**
	 * When doing Digest HTTP authentication this variable is set to the 'Authorization' header sent by the client (which you should then use to make the appropriate validation).
	 * @return null|string
	 */
	public function getPhpAuthDigest()
	{
		return $this->string('PHP_AUTH_DIGEST');
	}

	/**
	 * When doing HTTP authentication this variable is set to the username provided by the user.
	 * @return null|string
	 */
	public function getPhpAuthUser()
	{
		return $this->string('PHP_AUTH_USER');
	}

	/**
	 * When doing HTTP authentication this variable is set to the password provided by the user.
	 * @return null|string
	 */
	public function getPhpAuthPassword()
	{
		return $this->string('PHP_AUTH_PW');
	}

	/**
	 * When doing HTTP authentication this variable is set to the authentication type.
	 * @return null|string
	 */
	public function getAuthType()
	{
		return $this->string('AUTH_TYPE');
	}

	/**
	 * Contains any client-provided pathname information trailing the actual script filename but preceding the query string, if available. For instance, if the current script was accessed via the URL http://www.example.com/php/path_info.php/some/stuff?foo=bar, then $_SERVER['PATH_INFO'] would contain /some/stuff.
	 * @return null|string
	 */
	public function getPathInfo()
	{
		return $this->string('PATH_INFO');
	}

}