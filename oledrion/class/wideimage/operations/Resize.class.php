<?php
    /**
    This file is part of WideImage.

    WideImage is free software; you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation; either version 2.1 of the License, or
    (at your option) any later version.

    WideImage is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with WideImage; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  **/

    class wiInvalidFitMethodException extends wiException {}

    class wioResize
    {
        protected function prepareDimensions($img, $width, $height, $fit)
        {
            if ($width === null)
                $width = $height;

            if ($height === null)
                $height = $width;

            $width = wiDimension::fix($img->getWidth(), $width);
            $height = wiDimension::fix($img->getHeight(), $height);

            $dim = array();
            if ($fit == 'fill')
            {
                $dim['width'] = $width;
                $dim['height'] = $height;
            }
            elseif ($fit == 'inside' || $fit == 'outside')
            {
                $rx = $img->getWidth() / $width;
                $ry = $img->getHeight() / $height;

                if ($fit == 'inside')
                    $ratio = ($rx > $ry) ? $rx : $ry;
                else
                    $ratio = ($rx < $ry) ? $rx : $ry;

                $dim['width'] = round($img->getWidth() / $ratio);
                $dim['height'] = round($img->getHeight() / $ratio);
            }
            else
                throw new wiInvalidFitMethodException("{$fit} is not a valid resize-fit method.");

            return $dim;
        }

        function execute($img, $width, $height, $fit)
        {
            if (!$img instanceof wiImage || !$img->isValid())
                throw new wiInvalidImageException("Can't resize an invalid image.");

            $dim = $this->prepareDimensions($img, $width, $height, $fit);
            $new = wiTrueColorImage::create($dim['width'], $dim['height']);

            if ($img->isTransparent())
            {
                $new->copyTransparencyFrom($img);
                imagecopyresized(
                    $new->getHandle(), $img->getHandle(), 0, 0, 0, 0, $new->getWidth(), $new->getHeight(), $img->getWidth(), $img->getHeight()
                    );
            }
            else
            {
                $new->alphaBlending(false);
                $new->saveAlpha(true);
                imagecopyresampled(
                    $new->getHandle(), $img->getHandle(), 0, 0, 0, 0, $new->getWidth(), $new->getHeight(), $img->getWidth(), $img->getHeight()
                    );
            }

            return $new;
        }
    }
