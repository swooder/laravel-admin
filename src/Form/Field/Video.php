<?php

namespace Encore\Admin\Form\Field;

use Intervention\Image\ImageManagerStatic;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Video extends File
{

    protected $calls = [];

    public function defaultStorePath()
    {
        return config('admin.upload.directory.video');
    }


    /**
     * @param $target
     *
     * @return mixed
     */
    public function executeCalls($target)
    {
        if (!empty($this->calls)) {
            $image = ImageManagerStatic::make($target);

            foreach ($this->calls as $call) {
                call_user_func_array([$image, $call['method']], $call['arguments'])->save($target);
            }
        }

        return $target;
    }

    protected function buildPreviewItem($image)
    {
        return '<video src="'.$this->objectUrl($image).'" controls="controls" class="file-preview-video">';
    }


    public function __call($method, $arguments)
    {
        $this->calls[] = [
            'method'    => $method,
            'arguments' => $arguments,
        ];

        return $this;
    }
}
