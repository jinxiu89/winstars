<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/6
 * Time: 18:33
 */

namespace app\common\model;


class DocumentDownload extends BaseModel
{
    protected $table = 'tb_document_download';

    public function document()
    {
        return $this->belongsTo('Document');
    }

}