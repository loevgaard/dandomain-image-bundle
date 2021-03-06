<?php

namespace Loevgaard\DandomainImageBundle\Helper;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tinify\Source;

class Helper
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getImageSettings() {
        return $this->container->getParameter('loevgaard_dandomain_image.image_settings');
    }

    /**
     * Takes any given image path, i.e. /images/image.jpg and returns an array with all image variations, i.e.
     *
     * [
     *     'product' =>     '/images/image.jpg',
     *     'related' =>     '/images/image-r.jpg',
     *     'thumbnail' =>   '/images/image-t.jpg',
     *     'popup' =>       '/images/image-p.jpg',
     *     'original' =>    '/images/image-o.jpg',
     * ]
     *
     * If $stripToBaseImage is set a filename like image-p.jpg will first be stripped to image.jpg
     *
     * @param string $filename
     * @param bool $stripToBaseImage
     * @return array
     */
    public function getImageFilenameVariations($filename, $stripToBaseImage = true) {
        if($stripToBaseImage) {
            // if the image matches this pattern we have to strip out the image variation
            if (preg_match('/\-(p|r|o|t)\.[^.]+$/i', $filename)) {
                $filename = preg_replace('/\-(p|r|o|t)\.([^.]+)$/i', '.$2', $filename);
            }
        }
        $pathInfo = pathinfo($filename);

        $dir = '';
        if($pathInfo['dirname'] != '.') {
            $dir = $pathInfo['dirname'] . '/';
        }

        return [
            'product'   => $dir.$pathInfo['filename'].'.'.$pathInfo['extension'],
            'related'   => $dir.$pathInfo['filename'].'-r.'.$pathInfo['extension'],
            'thumbnail' => $dir.$pathInfo['filename'].'-t.'.$pathInfo['extension'],
            'popup'     => $dir.$pathInfo['filename'].'-p.'.$pathInfo['extension'],
            'original'  => $dir.$pathInfo['filename'].'-o.'.$pathInfo['extension'],
        ];
    }

    public function createImageVariations($file) {
        $imageSettings = $this->getImageSettings();
        $imageFileVariations = $this->getImageFilenameVariations($file);
        $imagesCreated = [];

        if($this->container->getParameter('loevgaard_dandomain_image.tinypng')) {
            // since we are optimizing images we save the original image so we can revert this if we want
            copy($file, $imageFileVariations['original']);
            $imagesCreated['original'] = $imageFileVariations['original'];

            /** @var Source $source */
            $source = \Tinify\fromFile($file);

            foreach ($imageSettings as $imageType => $imageSetting) {
                $image = $source->resize([
                    'method'    => 'fit',
                    'width'     => $imageSetting['width'],
                    'height'    => $imageSetting['height'],
                ]);

                $image->toFile($imageFileVariations[$imageType]);
                $imagesCreated[$imageType] = $imageFileVariations[$imageType];
            }
        } else {
            $imagine = new Imagine();

            $saveOptions = array(
                'resolution-units'      => ImageInterface::RESOLUTION_PIXELSPERINCH,
                'resolution-x'          => $this->container->getParameter('loevgaard_dandomain_image.resolution_x'),
                'resolution-y'          => $this->container->getParameter('loevgaard_dandomain_image.resolution_y'),
                'jpeg_quality'          => $this->container->getParameter('loevgaard_dandomain_image.jpeg_quality'),
                'png_compression_level' => $this->container->getParameter('loevgaard_dandomain_image.png_compression_level'),
            );

            foreach ($imageSettings as $imageType => $imageSetting) {
                $image = $imagine->open($file);

                // if the height is set the image resulting image has to be within a container
                // that has the size of $imageSetting['width'] x $imageSetting['height']
                if ($imageSetting['height']) {
                    if ($image->getSize()->getWidth() > $image->getSize()->getHeight()) {
                        $image->resize($image->getSize()->widen($imageSetting['width']),
                            ImageInterface::FILTER_LANCZOS);
                    } else {
                        $image->resize($image->getSize()->heighten($imageSetting['height']),
                            ImageInterface::FILTER_LANCZOS);
                    }

                    $actualRatio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
                    $wantedRatio = $imageSetting['width'] / $imageSetting['height'];
                    if ($actualRatio !== $wantedRatio) {
                        // if the two ratios are different we add a white background to accommodate the difference
                        $newImage = $imagine->create(new Box($imageSetting['width'], $imageSetting['height']));

                        $x = round(($newImage->getSize()->getWidth() - $image->getSize()->getWidth()) / 2);
                        $y = round(($newImage->getSize()->getHeight() - $image->getSize()->getHeight()) / 2);

                        $newImage->paste($image, new Point($x, $y));
                        $image = $newImage;
                    }
                } else {
                    $image->resize($image->getSize()->widen($imageSetting['width']), ImageInterface::FILTER_LANCZOS);
                }

                $image->save($imageFileVariations[$imageType], $saveOptions);
                $imagesCreated[$imageType] = $imageFileVariations[$imageType];
            }
        }

        return $imagesCreated;
    }
}
