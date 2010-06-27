<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Util
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * AjaxUtil.
 */
class AjaxUtil
{
    /**
     * Immediately stops execution and returns an error message.
     *
     * @param string $error Error text.
     * @param string $code  Optional error code, default '400 Bad data'.
     *
     * @return void
     */
    public static function error($message = '', $other = array(), $createauthid = false, $displayalert = true, $code = '400 Bad data')
    {
        if (empty($message)) {
            $type = LogUtil::getErrorType();
            $code = $type ? $type : $code;
            $message = LogUtil::getErrorMessagesText("\n");
        }
        if (!empty($message)) {
            $data = array('errormessage' => $message);
            if (is_array($other)) {
                $data = array_merge($data, $other);
            }
        }
        $data['displayalert'] = ($displayalert === true ? '1' : '0');
        self::output($data, $createauthid, false, true, $code);
    }

    /**
     * Encode data in JSON and return.
     *
     * This functions can add a new authid if requested to do so (default).
     * If the supplied args is not an array, it will be converted to an
     * array with 'data' as key.
     * Authid field will always be named 'authid'. Any other field 'authid'
     * will be overwritten!
     * Script execution stops here
     *
     * @param mixed   $args         String or array of data.
     * @param boolean $createauthid Create a new authid and send it back to the calling javascript.
     * @param boolean $xjsonheader  Send result in X-JSON: header for prototype.js.
     * @param boolean $statusmsg    Include statusmsg in output.
     *
     * @return void
     */
    public static function output($args, $createauthid = false, $xjsonheader = false, $statusmsg = true, $code = '200 OK')
    {
        // check if an error message is set
        $msgs = LogUtil::getErrorMessagesText('<br />');
        if ($msgs != false && !empty($msgs)) {
            self::error($msgs);
        }

        if (!is_array($args)) {
            $data = array('data' => $args);
        } else {
            $data = $args;
        }

        if ($statusmsg === true) {
            // now check if a status message is set
            $msgs = LogUtil::getStatusMessagesText('<br />');
            $data['statusmsg'] = $msgs;
        }

        if ($createauthid === true) {
            $data['authid'] = SecurityUtil::generateAuthKey(ModUtil::getName());
        }

        // set locale to en_US to ensure correct decimal delimiters
        if (stristr(getenv('OS'), 'windows')) {
            setlocale(LC_ALL, 'eng');
        } else {
            setlocale(LC_ALL, 'en_US');
        }

        // convert the data to UTF-8 if not already encoded as such
        // Note: this isn't strict test but relying on the site language pack encoding seems to be a good compromise
        if (ZLanguage::getEncoding() != 'utf-8') {
            $data = DataUtil::convertToUTF8($data);
        }

        $output = json_encode($data);

        header("HTTP/1.0 $code");
        header('Content-type: application/json');
        if ($xjsonheader == true) {
            header('X-JSON:(' . $output . ')');
        }
        echo $output;
        System::shutdown();
    }

}
