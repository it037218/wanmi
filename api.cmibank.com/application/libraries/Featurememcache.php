<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 *
 * @package	   syslib
 * @subpackage Libraries
 * @author	   luyf <luyf@kingnet.com>
 * @link
 */


class Featurememcache
{


    //Memcache对象
    protected $_memcache ;
    protected $_memcache_prefix = 'baselib';


  
    /**
     * construct
     *
     * @return object	
     */

    public function __construct($config = array()) {

        if (extension_loaded('memcache')) {
            return $this->setup_server($config);
        } else {
            log_message('debug', 'The Memcache extension must be loaded to use Memcache.');
            return FALSE;
        }
    }



    protected function setup_server($config) {

        try{

            $this->_memcache = new Memcache();

            foreach($config as $key => $value) {
				$this->_memcache->addServer( $value['host'], $value['port'] , true , $value['weight']);

            }

        } catch (exception $e) {

            log_message('debug', 'Cache: Memcache connection refused ('.$e->getMessage().')');
            return false;

        }

        return $this;

    }



    public function set( $key , $value , $ttl = 0) {
        return $this->_memcache->set($this->_memcache_prefix . md5($key) , $value , MEMCACHE_COMPRESSED , $ttl);
    }

    public function get( $key ) {
        return $this->_memcache->get($this->_memcache_prefix . md5($key));
    }


    public function delete($key) {
        return $this->_memcache->delete($this->_memcache_prefix . md5($key), 0);
    }


	function memStatus(){ 

		$status = $this->_memcache->getStats();
		if($status){

			echo "<table border='1'>"; 
			echo "<tr><td>Memcache Server version:</td><td> ".$status ["version"]."</td></tr>"; 
			echo "<tr><td>Process id of this server process </td><td>".$status ["pid"]."</td></tr>"; 
			echo "<tr><td>Number of seconds this server has been running </td><td>".$status ["uptime"]."</td></tr>"; 
			echo "<tr><td>Accumulated user time for this process </td><td>".$status ["rusage_user"]." seconds</td></tr>"; 
			echo "<tr><td>Accumulated system time for this process </td><td>".$status ["rusage_system"]." seconds</td></tr>"; 
			echo "<tr><td>Total number of items stored by this server ever since it started </td><td>".$status ["total_items"]."</td></tr>"; 
			echo "<tr><td>Number of open connections </td><td>".$status ["curr_connections"]."</td></tr>"; 
			echo "<tr><td>Total number of connections opened since the server started running </td><td>".$status ["total_connections"]."</td></tr>"; 
			echo "<tr><td>Number of connection structures allocated by the server </td><td>".$status ["connection_structures"]."</td></tr>"; 
			echo "<tr><td>Cumulative number of retrieval requests </td><td>".$status ["cmd_get"]."</td></tr>"; 
			echo "<tr><td> Cumulative number of storage requests </td><td>".$status ["cmd_set"]."</td></tr>"; 

			$percCacheHit=((real)$status ["get_hits"]/ (real)$status ["cmd_get"] *100); 
			$percCacheHit=round($percCacheHit,3); 
			$percCacheMiss=100-$percCacheHit; 

			echo "<tr><td>Number of keys that have been requested and found present </td><td>".$status ["get_hits"]." ($percCacheHit%)</td></tr>"; 
			echo "<tr><td>Number of items that have been requested and not found </td><td>".$status ["get_misses"]."($percCacheMiss%)</td></tr>"; 

			$MBRead= (real)$status["bytes_read"]/(1024*1024); 

			echo "<tr><td>Total number of bytes read by this server from network </td><td>".$MBRead." MB</td></tr>"; 
			$MBWrite=(real) $status["bytes_written"]/(1024*1024) ; 
			echo "<tr><td>Total number of bytes sent by this server to network </td><td>".$MBWrite." MB</td></tr>"; 
			$MBSize=(real) $status["limit_maxbytes"]/(1024*1024) ; 
			echo "<tr><td>Number of bytes this server is allowed to use for storage.</td><td>".$MBSize." MB</td></tr>"; 
			echo "<tr><td>Number of valid items removed from cache to free memory for new items.</td><td>".$status ["evictions"]."</td></tr>"; 

			echo "</table>"; 
		} else {

			echo "empty status";

		}

	} 





    
	/**

	 * Class destructor
	 *
	 * Closes the connection to Redis if present.
	 *
	 * @return	void
	 */
	public function __destruct() {
		if ($this->_memcache) {
			$this->_memcache->close();
		}
	}


}
