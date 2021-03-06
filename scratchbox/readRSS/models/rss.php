<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jorgpatzer
 * Date: 20.03.12
 * Time: 21:01
 * To change this template use File | Settings | File Templates.
 */

/**
 *
 */
class feedRss extends gaiaModelList {

    protected $_title;

    /**
     * @param null $name
     */
    protected function __load($name = NULL) {
        $this->__loadDefault();
    }

    /**
     * @throws gaiaException
     */
    protected function __loadDefault() {

        $feeds = array(
            'postillon' =>  'http://feeds.feedburner.com/blogspot/rkEL?format=xml',
            'heise'     =>  'http://www.heise.de/newsticker/heise-atom.xml',
            'sz'        =>  'http://rss.feedsportal.com/795/f/449002/index.rss',
        );

        if (!isset($feeds[$this->_uid])) throw new gaiaException(__CLASS__.': wrong feed name');

        $xml = @simplexml_load_file(rawurlencode($feeds[$this->_uid]));

        if(empty($xml)) throw new gaiaException(__CLASS__.': failed to load rss feed');

        //check feed structure for title
        if (isset($xml->channel)) {
            $this->_title = $xml->channel->title;

        } elseif(isset($xml->entry)) {
            $this->_title = $xml->title;

        } else {
            $this->_title = $this->_uid;
        }

        // fetch items by xpath, search for local name item in every namespace
        foreach($xml->xpath("//*[local-name() = 'item']") as $item) {
            $this->__items[] = $item;
        }
    }

    /**
     * @param $guid
     * @return mixed
     * @throws gaiaException
     */
    public function byGuid($guid) {

        // Modellist has ArrayPattern, can use like an array
        foreach ($this as $v) {

            if (md5($v->guid) == $guid) {
                return $v;
            }
        }

        throw new gaiaException(__CLASS__.' guid not found');
    }
}