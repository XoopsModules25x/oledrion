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

    class wioCopyChannelsPalette
    {
        static function getInstance()
        {
            static $instance;
            if (!$instance)
                $instance = new wioCopyChannelsPalette();

            return $instance;
        }

        function execute($img, $channels)
        {
            $blank = array('red' => 0, 'green' => 0, 'blue' => 0);
            if (isset($channels['alpha']))
                unset($channels['alpha']);

            $width = $img->getWidth();
            $height = $img->getHeight();
            $copy = wiPaletteImage::create($width, $height);

            if ($img->isTransparent())
            {
                $TRGB = $img->getTransparentColorRGB();
                $newTRGB = $blank;
                foreach ($channels as $channel)
                    $newTRGB[$channel] = $TRGB[$channel];

                $tci = $copy->allocateColor($newTRGB);
            }

            if (count($channels) > 0)
                for ($x = 0; $x < $width; $x++)
                    for ($y = 0; $y < $height; $y++)
                    {
                        $RGB = $img->getRGBAt($x, $y);
                        $newRGB = $blank;
                        foreach ($channels as $channel)
                            $newRGB[$channel] = $RGB[$channel];

                        $color = $copy->getExactColor($newRGB);
                        if ($color == -1)
                            $color = $copy->allocateColor($newRGB);

                        $copy->setColorAt($x, $y, $color);
                    }

            if ($img->isTransparent())
                $copy->setTransparentColor($tci);

            return $copy;
        }
    }
