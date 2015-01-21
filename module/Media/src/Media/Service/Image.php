<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 12:25 PM
 */

namespace Media\Service;

class Image extends File
{
    const IMAGES_PATH = "images/";

    const ORIGINAL = 3;
    const BIG_THUMB = 2;
    const SMALL_THUMB = 1;
    const EXTRA_SMALL_THUMB = 4;
    const B_THUMB_WIDTH = 400;
    const B_THUMB_HEIGHT = 400;
    const S_THUMB_WIDTH = 150;
    const S_THUMB_HEIGHT = 150;
    const XS_THUMB_WIDTH = 20;
    const XS_THUMB_HEIGHT = 20;

    /**
     * @param $type
     * @param $id
     * @param $ext
     * @param bool $from
     * @return string
     * @throws \Exception
     */
    public static function imgPath($type, $id, $ext, $from = File::FROM_ROOT)//$onlyPath it's because we need another
    {//path when working with Original and when we are getting it
        if (self::ORIGINAL == $type) {
            if ($from == File::FROM_ROOT) {
                $path = self::PUBLIC_PATH . self::UPLOADS_PATH . self::IMAGES_PATH . "original/";
            } else {
                $path = DIRECTORY_SEPARATOR . self::UPLOADS_PATH . self::IMAGES_PATH . "original/";
            }


        } else {
            $size = self::sizeByType($type);
            if (empty($size)) {
                throw new \Exception('Unsupported size');
            }
            if ($from == File::FROM_ROOT) {
                $path = self::PUBLIC_PATH .
                    self::UPLOADS_PATH .
                    self::IMAGES_PATH .
                    $size['width'] .
                    'x' .
                    $size['height'];
            } elseif ($from == File::FROM_PUBLIC) {
                $path = DIRECTORY_SEPARATOR .
                    self::UPLOADS_PATH .
                    self::IMAGES_PATH .
                    $size['width'] . 'x' . $size['height'];
            }
        }

        return self::buildFilePath($id, $path, $ext);
    }

    /**
     * @param $type
     * @return array
     */
    private static function sizeByType($type)
    {
        switch ($type) {
            case self::BIG_THUMB:
                return array('width' => self::B_THUMB_WIDTH, 'height' => self::B_THUMB_HEIGHT);
            case self::SMALL_THUMB:
                return array('width' => self::S_THUMB_WIDTH, 'height' => self::S_THUMB_HEIGHT);
            case self::EXTRA_SMALL_THUMB:
                return array('width' => self::XS_THUMB_WIDTH, 'height' => self::XS_THUMB_HEIGHT);
        }
    }
}
